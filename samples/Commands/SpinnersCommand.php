<?php

namespace OmniTerm\Samples;

use Illuminate\Console\Command;
use OmniTerm\OmniTerm;

/**
 * Sample: Spinners
 *
 * Demonstrates all available spinner/loader animations.
 *
 * Run: php artisan omniterm:spinners
 */
class SpinnersCommand extends Command
{
    use OmniTerm;

    protected $signature = 'omniterm:spinners {--type= : Show only a specific spinner type}';

    protected $description = 'OmniTerm Sample: Spinner Animations';

    public function handle(): int
    {
        $this->omni->titleBar('Spinner Animations', 'amber');
        $this->newLine();

        $spinnerTypes = [
            'dots' => 'Classic braille dots',
            'dots2' => 'Double braille pattern',
            'dots3' => 'Flowing dots',
            'dotsCircle' => 'Circular dot pattern',
            'sand' => 'Filling hourglass effect',
            'clock' => 'Clock face animation',
            'material' => 'Material design loader',
            'pong' => 'Bouncing ball',
            'progress' => 'Progress indicator',
            'progressLoader' => 'Looping progress',
        ];

        $selectedType = $this->option('type');

        if ($selectedType) {
            if (! isset($spinnerTypes[$selectedType])) {
                $this->omni->error("Unknown spinner type: {$selectedType}");
                $this->line('Available types: '.implode(', ', array_keys($spinnerTypes)));

                return Command::FAILURE;
            }
            $spinnerTypes = [$selectedType => $spinnerTypes[$selectedType]];
        }

        $this->omni->roundedBox('Spinner Animations', 'text-cyan-500');
        $this->newLine();

        $this->omni->info('Each spinner will run for ~2 seconds');
        $this->newLine();

        foreach ($spinnerTypes as $type => $description) {
            $this->omni->line("<div><span class='text-yellow-400'>{$type}</span>  - {$description}</div>");
        }

        $this->newLine();
        $this->omni->hrInfo();
        $this->newLine();

        foreach ($spinnerTypes as $type => $description) {
            // Different color schemes for variety
            $colors = $this->getColorsForType($type);

            $this->omni->newLoader($type, $colors, 80000); // 80ms per frame

            $this->omni->runTask("Spinner: {$type}", function () {
                // Simulate work for 2 seconds
                usleep(2000000);

                return [
                    'state' => 'success',
                    'message' => 'Animation complete',
                ];
            });

            usleep(300000); // Brief pause between spinners
        }

        $this->newLine();
        $this->omni->success('All spinner demos complete!');
        $this->newLine();

        $this->omni->line('<div class="text-gray-400">Tip: Run with --type=sand to demo a specific spinner</div>');

        return Command::SUCCESS;
    }

    private function getColorsForType(string $type): array
    {
        return match ($type) {
            'dots' => ['text-sky-500', 'text-cyan-500'],
            'dots2' => ['text-violet-500', 'text-purple-500'],
            'dots3' => ['text-pink-500', 'text-rose-500'],
            'dotsCircle' => ['text-emerald-500', 'text-teal-500'],
            'sand' => ['text-amber-500', 'text-yellow-500'],
            'clock' => ['text-orange-500'],
            'material' => ['text-sky-500', 'text-emerald-500', 'text-amber-500'],
            'pong' => ['text-lime-500', 'text-green-500'],
            'progress' => ['text-indigo-500', 'text-blue-500'],
            'progressLoader' => ['text-rose-500', 'text-pink-500', 'text-fuchsia-500'],
            default => ['text-amber-500', 'text-emerald-500'],
        };
    }
}
