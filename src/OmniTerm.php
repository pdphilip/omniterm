<?php

namespace OmniTerm;

use OmniTerm\Helpers\OmniHelpers;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait OmniTerm
{
    public OmniHelpers $omni;

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->omni = new OmniHelpers;
        parent::initialize($input, $output);
    }
}
