<?php

namespace OmniTerm;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait HasOmniTerm
{
    public OmniTerm $omni;

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->omni = new OmniTerm;
        parent::initialize($input, $output);
    }
}
