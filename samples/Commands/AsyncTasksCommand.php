<?php

namespace OmniTerm\Samples;

use Illuminate\Console\Command;
use OmniTerm\Async\Spinner;
use OmniTerm\HasOmniTerm;

/**
 * Sample: Async Tasks
 *
 * Demonstrates running async tasks with visual feedback.
 *
 * Run: php artisan omniterm:async-tasks
 */
class AsyncTasksCommand extends Command
{
    use HasOmniTerm;

    protected $signature = 'omniterm:async-tasks';

    protected $description = 'OmniTerm Sample: Async Task Execution';

    public function handle(): int
    {
        $this->omni->titleBar('Async Tasks', 'rose');
        $this->omni->newLine();

        $this->omni->info('Running tasks with different outcomes...');
        $this->omni->newLine();

        // Task 1: Success
        $this->omni->newLoader(Spinner::Sand, ['text-amber-500', 'text-emerald-500']);

        $result = $this->omni->runTask('Connecting to database', function () {
            usleep(1500000); // 1.5 seconds

            return [
                'state' => 'success',
                'message' => 'Database connected',
                'details' => 'MySQL 8.0 on localhost:3306',
            ];
        });

        usleep(300000);

        // Task 2: Success with custom message
        $this->omni->newLoader(Spinner::Dots, ['text-sky-500', 'text-cyan-500']);

        $result = $this->omni->runTask('Fetching user data', function () {
            usleep(2000000); // 2 seconds

            return [
                'state' => 'success',
                'message' => 'Loaded 1,234 users',
            ];
        });

        usleep(300000);

        // Task 3: Warning
        $this->omni->newLoader(Spinner::DotsCircle, ['text-amber-500', 'text-orange-500']);

        $result = $this->omni->runTask('Checking cache status', function () {
            usleep(1000000); // 1 second

            return [
                'state' => 'warning',
                'message' => 'Cache is stale',
                'details' => 'Last refresh: 2 hours ago',
            ];
        });

        usleep(300000);

        // Task 4: Error
        $this->omni->newLoader(Spinner::Material, ['text-rose-500', 'text-red-500']);

        $result = $this->omni->runTask('Connecting to external API', function () {
            usleep(2500000); // 2.5 seconds

            return [
                'state' => 'error',
                'message' => 'API connection failed',
                'details' => 'Timeout after 30s',
            ];
        });

        $result = $this->omni->runTask('Connecting to external API', function () {
            usleep(2500000); // 2.5 seconds

            return [
                'state' => 'error',
                'message' => 'API connection failed',
                'details' => 'Timeout after 30s',
            ];
        });

        usleep(300000);

        // Task 5: Long running task with progress spinner
        $this->omni->newLoader(Spinner::ProgressLoader, ['text-indigo-500', 'text-violet-500']);

        $result = $this->omni->runTask('Processing batch job', function () {
            usleep(3000000); // 3 seconds

            return [
                'state' => 'success',
                'message' => 'Batch complete',
                'details' => '500 records processed',
            ];
        });

        $this->omni->newLine();
        $this->omni->hrInfo();
        $this->omni->newLine();

        // Using result data
        $this->omni->info('Task results can be used in your code:');
        $this->omni->newLine();

        $this->omni->newLoader(Spinner::Sand);

        $result = $this->omni->runTask('Computing statistics', function () {
            usleep(1500000);

            return [
                'state' => 'success',
                'message' => 'Statistics computed',
                'data' => [
                    'total' => 1000,
                    'average' => 42.5,
                    'max' => 100,
                ],
            ];
        });

        $this->omni->newLine();

        if ($result && ! empty($result->data)) {
            $this->omni->tableHeader('Metric', 'Value');
            $this->omni->tableRow('Total', (string) $result->data['total']);
            $this->omni->tableRow('Average', (string) $result->data['average']);
            $this->omni->tableRow('Maximum', (string) $result->data['max']);
        }

        $this->omni->newLine();
        $this->omni->success('Async tasks demo complete!');

        return Command::SUCCESS;
    }
}
