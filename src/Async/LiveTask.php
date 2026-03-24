<?php

declare(strict_types=1);

namespace OmniTerm\Async;

use OmniTerm\AsyncHtmlRenderer;

class LiveTask
{
    protected array $rows = [];

    protected AsyncHtmlRenderer $async;

    protected Spinner $spinner;

    protected array $colors;

    protected int $us;

    public function __construct(
        protected string $title,
        Spinner $spinner = Spinner::Sand,
        ?array $colors = null,
        int $us = 1000,
    ) {
        $this->spinner = $spinner;
        $this->colors = $colors ?? ['text-amber-500', 'text-emerald-500', 'text-rose-500', 'text-sky-500'];
        $this->us = $us;
        $this->async = new AsyncHtmlRenderer(function () {});
    }

    public function row(string $label, mixed $value = 0, string $color = '', string $details = ''): static
    {
        $this->rows[$label] = [
            'details' => $details,
            'value' => $value,
            'color' => $color,
        ];

        $this->renderState('running', 1);

        return $this;
    }

    public function run(callable $task): mixed
    {
        $this->async->withTask($task);
        $this->async->withFailOver($this->buildHtml('failover', 1));

        $result = $this->async->run(function () {
            $this->async->render($this->buildHtml('running', $this->async->getInterval()));
        }, $this->us);

        $this->renderState('running', 1);

        return $result;
    }

    public function runTask(callable $task): TaskResult|false
    {
        $result = $this->run($task);

        if (empty($result)) {
            $this->finishWithError($this->title.' failed');

            return false;
        }

        $taskResult = is_array($result)
            ? TaskResult::fromArray($result, $this->title.' completed')
            : TaskResult::success($this->title.' completed');

        match ($taskResult->state) {
            'error' => $this->finishWithError($taskResult->message),
            'warning' => $this->finishWithWarning($taskResult->message),
            default => $this->finish($taskResult->message),
        };

        return $taskResult;
    }

    public function get(string $label): mixed
    {
        return $this->rows[$label]['value'] ?? null;
    }

    public function set(string $label, mixed $value): static
    {
        if (isset($this->rows[$label])) {
            $this->rows[$label]['value'] = $value;
        }

        $this->renderState('running', 1);

        return $this;
    }

    public function increment(string $label, int|float $amount = 1): static
    {
        if (isset($this->rows[$label])) {
            $this->rows[$label]['value'] += $amount;
        }

        $this->renderState('running', 1);

        return $this;
    }

    public function finish(?string $message = null): void
    {
        $this->renderState('success', 1, $message);
    }

    public function finishWithError(?string $message = null): void
    {
        $this->renderState('error', 1, $message);
    }

    public function finishWithWarning(?string $message = null): void
    {
        $this->renderState('warning', 1, $message);
    }

    protected function renderState(string $state, int $frame, ?string $message = null): void
    {
        $this->async->render($this->buildHtml($state, $frame, $message));
    }

    protected function buildHtml(string $state, int $frame, ?string $message = null): string
    {
        return (string) view('omniterm::live-task', [ // @phpstan-ignore argument.type
            'title' => $message ?? $this->title,
            'state' => $state,
            'frame' => $frame,
            'frames' => $this->spinner->frames(),
            'colors' => $this->colors,
            'rows' => $this->rows,
        ]);
    }
}
