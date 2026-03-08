<?php

use OmniTerm\Rendering\Renderer;

beforeEach(function () {
    $this->renderer = new Renderer;
});

describe('ask question view rendering', function () {
    it('renders a question with no options', function () {
        $html = view('omniterm::elements.question', [
            'question' => 'Enter your name',
            'options' => [],
            'default' => null,
        ])->render();

        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)
            ->toContain('Enter your name')
            ->not->toContain('default:');
    });

    it('renders a question with options', function () {
        $html = view('omniterm::elements.question', [
            'question' => 'Continue?',
            'options' => ['Yes', 'No'],
            'default' => null,
        ])->render();

        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)
            ->toContain('Continue?')
            ->toContain('Yes/No');
    });

    it('renders a question with a default value', function () {
        $html = view('omniterm::elements.question', [
            'question' => 'Tolerance %',
            'options' => [0.1, 0.5, 1, 5],
            'default' => 0.1,
        ])->render();

        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)
            ->toContain('Tolerance %')
            ->toContain('0.1/0.5/1/5')
            ->toContain('default: 0.1');
    });

    it('does not render default hint when default is null', function () {
        $html = view('omniterm::elements.question', [
            'question' => 'Pick one',
            'options' => ['A', 'B'],
            'default' => null,
        ])->render();

        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->not->toContain('default:');
    });

    it('renders default hint for string defaults', function () {
        $html = view('omniterm::elements.question', [
            'question' => 'Environment',
            'options' => ['production', 'staging', 'local'],
            'default' => 'local',
        ])->render();

        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)
            ->toContain('default: local');
    });

    it('renders default hint for integer defaults', function () {
        $html = view('omniterm::elements.question', [
            'question' => 'Max retries',
            'options' => [1, 3, 5, 10],
            'default' => 3,
        ])->render();

        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)
            ->toContain('default: 3');
    });

    it('renders the prompt character', function () {
        $html = view('omniterm::elements.question', [
            'question' => 'Name?',
            'options' => [],
            'default' => null,
        ])->render();

        $ansi = $this->renderer->toAnsi($html);

        expect($ansi)->toContain('❯');
    });
});
