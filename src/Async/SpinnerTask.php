<?php

declare(strict_types=1);

namespace OmniTerm\Async;

use OmniTerm\AsyncHtmlRenderer;

class SpinnerTask
{
    protected AsyncHtmlRenderer $async;

    public function __construct(
        protected Spinner $spinner = Spinner::Sand,
        protected array $colors = [],
        protected int $us = 50_000,
    ) {
        if (empty($this->colors)) {
            $this->colors = ['text-amber-500', 'text-emerald-500', 'text-rose-500', 'text-sky-500'];
        }
        $this->async = new AsyncHtmlRenderer(function () {});
    }

    public function run(string $title, callable $task): TaskResult|false
    {
        $this->async->withTask($task);
        $this->async->withFailOver($this->buildHtml('failover', $title, 1));

        $result = $this->async->run(function () use ($title) {
            $this->async->render($this->buildHtml('running', $title, $this->async->getInterval()));
        }, $this->us);

        if (empty($result)) {
            $this->renderFinished('error', $title.' failed');

            return false;
        }

        $taskResult = is_array($result)
            ? TaskResult::fromArray($result, $title.' completed')
            : TaskResult::success($title.' completed');

        $this->renderFinished($taskResult->state, $taskResult->message, $taskResult->details);

        return $taskResult;
    }

    protected function renderFinished(string $state, string $message, string $details = ''): void
    {
        $this->async->render($this->buildHtml($state, $message, 1, $details));
    }

    protected function buildHtml(string $state, string $message, int $frame, string $details = ''): string
    {
        return (string) view($this->spinner->view(), [
            'state' => $state,
            'frames' => $this->spinner->frames(),
            'colors' => $this->colors,
            'message' => $message,
            'details' => $details,
            'i' => $frame,
        ]);
    }
}
