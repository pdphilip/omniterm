<?php

declare(strict_types=1);

namespace OmniTerm\Rendering;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Renderer
{
    protected int $termWidth;

    protected ClassParser $classes;

    protected static ?OutputInterface $staticOutput = null;

    public function __construct()
    {
        $this->termWidth = (new Terminal)->getWidth();
        $this->classes = new ClassParser;
    }

    public static function renderUsing(?OutputInterface $output): void
    {
        static::$staticOutput = $output;
    }

    public function render(string $html, int $options = OutputInterface::OUTPUT_NORMAL): void
    {
        $output = static::$staticOutput ?? new ConsoleOutput;
        $output->writeln($this->toAnsi($html), $options);
    }

    public function parse(string $html): ParsedOutput
    {
        return new ParsedOutput($this->toAnsi($html), static::$staticOutput);
    }

    public function toAnsi(string $html): string
    {
        $html = trim($html);
        if ($html === '') {
            return '';
        }

        $body = $this->loadBody($html);
        if (! $body) {
            return '';
        }

        return implode("\n", $this->processChildren($body, [], $this->termWidth));
    }

    protected function loadBody(string $html): ?DOMNode
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        @$dom->loadHTML('<meta charset="UTF-8"><body>'.$html.'</body>', LIBXML_NOERROR | LIBXML_NOWARNING);

        return $dom->getElementsByTagName('body')->item(0);
    }

    protected function processChildren(DOMNode $parent, array $inherited, int $availableWidth): array
    {
        $lines = [];
        foreach ($parent->childNodes as $node) {
            if ($node instanceof DOMText) {
                $text = $this->collapseWhitespace($this->cleanText($node->textContent));
                if ($text !== '') {
                    $lines[] = Ansi::wrapInherited($text, $inherited);
                }
            } elseif ($node instanceof DOMElement) {
                array_push($lines, ...$this->processElement($node, $inherited, $availableWidth));
            }
        }

        return $lines;
    }

    protected function processElement(DOMElement $el, array $inherited, int $availableWidth): array
    {
        $style = new ElementStyle($el, $inherited, $this->classes);

        if ($style->isFlexDiv()) {
            return $style->wrapLines($this->processFlexRow($el, $style, $style->rowWidth($availableWidth)));
        }

        if ($style->isDiv()) {
            return $style->wrapLines($this->processChildren($el, $style->merged, $style->innerWidth($availableWidth)));
        }

        return $style->applyVerticalMargins([$this->renderInline($el, $style)]);
    }

    protected function processFlexRow(DOMElement $el, ElementStyle $style, int $rowWidth): array
    {
        $groups = [];
        $currentInline = [];

        foreach ($el->childNodes as $node) {
            if ($node instanceof DOMText) {
                if (trim($node->textContent) !== '') {
                    $currentInline[] = $node;
                }

                continue;
            }
            if ($node instanceof DOMElement) {
                if (strtolower($node->tagName) === 'div') {
                    if (! empty($currentInline)) {
                        $groups[] = ['type' => 'inline', 'nodes' => $currentInline];
                        $currentInline = [];
                    }
                    $groups[] = ['type' => 'block', 'node' => $node];
                } else {
                    $currentInline[] = $node;
                }
            }
        }
        if (! empty($currentInline)) {
            $groups[] = ['type' => 'inline', 'nodes' => $currentInline];
        }

        $lines = [];
        foreach ($groups as $group) {
            if ($group['type'] === 'inline') {
                $lines[] = $this->layoutFlexLine($group['nodes'], $style, $rowWidth);
            } else {
                array_push($lines, ...$this->processElement($group['node'], $style->merged, $rowWidth));
            }
        }

        if (empty($lines)) {
            $lines = [str_repeat(' ', $rowWidth)];
        }

        return $lines;
    }

    protected function layoutFlexLine(array $children, ElementStyle $style, int $rowWidth): string
    {
        $innerWidth = $rowWidth - $style->pl - $style->pr;

        if (empty($children)) {
            return str_repeat(' ', $rowWidth);
        }

        $measured = $this->measureChildren($children, $style->merged, $style->spaceX);
        $remaining = $innerWidth - $measured['totalGaps'] - $measured['totalFixed'];
        $flexWidth = ($measured['flexCount'] > 0 && $remaining > 0)
            ? (int) floor($remaining / $measured['flexCount'])
            : 0;

        $parts = [];

        if ($style->pl) {
            $parts[] = Ansi::styledSpaces($style->pl, $style->bgColor);
        }

        foreach ($measured['infos'] as $i => $info) {
            if ($i > 0 && $style->spaceX > 0) {
                $parts[] = str_repeat(' ', $style->spaceX);
            }
            $width = $info['type'] === 'flex' ? $flexWidth : $info['totalWidth'];
            $parts[] = $this->renderFlexChild($info, $width, $style->merged);
        }

        if ($style->pr) {
            $parts[] = Ansi::styledSpaces($style->pr, $style->bgColor);
        }

        $line = implode('', $parts);

        if ($style->gradient && $style->gradient['from']) {
            $line = Ansi::applyGradient($line, $rowWidth, $style->gradient);
        }

        return $line;
    }

    protected function measureChildren(array $children, array $inherited, int $spaceX): array
    {
        $infos = [];
        $totalFixed = 0;
        $flexCount = 0;

        foreach ($children as $child) {
            $info = $this->measureFlexChild($child, $inherited);
            $infos[] = $info;
            if ($info['type'] === 'flex') {
                $flexCount++;
            } else {
                $totalFixed += $info['totalWidth'];
            }
        }

        return [
            'infos' => $infos,
            'totalFixed' => $totalFixed,
            'flexCount' => $flexCount,
            'totalGaps' => max(0, count($children) - 1) * $spaceX,
        ];
    }

    protected function measureFlexChild(DOMNode $node, array $inherited): array
    {
        if ($node instanceof DOMText) {
            $text = $this->collapseWhitespace($this->cleanText($node->textContent));

            return [
                'type' => 'content',
                'node' => $node,
                'totalWidth' => mb_strwidth($text),
            ];
        }

        /** @var DOMElement $el */
        $el = $node;
        $style = new ElementStyle($el, $inherited, $this->classes);

        if ($style->flex1) {
            return [
                'type' => 'flex',
                'node' => $node,
                'totalWidth' => 0,
                'isFlexDiv' => false,
            ];
        }

        if ($style->w !== null) {
            return [
                'type' => 'content',
                'node' => $node,
                'totalWidth' => $style->w + $style->ml + $style->mr,
                'isFlexDiv' => $style->isFlexDiv(),
            ];
        }

        if ($style->isFlexDiv()) {
            $childWidths = 0;
            $childCount = 0;

            foreach ($el->childNodes as $innerChild) {
                if ($innerChild instanceof DOMText && trim($innerChild->textContent) === '') {
                    continue;
                }
                $childWidths += $this->measureFlexChild($innerChild, $style->merged)['totalWidth'];
                $childCount++;
            }

            $gaps = max(0, $childCount - 1) * $style->spaceX;

            return [
                'type' => 'content',
                'node' => $node,
                'totalWidth' => $style->ml + $style->pl + $childWidths + $gaps + $style->pr + $style->mr,
                'isFlexDiv' => true,
            ];
        }

        if ($this->hasChildElements($el)) {
            $contentLen = Ansi::visibleLength($this->renderInlineChildren($el, $style->merged));
        } else {
            $contentLen = mb_strwidth($this->collapseWhitespace($this->cleanText($el->textContent)));
        }

        return [
            'type' => 'content',
            'node' => $node,
            'totalWidth' => $style->ml + $style->pl + $contentLen + $style->pr + $style->mr,
            'isFlexDiv' => false,
        ];
    }

    protected function renderFlexChild(array $info, int $allocatedWidth, array $inherited): string
    {
        $node = $info['node'];

        if ($node instanceof DOMText) {
            return Ansi::pad($this->collapseWhitespace($this->cleanText($node->textContent)), $allocatedWidth);
        }

        /** @var DOMElement $el */
        $el = $node;

        if ($info['isFlexDiv'] ?? false) {
            return $this->renderFlexContainer($el, $inherited, $allocatedWidth);
        }

        return $this->renderFlexElement($el, $inherited, $allocatedWidth);
    }

    protected function renderFlexContainer(DOMElement $el, array $inherited, int $allocatedWidth): string
    {
        $style = new ElementStyle($el, $inherited, $this->classes);
        $rendered = implode("\n", $this->processFlexRow($el, $style, $style->elementWidth($allocatedWidth)));

        return $style->addMargins($rendered);
    }

    protected function renderFlexElement(DOMElement $el, array $inherited, int $allocatedWidth): string
    {
        $style = new ElementStyle($el, $inherited, $this->classes);
        $content = $this->buildContent($el, $style, $style->contentWidth($allocatedWidth));
        $styled = $style->styleContent($style->padContent($content), $style->elementWidth($allocatedWidth));

        return $style->addMargins($styled);
    }

    protected function buildContent(DOMElement $el, ElementStyle $style, int $width): string
    {
        if ($style->contentRepeat !== null) {
            return Ansi::repeatChar($style->contentRepeat, $width);
        }

        if ($this->hasChildElements($el)) {
            return Ansi::pad($this->renderInlineChildren($el, $style->merged), $width, $style->align);
        }

        return Ansi::pad($this->cleanText($el->textContent), $width, $style->align);
    }

    protected function renderInline(DOMElement $el, ElementStyle $style): string
    {
        return Ansi::wrap($this->renderInlineChildren($el, $style->merged), $style->textColor, $style->bgColor, $style->bold);
    }

    protected function renderInlineChildren(DOMElement $el, array $inherited): string
    {
        $parts = [];
        foreach ($el->childNodes as $node) {
            if ($node instanceof DOMText) {
                $text = $this->collapseWhitespace($this->cleanText($node->textContent));
                if ($text !== '') {
                    $parts[] = Ansi::wrapInherited($text, $inherited);
                }
            } elseif ($node instanceof DOMElement) {
                $parts[] = $this->renderInline($node, new ElementStyle($node, $inherited, $this->classes));
            }
        }

        return implode('', $parts);
    }

    protected function cleanText(string $text): string
    {
        return str_replace("\xC2\xA0", ' ', $text);
    }

    protected function collapseWhitespace(string $text): string
    {
        return trim(preg_replace('/[ \t\n\r]+/', ' ', $text));
    }

    protected function hasChildElements(DOMElement $el): bool
    {
        foreach ($el->childNodes as $child) {
            if ($child instanceof DOMElement) {
                return true;
            }
        }

        return false;
    }
}
