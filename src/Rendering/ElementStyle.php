<?php

declare(strict_types=1);

namespace OmniTerm\Rendering;

use DOMElement;

class ElementStyle
{
    public readonly string $tag;

    public readonly bool $flex;

    public readonly bool $flex1;

    public readonly bool $bold;

    public readonly bool $italic;

    public readonly bool $underline;

    public readonly bool $lineThrough;

    public readonly ?string $textTransform;

    public readonly bool $truncate;

    public readonly ?array $textColor;

    public readonly ?array $bgColor;

    public readonly ?array $gradient;

    public readonly ?int $w;

    public readonly ?int $minW;

    public readonly ?int $maxW;

    public readonly bool $wFull;

    public readonly bool $wAuto;

    public readonly int $spaceX;

    public readonly int $spaceY;

    public readonly ?string $contentRepeat;

    public readonly string $align;

    public readonly int $ml;

    public readonly int $mr;

    public readonly int $mt;

    public readonly int $mb;

    public readonly int $pl;

    public readonly int $pr;

    public readonly int $pt;

    public readonly int $pb;

    public readonly bool $block;

    public readonly bool $hidden;

    public readonly bool $invisible;

    public readonly ?string $justify;

    public readonly ?string $listStyle;

    public readonly array $merged;

    public function __construct(DOMElement $el, array $inherited, ClassParser $parser)
    {
        $raw = $parser->parse($el->getAttribute('class'));
        $spacing = $parser->resolveSpacing($raw);

        $this->tag = strtolower($el->tagName);
        $this->flex = $raw['flex'];
        $this->flex1 = $raw['flex1'];
        $this->bgColor = $raw['bgColor'];
        $this->gradient = $raw['gradient'];
        $this->w = $raw['w'];
        $this->minW = $raw['minW'];
        $this->maxW = $raw['maxW'];
        $this->wFull = $raw['wFull'];
        $this->wAuto = $raw['wAuto'];
        $this->spaceX = $raw['spaceX'];
        $this->spaceY = $raw['spaceY'];
        $this->contentRepeat = $raw['contentRepeat'];
        $this->align = $parser->resolveAlignment($raw);
        $this->truncate = $raw['truncate'];
        $this->block = $raw['block'];
        $this->hidden = $raw['hidden'];
        $this->invisible = $raw['invisible'];
        $this->justify = $raw['justify'];
        $this->listStyle = $raw['listStyle'];

        $this->ml = $spacing['ml'];
        $this->mr = $spacing['mr'];
        $this->mt = $spacing['mt'];
        $this->mb = $spacing['mb'];
        $this->pl = $spacing['pl'];
        $this->pr = $spacing['pr'];
        $this->pt = $spacing['pt'];
        $this->pb = $spacing['pb'];

        $this->merged = $parser->mergeInherited($inherited, $raw);
        $this->textColor = $this->merged['textColor'];
        $this->bold = $this->merged['bold'];
        $this->italic = $this->merged['italic'];
        $this->underline = $this->merged['underline'];
        $this->lineThrough = $this->merged['lineThrough'];
        $this->textTransform = $this->merged['textTransform'];
    }

    public function isFlexDiv(): bool
    {
        return ($this->tag === 'div' || $this->block) && $this->flex;
    }

    public function isDiv(): bool
    {
        return $this->tag === 'div' || $this->block;
    }

    public function constrainWidth(int $width): int
    {
        if ($this->minW !== null && $width < $this->minW) {
            $width = $this->minW;
        }
        if ($this->maxW !== null && $width > $this->maxW) {
            $width = $this->maxW;
        }

        return $width;
    }

    public function rowWidth(int $availableWidth): int
    {
        return $this->constrainWidth($this->w ?? ($availableWidth - $this->ml - $this->mr));
    }

    public function innerWidth(int $availableWidth): int
    {
        return $this->constrainWidth(($this->w ?? $availableWidth) - $this->ml - $this->mr);
    }

    public function contentWidth(int $allocatedWidth): int
    {
        return max(0, $allocatedWidth - $this->ml - $this->mr - $this->pl - $this->pr);
    }

    public function elementWidth(int $allocatedWidth): int
    {
        return $allocatedWidth - $this->ml - $this->mr;
    }

    public function padContent(string $content): string
    {
        if (! $this->pl && ! $this->pr) {
            return $content;
        }

        return str_repeat(' ', $this->pl).$content.str_repeat(' ', $this->pr);
    }

    public function styleContent(string $inner, int $elementWidth): string
    {
        if ($this->gradient && $this->gradient['from']) {
            $prefix = Ansi::buildPrefix($this->textColor, null, $this->bold, $this->italic, $this->underline, $this->lineThrough);
            $suffix = $prefix !== '' ? Ansi::reset() : '';

            return Ansi::applyGradient($prefix.$inner.$suffix, $elementWidth, $this->gradient);
        }

        return Ansi::wrap($inner, $this->textColor, $this->bgColor, $this->bold, $this->italic, $this->underline, $this->lineThrough);
    }

    public function wrapLines(array $lines): array
    {
        if ($this->pt) {
            array_unshift($lines, ...array_fill(0, $this->pt, ''));
        }
        if ($this->pb) {
            array_push($lines, ...array_fill(0, $this->pb, ''));
        }

        if ($this->ml || $this->mr) {
            $left = str_repeat(' ', $this->ml);
            $right = str_repeat(' ', $this->mr);
            $lines = array_map(fn ($l) => $left.$l.$right, $lines);
        }

        return $this->applyVerticalMargins($lines);
    }

    public function applyVerticalMargins(array $lines): array
    {
        if ($this->mt) {
            array_unshift($lines, ...array_fill(0, $this->mt, ''));
        }
        if ($this->mb) {
            array_push($lines, ...array_fill(0, $this->mb, ''));
        }

        return $lines;
    }

    public function addMargins(string $content): string
    {
        if (! $this->ml && ! $this->mr) {
            return $content;
        }

        return str_repeat(' ', $this->ml).$content.str_repeat(' ', $this->mr);
    }
}
