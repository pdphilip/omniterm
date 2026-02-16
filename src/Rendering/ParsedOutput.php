<?php

declare(strict_types=1);

namespace OmniTerm\Rendering;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ParsedOutput
{
    public function __construct(
        protected string $ansi,
        protected ?OutputInterface $output = null,
    ) {}

    public function toString(): string
    {
        return $this->ansi;
    }

    public function render(int $options = OutputInterface::OUTPUT_NORMAL): void
    {
        $output = $this->output ?? new ConsoleOutput;
        $output->writeln($this->ansi, $options);
    }
}
