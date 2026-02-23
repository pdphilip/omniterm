<?php

declare(strict_types=1);

namespace OmniTerm\Rendering;

class ClassParser
{
    public function parse(string $classStr): array
    {
        $result = [
            'flex' => false, 'flex1' => false, 'bold' => false, 'fontNormal' => false,
            'italic' => false, 'underline' => false, 'lineThrough' => false,
            'textCenter' => false, 'textRight' => false, 'textLeft' => false,
            'textTransform' => null, 'truncate' => false,
            'textColor' => null, 'bgColor' => null, 'gradient' => null,
            'w' => null, 'minW' => null, 'maxW' => null, 'wFull' => false, 'wAuto' => false,
            'px' => 0, 'pl' => 0, 'pr' => 0, 'py' => 0, 'pt' => 0, 'pb' => 0, 'p' => 0,
            'mx' => 0, 'ml' => 0, 'mr' => 0, 'my' => 0,
            'mb' => 0, 'mt' => 0, 'm' => 0,
            'spaceX' => 0, 'spaceY' => 0, 'contentRepeat' => null,
            'block' => false, 'hidden' => false, 'invisible' => false,
            'justify' => null, 'listStyle' => null, 'preserveWhitespace' => false,
        ];

        foreach (preg_split('/\s+/', trim($classStr)) as $class) {
            if ($class === '') {
                continue;
            }
            $this->parseLayoutClass($class, $result)
                || $this->parseSizeClass($class, $result)
                || $this->parseSpacingClass($class, $result)
                || $this->parseColorClass($class, $result);
        }

        return $result;
    }

    public function resolveSpacing(array $styles): array
    {
        return [
            'ml' => $styles['ml'] ?: ($styles['mx'] ?: $styles['m']),
            'mr' => $styles['mr'] ?: ($styles['mx'] ?: $styles['m']),
            'mt' => $styles['mt'] ?: ($styles['my'] ?: $styles['m']),
            'mb' => $styles['mb'] ?: ($styles['my'] ?: $styles['m']),
            'pl' => $styles['pl'] ?: $styles['px'] ?: $styles['p'],
            'pr' => $styles['pr'] ?: $styles['px'] ?: $styles['p'],
            'pt' => $styles['pt'] ?: $styles['py'] ?: $styles['p'],
            'pb' => $styles['pb'] ?: $styles['py'] ?: $styles['p'],
        ];
    }

    public function resolveAlignment(array $styles): string
    {
        if ($styles['textRight'] ?? false) {
            return 'right';
        }
        if ($styles['textCenter'] ?? false) {
            return 'center';
        }

        return 'left';
    }

    public function mergeInherited(array $inherited, array $styles): array
    {
        $bold = $styles['fontNormal']
            ? false
            : (($styles['bold'] ?? false) || ($inherited['bold'] ?? false));

        return [
            'textColor' => $styles['textColor'] ?? $inherited['textColor'] ?? null,
            'bold' => $bold,
            'italic' => ($styles['italic'] ?? false) || ($inherited['italic'] ?? false),
            'underline' => ($styles['underline'] ?? false) || ($inherited['underline'] ?? false),
            'lineThrough' => ($styles['lineThrough'] ?? false) || ($inherited['lineThrough'] ?? false),
            'textTransform' => $styles['textTransform'] ?? $inherited['textTransform'] ?? null,
            'preserveWhitespace' => ($styles['preserveWhitespace'] ?? false) || ($inherited['preserveWhitespace'] ?? false),
        ];
    }

    private function parseLayoutClass(string $class, array &$result): bool
    {
        $flags = [
            'flex' => 'flex', 'flex-1' => 'flex1',
            'font-bold' => 'bold', 'font-normal' => 'fontNormal',
            'italic' => 'italic', 'underline' => 'underline', 'line-through' => 'lineThrough',
            'text-center' => 'textCenter', 'text-right' => 'textRight', 'text-left' => 'textLeft',
            'truncate' => 'truncate',
            'block' => 'block', 'hidden' => 'hidden', 'invisible' => 'invisible',
            'w-full' => 'wFull', 'w-auto' => 'wAuto',
        ];

        if (isset($flags[$class])) {
            $result[$flags[$class]] = true;

            return true;
        }

        $transforms = ['uppercase', 'lowercase', 'capitalize', 'snakecase'];
        if (in_array($class, $transforms, true)) {
            $result['textTransform'] = $class;

            return true;
        }

        if (preg_match('/^justify-(between|around|evenly|center)$/', $class, $m)) {
            $result['justify'] = $m[1];

            return true;
        }

        if (preg_match('/^list-(disc|decimal|square|none)$/', $class, $m)) {
            $result['listStyle'] = $m[1];

            return true;
        }

        return false;
    }

    private function parseSizeClass(string $class, array &$result): bool
    {
        $patterns = [
            'w' => '/^w-(\d+)$/',
            'minW' => '/^min-w-(\d+)$/',
            'maxW' => '/^max-w-(\d+)$/',
        ];

        foreach ($patterns as $key => $pattern) {
            if (preg_match($pattern, $class, $m)) {
                $result[$key] = (int) $m[1];

                return true;
            }
        }

        return false;
    }

    private function parseSpacingClass(string $class, array &$result): bool
    {
        $patterns = [
            'px' => '/^px-(\d+)$/', 'pl' => '/^pl-(\d+)$/', 'pr' => '/^pr-(\d+)$/',
            'py' => '/^py-(\d+)$/', 'pt' => '/^pt-(\d+)$/', 'pb' => '/^pb-(\d+)$/',
            'p' => '/^p-(\d+)$/',
            'mx' => '/^mx-(\d+)$/', 'ml' => '/^ml-(\d+)$/', 'mr' => '/^mr-(\d+)$/',
            'my' => '/^my-(\d+)$/',
            'mb' => '/^mb-(\d+)$/', 'mt' => '/^mt-(\d+)$/', 'm' => '/^m-(\d+)$/',
            'spaceX' => '/^space-x-(\d+)$/', 'spaceY' => '/^space-y-(\d+)$/',
        ];

        foreach ($patterns as $key => $pattern) {
            if (preg_match($pattern, $class, $m)) {
                $result[$key] = (int) $m[1];

                return true;
            }
        }

        return false;
    }

    private function parseColorClass(string $class, array &$result): bool
    {
        if (preg_match('/^content-repeat-\[(.)\]$/u', $class, $m)) {
            $result['contentRepeat'] = $m[1];

            return true;
        }

        if (preg_match('/^bg-\[(\d+),(\d+),(\d+)\]$/', $class, $m)) {
            $result['bgColor'] = [(int) $m[1], (int) $m[2], (int) $m[3]];

            return true;
        }
        if (preg_match('/^text-\[(\d+),(\d+),(\d+)\]$/', $class, $m)) {
            $result['textColor'] = [(int) $m[1], (int) $m[2], (int) $m[3]];

            return true;
        }

        if ($this->parseGradientClass($class, $result)) {
            return true;
        }

        if (str_starts_with($class, 'text-')) {
            $result['textColor'] = $this->resolveColor(substr($class, 5));

            return true;
        }
        if (str_starts_with($class, 'bg-')) {
            $result['bgColor'] = $this->resolveColor(substr($class, 3));

            return true;
        }

        return false;
    }

    private function parseGradientClass(string $class, array &$result): bool
    {
        if ($class === 'bg-gradient-to-r' || $class === 'bg-gradient-to-l') {
            $result['gradient'] ??= ['dir' => 'r', 'from' => null, 'to' => null, 'via' => null];
            $result['gradient']['dir'] = $class === 'bg-gradient-to-r' ? 'r' : 'l';

            return true;
        }

        $prefixes = ['from-' => 'from', 'via-' => 'via', 'to-' => 'to'];
        foreach ($prefixes as $prefix => $key) {
            if (str_starts_with($class, $prefix)) {
                $rgb = $this->resolveColor(substr($class, strlen($prefix)));
                if ($rgb) {
                    $result['gradient'] ??= ['dir' => 'r', 'from' => null, 'to' => null, 'via' => null];
                    $result['gradient'][$key] = $rgb;
                }

                return true;
            }
        }

        return false;
    }

    private function resolveColor(string $value): ?array
    {
        if (! str_contains($value, '-')) {
            return Colors::rgb($value, 500);
        }

        $parts = explode('-', $value);
        if (count($parts) === 2 && is_numeric($parts[1])) {
            return Colors::rgb($parts[0], (int) $parts[1]);
        }

        return null;
    }
}
