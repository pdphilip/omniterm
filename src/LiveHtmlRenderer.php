<?php

declare(strict_types=1);

namespace OmniTerm;

use OmniTerm\Rendering\Ansi;
use OmniTerm\Rendering\Renderer;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Terminal;

/**
 * @internal
 */
final class LiveHtmlRenderer
{
    public ?string $currentMessage = null;

    protected int $options = OutputInterface::OUTPUT_NORMAL;

    protected Renderer $renderer;

    private OutputInterface $output;

    private int $width;

    private int $liveRows = 0;

    private int $belowRows = 0;

    public function __construct(?string $html = null, int $options = OutputInterface::OUTPUT_NORMAL)
    {
        $this->output = new ConsoleOutput;
        $this->renderer = new Renderer;
        $this->options = $options;
        $this->width = (new Terminal)->getWidth();
        if ($html !== null) {
            $this->reRender($html);
        }
    }

    public function getScreenWidth(): int
    {
        return $this->width;
    }

    public function newLine(int $count = 1): void
    {
        $this->output->write(str_repeat(\PHP_EOL, $count));
        $this->belowRows += $count;
    }

    public function write(string $html): void
    {
        $message = $this->renderer->parse($html)->toString();
        $this->output->writeln($message, $this->options);
        $this->belowRows += substr_count($message, "\n") + 1;
    }

    public function writeView(string $view, array $data = []): void
    {
        $this->write(view($view, $data)->render());
    }

    public function reRenderView(string $view, array $data = []): void
    {
        $this->reRender(view($view, $data)->render());
    }

    public function reRender(string $html): void
    {
        $message = $this->renderer->parse($html)->toString();
        if ($message === $this->currentMessage) {
            return;
        }

        $this->output->write(Ansi::hideCursor());
        $newRows = substr_count($message, "\n") + 1;

        if ($this->currentMessage !== null && $this->liveRows > 0) {
            $totalUp = $this->liveRows + $this->belowRows;
            $this->output->write(Ansi::moveUp($totalUp));
            $this->clearLines($this->liveRows);
        }

        $this->currentMessage = $message;
        $this->liveRows = $newRows;
        $this->output->writeln($message, $this->options);

        if ($this->belowRows > 0) {
            $this->output->write(Ansi::moveDown($this->belowRows));
        }

        $this->output->write(Ansi::showCursor());
    }

    private function clearLines(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $this->output->write(Ansi::eraseLine());
            if ($i < $count - 1) {
                $this->output->write(Ansi::moveDown());
            }
        }
        if ($count > 1) {
            $this->output->write(Ansi::moveUp($count - 1));
        }
        $this->output->write(Ansi::carriageReturn());
    }
}
