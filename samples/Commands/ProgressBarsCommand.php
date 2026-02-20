<?php

namespace OmniTerm\Samples;

use Illuminate\Console\Command;
use OmniTerm\HasOmniTerm;

/**
 * Sample: Progress Bars
 *
 * Demonstrates different progress bar styles.
 *
 * Run: php artisan omniterm:progress-bars
 */
class ProgressBarsCommand extends Command
{
    use HasOmniTerm;

    protected $signature = 'omniterm:progress-bars';

    protected $description = 'OmniTerm Sample: Progress Bars';

    public function handle(): int
    {
        $this->omni->titleBar('Progress Bars', 'emerald');
        $this->omni->newLine();

        $total = 50;
        $sleep = 50_000;

        // ── Simple (default sky color) ───────────────────────────────────
        $this->omni->divider('Simple');
        $bar = $this->omni->progressBar($total);
        $bar->start();
        for ($i = 0; $i < $total; $i++) {
            usleep($sleep);
            $bar->advance();
        }
        $bar->finish();
        $this->omni->newLine();

        // ── Simple with color steps ──────────────────────────────────────
        $this->omni->divider('Simple with color steps');
        $bar = $this->omni->progressBar($total)->steps();
        $bar->start();
        for ($i = 0; $i < $total; $i++) {
            usleep($sleep);
            $bar->advance();
        }
        $bar->finish();
        $this->omni->newLine();

        // ── Simple with custom color ─────────────────────────────────────
        $this->omni->divider('Simple with custom color');
        $bar = $this->omni->progressBar($total)->color('indigo');
        $bar->start();
        for ($i = 0; $i < $total; $i++) {
            usleep($sleep);
            $bar->advance();
        }
        $bar->finish();
        $this->omni->newLine();

        // ── Framed with custom color ─────────────────────────────────────
        $this->omni->divider('Framed with custom color');
        $bar = $this->omni->progressBar($total)->framed()->color('indigo');
        $bar->start();
        for ($i = 0; $i < $total; $i++) {
            usleep($sleep);
            $bar->advance();
        }
        $bar->finish();
        $this->omni->newLine();

        // ── Framed with color steps ──────────────────────────────────────
        $this->omni->divider('Framed with color steps');
        $bar = $this->omni->progressBar($total)->framed()->steps();
        $bar->start();
        for ($i = 0; $i < $total; $i++) {
            usleep($sleep);
            $bar->advance();
        }
        $bar->finish();
        $this->omni->newLine();

        // ── Framed with custom step colors ───────────────────────────────
        $this->omni->divider('Framed with custom step colors');
        $bar = $this->omni->progressBar($total)->framed()->steps(['rose', 'amber', 'emerald']);
        $bar->start();
        for ($i = 0; $i < $total; $i++) {
            usleep($sleep);
            $bar->advance();
        }
        $bar->finish();
        $this->omni->newLine();

        // ── Gradient ─────────────────────────────────────────────────────
        $this->omni->divider('Gradient');
        $bar = $this->omni->progressBar($total)->gradient();
        $bar->start();
        for ($i = 0; $i < $total; $i++) {
            usleep($sleep);
            $bar->advance();
        }
        $bar->finish();
        $this->omni->newLine();

        // ── Framed gradient with custom colors ───────────────────────────
        $this->omni->divider('Framed gradient (rose → sky)');
        $bar = $this->omni->progressBar($total)->framed()->gradient('rose', 'sky');
        $bar->start();
        for ($i = 0; $i < $total; $i++) {
            usleep($sleep);
            $bar->advance();
        }
        $bar->finish();
        $this->omni->newLine();

        // ── Variable increment ───────────────────────────────────────────
        $this->omni->divider('Variable increment');
        $bar = $this->omni->progressBar(100)->framed()->steps();
        $bar->start();
        $increments = [5, 10, 15, 20, 10, 5, 15, 10, 5, 5];
        foreach ($increments as $increment) {
            usleep(200_000);
            $bar->advance($increment);
        }
        $bar->finish();
        $this->omni->newLine();

        $this->omni->success('All progress bar demos complete!');

        return Command::SUCCESS;
    }
}
