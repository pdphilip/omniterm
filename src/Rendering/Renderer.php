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

    protected static ?OutputInterface $staticOutput = null;

    public function __construct()
    {
        $this->termWidth = (new Terminal)->getWidth();
    }

    public static function renderUsing(?OutputInterface $output): void
    {
        static::$staticOutput = $output;
    }

    public function render(string $html, int $options = OutputInterface::OUTPUT_NORMAL): void
    {
        $output = static::$staticOutput ?? new ConsoleOutput;
        $ansi = $this->toAnsi($html);
        $output->writeln($ansi, $options);
    }

    public function parse(string $html): ParsedOutput
    {
        $ansi = $this->toAnsi($html);

        return new ParsedOutput($ansi, static::$staticOutput);
    }

    public function toAnsi(string $html): string
    {
        $html = trim($html);
        if ($html === '') {
            return '';
        }

        $dom = new DOMDocument('1.0', 'UTF-8');
        $wrapped = '<meta charset="UTF-8"><body>'.$html.'</body>';
        @$dom->loadHTML($wrapped, LIBXML_NOERROR | LIBXML_NOWARNING);

        $body = $dom->getElementsByTagName('body')->item(0);
        if (! $body) {
            return '';
        }

        $lines = $this->processChildren($body, [], $this->termWidth);

        return implode("\n", $lines);
    }

    // ---------------------------------------------------------------
    // DOM Processing
    // ---------------------------------------------------------------

    protected function processChildren(DOMNode $parent, array $inherited, int $availableWidth): array
    {
        $lines = [];
        foreach ($parent->childNodes as $node) {
            if ($node instanceof DOMText) {
                $text = $this->collapseWhitespace($this->cleanText($node->textContent));
                if ($text !== '') {
                    $lines[] = $this->applyInheritedStyle($text, $inherited);
                }
            } elseif ($node instanceof DOMElement) {
                $childLines = $this->processElement($node, $inherited, $availableWidth);
                array_push($lines, ...$childLines);
            }
        }

        return $lines;
    }

    protected function processElement(DOMElement $el, array $inherited, int $availableWidth): array
    {
        $styles = $this->parseClasses($el->getAttribute('class'));
        $tag = strtolower($el->tagName);
        $merged = $this->mergeInherited($inherited, $styles);

        $ml = $styles['ml'] ?: ($styles['mx'] ?: $styles['m']);
        $mr = $styles['mr'] ?: ($styles['mx'] ?: $styles['m']);
        $mb = $styles['mb'] ?: $styles['m'];
        $mt = $styles['mt'] ?: $styles['m'];

        if ($tag === 'div' && $styles['flex']) {
            $rowWidth = $styles['w'] ?? ($availableWidth - $ml - $mr);
            $flexLines = $this->processFlexRow($el, $styles, $merged, $rowWidth);

            if ($ml || $mr) {
                $leftPad = str_repeat(' ', $ml);
                $rightPad = str_repeat(' ', $mr);
                $flexLines = array_map(fn ($l) => $leftPad.$l.$rightPad, $flexLines);
            }

            $lines = $flexLines;
        } elseif ($tag === 'div') {
            $innerWidth = ($styles['w'] ?? $availableWidth) - $ml - $mr;
            $lines = $this->processChildren($el, $merged, $innerWidth);

            if ($ml || $mr) {
                $leftPad = str_repeat(' ', $ml);
                $rightPad = str_repeat(' ', $mr);
                $lines = array_map(fn ($l) => $leftPad.$l.$rightPad, $lines);
            }
        } else {
            $text = $this->renderInline($el, $styles, $inherited);
            $lines = [$text];
        }

        if ($mt) {
            array_unshift($lines, ...array_fill(0, $mt, ''));
        }
        if ($mb) {
            array_push($lines, ...array_fill(0, $mb, ''));
        }

        return $lines;
    }

    // ---------------------------------------------------------------
    // Flex Layout
    // ---------------------------------------------------------------

    /**
     * @return array<string>
     */
    protected function processFlexRow(DOMElement $el, array $containerStyles, array $inherited, int $rowWidth): array
    {
        $spaceX = $containerStyles['spaceX'];

        // Separate inline children (span/text) from block children (div)
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
                $lines[] = $this->layoutFlexLine($group['nodes'], $containerStyles, $inherited, $rowWidth, $spaceX);
            } else {
                $blockLines = $this->processElement($group['node'], $inherited, $rowWidth);
                array_push($lines, ...$blockLines);
            }
        }

        if (empty($lines)) {
            $lines = [str_repeat(' ', $rowWidth)];
        }

        return $lines;
    }

    protected function layoutFlexLine(array $children, array $containerStyles, array $inherited, int $rowWidth, int $spaceX): string
    {
        $pl = $containerStyles['pl'] ?: $containerStyles['px'];
        $pr = $containerStyles['pr'] ?: $containerStyles['px'];
        $innerWidth = $rowWidth - $pl - $pr;

        if (empty($children)) {
            return str_repeat(' ', $rowWidth);
        }

        // Phase 1: Measure children
        $childInfos = [];
        $totalFixed = 0;
        $flexCount = 0;
        $totalGaps = max(0, count($children) - 1) * $spaceX;

        foreach ($children as $child) {
            $info = $this->measureFlexChild($child, $inherited);
            $childInfos[] = $info;
            if ($info['type'] === 'flex') {
                $flexCount++;
            } else {
                $totalFixed += $info['totalWidth'];
            }
        }

        // Phase 2: Allocate flex widths
        $remaining = $innerWidth - $totalGaps - $totalFixed;
        $flexWidth = ($flexCount > 0 && $remaining > 0)
            ? (int) floor($remaining / $flexCount)
            : 0;

        // Phase 3: Render
        $containerBg = $containerStyles['bgColor'];
        $parts = [];

        if ($pl) {
            $parts[] = $this->styledSpaces($pl, $containerBg);
        }

        foreach ($childInfos as $i => $info) {
            if ($i > 0 && $spaceX > 0) {
                $parts[] = str_repeat(' ', $spaceX);
            }
            $width = $info['type'] === 'flex' ? $flexWidth : $info['totalWidth'];
            $parts[] = $this->renderFlexChild($info, $width, $inherited);
        }

        if ($pr) {
            $parts[] = $this->styledSpaces($pr, $containerBg);
        }

        return implode('', $parts);
    }

    protected function measureFlexChild(DOMNode $node, array $inherited): array
    {
        if ($node instanceof DOMText) {
            $text = $this->collapseWhitespace($this->cleanText($node->textContent));

            return [
                'type' => 'content',
                'node' => $node,
                'totalWidth' => mb_strwidth($text),
                'styles' => [],
            ];
        }

        /** @var DOMElement $el */
        $el = $node;
        $styles = $this->parseClasses($el->getAttribute('class'));
        $merged = $this->mergeInherited($inherited, $styles);

        $ml = $styles['ml'] ?: ($styles['mx'] ?: $styles['m']);
        $mr = $styles['mr'] ?: ($styles['mx'] ?: $styles['m']);
        $padL = $styles['pl'] ?: $styles['px'];
        $padR = $styles['pr'] ?: $styles['px'];

        if ($styles['flex1']) {
            return [
                'type' => 'flex',
                'node' => $node,
                'styles' => $styles,
                'totalWidth' => 0,
                'ml' => $ml,
                'mr' => $mr,
                'padL' => $padL,
                'padR' => $padR,
            ];
        }

        if ($styles['w'] !== null) {
            return [
                'type' => 'content',
                'node' => $node,
                'styles' => $styles,
                'totalWidth' => $styles['w'] + $ml + $mr,
                'innerWidth' => $styles['w'],
                'ml' => $ml,
                'mr' => $mr,
                'padL' => $padL,
                'padR' => $padR,
            ];
        }

        // Nested flex container — measure by summing children
        if (strtolower($el->tagName) === 'div' && $styles['flex']) {
            $innerPl = $padL;
            $innerPr = $padR;
            $innerSpaceX = $styles['spaceX'];
            $childWidths = 0;
            $childCount = 0;

            foreach ($el->childNodes as $innerChild) {
                if ($innerChild instanceof DOMText && trim($innerChild->textContent) === '') {
                    continue;
                }
                $innerInfo = $this->measureFlexChild($innerChild, $merged);
                $childWidths += $innerInfo['totalWidth'];
                $childCount++;
            }

            $gaps = max(0, $childCount - 1) * $innerSpaceX;

            return [
                'type' => 'content',
                'node' => $node,
                'styles' => $styles,
                'totalWidth' => $ml + $innerPl + $childWidths + $gaps + $innerPr + $mr,
                'ml' => $ml,
                'mr' => $mr,
                'padL' => $padL,
                'padR' => $padR,
            ];
        }

        // Inline element — measure content
        if ($this->hasChildElements($el)) {
            // Has nested elements — pre-render to get accurate width
            $rendered = $this->renderInlineChildren($el, $merged);
            $contentLen = $this->visibleLength($rendered);
        } else {
            $contentLen = mb_strwidth($this->collapseWhitespace($this->getTextContent($el)));
        }

        return [
            'type' => 'content',
            'node' => $node,
            'styles' => $styles,
            'totalWidth' => $ml + $padL + $contentLen + $padR + $mr,
            'contentLen' => $contentLen,
            'ml' => $ml,
            'mr' => $mr,
            'padL' => $padL,
            'padR' => $padR,
        ];
    }

    protected function renderFlexChild(array $info, int $allocatedWidth, array $inherited): string
    {
        $node = $info['node'];
        $styles = $info['styles'] ?? [];

        // Plain text node
        if ($node instanceof DOMText) {
            $text = $this->collapseWhitespace($this->cleanText($node->textContent));

            return $this->padToWidth($text, $allocatedWidth, 'left');
        }

        /** @var DOMElement $el */
        $el = $node;
        $merged = $this->mergeInherited($inherited, $styles);

        $ml = $info['ml'] ?? 0;
        $mr = $info['mr'] ?? 0;
        $padL = $info['padL'] ?? 0;
        $padR = $info['padR'] ?? 0;

        // Nested flex container
        if (strtolower($el->tagName) === 'div' && ($styles['flex'] ?? false)) {
            $innerWidth = $allocatedWidth - $ml - $mr;
            $renderedLines = $this->processFlexRow($el, $styles, $merged, $innerWidth);
            $rendered = implode("\n", $renderedLines);
            $result = '';
            if ($ml) {
                $result .= str_repeat(' ', $ml);
            }
            $result .= $rendered;
            if ($mr) {
                $result .= str_repeat(' ', $mr);
            }

            return $result;
        }

        // Regular element
        $elementWidth = $allocatedWidth - $ml - $mr;
        $contentWidth = max(0, $elementWidth - $padL - $padR);

        $textColor = $styles['textColor'] ?? $inherited['textColor'] ?? null;
        $bgColor = $styles['bgColor'] ?? null;
        $bold = ($styles['bold'] ?? false) || ($inherited['bold'] ?? false);

        $align = 'left';
        if ($styles['textCenter'] ?? false) {
            $align = 'center';
        }
        if ($styles['textRight'] ?? false) {
            $align = 'right';
        }

        // Build content
        $contentRepeat = $styles['contentRepeat'] ?? null;
        if ($contentRepeat !== null) {
            $content = $this->repeatToWidth($contentRepeat, $contentWidth);
        } elseif ($this->hasChildElements($el)) {
            $content = $this->renderInlineChildren($el, $merged);
            $visLen = $this->visibleLength($content);
            $pad = max(0, $contentWidth - $visLen);
            $content = match ($align) {
                'right' => str_repeat(' ', $pad).$content,
                'center' => str_repeat(' ', (int) floor($pad / 2)).$content.str_repeat(' ', $pad - (int) floor($pad / 2)),
                default => $content.str_repeat(' ', $pad),
            };
        } else {
            $text = $this->getTextContent($el);
            $content = $this->padToWidth($text, $contentWidth, $align);
        }

        // Assemble with ANSI codes
        $prefix = $this->buildAnsiPrefix($textColor, $bgColor, $bold);
        $suffix = $prefix !== '' ? "\e[0m" : '';

        $inner = str_repeat(' ', $padL).$content.str_repeat(' ', $padR);
        $styled = $prefix.$inner.$suffix;

        $result = '';
        if ($ml) {
            $result .= str_repeat(' ', $ml);
        }
        $result .= $styled;
        if ($mr) {
            $result .= str_repeat(' ', $mr);
        }

        return $result;
    }

    // ---------------------------------------------------------------
    // Inline Rendering
    // ---------------------------------------------------------------

    protected function renderInline(DOMElement $el, array $styles, array $inherited): string
    {
        $merged = $this->mergeInherited($inherited, $styles);

        $textColor = $styles['textColor'] ?? $inherited['textColor'] ?? null;
        $bgColor = $styles['bgColor'] ?? null;
        $bold = ($styles['bold'] ?? false) || ($inherited['bold'] ?? false);

        $content = $this->renderInlineChildren($el, $merged);

        $prefix = $this->buildAnsiPrefix($textColor, $bgColor, $bold);
        if ($prefix !== '') {
            return $prefix.$content."\e[0m";
        }

        return $content;
    }

    protected function renderInlineChildren(DOMElement $el, array $inherited): string
    {
        $parts = [];
        foreach ($el->childNodes as $node) {
            if ($node instanceof DOMText) {
                $text = $this->collapseWhitespace($this->cleanText($node->textContent));
                if ($text !== '') {
                    $parts[] = $this->applyInheritedStyle($text, $inherited);
                }
            } elseif ($node instanceof DOMElement) {
                $childStyles = $this->parseClasses($node->getAttribute('class'));
                $parts[] = $this->renderInline($node, $childStyles, $inherited);
            }
        }

        return implode('', $parts);
    }

    // ---------------------------------------------------------------
    // Style Utilities
    // ---------------------------------------------------------------

    protected function parseClasses(string $classStr): array
    {
        $result = [
            'flex' => false, 'flex1' => false, 'bold' => false,
            'textCenter' => false, 'textRight' => false,
            'textColor' => null, 'bgColor' => null,
            'w' => null, 'px' => 0, 'pl' => 0, 'pr' => 0,
            'mx' => 0, 'ml' => 0, 'mr' => 0,
            'mb' => 0, 'mt' => 0, 'm' => 0,
            'spaceX' => 0, 'contentRepeat' => null,
        ];

        foreach (preg_split('/\s+/', trim($classStr)) as $class) {
            if ($class === '') {
                continue;
            }

            // Layout & typography (check before text-* colors)
            if ($class === 'flex') {
                $result['flex'] = true;

                continue;
            }
            if ($class === 'flex-1') {
                $result['flex1'] = true;

                continue;
            }
            if ($class === 'font-bold') {
                $result['bold'] = true;

                continue;
            }
            if ($class === 'text-center') {
                $result['textCenter'] = true;

                continue;
            }
            if ($class === 'text-right') {
                $result['textRight'] = true;

                continue;
            }

            // Fixed width
            if (preg_match('/^w-(\d+)$/', $class, $m)) {
                $result['w'] = (int) $m[1];

                continue;
            }

            // Spacing
            if (preg_match('/^px-(\d+)$/', $class, $m)) {
                $result['px'] = (int) $m[1];

                continue;
            }
            if (preg_match('/^pl-(\d+)$/', $class, $m)) {
                $result['pl'] = (int) $m[1];

                continue;
            }
            if (preg_match('/^pr-(\d+)$/', $class, $m)) {
                $result['pr'] = (int) $m[1];

                continue;
            }
            if (preg_match('/^mx-(\d+)$/', $class, $m)) {
                $result['mx'] = (int) $m[1];

                continue;
            }
            if (preg_match('/^ml-(\d+)$/', $class, $m)) {
                $result['ml'] = (int) $m[1];

                continue;
            }
            if (preg_match('/^mr-(\d+)$/', $class, $m)) {
                $result['mr'] = (int) $m[1];

                continue;
            }
            if (preg_match('/^mb-(\d+)$/', $class, $m)) {
                $result['mb'] = (int) $m[1];

                continue;
            }
            if (preg_match('/^mt-(\d+)$/', $class, $m)) {
                $result['mt'] = (int) $m[1];

                continue;
            }
            if (preg_match('/^m-(\d+)$/', $class, $m)) {
                $result['m'] = (int) $m[1];

                continue;
            }
            if (preg_match('/^space-x-(\d+)$/', $class, $m)) {
                $result['spaceX'] = (int) $m[1];

                continue;
            }

            // Content repeat — e.g. content-repeat-[─]
            if (preg_match('/^content-repeat-\[(.)\]$/u', $class, $m)) {
                $result['contentRepeat'] = $m[1];

                continue;
            }

            // Colors
            if (str_starts_with($class, 'text-')) {
                $result['textColor'] = $this->resolveColor(substr($class, 5));

                continue;
            }
            if (str_starts_with($class, 'bg-')) {
                $result['bgColor'] = $this->resolveColor(substr($class, 3));
            }
        }

        return $result;
    }

    protected function resolveColor(string $value): ?array
    {
        // No shade — e.g. "gray" → gray-500
        if (! str_contains($value, '-')) {
            return Colors::rgb($value, 500);
        }

        $parts = explode('-', $value);
        if (count($parts) === 2 && is_numeric($parts[1])) {
            return Colors::rgb($parts[0], (int) $parts[1]);
        }

        return null;
    }

    protected function mergeInherited(array $inherited, array $styles): array
    {
        return [
            'textColor' => $styles['textColor'] ?? $inherited['textColor'] ?? null,
            'bold' => ($styles['bold'] ?? false) || ($inherited['bold'] ?? false),
        ];
    }

    protected function buildAnsiPrefix(?array $textColor, ?array $bgColor, bool $bold): string
    {
        $prefix = '';
        if ($textColor) {
            $prefix .= Colors::fgFromRgb($textColor);
        }
        if ($bgColor) {
            $prefix .= Colors::bgFromRgb($bgColor);
        }
        if ($bold) {
            $prefix .= "\e[1m";
        }

        return $prefix;
    }

    protected function applyInheritedStyle(string $text, array $inherited): string
    {
        $textColor = $inherited['textColor'] ?? null;
        $bold = $inherited['bold'] ?? false;
        $prefix = $this->buildAnsiPrefix($textColor, null, $bold);
        if ($prefix !== '') {
            return $prefix.$text."\e[0m";
        }

        return $text;
    }

    // ---------------------------------------------------------------
    // Text Utilities
    // ---------------------------------------------------------------

    protected function getTextContent(DOMNode $node): string
    {
        return $this->cleanText($node->textContent);
    }

    protected function cleanText(string $text): string
    {
        // Convert non-breaking spaces to regular spaces
        return str_replace("\xC2\xA0", ' ', $text);
    }

    protected function collapseWhitespace(string $text): string
    {
        // Collapse runs of regular whitespace into single space, then trim
        return trim(preg_replace('/[ \t\n\r]+/', ' ', $text));
    }

    protected function padToWidth(string $text, int $width, string $align): string
    {
        $visLen = mb_strwidth($text);
        $pad = max(0, $width - $visLen);

        return match ($align) {
            'right' => str_repeat(' ', $pad).$text,
            'center' => str_repeat(' ', (int) floor($pad / 2)).$text.str_repeat(' ', $pad - (int) floor($pad / 2)),
            default => $text.str_repeat(' ', $pad),
        };
    }

    protected function repeatToWidth(string $char, int $width): string
    {
        if ($width <= 0) {
            return '';
        }
        $charWidth = mb_strwidth($char);
        if ($charWidth === 0) {
            return str_repeat(' ', $width);
        }
        $count = (int) floor($width / $charWidth);

        return str_repeat($char, $count);
    }

    protected function styledSpaces(int $count, ?array $bgColor): string
    {
        if ($count <= 0) {
            return '';
        }
        $spaces = str_repeat(' ', $count);
        if ($bgColor) {
            return Colors::bgFromRgb($bgColor).$spaces."\e[0m";
        }

        return $spaces;
    }

    protected function visibleLength(string $text): int
    {
        $stripped = preg_replace('/\e\[[0-9;]*m/', '', $text);

        return mb_strwidth($stripped);
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
