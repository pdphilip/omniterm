<?php

declare(strict_types=1);

namespace OmniTerm\Rendering;

final class Ansi
{
    private const ESC = "\033[";

    public static function hideCursor(): string
    {
        return self::ESC.'?25l';
    }

    public static function showCursor(): string
    {
        return self::ESC.'?25h';
    }

    public static function moveUp(int $lines = 1): string
    {
        return $lines > 0 ? self::ESC."{$lines}A" : '';
    }

    public static function moveDown(int $lines = 1): string
    {
        return $lines > 0 ? self::ESC."{$lines}B" : '';
    }

    public static function moveToColumn(int $col = 1): string
    {
        return self::ESC."{$col}G";
    }

    public static function eraseLine(): string
    {
        return self::ESC.'2K';
    }

    public static function eraseDown(): string
    {
        return self::ESC.'J';
    }

    public static function carriageReturn(): string
    {
        return "\r";
    }

    public static function bold(): string
    {
        return "\e[1m";
    }

    public static function reset(): string
    {
        return "\e[0m";
    }

    public static function buildPrefix(?array $textColor, ?array $bgColor, bool $bold): string
    {
        $prefix = '';
        if ($textColor) {
            $prefix .= Colors::fgFromRgb($textColor);
        }
        if ($bgColor) {
            $prefix .= Colors::bgFromRgb($bgColor);
        }
        if ($bold) {
            $prefix .= self::bold();
        }

        return $prefix;
    }

    public static function wrap(string $content, ?array $textColor, ?array $bgColor, bool $bold): string
    {
        $prefix = self::buildPrefix($textColor, $bgColor, $bold);
        if ($prefix === '') {
            return $content;
        }

        return $prefix.$content.self::reset();
    }

    public static function wrapInherited(string $content, array $inherited): string
    {
        return self::wrap($content, $inherited['textColor'] ?? null, null, $inherited['bold'] ?? false);
    }

    public static function styledSpaces(int $count, ?array $bgColor): string
    {
        if ($count <= 0) {
            return '';
        }
        $spaces = str_repeat(' ', $count);
        if ($bgColor) {
            return Colors::bgFromRgb($bgColor).$spaces.self::reset();
        }

        return $spaces;
    }

    public static function pad(string $text, int $width, string $align = 'left'): string
    {
        $pad = max(0, $width - self::visibleLength($text));

        return match ($align) {
            'right' => str_repeat(' ', $pad).$text,
            'center' => str_repeat(' ', (int) floor($pad / 2)).$text.str_repeat(' ', $pad - (int) floor($pad / 2)),
            default => $text.str_repeat(' ', $pad),
        };
    }

    public static function repeatChar(string $char, int $width): string
    {
        if ($width <= 0) {
            return '';
        }
        $charWidth = mb_strwidth($char);
        if ($charWidth === 0) {
            return str_repeat(' ', $width);
        }

        return str_repeat($char, (int) floor($width / $charWidth));
    }

    public static function applyGradient(string $content, int $totalWidth, array $gradient): string
    {
        $from = $gradient['from'] ?? [0, 0, 0];
        $to = $gradient['to'] ?? $from;
        $via = $gradient['via'] ?? null;
        $dir = $gradient['dir'] ?? 'r';

        $segments = preg_split('/(\e\[[0-9;]*m)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        $result = '';
        $visPos = 0;

        foreach ($segments as $segment) {
            if (str_starts_with($segment, "\e[")) {
                $result .= $segment;
            } else {
                foreach (mb_str_split($segment) as $char) {
                    $t = $totalWidth > 1 ? $visPos / ($totalWidth - 1) : 0.0;
                    if ($dir === 'l') {
                        $t = 1.0 - $t;
                    }
                    $result .= Colors::bgFromRgb(self::lerpColor($from, $to, $via, $t)).$char;
                    $visPos++;
                }
            }
        }

        return $result.self::reset();
    }

    public static function visibleLength(string $text): int
    {
        return mb_strwidth(preg_replace('/\e\[[0-9;]*m/', '', $text));
    }

    private static function lerpColor(array $from, array $to, ?array $via, float $t): array
    {
        $t = max(0.0, min(1.0, $t));

        if ($via !== null) {
            if ($t <= 0.5) {
                $t2 = $t * 2;

                return [
                    (int) round($from[0] + ($via[0] - $from[0]) * $t2),
                    (int) round($from[1] + ($via[1] - $from[1]) * $t2),
                    (int) round($from[2] + ($via[2] - $from[2]) * $t2),
                ];
            }
            $t2 = ($t - 0.5) * 2;

            return [
                (int) round($via[0] + ($to[0] - $via[0]) * $t2),
                (int) round($via[1] + ($to[1] - $via[1]) * $t2),
                (int) round($via[2] + ($to[2] - $via[2]) * $t2),
            ];
        }

        return [
            (int) round($from[0] + ($to[0] - $from[0]) * $t),
            (int) round($from[1] + ($to[1] - $from[1]) * $t),
            (int) round($from[2] + ($to[2] - $from[2]) * $t),
        ];
    }
}
