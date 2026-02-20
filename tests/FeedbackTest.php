<?php

use OmniTerm\Rendering\Colors;
use OmniTerm\Rendering\Renderer;

beforeEach(function () {
    Colors::forceMode('truecolor');
    $this->renderer = new Renderer;
});

describe('feedback view rendering', function () {
    it('renders feedback with title badge and message', function () {
        $html = '<div class="flex mb-1 mx-1"><span class="bg-emerald-600 text-emerald-100 px-1 uppercase">GOOD</span><span class="pl-1 flex-1">Operation complete</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)
            ->toContain('GOOD')
            ->toContain('Operation complete');
    });

    it('renders error feedback with rose colors', function () {
        $html = '<div class="flex mb-1 mx-1"><span class="bg-rose-600 text-rose-100 px-1 uppercase">ERROR</span><span class="pl-1 flex-1">Something broke</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        $bgRgb = Colors::rgb('rose', 600);
        expect($ansi)
            ->toContain("\e[48;2;{$bgRgb[0]};{$bgRgb[1]};{$bgRgb[2]}m")
            ->toContain('ERROR')
            ->toContain('Something broke');
    });

    it('renders custom title text', function () {
        $html = '<div class="flex mb-1 mx-1"><span class="bg-sky-600 text-sky-100 px-1 uppercase">DEPLOY</span><span class="pl-1 flex-1">Pushed to staging</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)
            ->toContain('DEPLOY')
            ->toContain('Pushed to staging');
    });

    it('renders custom color feedback', function () {
        $html = '<div class="flex mb-1 mx-1"><span class="bg-violet-600 text-violet-100 px-1 uppercase">CUSTOM</span><span class="pl-1 flex-1">Custom message</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        $bgRgb = Colors::rgb('violet', 600);
        expect($ansi)
            ->toContain("\e[48;2;{$bgRgb[0]};{$bgRgb[1]};{$bgRgb[2]}m")
            ->toContain('CUSTOM')
            ->toContain('Custom message');
    });

    it('uppercases the title', function () {
        $html = '<div class="flex mb-1 mx-1"><span class="bg-sky-600 text-sky-100 px-1 uppercase">info</span><span class="pl-1 flex-1">test</span></div>';
        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toContain('INFO');
    });
});
