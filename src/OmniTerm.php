<?php

namespace OmniTerm;

use OmniTerm\Helpers\OmniHelpers;

trait OmniTerm
{
    public OmniHelpers $omni;

    public function __construct()
    {
        parent::__construct();
        $this->omni = new OmniHelpers;
    }
}
