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

    public static function italic(): string
    {
        return "\e[3m";
    }

    public static function underline(): string
    {
        return "\e[4m";
    }

    public static function strikethrough(): string
    {
        return "\e[9m";
    }

    public static function dim(): string
    {
        return "\e[90m";
    }

    public static function reset(): string
    {
        return "\e[0m";
    }

    public static function buildPrefix(
        ?array $textColor,
        ?array $bgColor,
        bool $bold,
        bool $italic = false,
        bool $underline = false,
        bool $lineThrough = false,
    ): string {
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
        if ($italic) {
            $prefix .= self::italic();
        }
        if ($underline) {
            $prefix .= self::underline();
        }
        if ($lineThrough) {
            $prefix .= self::strikethrough();
        }

        return $prefix;
    }

    public static function wrap(
        string $content,
        ?array $textColor,
        ?array $bgColor,
        bool $bold,
        bool $italic = false,
        bool $underline = false,
        bool $lineThrough = false,
    ): string {
        $prefix = self::buildPrefix($textColor, $bgColor, $bold, $italic, $underline, $lineThrough);
        if ($prefix === '') {
            return $content;
        }

        return $prefix.$content.self::reset();
    }

    public static function wrapInherited(string $content, array $inherited): string
    {
        return self::wrap(
            $content,
            $inherited['textColor'] ?? null,
            null,
            $inherited['bold'] ?? false,
            $inherited['italic'] ?? false,
            $inherited['underline'] ?? false,
            $inherited['lineThrough'] ?? false,
        );
    }

    public static function transformText(string $text, ?string $transform): string
    {
        return match ($transform) {
            'uppercase' => mb_strtoupper($text),
            'lowercase' => mb_strtolower($text),
            'capitalize' => mb_convert_case($text, MB_CASE_TITLE),
            'snakecase' => strtolower(preg_replace('/\s+/', '_', trim($text))),
            default => $text,
        };
    }

    public static function truncate(string $text, int $width): string
    {
        if ($width <= 0) {
            return '';
        }
        if (self::visibleLength($text) <= $width) {
            return $text;
        }

        $plain = self::stripAnsi($text);
        if ($width <= 1) {
            return mb_substr($plain, 0, $width);
        }

        return mb_substr($plain, 0, $width - 1).'…';
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

    public static function hyperlink(string $text, string $url): string
    {
        return "\033]8;;{$url}\033\\{$text}\033]8;;\033\\";
    }

    public static function visibleLength(string $text): int
    {
        return mb_strwidth(self::stripAnsi($text));
    }

    public static function stripAnsi(string $text): string
    {
        $text = preg_replace('/\e\[[0-9;]*m/', '', $text);

        return preg_replace('/\e\]8;;[^\e]*\e\\\\/', '', $text);
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
