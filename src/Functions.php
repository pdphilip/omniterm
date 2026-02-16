<?php

declare(strict_types=1);

namespace OmniTerm;

use Closure;
use OmniTerm\Rendering\Renderer;
use OmniTerm\Rendering\Terminal;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

if (! function_exists('OmniTerm\renderUsing')) {
    /**
     * Sets the renderer implementation.
     */
    function renderUsing(?OutputInterface $renderer): void
    {
        Renderer::renderUsing($renderer);
    }
}

if (! function_exists('OmniTerm\style')) {
    /**
     * Creates a new style (no-op — custom styles not supported in OmniTerm renderer).
     */
    function style(string $name, ?Closure $callback = null): void
    {
        // Custom styles not supported in OmniTerm's own renderer
    }
}

if (! function_exists('OmniTerm\render')) {
    /**
     * Render HTML to the terminal.
     */
    function render(string $html, int $options = OutputInterface::OUTPUT_NORMAL): void
    {
        (new Renderer)->render($html, $options);
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
        return (new Renderer)->parse($html)->toString();
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
        (new Renderer)->render($question);

        $helper = new QuestionHelper;
        $q = new Question('');
        if ($autocomplete !== null) {
            $q->setAutocompleterValues($autocomplete);
        }

        return $helper->ask(new ArrayInput([]), new ConsoleOutput, $q);
    }
}
