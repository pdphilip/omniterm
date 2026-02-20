<?php

namespace OmniTerm\Samples;

use Illuminate\Console\Command;
use OmniTerm\HasOmniTerm;

class TitleBarsCommand extends Command
{
    use HasOmniTerm;

    protected $signature = 'omniterm:title-bars';

    protected $description = 'OmniTerm Sample: Title Bar Colors';

    public function handle(): int
    {
        $colors = [
            'slate', 'gray', 'zinc', 'neutral', 'stone',
            'red', 'orange', 'amber', 'yellow', 'lime',
            'green', 'emerald', 'teal', 'cyan', 'sky',
            'blue', 'indigo', 'violet', 'purple', 'fuchsia',
            'pink', 'rose',
        ];

        foreach ($colors as $color) {
            $this->omni->titleBar($color, $color);
            $this->omni->newLine();
        }

        return self::SUCCESS;
    }
}
