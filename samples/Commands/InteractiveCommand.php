<?php

namespace OmniTerm\Samples;

use Illuminate\Console\Command;
use OmniTerm\HasOmniTerm;

/**
 * Sample: Interactive Prompts
 *
 * Demonstrates interactive user input with autocomplete.
 *
 * Run: php artisan omniterm:interactive
 */
class InteractiveCommand extends Command
{
    use HasOmniTerm;

    protected $signature = 'omniterm:interactive';

    protected $description = 'OmniTerm Sample: Interactive Prompts';

    public function handle(): int
    {
        $this->omni->titleBar('Interactive Prompts', 'indigo');
        $this->omni->newLine();

        // Simple question
        $name = $this->omni->ask('What is your name?');
        $this->omni->newLine();

        $this->omni->tableRow('You entered', $name ?: '(empty)');
        $this->omni->newLine();

        // Question with autocomplete options
        $framework = $this->omni->ask('What is your favorite PHP framework?', [
            'Laravel',
            'Symfony',
            'CodeIgniter',
            'CakePHP',
            'Yii',
            'Laminas',
        ]);
        $this->omni->newLine();

        $this->omni->tableRow('You chose', $framework ?: '(empty)');
        $this->omni->newLine();

        // Another example with colors
        $color = $this->omni->ask('Pick a color:', [
            'red',
            'green',
            'blue',
            'yellow',
            'purple',
            'orange',
        ]);
        $this->omni->newLine();

        if ($color) {
            $this->omni->tableRow('Your color', $color, null, "text-{$color}-500");
        }

        $this->omni->newLine();
        $this->omni->hrInfo();
        $this->omni->newLine();

        // Summary
        $this->omni->tableHeader('Question', 'Your Answer');
        $this->omni->tableRow('Name', $name ?: '(not provided)');
        $this->omni->tableRow('Framework', $framework ?: '(not provided)');
        $this->omni->tableRow('Color', $color ?: '(not provided)');

        $this->omni->newLine();
        $this->omni->success('Interactive demo complete!');

        return Command::SUCCESS;
    }
}
