<?php

declare(strict_types=1);

namespace OmniTerm\Async;

use Closure;
use OmniTerm\OmniTerm;

class ConfirmTask
{
    public function __construct(
        protected string $question,
        protected Closure $callback,
        protected OmniTerm $omni,
        protected string $confirmColor = 'emerald',
        protected string $declineColor = 'rose',
    ) {}

    public function run(): mixed
    {
        $renderer = $this->omni->liveView('omniterm::confirm', $this->viewData('asking'));

        if (! $this->readConfirmation()) {
            $renderer->reRenderView('omniterm::confirm', $this->viewData('declined'));
            $this->omni->hr("text-{$this->declineColor}-500");
            $this->omni->endLiveView();

            return false;
        }

        $renderer->reRenderView('omniterm::confirm', $this->viewData('confirmed'));

        $result = ($this->callback)();

        $this->omni->hr("text-{$this->confirmColor}-500");
        $this->omni->endLiveView();

        return $result ?? true;
    }

    // ------------------------------------------------------------------
    // Input
    // ------------------------------------------------------------------

    private function readConfirmation(): bool
    {
        $stty = @shell_exec('stty -g');

        if ($stty === null || trim($stty) === '') {
            return $this->readWithEnter();
        }

        $stty = trim($stty);
        @system('stty -icanon -echo');

        try {
            while (true) {
                $char = fread(STDIN, 1);
                if ($char === false || $char === '') {
                    return false;
                }

                $lower = strtolower($char);
                if ($lower === 'y') {
                    return true;
                }
                if ($lower === 'n' || ord($char) === 27) {
                    return false;
                }
            }
        } finally {
            @system("stty '{$stty}'");
        }
    }

    private function readWithEnter(): bool
    {
        $line = trim(fgets(STDIN) ?: '');

        return in_array(strtolower($line), ['y', 'yes']);
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
        ];
    }
}
