<?php

namespace OmniTerm\Helpers;

use Closure;
use InvalidArgumentException;
use OmniTerm\Async\LiveTask;
use OmniTerm\Async\SplitBrowser;
use OmniTerm\AsyncHtmlRenderer;
use OmniTerm\LiveHtmlRenderer;
use OmniTerm\Rendering\Renderer;
use OmniTerm\Rendering\Terminal;
use Symfony\Component\Console\Output\OutputInterface;

use function OmniTerm\ask;
use function OmniTerm\asyncFunction;
use function OmniTerm\parse;
use function OmniTerm\render;

class OmniHelpers
{
    public mixed $progressInstance;

    public mixed $asyncLoader;

    public mixed $async;

    private ?LiveHtmlRenderer $activeLiveRenderer = null;

    public string $disabledColor = 'zinc';

    public string $infoColor = 'sky';

    public string $successColor = 'emerald';

    public string $warningColor = 'amber';

    public string $errorColor = 'rose';

    public function statusColors(): array
    {
        return [
            'disabled' => $this->disabledColor,
            'info' => $this->infoColor,
            'success' => $this->successColor,
            'warning' => $this->warningColor,
            'error' => $this->errorColor,
        ];
    }

    // ----------------------------------------------------------------------
    // Internal
    // ----------------------------------------------------------------------

    public function omniError(string $method, string $error, string $help = ''): never
    {
        render(view('omniterm::status.omni-error', ['method' => $method, 'error' => $error, 'help' => $help]));
        exit(1);
    }

    protected function renderView(string $view, array $data = []): string
    {
        try {
            return view($view, $data)->render();
        } catch (InvalidArgumentException $e) {
            $this->omniError($view, 'View not found', 'Check that the omniterm views are published or the package is installed correctly');
        }
    }

    protected function outputHtml(string $html): void
    {
        if ($this->activeLiveRenderer !== null) {
            $this->activeLiveRenderer->write($html);
        } else {
            render($html);
        }
    }

    // ----------------------------------------------------------------------
    // Inline HTML
    // ----------------------------------------------------------------------

    public function view(string $view, array $data = []): void
    {
        $this->outputHtml($this->renderView($view, $data));
    }

    public function line(string $html): void
    {
        $this->outputHtml($html);
    }

    public function parse(string $html): string
    {
        return parse($html);
    }

    public function terminal(): Terminal
    {
        return new Terminal;
    }

    public function renderUsing(?OutputInterface $renderer): void
    {
        Renderer::renderUsing($renderer);
    }

    public function liveView(string $view = '', array $data = []): LiveHtmlRenderer
    {
        $html = $view !== '' ? $this->renderView($view, $data) : '';
        $this->activeLiveRenderer = new LiveHtmlRenderer($html);

        return $this->activeLiveRenderer;
    }

    public function endLiveView(): void
    {
        $this->activeLiveRenderer = null;
    }

    public function async(callable $task): AsyncHtmlRenderer
    {
        return new AsyncHtmlRenderer($task);
    }

    // ----------------------------------------------------------------------
    // Elements
    // ----------------------------------------------------------------------

    public function titleBar(string $title, string $color = 'sky'): void
    {
        $this->outputHtml($this->renderView('omniterm::elements.title-bar', ['t' => '', 'color' => $color]));
        $this->outputHtml($this->renderView('omniterm::elements.title-bar', ['t' => $title, 'color' => $color]));
        $this->outputHtml($this->renderView('omniterm::elements.title-bar', ['t' => '', 'color' => $color]));
    }

    public function box($title, $borderColor = 'text-gray', $textColor = 'text-gray'): void
    {
        $this->outputHtml($this->renderView('omniterm::elements.box', ['title' => $title, 'borderColor' => $borderColor, 'textColor' => $textColor, 'type' => 'square']));
    }

    public function roundedBox($title, $borderColor = 'text-gray', $textColor = 'text-gray'): void
    {
        $this->outputHtml($this->renderView('omniterm::elements.box', ['title' => $title, 'borderColor' => $borderColor, 'textColor' => $textColor, 'type' => 'rounded']));
    }

    public function hr($color = 'text-gray'): void
    {
        $this->outputHtml($this->renderView('omniterm::elements.hr', ['color' => $color]));
    }

    public function hrSuccess(): void
    {
        $this->outputHtml($this->renderView('omniterm::elements.hr', ['color' => 'text-'.$this->successColor.'-500']));
    }

    public function hrInfo(): void
    {
        $this->outputHtml($this->renderView('omniterm::elements.hr', ['color' => 'text-'.$this->infoColor.'-500']));
    }

    public function hrWarning(): void
    {
        $this->outputHtml($this->renderView('omniterm::elements.hr', ['color' => 'text-'.$this->warningColor.'-500']));
    }

    public function hrError(): void
    {
        $this->outputHtml($this->renderView('omniterm::elements.hr', ['color' => 'text-'.$this->errorColor.'-500']));
    }

    public function hrDisabled(): void
    {
        $this->outputHtml($this->renderView('omniterm::elements.hr', ['color' => 'text-'.$this->disabledColor.'-500']));
    }

    // ----------------------------------------------------------------------
    // Data tables
    // ----------------------------------------------------------------------

    public function tableHeader($keyName, $valueName, $detailsName = null): void
    {
        $this->outputHtml($this->renderView('omniterm::elements.header-row', ['keyName' => $keyName, 'valueName' => $valueName, 'detailsName' => $detailsName]));
    }

    public function tableRow($key, $value, $details = null, $valueClass = null, $help = []): void
    {
        $this->outputHtml($this->renderView('omniterm::elements.data-row', ['key' => $key, 'value' => $value, 'details' => $details, 'help' => $help, 'class' => $valueClass, 'statusColors' => $this->statusColors()]));
    }

    public function tableRowSuccess($key, $details = null, $help = []): void
    {
        $this->tableRowAsStatus($key, 'success', $details, $help);
    }

    public function tableRowEnabled($key, $details = null, $help = []): void
    {
        $this->tableRowAsStatus($key, 'enabled', $details, $help);
    }

    public function tableRowDisabled($key, $details = null, $help = []): void
    {
        $this->tableRowAsStatus($key, 'disabled', $details, $help);
    }

    public function tableRowWarning($key, $details = null, $help = []): void
    {
        $this->tableRowAsStatus($key, 'warning', $details, $help);
    }

    public function tableRowError($key, $details = null, $help = []): void
    {
        $this->tableRowAsStatus($key, 'error', $details, $help);
    }

    public function tableRowInfo($key, $details = null, $help = []): void
    {
        $this->tableRowAsStatus($key, 'info', $details, $help);
    }

    public function tableRowOk($key, $details = null, $help = []): void
    {
        $this->tableRowAsStatus($key, 'ok', $details, $help);
    }

    public function tableRowFailed($key, $details = null, $help = []): void
    {
        $this->tableRowAsStatus($key, 'failed', $details, $help);
    }

    public function tableRowAsStatus($key, $status, $details = null, $help = []): void
    {
        $this->outputHtml($this->renderView('omniterm::elements.data-row-status', ['key' => $key, 'status' => $status, 'details' => $details, 'help' => $help, 'statusColors' => $this->statusColors()]));
    }

    // ----------------------------------------------------------------------
    // ASK
    // ----------------------------------------------------------------------

    public function ask($question, $options = []): mixed
    {
        return ask($this->renderView('omniterm::elements.question', ['question' => $question, 'options' => $options]), $options);
    }

    // ----------------------------------------------------------------------
    // Browser
    // ----------------------------------------------------------------------

    public function browse(string $label, array $items, Closure $detail, int $scroll = 12, string $hint = ''): mixed
    {
        return SplitBrowser::browse($label, $items, $detail, $scroll, $hint);
    }

    // ----------------------------------------------------------------------
    // Live Tasks
    // ----------------------------------------------------------------------

    public function liveTask(string $title, string $spinner = 'sand', ?array $colors = null, int $us = 1000): LiveTask
    {
        return new LiveTask($title, $spinner, $colors, $us);
    }

    public function task(string $title, callable $callback, string $spinner = 'sand', ?array $colors = null): mixed
    {
        $liveTask = $this->liveTask($title, $spinner, $colors);
        $result = $liveTask->run($callback);

        if (empty($result)) {
            $liveTask->finishWithError($title.' failed');

            return false;
        }

        $state = $result['state'] ?? 'success';
        $message = $result['message'] ?? $title.' completed';

        match ($state) {
            'error' => $liveTask->finishWithError($message),
            'warning' => $liveTask->finishWithWarning($message),
            default => $liveTask->finish($message),
        };

        return $result;
    }

    // ----------------------------------------------------------------------
    // Feedback titles
    // ----------------------------------------------------------------------

    public function error($message): void
    {
        $this->outputHtml($this->renderView('omniterm::status.error', ['message' => $message, 'color' => $this->errorColor]));
    }

    public function success($message = 'ok'): void
    {
        $this->outputHtml($this->renderView('omniterm::status.success', ['message' => $message, 'color' => $this->successColor]));
    }

    public function warning($message): void
    {
        $this->outputHtml($this->renderView('omniterm::status.warning', ['message' => $message, 'color' => $this->warningColor]));
    }

    public function info($message): void
    {
        $this->outputHtml($this->renderView('omniterm::status.info', ['message' => $message], ['color' => $this->infoColor]));
    }

    public function disabled($message): void
    {
        $this->outputHtml($this->renderView('omniterm::status.disabled', ['message' => $message], ['color' => $this->disabledColor]));
    }

    // ----------------------------------------------------------------------
    // Statuses
    // ----------------------------------------------------------------------

    public function status(string $status, string $title, string $details, array $help = []): void
    {
        $this->outputHtml($this->renderView('omniterm::status.custom', ['status' => $status, 'title' => $title, 'details' => $details, 'help' => $help, 'statusColors' => $this->statusColors()]));
    }

    public function statusSuccess(string $title, string $details, array $help = []): void
    {
        $this->status('success', $title, $details, $help);
    }

    public function statusInfo(string $title, string $details, array $help = []): void
    {
        $this->status('info', $title, $details, $help);
    }

    public function statusWarning(string $title, string $details, array $help = []): void
    {
        $this->status('warning', $title, $details, $help);
    }

    public function statusError(string $title, string $details, array $help = []): void
    {
        $this->status('error', $title, $details, $help);
    }

    public function statusDisabled(string $title, string $details, array $help = []): void
    {
        $this->status('disabled', $title, $details, $help);
    }

    // ----------------------------------------------------------------------
    // Progress bars
    // ----------------------------------------------------------------------

    public function createProgressBar($total, $withColors = true)
    {
        if ($withColors) {
            $this->progressInstance = new ProgressBar('framed-color');
        } else {
            $this->progressInstance = new ProgressBar('framed');
        }
        $this->progressInstance->setTotal($total);

    }

    public function createGradientProgressBar($total)
    {
        $this->progressInstance = new ProgressBar('gradient');
        $this->progressInstance->setTotal($total);
    }

    public function createGradientFramedProgressBar($total)
    {
        $this->progressInstance = new ProgressBar('gradient-framed');
        $this->progressInstance->setTotal($total);
    }

    public function createSimpleProgressBar($total, $withColors = true)
    {
        if ($withColors) {
            $this->progressInstance = new ProgressBar('simple-color');
        } else {
            $this->progressInstance = new ProgressBar('simple');
        }
        $this->progressInstance->setTotal($total);
    }

    public function showProgress(): void
    {
        if (empty($this->progressInstance)) {
            $this->omniError('showProgress()', 'No progress bar instance found', 'Call createProgressBar() first');
        }
        $this->progressInstance->show();
    }

    public function progressAdvance($by = 1): void
    {
        if (empty($this->progressInstance)) {
            $this->omniError('progressAdvance()', 'No progress bar instance found', 'Call createProgressBar() first');
        }
        $this->progressInstance->increment($by);
    }

    public function progressFinish(): void
    {
        if (empty($this->progressInstance)) {
            $this->omniError('progressFinish()', 'No progress bar instance found', 'Call createProgressBar() first');
        }
        $this->progressInstance->finish();
    }

    // ----------------------------------------------------------------------
    // Loaders
    // ----------------------------------------------------------------------

    public function newLoader($type = 'sand', $colors = null, $us = 1000): void
    {
        if (! $colors) {
            $colors = ['text-amber-500', 'text-emerald-500', 'text-rose-500', 'text-sky-500'];
        }

        $this->asyncLoader = asyncFunction(function () {});
        if ($type == 'loader') {
            $this->async = [
                'view' => 'omniterm::loaders.loading',
                'type' => $type,
                'us' => $us,
                'colors' => $colors,
            ];
        } else {
            $this->async = [
                'view' => 'omniterm::loaders.spinner',
                'type' => $type,
                'us' => $us,
                'colors' => $colors,
            ];
        }

    }

    public function runTask($title, $task): mixed
    {
        if (empty($this->async)) {
            $this->omniError('runTask()', 'No loader instance found', 'Call newLoader() first');
        }
        $async = $this->asyncLoader;
        $async->withTask($task);
        $async->withFailOver($this->renderView($this->async['view'], [
            'state' => 'failover',
            'message' => $title,
            'i' => 1,
        ]));
        $result = $async->run(function () use ($async, $title) {
            $async->render($this->renderView($this->async['view'], [
                'state' => 'running',
                'type' => $this->async['type'],
                'colors' => $this->async['colors'],
                'message' => $title,
                'i' => $async->getInterval(),
            ]));
        }, $this->async['us']);
        if (empty($result)) {
            $async->render($this->renderView($this->async['view'], [
                'state' => 'error',
                'type' => $this->async['type'],
                'colors' => $this->async['colors'],
                'message' => $title.' failed',
                'i' => 1,
            ]));

            return false;
        }

        $state = 'success';
        $message = $title.' completed';
        $details = '';
        if (! empty($result['state'])) {
            $state = $result['state'];
        }
        if (! empty($result['message'])) {
            $message = $result['message'];
        }
        if (! empty($result['details'])) {
            $details = $result['details'];
        }
        $async->render($this->renderView($this->async['view'], [
            'state' => $state,
            'type' => $this->async['type'],
            'colors' => $this->async['colors'],
            'message' => $message,
            'details' => $details,
            'i' => 1,
        ]));

        return $result;
    }
}
