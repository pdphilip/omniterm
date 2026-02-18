<?php

namespace OmniTerm\Samples;

use Illuminate\Console\Command;
use OmniTerm\OmniTerm;

class FullDemoCommand extends Command
{
    use OmniTerm;

    protected $signature = 'omniterm:full-demo';

    protected $description = 'OmniTerm: One of every feature';

    public function handle(): int
    {
        // ── Title Bar ──────────────────────────────────────────────────────
        $this->omni->titleBar('OmniTerm Feature Check', 'cyan');
        $this->newLine();

        // ── Inline HTML ────────────────────────────────────────────────────
        $this->omni->render('<div class="px-2"><span class="text-sky-400 font-bold">render()</span> <span class="text-gray-400">— raw HTML to ANSI</span></div>');

        $parsed = $this->omni->parse('<span class="text-emerald-400">parse()</span> returns a string');
        $this->omni->render('<div class="px-2"><span>'.$parsed.'</span></div>');

        $this->newLine();

        // ── Feedback Messages ──────────────────────────────────────────────
        $this->omni->success('Success message');
        $this->omni->info('Info message');
        $this->omni->warning('Warning message');
        $this->omni->error('Error message');
        $this->omni->disabled('Disabled message');

        $this->newLine();

        // ── Elements ───────────────────────────────────────────────────────
        $this->omni->box('Square Box', 'text-sky-500', 'text-sky-300');
        $this->omni->roundedBox('Rounded Box', 'text-emerald-500', 'text-emerald-300');

        $this->newLine();

        $this->omni->hr();
        $this->omni->hrSuccess();
        $this->omni->hrInfo();
        $this->omni->hrWarning();
        $this->omni->hrError();
        $this->omni->hrDisabled();

        $this->newLine();

        // ── Data Tables ────────────────────────────────────────────────────
        $this->omni->tableHeader('Check', 'Status', 'Details');
        $this->omni->tableRow('Plain Row', 'value', 'details');
        $this->omni->tableRowSuccess('PHP 8.3', '8.3.15');
        $this->omni->tableRowInfo('Laravel', '12.x');
        $this->omni->tableRowWarning('NPM', '10.2 (10.4+ recommended)');
        $this->omni->tableRowError('Redis', 'Connection refused');
        $this->omni->tableRowEnabled('Debug Mode');
        $this->omni->tableRowDisabled('Maintenance');
        $this->omni->tableRowOk('Health Check');
        $this->omni->tableRowFailed('Queue Worker', 'Not running');

        $this->newLine();

        // ── Status Blocks ──────────────────────────────────────────────────
        $this->omni->statusSuccess('Build Passed', 'All 42 tests green', ['Duration: 0.31s']);
        $this->omni->statusInfo('Update Available', 'v5.4.0 released', ['Run: composer update']);
        $this->omni->statusWarning('Cache Stale', 'Last refresh 2 hours ago', ['Run: php artisan cache:clear']);
        $this->omni->statusError('Deploy Failed', 'Migration error on users table', ['Check: storage/logs/laravel.log']);
        $this->omni->statusDisabled('Cron Inactive', 'Scheduler not running', ['Run: php artisan schedule:work']);

        $this->newLine();

        // ── Progress Bars ──────────────────────────────────────────────────
        $this->omni->info('Progress bars');
        $this->newLine();

        $total = 50;
        $sleep = 50_000;

        // Framed with colors
        $this->omni->divider('Framed with colors');
        $this->omni->createProgressBar($total, withColors: true);
        $this->omni->showProgress();
        for ($i = 0; $i < $total; $i++) {
            usleep($sleep);
            $this->omni->progressAdvance();
        }
        $this->omni->progressFinish();
        $this->newLine();

        // Framed without colors
        $this->omni->divider('Framed without colors');
        $this->omni->createProgressBar($total, withColors: false);
        $this->omni->showProgress();
        for ($i = 0; $i < $total; $i++) {
            usleep($sleep);
            $this->omni->progressAdvance();
        }
        $this->omni->progressFinish();
        $this->newLine();
        // Simple with colors
        $this->omni->divider('Simple with colors');
        $this->omni->createSimpleProgressBar($total, withColors: true);
        $this->omni->showProgress();
        for ($i = 0; $i < $total; $i++) {
            usleep($sleep);
            $this->omni->progressAdvance();
        }
        $this->omni->progressFinish();
        $this->newLine();

        // Simple without colors
        $this->omni->divider('Simple without colors');
        $this->omni->createSimpleProgressBar($total, withColors: false);
        $this->omni->showProgress();
        for ($i = 0; $i < $total; $i++) {
            usleep($sleep);
            $this->omni->progressAdvance();
        }
        $this->omni->progressFinish();
        $this->newLine();

        // Gradient
        $this->omni->divider('Gradient');
        $this->omni->createGradientProgressBar($total);
        $this->omni->showProgress();
        for ($i = 0; $i < $total; $i++) {
            usleep($sleep);
            $this->omni->progressAdvance();
        }
        $this->omni->progressFinish();
        $this->newLine();

        // Gradient framed
        $this->omni->divider('Gradient Framed');
        $this->omni->createGradientFramedProgressBar($total);
        $this->omni->showProgress();
        for ($i = 0; $i < $total; $i++) {
            usleep($sleep);
            $this->omni->progressAdvance();
        }
        $this->omni->progressFinish();
        $this->newLine();

        // ── Spinner Tasks (newLoader + runTask) ────────────────────────────
        $this->omni->info('Spinner tasks');
        $this->newLine();

        $this->omni->newLoader('sand', ['text-amber-500', 'text-emerald-500']);
        $this->omni->runTask('Task with success', function () {
            usleep(1_000_000);

            return ['state' => 'success', 'message' => 'Completed', 'details' => '128 records'];
        });

        $this->omni->newLoader('dots', ['text-rose-500', 'text-pink-500']);
        $this->omni->runTask('Task with warning', function () {
            usleep(1_000_000);

            return ['state' => 'warning', 'message' => 'Partial completion', 'details' => '3 skipped'];
        });

        $this->omni->newLoader('material', ['text-sky-500', 'text-cyan-500']);
        $this->omni->runTask('Task with error', function () {
            usleep(1_000_000);

            return ['state' => 'error', 'message' => 'Connection timeout', 'details' => 'After 30s'];
        });

        $this->newLine();

        // ── LiveTask via task() ────────────────────────────────────────────
        $this->omni->info('LiveTask via task()');
        $this->newLine();

        $this->omni->task('Processing batch job', function () {
            usleep(1_500_000);

            return ['state' => 'success', 'message' => 'Batch complete', 'details' => '500 records'];
        }, 'dotsCircle', ['text-indigo-500', 'text-violet-500']);

        $this->newLine();

        // ── Manual LiveTask with rows ──────────────────────────────────────
        $this->omni->info('Manual LiveTask with rows');
        $this->newLine();

        $liveTask = $this->omni->liveTask('Syncing data', 'sand', ['text-sky-500', 'text-emerald-500']);
        $liveTask->row('Users', 0, 'text-sky-500');
        $liveTask->row('Orders', 0, 'text-emerald-500');
        $liveTask->row('Products', 0, 'text-amber-500');

        $liveTask->run(function () use ($liveTask) {
            for ($i = 1; $i <= 5; $i++) {
                usleep(300_000);
                $liveTask->increment('Users', rand(10, 50));
                $liveTask->increment('Orders', rand(5, 20));
                $liveTask->increment('Products', rand(2, 10));
            }

            return ['state' => 'success', 'message' => 'Sync complete'];
        });

        $liveTask->finish('Sync complete');

        $this->newLine();
        $this->omni->success('Feature check complete');

        return Command::SUCCESS;
    }
}
