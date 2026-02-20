<?php

namespace OmniTerm\Samples;

use Illuminate\Console\Command;
use OmniTerm\HasOmniTerm;

/**
 * Sample: Visual Elements
 *
 * Demonstrates boxes and horizontal rules.
 *
 * Run: php artisan omniterm:visual-elements
 */
class VisualElementsCommand extends Command
{
    use HasOmniTerm;

    protected $signature = 'omniterm:visual-elements';

    protected $description = 'OmniTerm Sample: Visual Elements (Boxes & Lines)';

    public function handle(): int
    {
        $this->omni->titleBar('Visual Elements', 'violet');
        $this->omni->newLine();

        // Rounded boxes (default style)
        $this->omni->roundedBox('Rounded Box - Default');

        $this->omni->newLine();

        $this->omni->roundedBox('Rounded Box - Styled', 'text-cyan-500', 'text-cyan-300');

        $this->omni->newLine();

        $this->omni->roundedBox('Success Box', 'text-emerald-500', 'text-emerald-300');

        $this->omni->newLine();

        $this->omni->roundedBox('Warning Box', 'text-amber-500', 'text-amber-300');

        $this->omni->newLine();

        // Square boxes
        $this->omni->box('Square Box - Default');

        $this->omni->newLine();

        $this->omni->box('Square Box - Styled', 'text-rose-500', 'text-rose-300');

        $this->omni->newLine();

        // Horizontal rules
        $this->omni->info('Horizontal Rules:');
        $this->omni->newLine();

        $this->line('  Default (gray):');
        $this->omni->hr();

        $this->omni->newLine();
        $this->line('  Custom color:');
        $this->omni->hr('text-purple-500');

        $this->omni->newLine();
        $this->line('  Success (green):');
        $this->omni->hrSuccess();

        $this->omni->newLine();
        $this->line('  Error (red):');
        $this->omni->hrError();

        $this->omni->newLine();
        $this->line('  Warning (amber):');
        $this->omni->hrWarning();

        $this->omni->newLine();
        $this->line('  Info (blue):');
        $this->omni->hrInfo();

        $this->omni->newLine();
        $this->line('  Disabled (gray):');
        $this->omni->hrDisabled();

        $this->omni->newLine();

        // Combined example
        $this->omni->roundedBox('Section Title', 'text-sky-500', 'text-white');
        $this->omni->tableRow('Item 1', 'Value 1');
        $this->omni->tableRow('Item 2', 'Value 2');
        $this->omni->tableRow('Item 3', 'Value 3');
        $this->omni->hrInfo();

        return Command::SUCCESS;
    }
}
