<?php

declare(strict_types=1);

namespace OmniTerm\Prompts;

use Laravel\Prompts\MultiSelectPrompt;
use Laravel\Prompts\PasswordPrompt;
use Laravel\Prompts\Prompt;
use Laravel\Prompts\SelectPrompt;
use Laravel\Prompts\SuggestPrompt;
use Laravel\Prompts\TextPrompt;
use OmniTerm\Prompts\Renderers\MultiSelectPromptRenderer;
use OmniTerm\Prompts\Renderers\PasswordPromptRenderer;
use OmniTerm\Prompts\Renderers\SelectPromptRenderer;
use OmniTerm\Prompts\Renderers\SuggestPromptRenderer;
use OmniTerm\Prompts\Renderers\TextPromptRenderer;

/**
 * Registers OmniTerm's renderers as a laravel/prompts theme and runs a prompt
 * under it, restoring the caller's theme afterwards so OmniTerm never hijacks
 * prompts made elsewhere in the consuming app.
 */
class PromptTheme
{
    public const NAME = 'omniterm';

    protected static bool $installed = false;

    public static function install(): void
    {
        if (static::$installed) {
            return;
        }

        Prompt::addTheme(static::NAME, [
            TextPrompt::class => TextPromptRenderer::class,
            SuggestPrompt::class => SuggestPromptRenderer::class,
            SelectPrompt::class => SelectPromptRenderer::class,
            MultiSelectPrompt::class => MultiSelectPromptRenderer::class,
            PasswordPrompt::class => PasswordPromptRenderer::class,
        ]);

        static::$installed = true;
    }

    /**
     * Run a prompt under the OmniTerm theme, then restore the previous theme.
     *
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     */
    public static function run(callable $callback): mixed
    {
        static::install();

        $previous = Prompt::theme();
        Prompt::theme(static::NAME);

        try {
            return $callback();
        } finally {
            Prompt::theme($previous);
        }
    }
}
