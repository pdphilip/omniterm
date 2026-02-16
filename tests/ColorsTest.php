<?php

use OmniTerm\Rendering\Colors;

beforeEach(function () {
    // Reset cached color mode between tests
    Colors::forceMode('truecolor');
});

// ---------------------------------------------------------------
// Color Mode Detection
// ---------------------------------------------------------------

describe('color mode detection', function () {
    beforeEach(function () {
        // Clear cached mode so detectMode() re-evaluates
        $ref = new ReflectionProperty(Colors::class, 'colorMode');
        $ref->setAccessible(true);
        $ref->setValue(null, null);

        // Save original env
        $this->origColorterm = getenv('COLORTERM');
        $this->origTerm = getenv('TERM');
        $this->origTermProgram = getenv('TERM_PROGRAM');
    });

    afterEach(function () {
        // Restore original env
        $this->origColorterm !== false ? putenv("COLORTERM={$this->origColorterm}") : putenv('COLORTERM');
        $this->origTerm !== false ? putenv("TERM={$this->origTerm}") : putenv('TERM');
        $this->origTermProgram !== false ? putenv("TERM_PROGRAM={$this->origTermProgram}") : putenv('TERM_PROGRAM');

        // Clear cached mode
        $ref = new ReflectionProperty(Colors::class, 'colorMode');
        $ref->setAccessible(true);
        $ref->setValue(null, null);
    });

    it('detects truecolor from COLORTERM=truecolor', function () {
        putenv('COLORTERM=truecolor');
        putenv('TERM=xterm');
        putenv('TERM_PROGRAM');

        expect(Colors::detectMode())->toBe('truecolor');
    });

    it('detects truecolor from COLORTERM=24bit', function () {
        putenv('COLORTERM=24bit');
        putenv('TERM=xterm');
        putenv('TERM_PROGRAM');

        expect(Colors::detectMode())->toBe('truecolor');
    });

    it('detects 256 for Apple_Terminal', function () {
        putenv('COLORTERM');
        putenv('TERM=xterm-256color');
        putenv('TERM_PROGRAM=Apple_Terminal');

        expect(Colors::detectMode())->toBe('256');
    });

    it('detects truecolor for xterm-256color', function () {
        putenv('COLORTERM');
        putenv('TERM=xterm-256color');
        putenv('TERM_PROGRAM');

        expect(Colors::detectMode())->toBe('truecolor');
    });

    it('detects truecolor for kitty', function () {
        putenv('COLORTERM');
        putenv('TERM=xterm-kitty');
        putenv('TERM_PROGRAM');

        expect(Colors::detectMode())->toBe('truecolor');
    });

    it('detects truecolor for known terminal programs', function () {
        putenv('COLORTERM');
        putenv('TERM=xterm');
        putenv('TERM_PROGRAM=iTerm.app');

        expect(Colors::detectMode())->toBe('truecolor');
    });

    it('falls back to 256 when no signals present', function () {
        putenv('COLORTERM');
        putenv('TERM=dumb');
        putenv('TERM_PROGRAM');

        expect(Colors::detectMode())->toBe('256');
    });
});

// ---------------------------------------------------------------
// Palette Lookup
// ---------------------------------------------------------------

describe('rgb palette lookup', function () {
    it('returns correct RGB for known colors', function () {
        expect(Colors::rgb('red', 500))->toBe([239, 68, 68]);
        expect(Colors::rgb('emerald', 500))->toBe([16, 185, 129]);
        expect(Colors::rgb('sky', 500))->toBe([14, 165, 233]);
    });

    it('returns null for unknown color', function () {
        expect(Colors::rgb('banana', 500))->toBeNull();
    });

    it('returns null for unknown shade', function () {
        expect(Colors::rgb('red', 999))->toBeNull();
    });
});

// ---------------------------------------------------------------
// ANSI Code Generation
// ---------------------------------------------------------------

describe('ANSI code generation', function () {
    it('generates truecolor foreground escape', function () {
        Colors::forceMode('truecolor');

        expect(Colors::fgFromRgb([100, 200, 50]))->toBe("\e[38;2;100;200;50m");
    });

    it('generates truecolor background escape', function () {
        Colors::forceMode('truecolor');

        expect(Colors::bgFromRgb([100, 200, 50]))->toBe("\e[48;2;100;200;50m");
    });

    it('generates 256-color foreground escape', function () {
        Colors::forceMode('256');

        $result = Colors::fgFromRgb([100, 200, 50]);
        expect($result)->toMatch('/^\e\[38;5;\d+m$/');
    });

    it('generates 256-color background escape', function () {
        Colors::forceMode('256');

        $result = Colors::bgFromRgb([100, 200, 50]);
        expect($result)->toMatch('/^\e\[48;5;\d+m$/');
    });

    it('generates text ANSI from color name', function () {
        Colors::forceMode('truecolor');

        $result = Colors::textAnsi('red', 500);
        expect($result)->toBe("\e[38;2;239;68;68m");
    });

    it('generates bg ANSI from color name', function () {
        Colors::forceMode('truecolor');

        $result = Colors::bgAnsi('red', 500);
        expect($result)->toBe("\e[48;2;239;68;68m");
    });

    it('returns empty string for unknown color name', function () {
        expect(Colors::textAnsi('banana', 500))->toBe('');
        expect(Colors::bgAnsi('banana', 500))->toBe('');
    });
});

// ---------------------------------------------------------------
// RGB → 256 Conversion
// ---------------------------------------------------------------

describe('RGB to 256-color conversion', function () {
    it('maps black to index 16', function () {
        expect(Colors::rgbTo256(0, 0, 0))->toBe(16);
    });

    it('maps white to index 231', function () {
        expect(Colors::rgbTo256(255, 255, 255))->toBe(231);
    });

    it('maps pure red to cube red', function () {
        $index = Colors::rgbTo256(255, 0, 0);
        // Pure red should be in cube: 16 + 36*5 + 6*0 + 0 = 196
        expect($index)->toBe(196);
    });

    it('maps gray to grayscale ramp', function () {
        // Mid-gray should pick from the grayscale ramp (232-255)
        $index = Colors::rgbTo256(128, 128, 128);
        expect($index)->toBeGreaterThanOrEqual(232)
            ->toBeLessThanOrEqual(255);
    });

    it('returns consistent results for same input', function () {
        $a = Colors::rgbTo256(100, 150, 200);
        $b = Colors::rgbTo256(100, 150, 200);
        expect($a)->toBe($b);
    });

    it('returns index in valid range 0-255', function () {
        $testCases = [[0, 0, 0], [255, 255, 255], [128, 64, 192], [10, 240, 10]];
        foreach ($testCases as [$r, $g, $b]) {
            $index = Colors::rgbTo256($r, $g, $b);
            expect($index)->toBeGreaterThanOrEqual(0)->toBeLessThanOrEqual(255);
        }
    });
});

// ---------------------------------------------------------------
// Color Interpolation
// ---------------------------------------------------------------

describe('colorAt interpolation', function () {
    it('returns from-color at 0%', function () {
        $result = Colors::colorAt(0, 'amber', 500, 'emerald', 500);
        expect($result)->toBe(Colors::rgb('amber', 500));
    });

    it('returns to-color at 100%', function () {
        $result = Colors::colorAt(100, 'amber', 500, 'emerald', 500);
        expect($result)->toBe(Colors::rgb('emerald', 500));
    });

    it('returns midpoint at 50%', function () {
        $from = Colors::rgb('amber', 500);
        $to = Colors::rgb('emerald', 500);
        $mid = Colors::colorAt(50, 'amber', 500, 'emerald', 500);

        // Each channel should be the average of from and to
        expect($mid[0])->toBe((int) round(($from[0] + $to[0]) / 2));
        expect($mid[1])->toBe((int) round(($from[1] + $to[1]) / 2));
        expect($mid[2])->toBe((int) round(($from[2] + $to[2]) / 2));
    });

    it('clamps below 0% to from-color', function () {
        $result = Colors::colorAt(-50, 'red', 500, 'blue', 500);
        expect($result)->toBe(Colors::rgb('red', 500));
    });

    it('clamps above 100% to to-color', function () {
        $result = Colors::colorAt(150, 'red', 500, 'blue', 500);
        expect($result)->toBe(Colors::rgb('blue', 500));
    });

    it('returns [0,0,0] for unknown colors', function () {
        $result = Colors::colorAt(50, 'banana', 500, 'kiwi', 500);
        expect($result)->toBe([0, 0, 0]);
    });

    it('interpolates monotonically', function () {
        // At increasing percentages, the result should move steadily from→to
        $prev = Colors::colorAt(0, 'amber', 500, 'emerald', 500);
        $from = Colors::rgb('amber', 500);
        $to = Colors::rgb('emerald', 500);

        for ($p = 10; $p <= 100; $p += 10) {
            $curr = Colors::colorAt($p, 'amber', 500, 'emerald', 500);
            // Distance from 'from' should increase or stay same
            $prevDist = abs($prev[0] - $from[0]) + abs($prev[1] - $from[1]) + abs($prev[2] - $from[2]);
            $currDist = abs($curr[0] - $from[0]) + abs($curr[1] - $from[1]) + abs($curr[2] - $from[2]);
            expect($currDist)->toBeGreaterThanOrEqual($prevDist);
            $prev = $curr;
        }
    });
});
