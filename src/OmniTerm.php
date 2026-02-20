<?php

namespace OmniTerm;

use InvalidArgumentException;
use OmniTerm\Async\ConfirmTask;
use OmniTerm\Async\LiveTask;
use OmniTerm\Async\Spinner;
use OmniTerm\Async\SpinnerTask;
use OmniTerm\Async\SplitBrowser;
use OmniTerm\Async\TaskResult;
use OmniTerm\Helpers\ProgressBar;
use OmniTerm\Rendering\Renderer;
use OmniTerm\Rendering\Terminal;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class OmniTerm
{
    public mixed $progressInstance;

    protected ?SpinnerTask $spinnerTask = null;

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
        (new Renderer)->render((string) view('omniterm::status.omni-error', ['method' => $method, 'error' => $error, 'help' => $help]));
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
            (new Renderer)->render($html);
        }
    }

    // ----------------------------------------------------------------------
    // Inline HTML
    // ----------------------------------------------------------------------

    public function view(string $view, array $data = []): void
    {
        $this->outputHtml($this->renderView($view, $data));
    }

    public function render(string $html): void
    {
        $this->outputHtml($html);
    }

    public function parse(string $html): string
    {
        return (new Renderer)->parse($html)->toString();
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

    public function divider(string $label, string $color = 'text-stone-400')
    {
        $this->outputHtml($this->renderView('omniterm::elements.divider', ['label' => $label, 'color' => $color]));
    }

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
        $html = $this->renderView('omniterm::elements.question', ['question' => $question, 'options' => $options]);
        (new Renderer)->render($html);

        $q = new Question('');
        if (! empty($options)) {
            $q->setAutocompleterValues($options);
        }

        return (new QuestionHelper)->ask(new ArrayInput([]), new ConsoleOutput, $q);
    }

    // ----------------------------------------------------------------------
    // Browser
    // ----------------------------------------------------------------------

    public function browse(string $label, array $items, int $scroll = 12, string $hint = ''): mixed
    {
        return SplitBrowser::browse($label, $items, $this, $scroll, $hint);
    }

    // ----------------------------------------------------------------------
    // Confirm
    // ----------------------------------------------------------------------

    public function confirm(string $question, callable $callback, string $confirmColor = 'emerald', string $declineColor = 'rose'): mixed
    {
        return (new ConfirmTask($question, $callback(...), $this, $confirmColor, $declineColor))->run();
    }

    // ----------------------------------------------------------------------
    // Live Tasks
    // ----------------------------------------------------------------------

    public function liveTask(string $title, Spinner $spinner = Spinner::Sand, ?array $colors = null, int $us = 25_000): LiveTask
    {
        return new LiveTask($title, $spinner, $colors, $us);
    }

    public function task(string $title, callable $callback, Spinner $spinner = Spinner::Sand, ?array $colors = null): TaskResult|false
    {
        return $this->liveTask($title, $spinner, $colors)->runTask($callback);
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
        $this->outputHtml($this->renderView('omniterm::status.info', ['message' => $message, 'color' => $this->infoColor]));
    }

    public function disabled($message): void
    {
        $this->outputHtml($this->renderView('omniterm::status.disabled', ['message' => $message, 'color' => $this->disabledColor]));
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

    public function progressBar(int $total): ProgressBar
    {
        $this->progressInstance = new ProgressBar($total);

        return $this->progressInstance;
    }

    public function createProgressBar($total, $withColors = true): void
    {
        $bar = $this->progressBar($total)->framed();
        if ($withColors) {
            $bar->steps();
        }
    }

    public function createGradientProgressBar($total): void
    {
        $this->progressBar($total)->gradient();
    }

    public function createGradientFramedProgressBar($total): void
    {
        $this->progressBar($total)->framed()->gradient();
    }

    public function createSimpleProgressBar($total, $withColors = true): void
    {
        $bar = $this->progressBar($total);
        if ($withColors) {
            $bar->steps();
        }
    }

    public function showProgress(): void
    {
        if (empty($this->progressInstance)) {
            $this->omniError('showProgress()', 'No progress bar instance found', 'Call progressBar() first');
        }
        $this->progressInstance->start();
    }

    public function progressAdvance($by = 1): void
    {
        if (empty($this->progressInstance)) {
            $this->omniError('progressAdvance()', 'No progress bar instance found', 'Call progressBar() first');
        }
        $this->progressInstance->advance($by);
    }

    public function progressFinish(): void
    {
        if (empty($this->progressInstance)) {
            $this->omniError('progressFinish()', 'No progress bar instance found', 'Call progressBar() first');
        }
        $this->progressInstance->finish();
    }

    // ----------------------------------------------------------------------
    // Loaders
    // ----------------------------------------------------------------------

    public function newLoader(Spinner $spinner = Spinner::Sand, ?array $colors = null, int $us = 50_000): void
    {
        $this->spinnerTask = new SpinnerTask($spinner, $colors ?? [], $us);
    }

    public function runTask(string $title, callable $task): TaskResult|false
    {
        if ($this->spinnerTask === null) {
            $this->omniError('runTask()', 'No loader instance found', 'Call newLoader() first');
        }

        return $this->spinnerTask->run($title, $task);
    }
}
