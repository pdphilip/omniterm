<?php

namespace App\Console\Commands\OmniTermSamples;

use Illuminate\Console\Command;
use OmniTerm\OmniTerm;

/**
 * Sample: Interactive Prompts
 *
 * Demonstrates interactive user input with autocomplete.
 *
 * Run: php artisan omniterm:interactive
 */
class InteractiveCommand extends Command
{
    use OmniTerm;

    protected $signature = 'omniterm:interactive';

    protected $description = 'OmniTerm Sample: Interactive Prompts';

    public function handle(): int
    {
        $this->initOmni();

        $this->omni->roundedBox('Interactive Prompts', 'text-cyan-500');
        $this->newLine();

        // Simple question
        $name = $this->omni->ask('What is your name?');
        $this->newLine();

        $this->omni->row('You entered', $name ?: '(empty)');
        $this->newLine();

        // Question with autocomplete options
        $framework = $this->omni->ask('What is your favorite PHP framework?', [
            'Laravel',
            'Symfony',
            'CodeIgniter',
            'CakePHP',
            'Yii',
            'Laminas',
        ]);
        $this->newLine();

        $this->omni->row('You chose', $framework ?: '(empty)');
        $this->newLine();

        // Another example with colors
        $color = $this->omni->ask('Pick a color:', [
            'red',
            'green',
            'blue',
            'yellow',
            'purple',
            'orange',
        ]);
        $this->newLine();

        if ($color) {
            $this->omni->row('Your color', $color, null, "text-{$color}-500");
        }

        $this->newLine();
        $this->omni->hrInfo();
        $this->newLine();

        // Summary
        $this->omni->header('Question', 'Your Answer');
        $this->omni->row('Name', $name ?: '(not provided)');
        $this->omni->row('Framework', $framework ?: '(not provided)');
        $this->omni->row('Color', $color ?: '(not provided)');

        $this->newLine();
        $this->omni->success('Interactive demo complete!');

        return Command::SUCCESS;
    }
}
