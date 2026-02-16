<?php

namespace OmniTerm\Samples;

use Illuminate\Console\Command;
use OmniTerm\OmniTerm;

/**
 * Sample: Custom Colors
 *
 * Demonstrates customizing the default status colors.
 *
 * Run: php artisan omniterm:custom-colors
 */
class CustomColorsCommand extends Command
{
    use OmniTerm;

    protected $signature = 'omniterm:custom-colors';

    protected $description = 'OmniTerm Sample: Custom Color Schemes';

    public function handle(): int
    {
        $this->initOmni();

        $this->omni->roundedBox('Custom Color Schemes', 'text-cyan-500');
        $this->newLine();

        // Default colors
        $this->omni->info('Default Colors:');
        $this->newLine();

        $this->omni->success('Default success (emerald)');
        $this->omni->error('Default error (rose)');
        $this->omni->warning('Default warning (amber)');
        $this->omni->info('Default info (sky)');
        $this->omni->disabled('Default disabled (zinc)');

        $this->newLine();
        $this->omni->hrInfo();
        $this->newLine();

        // Custom color scheme 1: Purple theme
        $this->omni->info('Purple Theme:');
        $this->newLine();

        $this->omni->successColor = 'violet';
        $this->omni->errorColor = 'fuchsia';
        $this->omni->warningColor = 'purple';
        $this->omni->infoColor = 'indigo';
        $this->omni->disabledColor = 'slate';

        $this->omni->success('Purple success (violet)');
        $this->omni->error('Purple error (fuchsia)');
        $this->omni->warning('Purple warning (purple)');
        $this->omni->info('Purple info (indigo)');
        $this->omni->disabled('Purple disabled (slate)');

        $this->newLine();
        $this->omni->hrInfo();
        $this->newLine();

        // Custom color scheme 2: Ocean theme
        $this->omni->info('Ocean Theme:');
        $this->newLine();

        $this->omni->successColor = 'teal';
        $this->omni->errorColor = 'red';
        $this->omni->warningColor = 'orange';
        $this->omni->infoColor = 'cyan';
        $this->omni->disabledColor = 'gray';

        $this->omni->success('Ocean success (teal)');
        $this->omni->error('Ocean error (red)');
        $this->omni->warning('Ocean warning (orange)');
        $this->omni->info('Ocean info (cyan)');
        $this->omni->disabled('Ocean disabled (gray)');

        $this->newLine();
        $this->omni->hrInfo();
        $this->newLine();

        // Status rows also use these colors
        $this->omni->info('Status rows inherit custom colors:');
        $this->newLine();

        $this->omni->header('Check', 'Status');
        $this->omni->rowSuccess('Teal Success');
        $this->omni->rowError('Red Error');
        $this->omni->rowWarning('Orange Warning');
        $this->omni->rowInfo('Cyan Info');
        $this->omni->rowDisabled('Gray Disabled');

        $this->newLine();

        // Reset to defaults
        $this->omni->successColor = 'emerald';
        $this->omni->errorColor = 'rose';
        $this->omni->warningColor = 'amber';
        $this->omni->infoColor = 'sky';
        $this->omni->disabledColor = 'zinc';

        $this->omni->success('Colors reset to defaults');

        return Command::SUCCESS;
    }
}
