<?php

namespace OmniTerm\Samples;

use Illuminate\Console\Command;
use OmniTerm\Async\Spinner;
use OmniTerm\HasOmniTerm;

/**
 * Sample: Spinners
 *
 * Demonstrates all available spinner/loader animations.
 *
 * Run: php artisan omniterm:spinners
 */
class SpinnersCommand extends Command
{
    use HasOmniTerm;

    protected $signature = 'omniterm:spinners {--type= : Show only a specific spinner type}';

    protected $description = 'OmniTerm Sample: Spinner Animations';

    public function handle(): int
    {
        $this->omni->titleBar('Spinner Animations', 'amber');
        $this->omni->newLine();

        $spinners = [
            Spinner::Dots,
            Spinner::Dots2,
            Spinner::Dots3,
            Spinner::DotsCircle,
            Spinner::Sand,
            Spinner::Clock,
            Spinner::Material,
            Spinner::Pong,
            Spinner::Progress,
            Spinner::ProgressLoader,
        ];

        $selectedType = $this->option('type');

        if ($selectedType) {
            $spinner = Spinner::tryFrom($selectedType);
            if (! $spinner) {
                $this->omni->error("Unknown spinner type: {$selectedType}");
                $this->line('Available types: '.implode(', ', array_map(fn (Spinner $s) => $s->value, $spinners)));

                return Command::FAILURE;
            }
            $spinners = [$spinner];
        }

        $this->omni->roundedBox('Spinner Animations', 'text-cyan-500');
        $this->omni->newLine();

        $this->omni->info('Each spinner will run for ~2 seconds');
        $this->omni->newLine();

        foreach ($spinners as $spinner) {
            $this->omni->render("<div><span class='text-yellow-400'>{$spinner->value}</span>  - {$spinner->label()}</div>");
        }

        $this->omni->newLine();
        $this->omni->hrInfo();
        $this->omni->newLine();

        foreach ($spinners as $spinner) {
            $colors = $this->getColorsForSpinner($spinner);

            $this->omni->newLoader($spinner, $colors, 80000);

            $this->omni->runTask("Spinner: {$spinner->value}", function () {
                usleep(2000000);

                return [
                    'state' => 'success',
                    'message' => 'Animation complete',
                ];
            });

            usleep(300000);
        }

        $this->omni->newLine();
        $this->omni->success('All spinner demos complete!');
        $this->omni->newLine();

        $this->omni->render('<div class="text-gray-400">Tip: Run with --type=sand to demo a specific spinner</div>');

        return Command::SUCCESS;
    }

    private function getColorsForSpinner(Spinner $spinner): array
    {
        return match ($spinner) {
            Spinner::Dots => ['text-sky-500', 'text-cyan-500'],
            Spinner::Dots2 => ['text-violet-500', 'text-purple-500'],
            Spinner::Dots3 => ['text-pink-500', 'text-rose-500'],
            Spinner::DotsCircle => ['text-emerald-500', 'text-teal-500'],
            Spinner::Sand => ['text-amber-500', 'text-yellow-500'],
            Spinner::Clock => ['text-orange-500'],
            Spinner::Material => ['text-sky-500', 'text-emerald-500', 'text-amber-500'],
            Spinner::Pong => ['text-lime-500', 'text-green-500'],
            Spinner::Progress => ['text-indigo-500', 'text-blue-500'],
            Spinner::ProgressLoader => ['text-rose-500', 'text-pink-500', 'text-fuchsia-500'],
            default => ['text-amber-500', 'text-emerald-500'],
        };
    }
}
