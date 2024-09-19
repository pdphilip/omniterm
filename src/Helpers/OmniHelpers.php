<?php

namespace OmniTerm\Helpers;

use Exception;

use function OmniTerm\ask;
use function OmniTerm\asyncFunction;
use function OmniTerm\render;

class OmniHelpers
{
    public mixed $progressInstance;

    public mixed $asyncLoader;

    public mixed $async;

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

    //----------------------------------------------------------------------
    // Elements
    //----------------------------------------------------------------------

    public function box($title, $borderColor = 'text-gray', $textColor = 'text-gray'): void
    {
        render(view('omniterm::elements.box', ['title' => $title, 'borderColor' => $borderColor, 'textColor' => $textColor, 'type' => 'square']));
    }

    public function roundedBox($title, $borderColor = 'text-gray', $textColor = 'text-gray'): void
    {
        render(view('omniterm::elements.box', ['title' => $title, 'borderColor' => $borderColor, 'textColor' => $textColor, 'type' => 'rounded']));
    }

    public function hr($color = 'text-gray'): void
    {
        render(view('omniterm::elements.hr', ['color' => $color]));
    }

    public function hrSuccess(): void
    {
        render(view('omniterm::elements.hr', ['color' => 'text-'.$this->successColor.'-500']));
    }

    public function hrInfo(): void
    {
        render(view('omniterm::elements.hr', ['color' => 'text-'.$this->infoColor.'-500']));
    }

    public function hrWarning(): void
    {
        render(view('omniterm::elements.hr', ['color' => 'text-'.$this->warningColor.'-500']));
    }

    public function hrError(): void
    {
        render(view('omniterm::elements.hr', ['color' => 'text-'.$this->errorColor.'-500']));
    }

    public function hrDisabled(): void
    {
        render(view('omniterm::elements.hr', ['color' => 'text-'.$this->disabledColor.'-500']));
    }

    //----------------------------------------------------------------------
    // Data tables
    //----------------------------------------------------------------------

    public function header($keyName, $valueName, $detailsName = null): void
    {
        render(view('omniterm::elements.header-row', ['keyName' => $keyName, 'valueName' => $valueName, 'detailsName' => $detailsName]));
    }

    public function row($key, $value, $details = null, $valueClass = null, $help = []): void
    {
        render(view('omniterm::elements.data-row', ['key' => $key, 'value' => $value, 'details' => $details, 'help' => $help, 'class' => $valueClass, 'statusColors' => $this->statusColors()]));
    }

    public function rowSuccess($key, $details = null, $help = []): void
    {
        $this->rowAsStatus($key, 'success', $details, $help);
    }

    public function rowEnabled($key, $details = null, $help = []): void
    {
        $this->rowAsStatus($key, 'enabled', $details, $help);
    }

    public function rowDisabled($key, $details = null, $help = []): void
    {
        $this->rowAsStatus($key, 'disabled', $details, $help);
    }

    public function rowWarning($key, $details = null, $help = []): void
    {
        $this->rowAsStatus($key, 'warning', $details, $help);
    }

    public function rowError($key, $details = null, $help = []): void
    {
        $this->rowAsStatus($key, 'error', $details, $help);
    }

    public function rowInfo($key, $details = null, $help = []): void
    {
        $this->rowAsStatus($key, 'info', $details, $help);
    }

    public function rowOk($key, $details = null, $help = []): void
    {
        $this->rowAsStatus($key, 'ok', $details, $help);
    }

    public function rowFailed($key, $details = null, $help = []): void
    {
        $this->rowAsStatus($key, 'failed', $details, $help);
    }

    public function rowAsStatus($key, $status, $details = null, $help = []): void
    {
        render(view('omniterm::elements.data-row-status', ['key' => $key, 'status' => $status, 'details' => $details, 'help' => $help, 'statusColors' => $this->statusColors()]));
    }

    //----------------------------------------------------------------------
    // ASK
    //----------------------------------------------------------------------

    public function ask($question, $options = []): mixed
    {
        return ask(view('omniterm::elements.question', ['question' => $question, 'options' => $options]), $options);
    }

    //----------------------------------------------------------------------
    // Feedback titles
    //----------------------------------------------------------------------

    public function error($message): void
    {
        render(view('omniterm::status.error', ['message' => $message, 'color' => $this->errorColor]));
    }

    public function success($message = 'ok'): void
    {
        render(view('omniterm::status.success', ['message' => $message, 'color' => $this->successColor]));
    }

    public function warning($message): void
    {
        render(view('omniterm::status.warning', ['message' => $message, 'color' => $this->warningColor]));
    }

    public function info($message): void
    {
        render(view('omniterm::status.info', ['message' => $message], ['color' => $this->infoColor]));
    }

    public function disabled($message): void
    {
        render(view('omniterm::status.disabled', ['message' => $message], ['color' => $this->disabledColor]));
    }

    //----------------------------------------------------------------------
    // Statuses
    //----------------------------------------------------------------------

    public function status(string $status, string $title, string $details, array $help = []): void
    {
        render(view('omniterm::status.custom', ['status' => $status, 'title' => $title, 'details' => $details, 'help' => $help, 'statusColors' => $this->statusColors()]));
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

    //----------------------------------------------------------------------
    // Progress bars
    //----------------------------------------------------------------------

    /**
     * @throws Exception
     */
    public function createProgressBar($total, $withColors = true)
    {
        if ($withColors) {
            $this->progressInstance = new ProgressBar('framed-color');
        } else {
            $this->progressInstance = new ProgressBar('framed');
        }
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

    /**
     * @throws Exception
     */
    public function showProgress(): void
    {
        if (empty($this->progressInstance)) {
            throw new Exception('No progress bar instance found');
        }
        $this->progressInstance->show();
    }

    /**
     * @throws Exception
     */
    public function progressAdvance($by = 1): void
    {
        if (empty($this->progressInstance)) {
            throw new Exception('No progress bar instance found');
        }
        $this->progressInstance->increment($by);
    }

    /**
     * @throws Exception
     */
    public function progressFinish(): void
    {
        if (empty($this->progressInstance)) {
            throw new Exception('No progress bar instance found');
        }
        $this->progressInstance->finish();
    }

    //----------------------------------------------------------------------
    // Loaders
    //----------------------------------------------------------------------

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
            throw new Exception('No loader instance found');
        }
        $async = $this->asyncLoader;
        $async->withTask($task);
        $async->withFailOver(view($this->async['view'], [
            'state' => 'failover',
            'message' => $title,
            'i' => 1,
        ]));
        $result = $async->run(function () use ($async, $title) {
            $async->render(view($this->async['view'], [
                'state' => 'running',
                'type' => $this->async['type'],
                'colors' => $this->async['colors'],
                'message' => $title,
                'i' => $async->getInterval(),
            ]));
        }, $this->async['us']);
        if (empty($result)) {
            $async->render(view($this->async['view'], [
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
        $async->render(view($this->async['view'], [
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
