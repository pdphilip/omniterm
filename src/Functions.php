<?php

declare(strict_types=1);

namespace OmniTerm;

use Closure;
use Symfony\Component\Console\Output\OutputInterface;
use Termwind\HtmlRenderer;
use Termwind\Question;
use Termwind\Repositories\Styles as StyleRepository;
use Termwind\Terminal;
use Termwind\Termwind;
use Termwind\ValueObjects\Style;
use Termwind\ValueObjects\Styles;

if (! function_exists('OmniTerm\renderUsing')) {
    /**
     * Sets the renderer implementation.
     */
    function renderUsing(?OutputInterface $renderer): void
    {
        Termwind::renderUsing($renderer);
    }
}

if (! function_exists('OmniTerm\style')) {
    /**
     * Creates a new style.
     *
     * @param  (Closure(Styles $renderable, string|int ...$arguments): Styles)|null  $callback
     */
    function style(string $name, ?Closure $callback = null): Style
    {
        return StyleRepository::create($name, $callback);
    }
}

if (! function_exists('OmniTerm\render')) {
    /**
     * Render HTML to the terminal.
     */
    function render(string $html, int $options = OutputInterface::OUTPUT_NORMAL): void
    {
        (new HtmlRenderer)->render($html, $options);
    }
}

if (! function_exists('OmniTerm\liveRender')) {
    /**
     * Returns a live render instance to the terminal.
     */
    function liveRender(string $html = '', int $options = OutputInterface::OUTPUT_NORMAL): LiveHtmlRenderer
    {
        return new LiveHtmlRenderer($html, $options);
    }
}

if (! function_exists('OmniTerm\asyncRender')) {
    /**
     * Returns an async render instance to the terminal.
     */
    function asyncFunction(callable $task, int $options = OutputInterface::OUTPUT_NORMAL): AsyncHtmlRenderer
    {
        return new AsyncHtmlRenderer($task, $options);
    }
}

if (! function_exists('OmniTerm\parse')) {
    /**
     * Parse HTML to a string that can be rendered in the terminal.
     */
    function parse(string $html): string
    {
        return (new HtmlRenderer)->parse($html)->toString();
    }
}

if (! function_exists('OmniTerm\terminal')) {
    /**
     * Returns a Terminal instance.
     */
    function terminal(): Terminal
    {
        return new Terminal;
    }
}

if (! function_exists('OmniTerm\ask')) {
    /**
     * Renders a prompt to the user.
     *
     * @param  iterable<array-key, string>|null  $autocomplete
     */
    function ask(string $question, ?iterable $autocomplete = null): mixed
    {
        return (new Question)->ask($question, $autocomplete);
    }
}
