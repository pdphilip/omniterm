<?php

namespace OmniTerm\Samples;

use Illuminate\Console\Command;
use OmniTerm\Async\Spinner;
use OmniTerm\HasOmniTerm;

class LiveTaskDemoCommand extends Command
{
    use HasOmniTerm;

    protected $signature = 'omniterm:live-task-demo';

    protected $description = 'Demo: LiveTask with feedback rows';

    public function handle(): int
    {
        $this->omni->titleBar('Live Tasks', 'fuchsia');
        $this->omni->newLine();

        // -----------------------------------------------------------------
        // Simple task() — one-liner, no rows
        // -----------------------------------------------------------------
        $this->omni->info('Simple one-shot task:');
        $this->omni->newLine();

        $result = $this->omni->task('Connecting to database', function () {
            usleep(1500000);

            return [
                'state' => 'success',
                'message' => 'Database connected',
            ];
        });

        $this->omni->newLine();

        $result = $this->omni->task('Checking external API', function () {
            usleep(2000000);

            return [
                'state' => 'warning',
                'message' => 'API responded slowly',
            ];
        }, spinner: Spinner::Dots);

        $this->omni->newLine();
        $this->omni->hrInfo();
        $this->omni->newLine();

        // -----------------------------------------------------------------
        // liveTask() — with feedback rows, simulating a bulk operation
        // -----------------------------------------------------------------
        $this->omni->info('Live task with feedback rows:');
        $this->omni->newLine();

        $task = $this->omni->liveTask('Processing records', spinner: Spinner::Dots3)
            ->row('Created', 0, 'text-sky-500')
            ->row('Updated', 0, 'text-emerald-500')
            ->row('Skipped', 0, 'text-amber-500')
            ->row('Failed', 0, 'text-rose-500');

        // Simulate 5 chunked batches
        for ($batch = 0; $batch < 5; $batch++) {
            $result = $task->run(function () {
                usleep(800000);

                return [
                    'created' => rand(10, 50),
                    'updated' => rand(5, 20),
                    'skipped' => rand(0, 5),
                    'failed' => rand(0, 2),
                ];
            });

            $task->increment('Created', $result['created']);
            $task->increment('Updated', $result['updated']);
            $task->increment('Skipped', $result['skipped']);
            $task->increment('Failed', $result['failed']);
        }

        $task->finish('Processing complete');

        $this->omni->newLine();
        $this->omni->success('Done!');

        return self::SUCCESS;
    }
}
