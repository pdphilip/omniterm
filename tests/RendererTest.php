<?php

use OmniTerm\Rendering\Ansi;
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

// ---------------------------------------------------------------
// Text Decoration Rendering
// ---------------------------------------------------------------

describe('text decoration rendering', function () {
    it('renders italic text', function () {
        $html = '<div class="flex"><span class="italic w-10">hello</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toContain("\e[3m");
    });

    it('renders underline text', function () {
        $html = '<div class="flex"><span class="underline w-10">hello</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toContain("\e[4m");
    });

    it('renders line-through text', function () {
        $html = '<div class="flex"><span class="line-through w-10">hello</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toContain("\e[9m");
    });
});

// ---------------------------------------------------------------
// font-normal
// ---------------------------------------------------------------

describe('font-normal', function () {
    it('resets inherited bold', function () {
        $html = '<div class="flex font-bold"><span class="font-normal w-10">hello</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->not->toContain("\e[1m");
    });
});

// ---------------------------------------------------------------
// Text Transforms
// ---------------------------------------------------------------

describe('text transforms', function () {
    it('renders uppercase text', function () {
        $html = '<div class="flex"><span class="uppercase w-10">hello</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toContain('HELLO');
    });

    it('renders lowercase text', function () {
        $html = '<div class="flex"><span class="lowercase w-10">HELLO</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toContain('hello');
    });

    it('renders capitalize text', function () {
        $html = '<div class="flex"><span class="capitalize w-10">hello</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toContain('Hello');
    });

    it('renders snakecase text', function () {
        $html = '<div class="flex"><span class="snakecase w-14">hello world</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toContain('hello_world');
    });
});

// ---------------------------------------------------------------
// Truncate
// ---------------------------------------------------------------

describe('truncate rendering', function () {
    it('truncates content with ellipsis', function () {
        $html = '<div class="flex"><span class="truncate w-6">hello world</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toContain('hello…');
    });
});

// ---------------------------------------------------------------
// Vertical Padding
// ---------------------------------------------------------------

describe('vertical padding', function () {
    it('adds blank lines for pt', function () {
        $html = '<div class="pt-2">content</div>';
        $lines = explode("\n", $this->renderer->toAnsi($html));

        expect($lines[0])->toBe('');
        expect($lines[1])->toBe('');
        expect($lines[2])->toContain('content');
    });

    it('adds blank lines for pb', function () {
        $html = '<div class="pb-2">content</div>';
        $lines = explode("\n", $this->renderer->toAnsi($html));

        expect($lines[0])->toContain('content');
        expect($lines[1])->toBe('');
        expect($lines[2])->toBe('');
    });
});

// ---------------------------------------------------------------
// space-y
// ---------------------------------------------------------------

describe('space-y', function () {
    it('adds blank lines between children', function () {
        $html = '<div class="space-y-1"><span>first</span><span>second</span></div>';
        $lines = explode("\n", $this->renderer->toAnsi($html));

        expect($lines[0])->toContain('first');
        expect($lines[1])->toBe('');
        expect($lines[2])->toContain('second');
    });
});

// ---------------------------------------------------------------
// Width Constraints
// ---------------------------------------------------------------

describe('width constraints', function () {
    it('clamps up to min-w', function () {
        $html = '<div class="flex"><span class="min-w-10 w-3">hi</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect(Ansi::visibleLength($ansi))->toBe(10);
    });

    it('clamps down to max-w', function () {
        $html = '<div class="flex"><span class="max-w-5 w-20">hi</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect(Ansi::visibleLength($ansi))->toBe(5);
    });
});

// ---------------------------------------------------------------
// w-full
// ---------------------------------------------------------------

describe('w-full', function () {
    it('absorbs remaining flex space', function () {
        $html = '<div class="flex w-30"><span class="w-10">left</span><span class="w-full">fill</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect(Ansi::visibleLength($ansi))->toBe(30);
    });
});

// ---------------------------------------------------------------
// Display
// ---------------------------------------------------------------

describe('display', function () {
    it('renders block span as block element', function () {
        $html = '<div><span class="block">content</span><span class="block">more</span></div>';
        $lines = explode("\n", $this->renderer->toAnsi($html));

        expect(count($lines))->toBe(2);
        expect($lines[0])->toContain('content');
        expect($lines[1])->toContain('more');
    });

    it('hidden returns nothing', function () {
        $html = '<div class="hidden">secret</div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toBe('');
    });

    it('invisible replaces content with spaces', function () {
        $html = '<div class="invisible">hello</div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->not->toContain('hello');
        expect(trim($ansi))->toBe('');
    });
});

// ---------------------------------------------------------------
// Justify
// ---------------------------------------------------------------

describe('justify', function () {
    it('distributes space with justify-between', function () {
        $html = '<div class="flex justify-between w-20"><span>A</span><span>B</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toMatch('/^A\s+B$/');
        expect(Ansi::visibleLength($ansi))->toBe(20);
    });

    it('centers content with justify-center', function () {
        $html = '<div class="flex justify-center w-20"><span>AB</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toMatch('/^\s+AB\s+$/');
    });

    it('distributes space with justify-around', function () {
        $html = '<div class="flex justify-around w-20"><span>A</span><span>B</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toMatch('/^\s+A\s+B\s+$/');
    });

    it('distributes space with justify-evenly', function () {
        $html = '<div class="flex justify-evenly w-21"><span>A</span><span>B</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toMatch('/^\s+A\s+B\s+$/');
    });
});

// ---------------------------------------------------------------
// List Styles
// ---------------------------------------------------------------

describe('list styles', function () {
    it('renders list-disc with bullet markers', function () {
        $html = '<div class="list-disc"><span>item one</span><span>item two</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toContain('• item one');
        expect($ansi)->toContain('• item two');
    });

    it('renders list-decimal with numbered markers', function () {
        $html = '<div class="list-decimal"><span>first</span><span>second</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toContain('1. first');
        expect($ansi)->toContain('2. second');
    });

    it('renders list-square with square markers', function () {
        $html = '<div class="list-square"><span>item</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toContain('▪ item');
    });

    it('renders list-none without markers', function () {
        $html = '<div class="list-none"><span>item</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toContain('item');
        expect($ansi)->not->toContain('•');
        expect($ansi)->not->toContain('▪');
    });
});
