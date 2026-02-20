<?php

use OmniTerm\Rendering\ClassParser;

beforeEach(function () {
    $this->parser = new ClassParser;
});

// ---------------------------------------------------------------
// Layout Flags
// ---------------------------------------------------------------

describe('layout flags', function () {
    it('parses italic', function () {
        expect($this->parser->parse('italic')['italic'])->toBeTrue();
    });

    it('parses underline', function () {
        expect($this->parser->parse('underline')['underline'])->toBeTrue();
    });

    it('parses line-through', function () {
        expect($this->parser->parse('line-through')['lineThrough'])->toBeTrue();
    });

    it('parses font-normal', function () {
        expect($this->parser->parse('font-normal')['fontNormal'])->toBeTrue();
    });

    it('parses truncate', function () {
        expect($this->parser->parse('truncate')['truncate'])->toBeTrue();
    });

    it('parses text-left', function () {
        expect($this->parser->parse('text-left')['textLeft'])->toBeTrue();
    });

    it('parses block', function () {
        expect($this->parser->parse('block')['block'])->toBeTrue();
    });

    it('parses hidden', function () {
        expect($this->parser->parse('hidden')['hidden'])->toBeTrue();
    });

    it('parses invisible', function () {
        expect($this->parser->parse('invisible')['invisible'])->toBeTrue();
    });

    it('parses w-full', function () {
        expect($this->parser->parse('w-full')['wFull'])->toBeTrue();
    });

    it('parses w-auto', function () {
        expect($this->parser->parse('w-auto')['wAuto'])->toBeTrue();
    });
});

// ---------------------------------------------------------------
// Value Classes
// ---------------------------------------------------------------

describe('value classes', function () {
    it('parses text transform classes', function () {
        expect($this->parser->parse('uppercase')['textTransform'])->toBe('uppercase');
        expect($this->parser->parse('lowercase')['textTransform'])->toBe('lowercase');
        expect($this->parser->parse('capitalize')['textTransform'])->toBe('capitalize');
        expect($this->parser->parse('snakecase')['textTransform'])->toBe('snakecase');
    });

    it('parses justify classes', function () {
        expect($this->parser->parse('justify-between')['justify'])->toBe('between');
        expect($this->parser->parse('justify-center')['justify'])->toBe('center');
        expect($this->parser->parse('justify-around')['justify'])->toBe('around');
        expect($this->parser->parse('justify-evenly')['justify'])->toBe('evenly');
    });

    it('parses list style classes', function () {
        expect($this->parser->parse('list-disc')['listStyle'])->toBe('disc');
        expect($this->parser->parse('list-decimal')['listStyle'])->toBe('decimal');
        expect($this->parser->parse('list-square')['listStyle'])->toBe('square');
        expect($this->parser->parse('list-none')['listStyle'])->toBe('none');
    });
});

// ---------------------------------------------------------------
// Size Classes
// ---------------------------------------------------------------

describe('size classes', function () {
    it('parses min-w-{n}', function () {
        expect($this->parser->parse('min-w-20')['minW'])->toBe(20);
    });

    it('parses max-w-{n}', function () {
        expect($this->parser->parse('max-w-40')['maxW'])->toBe(40);
    });
});

// ---------------------------------------------------------------
// Spacing Classes
// ---------------------------------------------------------------

describe('spacing classes', function () {
    it('parses py-{n}', function () {
        expect($this->parser->parse('py-2')['py'])->toBe(2);
    });

    it('parses pt-{n} and pb-{n}', function () {
        $result = $this->parser->parse('pt-1 pb-3');

        expect($result['pt'])->toBe(1);
        expect($result['pb'])->toBe(3);
    });

    it('parses p-{n}', function () {
        expect($this->parser->parse('p-2')['p'])->toBe(2);
    });

    it('parses my-{n}', function () {
        expect($this->parser->parse('my-1')['my'])->toBe(1);
    });

    it('parses space-y-{n}', function () {
        expect($this->parser->parse('space-y-1')['spaceY'])->toBe(1);
    });
});

// ---------------------------------------------------------------
// resolveSpacing
// ---------------------------------------------------------------

describe('resolveSpacing', function () {
    it('uses pt/pb directly when set', function () {
        $resolved = $this->parser->resolveSpacing($this->parser->parse('pt-3 pb-5'));

        expect($resolved['pt'])->toBe(3);
        expect($resolved['pb'])->toBe(5);
    });

    it('falls back from py to pt/pb', function () {
        $resolved = $this->parser->resolveSpacing($this->parser->parse('py-2'));

        expect($resolved['pt'])->toBe(2);
        expect($resolved['pb'])->toBe(2);
    });

    it('falls back from p to all sides', function () {
        $resolved = $this->parser->resolveSpacing($this->parser->parse('p-4'));

        expect($resolved['pt'])->toBe(4);
        expect($resolved['pb'])->toBe(4);
        expect($resolved['pl'])->toBe(4);
        expect($resolved['pr'])->toBe(4);
    });

    it('pt overrides py fallback', function () {
        $resolved = $this->parser->resolveSpacing($this->parser->parse('py-2 pt-5'));

        expect($resolved['pt'])->toBe(5);
        expect($resolved['pb'])->toBe(2);
    });

    it('falls back my to mt/mb', function () {
        $resolved = $this->parser->resolveSpacing($this->parser->parse('my-3'));

        expect($resolved['mt'])->toBe(3);
        expect($resolved['mb'])->toBe(3);
    });
});

// ---------------------------------------------------------------
// mergeInherited
// ---------------------------------------------------------------

describe('mergeInherited', function () {
    it('inherits italic from parent', function () {
        $merged = $this->parser->mergeInherited(['italic' => true], $this->parser->parse(''));

        expect($merged['italic'])->toBeTrue();
    });

    it('inherits underline from parent', function () {
        $merged = $this->parser->mergeInherited(['underline' => true], $this->parser->parse(''));

        expect($merged['underline'])->toBeTrue();
    });

    it('inherits lineThrough from parent', function () {
        $merged = $this->parser->mergeInherited(['lineThrough' => true], $this->parser->parse(''));

        expect($merged['lineThrough'])->toBeTrue();
    });

    it('inherits textTransform from parent', function () {
        $merged = $this->parser->mergeInherited(['textTransform' => 'uppercase'], $this->parser->parse(''));

        expect($merged['textTransform'])->toBe('uppercase');
    });

    it('font-normal resets inherited bold', function () {
        $merged = $this->parser->mergeInherited(['bold' => true], $this->parser->parse('font-normal'));

        expect($merged['bold'])->toBeFalse();
    });

    it('own bold propagates when no font-normal', function () {
        $merged = $this->parser->mergeInherited([], $this->parser->parse('font-bold'));

        expect($merged['bold'])->toBeTrue();
    });
});
