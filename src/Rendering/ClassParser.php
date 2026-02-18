<?php

declare(strict_types=1);

namespace OmniTerm\Rendering;

class ClassParser
{
    public function parse(string $classStr): array
    {
        $result = [
            'flex' => false, 'flex1' => false, 'bold' => false,
            'textCenter' => false, 'textRight' => false,
            'textColor' => null, 'bgColor' => null, 'gradient' => null,
            'w' => null, 'px' => 0, 'pl' => 0, 'pr' => 0,
            'mx' => 0, 'ml' => 0, 'mr' => 0,
            'mb' => 0, 'mt' => 0, 'm' => 0,
            'spaceX' => 0, 'contentRepeat' => null,
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
            'mt' => $styles['mt'] ?: $styles['m'],
            'mb' => $styles['mb'] ?: $styles['m'],
            'pl' => $styles['pl'] ?: $styles['px'],
            'pr' => $styles['pr'] ?: $styles['px'],
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
        return [
            'textColor' => $styles['textColor'] ?? $inherited['textColor'] ?? null,
            'bold' => ($styles['bold'] ?? false) || ($inherited['bold'] ?? false),
        ];
    }

    private function parseLayoutClass(string $class, array &$result): bool
    {
        $flags = [
            'flex' => 'flex',
            'flex-1' => 'flex1',
            'font-bold' => 'bold',
            'text-center' => 'textCenter',
            'text-right' => 'textRight',
        ];

        if (isset($flags[$class])) {
            $result[$flags[$class]] = true;

            return true;
        }

        return false;
    }

    private function parseSizeClass(string $class, array &$result): bool
    {
        if (preg_match('/^w-(\d+)$/', $class, $m)) {
            $result['w'] = (int) $m[1];

            return true;
        }

        return false;
    }

    private function parseSpacingClass(string $class, array &$result): bool
    {
        $patterns = [
            'px' => '/^px-(\d+)$/', 'pl' => '/^pl-(\d+)$/', 'pr' => '/^pr-(\d+)$/',
            'mx' => '/^mx-(\d+)$/', 'ml' => '/^ml-(\d+)$/', 'mr' => '/^mr-(\d+)$/',
            'mb' => '/^mb-(\d+)$/', 'mt' => '/^mt-(\d+)$/', 'm' => '/^m-(\d+)$/',
            'spaceX' => '/^space-x-(\d+)$/',
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
