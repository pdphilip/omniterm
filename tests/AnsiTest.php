<?php

use OmniTerm\Rendering\Ansi;
use OmniTerm\Rendering\Colors;

beforeEach(function () {
    Colors::forceMode('truecolor');
});

// ---------------------------------------------------------------
// Text Decoration Escapes
// ---------------------------------------------------------------

describe('text decoration escapes', function () {
    it('returns italic escape', function () {
        expect(Ansi::italic())->toBe("\e[3m");
    });

    it('returns underline escape', function () {
        expect(Ansi::underline())->toBe("\e[4m");
    });

    it('returns strikethrough escape', function () {
        expect(Ansi::strikethrough())->toBe("\e[9m");
    });
});

// ---------------------------------------------------------------
// buildPrefix
// ---------------------------------------------------------------

describe('buildPrefix', function () {
    it('includes italic escape when flag is true', function () {
        $prefix = Ansi::buildPrefix(null, null, false, italic: true);

        expect($prefix)->toContain("\e[3m");
    });

    it('includes underline escape when flag is true', function () {
        $prefix = Ansi::buildPrefix(null, null, false, underline: true);

        expect($prefix)->toContain("\e[4m");
    });

    it('includes strikethrough escape when lineThrough is true', function () {
        $prefix = Ansi::buildPrefix(null, null, false, lineThrough: true);

        expect($prefix)->toContain("\e[9m");
    });

    it('combines all decoration flags', function () {
        $prefix = Ansi::buildPrefix(null, null, true, true, true, true);

        expect($prefix)
            ->toContain("\e[1m")
            ->toContain("\e[3m")
            ->toContain("\e[4m")
            ->toContain("\e[9m");
    });

    it('returns empty string with no flags', function () {
        expect(Ansi::buildPrefix(null, null, false))->toBe('');
    });
});

// ---------------------------------------------------------------
// wrap
// ---------------------------------------------------------------

describe('wrap', function () {
    it('wraps content with italic and reset', function () {
        $result = Ansi::wrap('hello', null, null, false, italic: true);

        expect($result)
            ->toContain("\e[3m")
            ->toContain('hello')
            ->toContain("\e[0m");
    });

    it('wraps content with underline and reset', function () {
        $result = Ansi::wrap('hello', null, null, false, underline: true);

        expect($result)
            ->toContain("\e[4m")
            ->toContain('hello')
            ->toContain("\e[0m");
    });

    it('wraps content with strikethrough and reset', function () {
        $result = Ansi::wrap('hello', null, null, false, lineThrough: true);

        expect($result)
            ->toContain("\e[9m")
            ->toContain('hello')
            ->toContain("\e[0m");
    });

    it('returns plain content when no styles applied', function () {
        expect(Ansi::wrap('hello', null, null, false))->toBe('hello');
    });
});

// ---------------------------------------------------------------
// wrapInherited
// ---------------------------------------------------------------

describe('wrapInherited', function () {
    it('applies italic from inherited array', function () {
        $result = Ansi::wrapInherited('test', ['italic' => true]);

        expect($result)->toContain("\e[3m");
    });

    it('applies underline from inherited array', function () {
        $result = Ansi::wrapInherited('test', ['underline' => true]);

        expect($result)->toContain("\e[4m");
    });

    it('applies lineThrough from inherited array', function () {
        $result = Ansi::wrapInherited('test', ['lineThrough' => true]);

        expect($result)->toContain("\e[9m");
    });

    it('returns plain content with empty inherited', function () {
        expect(Ansi::wrapInherited('test', []))->toBe('test');
    });
});

// ---------------------------------------------------------------
// transformText
// ---------------------------------------------------------------

describe('transformText', function () {
    it('transforms to uppercase', function () {
        expect(Ansi::transformText('hello world', 'uppercase'))->toBe('HELLO WORLD');
    });

    it('transforms to lowercase', function () {
        expect(Ansi::transformText('Hello World', 'lowercase'))->toBe('hello world');
    });

    it('transforms to capitalize', function () {
        expect(Ansi::transformText('hello world', 'capitalize'))->toBe('Hello World');
    });

    it('transforms to snakecase', function () {
        expect(Ansi::transformText('hello world', 'snakecase'))->toBe('hello_world');
    });

    it('passes through with null transform', function () {
        expect(Ansi::transformText('Hello', null))->toBe('Hello');
    });

    it('passes through with unknown transform', function () {
        expect(Ansi::transformText('Hello', 'unknown'))->toBe('Hello');
    });
});

// ---------------------------------------------------------------
// truncate
// ---------------------------------------------------------------

describe('truncate', function () {
    it('returns text unchanged when it fits', function () {
        expect(Ansi::truncate('hello', 10))->toBe('hello');
    });

    it('truncates with ellipsis when text exceeds width', function () {
        $result = Ansi::truncate('hello world', 6);

        expect($result)->toBe('hello…');
        expect(mb_strwidth($result))->toBe(6);
    });

    it('returns empty string when width is 0', function () {
        expect(Ansi::truncate('hello', 0))->toBe('');
    });

    it('returns single character when width is 1', function () {
        expect(Ansi::truncate('hello', 1))->toBe('h');
    });

    it('strips ANSI before truncating', function () {
        $styled = "\e[1mhello world\e[0m";
        $result = Ansi::truncate($styled, 6);

        expect($result)->toBe('hello…');
    });
});
