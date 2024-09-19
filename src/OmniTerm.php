<?php

namespace OmniTerm;

use OmniTerm\Helpers\OmniHelpers;

trait OmniTerm
{
    public OmniHelpers $omni;

    public function initOmni(): void
    {
        $this->omni = new OmniHelpers;
    }
}
