<?php

declare(strict_types=1);

namespace OmniTerm\Prompts;

use Closure;

/**
 * Turns a validation rule - a keyword, a regex, an `in:` list, or a callable -
 * into the `fn ($value): ?string` closure that laravel/prompts expects, where a
 * returned string is the error message and null means the value is valid.
 */
class Validation
{
    public static function resolve(string|callable|null $rule): ?Closure
    {
        if ($rule === null) {
            return null;
        }

        if (! is_string($rule)) {
            return Closure::fromCallable($rule);
        }

        return match (true) {
            $rule === 'email' => static fn ($v) => filter_var((string) $v, FILTER_VALIDATE_EMAIL) !== false
                ? null
                : 'Enter a valid email address.',
            $rule === 'url' => static fn ($v) => filter_var((string) $v, FILTER_VALIDATE_URL) !== false
                ? null
                : 'Enter a valid URL.',
            $rule === 'int' || $rule === 'integer' => static fn ($v) => preg_match('/^-?\d+$/', (string) $v) === 1
                ? null
                : 'Enter a whole number.',
            str_starts_with($rule, 'regex:') => static::regex(substr($rule, 6)),
            str_starts_with($rule, 'in:') => static::in(explode(',', substr($rule, 3))),
            str_starts_with($rule, '/') => static::regex($rule),
            default => null,
        };
    }

    protected static function regex(string $pattern): Closure
    {
        if ($pattern === '' || $pattern[0] !== '/') {
            $pattern = '/'.$pattern.'/';
        }

        return static fn ($v) => preg_match($pattern, (string) $v) === 1
            ? null
            : 'The value is not in the expected format.';
    }

    /**
     * @param  array<int, string>  $allowed
     */
    protected static function in(array $allowed): Closure
    {
        $allowed = array_map('trim', $allowed);

        return static fn ($v) => in_array((string) $v, $allowed, true)
            ? null
            : 'Choose one of: '.implode(', ', $allowed).'.';
    }
}
