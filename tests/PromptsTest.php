<?php

use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;
use OmniTerm\OmniTerm;
use OmniTerm\Prompts\Validation;

beforeEach(function () {
    $this->omni = new OmniTerm;
});

afterEach(function () {
    Prompt::theme('default');
});

describe('Validation::resolve', function () {
    it('returns null when no rule is given', function () {
        expect(Validation::resolve(null))->toBeNull();
    });

    it('validates email', function () {
        $rule = Validation::resolve('email');
        expect($rule('not-an-email'))->toBeString();
        expect($rule('a@b.com'))->toBeNull();
    });

    it('validates url', function () {
        $rule = Validation::resolve('url');
        expect($rule('nope'))->toBeString();
        expect($rule('https://example.com'))->toBeNull();
    });

    it('validates integers', function () {
        $rule = Validation::resolve('int');
        expect($rule('12a'))->toBeString();
        expect($rule('-42'))->toBeNull();
    });

    it('validates a regex rule', function () {
        $rule = Validation::resolve('regex:^[A-Z]{2}$');
        expect($rule('usa'))->toBeString();
        expect($rule('US'))->toBeNull();
    });

    it('validates an in: list', function () {
        $rule = Validation::resolve('in:light,dark,both');
        expect($rule('blue'))->toBeString();
        expect($rule('dark'))->toBeNull();
    });

    it('wraps a callable rule', function () {
        $rule = Validation::resolve(fn ($v) => $v === 'ok' ? null : 'bad');
        expect($rule('nope'))->toBe('bad');
        expect($rule('ok'))->toBeNull();
    });
});

describe('select', function () {
    it('returns the highlighted option after navigation', function () {
        Prompt::fake([Key::DOWN, Key::ENTER]);

        $choice = $this->omni->select('Theme mode', ['light', 'dark', 'both']);

        expect($choice)->toBe('dark');
    });

    it('returns the chosen key for an associative list', function () {
        Prompt::fake([Key::ENTER]);

        $choice = $this->omni->select('Pick', ['a' => 'Alpha', 'b' => 'Beta']);

        expect($choice)->toBe('a');
    });
});

describe('multiselect', function () {
    it('returns selected values for a plain list', function () {
        Prompt::fake([Key::SPACE, Key::ENTER]);

        $selected = $this->omni->multiselect('Pick', ['one', 'two', 'three']);

        expect($selected)->toBe(['one']);
    });

    it('returns a key => bool map for the labelled spec shape', function () {
        Prompt::fake([Key::ENTER]);

        $selected = $this->omni->multiselect('Capabilities', [
            'PASSPORT' => ['label' => 'Passport OAuth2', 'default' => true],
            'REVERB' => ['label' => 'Websockets', 'default' => false],
            'STRIPE' => ['label' => 'Stripe billing', 'default' => false],
        ]);

        expect($selected)->toBe([
            'PASSPORT' => true,
            'REVERB' => false,
            'STRIPE' => false,
        ]);
    });
});

describe('ask', function () {
    it('returns the typed value', function () {
        Prompt::fake([...str_split('David'), Key::ENTER]);

        expect($this->omni->ask('Name'))->toBe('David');
    });

    it('re-prompts on validation failure then accepts a valid value', function () {
        // First submit "a@b" (invalid), then append ".com" to correct it.
        Prompt::fake([...str_split('a@b'), Key::ENTER, ...str_split('.com'), Key::ENTER]);

        $email = $this->omni->ask('Email', validate: 'email');

        expect($email)->toBe('a@b.com');
        Prompt::assertStrippedOutputContains('valid email');
    });
});

describe('PromptTheme', function () {
    it('restores the previous theme after running', function () {
        Prompt::fake([Key::ENTER]);

        expect(Prompt::theme())->toBe('default');
        $this->omni->select('Pick', ['a' => 'Alpha']);
        expect(Prompt::theme())->toBe('default');
    });
});
