<?php

declare(strict_types=1);

namespace OmniTerm\Helpers;

class DebugFormatter
{
    public static function format(mixed $var): array
    {
        if (is_null($var)) {
            return [self::row(null, null, 'null', 0)];
        }

        if (is_scalar($var)) {
            return [self::row(null, $var, self::typeOf($var), 0)];
        }

        $data = self::toArray($var);

        if (empty($data)) {
            return [self::row(null, '(empty)', 'empty', 0)];
        }

        return self::flatten($data, 0);
    }

    private static function flatten(array $data, int $depth, array $ancestors = []): array
    {
        $keys = array_keys($data);
        $lastKey = end($keys);
        $rows = [];

        foreach ($data as $key => $value) {
            $isLast = ($key === $lastKey);

            if (is_array($value) || is_object($value)) {
                $nested = is_object($value) ? self::toArray($value) : $value;
                if (empty($nested)) {
                    $rows[] = self::row(self::formatKey($key), '[]', 'empty', $depth, $isLast, $ancestors);
                } else {
                    $rows[] = ['type' => 'section', 'key' => self::formatKey($key), 'depth' => $depth, 'last' => $isLast, 'ancestors' => $ancestors];
                    $childAncestors = $depth > 0 ? [...$ancestors, ! $isLast] : $ancestors;
                    array_push($rows, ...self::flatten($nested, $depth + 1, $childAncestors));
                }

                continue;
            }

            $rows[] = self::row(self::formatKey($key), $value, self::typeOf($value), $depth, $isLast, $ancestors);
        }

        return $rows;
    }

    private static function row(?string $key, mixed $value, string $valueType, int $depth, bool $last = false, array $ancestors = []): array
    {
        return [
            'type' => 'row',
            'key' => $key,
            'value' => $value,
            'valueType' => $valueType,
            'depth' => $depth,
            'last' => $last,
            'ancestors' => $ancestors,
        ];
    }

    private static function formatKey(int|string $key): string
    {
        if (is_int($key)) {
            return '['.$key.']';
        }

        return (string) $key;
    }

    private static function typeOf(mixed $value): string
    {
        if (is_null($value)) {
            return 'null';
        }

        if (is_bool($value)) {
            return 'boolean';
        }

        if (is_int($value) || is_float($value)) {
            return 'number';
        }

        return 'string';
    }

    private static function toArray(mixed $var): array
    {
        if (is_array($var)) {
            return $var;
        }

        if (is_object($var)) {
            if (method_exists($var, 'toArray')) {
                return $var->toArray();
            }

            return get_object_vars($var);
        }

        return [];
    }
}
