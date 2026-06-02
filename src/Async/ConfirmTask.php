<?php

declare(strict_types=1);

namespace OmniTerm\Async;

use Closure;
use OmniTerm\OmniTerm;

class ConfirmTask
{
    public function __construct(
        protected string $question,
        protected ?Closure $callback,
        protected OmniTerm $omni,
        protected string $confirmColor = 'emerald',
        protected string $declineColor = 'rose',
        protected bool $default = true,
    ) {}

    public function run(): bool
    {
        $renderer = $this->omni->liveView('omniterm::confirm', $this->viewData('asking'));

        if (! $this->readConfirmation()) {
            $renderer->reRenderView('omniterm::confirm', $this->viewData('declined'));
            $this->omni->hr("text-{$this->declineColor}-500");
            $this->omni->endLiveView();

            return false;
        }

        $renderer->reRenderView('omniterm::confirm', $this->viewData('confirmed'));

        if ($this->callback !== null) {
            $this->omni->newLine();
            ($this->callback)();
        }

        $this->omni->hr("text-{$this->confirmColor}-500");
        $this->omni->endLiveView();

        return true;
    }

    // ------------------------------------------------------------------
    // Input
    // ------------------------------------------------------------------

    private function readConfirmation(): bool
    {
        $savedState = $this->enableRawMode();

        if ($savedState === null) {
            return $this->readWithEnter();
        }

        try {
            return $this->waitForYesOrNo();
        } finally {
            $this->restoreTerminal($savedState);
        }
    }

    private function waitForYesOrNo(): bool
    {
        while (true) {
            $char = fread(STDIN, 1);

            if ($char === false || $char === '') {
                return false;
            }

            $lower = strtolower($char);

            if ($lower === 'y') {
                return true;
            }

            if ($lower === 'n') {
                return false;
            }

            if ($char === "\e") {
                return false;
            }

            if ($char === "\n" || $char === "\r") {
                return $this->default;
            }
        }
    }

    private function readWithEnter(): bool
    {
        $line = trim(fgets(STDIN) ?: '');

        if ($line === '') {
            return $this->default;
        }

        return in_array(strtolower($line), ['y', 'yes'], true);
    }

    // ------------------------------------------------------------------
    // Terminal Raw Mode
    // ------------------------------------------------------------------

    // Switches STDIN to raw mode so fread() returns each keypress
    // immediately without waiting for Enter, and without echoing.
    // Returns the saved terminal state for restoreTerminal(), or
    // null if raw mode is unavailable (e.g. piped input, Windows).

    private function enableRawMode(): ?string
    {
        $state = @shell_exec('stty -g');

        if ($state === null || trim($state) === '') {
            return null;
        }

        @system('stty -icanon -echo');

        return trim($state);
    }

    private function restoreTerminal(string $state): void
    {
        @system("stty '{$state}'");
    }

    // ------------------------------------------------------------------
    // View
    // ------------------------------------------------------------------

    private function viewData(string $state): array
    {
        return [
            'question' => $this->question,
            'state' => $state,
            'confirmColor' => $this->confirmColor,
            'declineColor' => $this->declineColor,
            'default' => $this->default,
        ];
    }
}
