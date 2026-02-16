<?php

declare(strict_types=1);

namespace OmniTerm\Rendering;

use Symfony\Component\Console\Terminal as SymfonyTerminal;

class Terminal
{
    protected SymfonyTerminal $terminal;

    public function __construct()
    {
        $this->terminal = new SymfonyTerminal;
    }

    public function getWidth(): int
    {
        return $this->terminal->getWidth();
    }

    public function getHeight(): int
    {
        return $this->terminal->getHeight();
    }
}
