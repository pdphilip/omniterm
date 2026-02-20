<?php

namespace OmniTerm\Samples;

use Illuminate\Console\Command;
use OmniTerm\HasOmniTerm;

/**
 * Sample: Custom Colors
 *
 * Demonstrates customizing the default status colors.
 *
 * Run: php artisan omniterm:custom-colors
 */
class CustomColorsCommand extends Command
{
    use HasOmniTerm;

    protected $signature = 'omniterm:custom-colors';

    protected $description = 'OmniTerm Sample: Custom Color Schemes';

    public function handle(): int
    {
        $this->omni->titleBar('Custom Colors', 'pink');
        $this->omni->newLine();

        // Default colors
        $this->omni->info('Default Colors:');
        $this->omni->newLine();

        $this->omni->success('Default success (emerald)');
        $this->omni->error('Default error (rose)');
        $this->omni->warning('Default warning (amber)');
        $this->omni->info('Default info (sky)');
        $this->omni->disabled('Default disabled (zinc)');

        $this->omni->newLine();
        $this->omni->hrInfo();
        $this->omni->newLine();

        // Custom color scheme 1: Purple theme
        $this->omni->info('Purple Theme:');
        $this->omni->newLine();

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

        $this->omni->newLine();
        $this->omni->hrInfo();
        $this->omni->newLine();

        // Custom color scheme 2: Ocean theme
        $this->omni->info('Ocean Theme:');
        $this->omni->newLine();

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

        $this->omni->newLine();
        $this->omni->hrInfo();
        $this->omni->newLine();

        // Status rows also use these colors
        $this->omni->info('Status rows inherit custom colors:');
        $this->omni->newLine();

        $this->omni->tableHeader('Check', 'Status');
        $this->omni->tableRowSuccess('Teal Success');
        $this->omni->tableRowError('Red Error');
        $this->omni->tableRowWarning('Orange Warning');
        $this->omni->tableRowInfo('Cyan Info');
        $this->omni->tableRowDisabled('Gray Disabled');

        $this->omni->newLine();

        // Direct feedback() bypasses color properties entirely
        $this->omni->info('Direct feedback():');
        $this->omni->newLine();

        $this->omni->feedback('Uses the color you pass directly', 'CUSTOM', 'pink');
        $this->omni->feedback('Unaffected by color properties', 'DEPLOY', 'violet');

        $this->omni->newLine();
        $this->omni->hrInfo();
        $this->omni->newLine();

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
