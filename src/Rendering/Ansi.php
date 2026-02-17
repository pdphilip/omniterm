<?php

declare(strict_types=1);

namespace OmniTerm\Rendering;

/**
 * ANSI escape sequence builder.
 *
 * Produces raw escape strings for cursor and display control.
 * Avoids Symfony's Cursor class, which sends a DA1 query on show().
 */
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
}
