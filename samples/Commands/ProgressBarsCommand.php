<?php

namespace OmniTerm\Samples;

use Illuminate\Console\Command;
use OmniTerm\OmniTerm;

/**
 * Sample: Progress Bars
 *
 * Demonstrates different progress bar styles.
 *
 * Run: php artisan omniterm:progress-bars
 */
class ProgressBarsCommand extends Command
{
    use OmniTerm;

    protected $signature = 'omniterm:progress-bars';

    protected $description = 'OmniTerm Sample: Progress Bars';

    public function handle(): int
    {
        $this->initOmni();

        $this->omni->titleBar('Progress Bars', 'emerald');
        $this->newLine();

        // Framed progress bar with colors
        $this->omni->info('1. Framed Progress Bar (with color gradient)');
        $this->line('   Colors change from red → green as progress increases');
        $this->newLine();

        $this->omni->createProgressBar(50, withColors: true);
        $this->omni->showProgress();

        for ($i = 0; $i < 50; $i++) {
            usleep(30000); // 30ms delay
            $this->omni->progressAdvance();
        }

        $this->omni->progressFinish();
        $this->newLine();

        // Framed progress bar without colors
        $this->omni->info('2. Framed Progress Bar (monochrome)');
        $this->newLine();

        $this->omni->createProgressBar(30, withColors: false);
        $this->omni->showProgress();

        for ($i = 0; $i < 30; $i++) {
            usleep(40000);
            $this->omni->progressAdvance();
        }

        $this->omni->progressFinish();
        $this->newLine();

        // Simple progress bar with colors
        $this->omni->info('3. Simple Progress Bar (with colors)');
        $this->newLine();

        $this->omni->createSimpleProgressBar(40, withColors: true);
        $this->omni->showProgress();

        for ($i = 0; $i < 40; $i++) {
            usleep(35000);
            $this->omni->progressAdvance();
        }

        $this->omni->progressFinish();
        $this->newLine();

        // Simple progress bar without colors
        $this->omni->info('4. Simple Progress Bar (monochrome)');
        $this->newLine();

        $this->omni->createSimpleProgressBar(25, withColors: false);
        $this->omni->showProgress();

        for ($i = 0; $i < 25; $i++) {
            usleep(50000);
            $this->omni->progressAdvance();
        }

        $this->omni->progressFinish();
        $this->newLine();

        // Gradient progress bar
        $this->omni->info('5. Gradient Progress Bar (smooth amber → emerald)');
        $this->newLine();

        $this->omni->createGradientProgressBar(60);
        $this->omni->showProgress();

        for ($i = 0; $i < 60; $i++) {
            usleep(30000);
            $this->omni->progressAdvance();
        }

        $this->omni->progressFinish();
        $this->newLine();

        // Variable increment example
        $this->omni->info('6. Variable Increment (advance by different amounts)');
        $this->newLine();

        $this->omni->createProgressBar(100, withColors: true);
        $this->omni->showProgress();

        $increments = [5, 10, 15, 20, 10, 5, 15, 10, 5, 5];
        foreach ($increments as $increment) {
            usleep(200000); // 200ms delay
            $this->omni->progressAdvance($increment);
        }

        $this->omni->progressFinish();
        $this->newLine();

        $this->omni->success('All progress bar demos complete!');

        return Command::SUCCESS;
    }
}
