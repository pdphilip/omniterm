<?php

namespace OmniTerm\Helpers;

use Exception;
use OmniTerm\LiveHtmlRenderer;

use function OmniTerm\liveRender;

class ProgressBar
{
    protected LiveHtmlRenderer $instance;

    protected $type;

    protected $interval = 0;

    protected $max = 0;

    protected $screenWidth = 0;

    protected static $types = [
        'framed', 'framed-color', 'simple', 'simple-color',
    ];

    public function __construct($type)
    {
        if (! in_array($type, self::$types)) {
            throw new Exception('Invalid progress bar type');
        }
        $this->type = $type;
        $this->instance = liveRender();
        $this->screenWidth = $this->instance->getScreenWidth();
    }

    public function setTotal($int)
    {
        if ($int < 1) {
            throw new Exception('Invalid total, must be greater than 0');
        }

        $this->max = $int;
    }

    protected function progressView(): string
    {
        if ($this->max < 1) {
            throw new Exception('Total must be set before rendering progress');
        }

        return view('omniterm::progress.'.$this->type, [
            'max' => $this->max,
            'current' => $this->interval,
            'screenWidth' => $this->screenWidth,
        ]);
    }

    /**
     * @throws Exception
     */
    public function show(): void
    {
        $this->instance->reRender($this->progressView());
    }

    /**
     * @throws Exception
     */
    public function increment($by = 1): void
    {
        $this->interval += $by;
        if ($this->interval > $this->max) {
            $this->interval = $this->max;
        }
        $this->show();
    }

    public function finish(): void
    {
        $this->interval = $this->max;
        $this->show();
    }
}
