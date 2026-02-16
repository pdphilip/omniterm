<?php

use OmniTerm\Rendering\Colors;
use OmniTerm\Rendering\Renderer;

beforeEach(function () {
    Colors::forceMode('truecolor');
    $this->renderer = new Renderer;
});

describe('arbitrary RGB classes', function () {
    it('renders bg-[R,G,B] as background color', function () {
        $html = '<div class="flex"><span class="bg-[255,100,50] w-5">hello</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toContain("\e[48;2;255;100;50m");
    });

    it('renders text-[R,G,B] as text color', function () {
        $html = '<div class="flex"><span class="text-[0,200,150] w-5">hello</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toContain("\e[38;2;0;200;150m");
    });

    it('renders both bg and text arbitrary RGB together', function () {
        $html = '<div class="flex"><span class="bg-[40,40,80] text-[200,180,255] w-10">test</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)
            ->toContain("\e[38;2;200;180;255m")
            ->toContain("\e[48;2;40;40;80m");
    });
});

describe('gradient classes', function () {
    it('renders bg-gradient-to-r with from/to colors', function () {
        $html = '<div class="flex"><span class="bg-gradient-to-r from-red-500 to-blue-500 w-10"> </span></div>';
        $ansi = $this->renderer->toAnsi($html);

        // Should contain background escape sequences (per-character gradient)
        expect($ansi)->toContain("\e[48;2;");
    });

    it('renders bg-gradient-to-l in reverse direction', function () {
        $htmlR = '<div class="flex"><span class="bg-gradient-to-r from-red-500 to-blue-500 w-20">                    </span></div>';
        $htmlL = '<div class="flex"><span class="bg-gradient-to-l from-red-500 to-blue-500 w-20">                    </span></div>';

        $ansiR = $this->renderer->toAnsi($htmlR);
        $ansiL = $this->renderer->toAnsi($htmlL);

        // They should produce different output (reversed gradient)
        expect($ansiR)->not->toBe($ansiL);
    });
});

describe('tailwind color classes', function () {
    it('renders text color from class', function () {
        $html = '<div class="flex"><span class="text-red-500 w-5">hello</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        $rgb = Colors::rgb('red', 500);
        expect($ansi)->toContain("\e[38;2;{$rgb[0]};{$rgb[1]};{$rgb[2]}m");
    });

    it('renders bg color from class', function () {
        $html = '<div class="flex"><span class="bg-sky-600 w-5">hello</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        $rgb = Colors::rgb('sky', 600);
        expect($ansi)->toContain("\e[48;2;{$rgb[0]};{$rgb[1]};{$rgb[2]}m");
    });

    it('renders shorthand color (defaults to 500)', function () {
        $html = '<div class="flex"><span class="text-emerald w-5">hello</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        $rgb = Colors::rgb('emerald', 500);
        expect($ansi)->toContain("\e[38;2;{$rgb[0]};{$rgb[1]};{$rgb[2]}m");
    });

    it('renders content-repeat to fill width', function () {
        $html = '<div class="flex"><span class="w-5 content-repeat-[─]"></span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toContain('─────');
    });

    it('renders bold text', function () {
        $html = '<div class="flex"><span class="font-bold w-5">bold</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toContain("\e[1m");
    });
});
