<?php

namespace OmniTerm;

use Illuminate\Console\Command;
use OmniTerm\Helpers\OmniHelpers;

trait OmniTerm
{
    public OmniHelpers $omni;

    public function __construct()
    {
        Command::__construct();
        $this->omni = new OmniHelpers;
    }
}
