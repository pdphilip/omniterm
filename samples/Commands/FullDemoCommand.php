<?php

namespace OmniTerm\Samples;

use Illuminate\Console\Command;
use OmniTerm\OmniTerm;

/**
 * Sample: Full Demo
 *
 * A complete example showing multiple OmniTerm features working together.
 * Simulates a deployment process.
 *
 * Run: php artisan omniterm:full-demo
 */
class FullDemoCommand extends Command
{
    use OmniTerm;

    protected $signature = 'omniterm:full-demo';

    protected $description = 'OmniTerm Sample: Full Feature Demo (Simulated Deployment)';

    public function handle(): int
    {
        $this->initOmni();

        // Header
        $this->omni->roundedBox('Application Deployment', 'text-cyan-500', 'text-white');
        $this->newLine();

        // Pre-flight checks
        $this->omni->info('Running pre-flight checks...');
        $this->newLine();

        $this->omni->header('Requirement', 'Status', 'Details');

        $this->omni->rowSuccess('PHP Version', '8.2.15');
        $this->omni->rowSuccess('Composer', '2.7.1');
        $this->omni->rowSuccess('Node.js', '20.11.0');
        $this->omni->rowWarning('NPM', '10.2.4 (10.4+ recommended)');
        $this->omni->rowSuccess('Git', '2.43.0');
        $this->omni->rowEnabled('Production Mode');

        $this->newLine();
        $this->omni->hrSuccess();
        $this->newLine();

        // Pull latest code
        $this->omni->newLoader('dots', ['text-sky-500', 'text-cyan-500']);

        $this->omni->runTask('Pulling latest code from repository', function () {
            usleep(1500000);

            return [
                'state' => 'success',
                'message' => 'Code updated',
                'details' => '15 files changed',
            ];
        });

        // Install dependencies
        $this->omni->newLoader('sand', ['text-amber-500', 'text-yellow-500']);

        $this->omni->runTask('Installing Composer dependencies', function () {
            usleep(2000000);

            return [
                'state' => 'success',
                'message' => 'Dependencies installed',
                'details' => '124 packages',
            ];
        });

        $this->omni->newLoader('dots', ['text-emerald-500', 'text-teal-500']);

        $this->omni->runTask('Installing NPM dependencies', function () {
            usleep(1800000);

            return [
                'state' => 'success',
                'message' => 'NPM packages installed',
                'details' => '847 packages',
            ];
        });

        $this->newLine();

        // Build assets with progress bar
        $this->omni->info('Building frontend assets...');
        $this->newLine();

        $this->omni->createProgressBar(100, withColors: true);
        $this->omni->showProgress();

        // Simulate build steps
        $steps = [
            10 => 'Compiling TypeScript',
            25 => 'Processing SCSS',
            45 => 'Bundling JavaScript',
            65 => 'Optimizing images',
            80 => 'Generating source maps',
            95 => 'Minifying output',
            100 => 'Build complete',
        ];

        $current = 0;
        foreach ($steps as $target => $step) {
            while ($current < $target) {
                usleep(30000);
                $current++;
                $this->omni->progressAdvance();
            }
        }

        $this->omni->progressFinish();
        $this->newLine();

        // Run migrations
        $this->omni->newLoader('material', ['text-indigo-500', 'text-violet-500']);

        $this->omni->runTask('Running database migrations', function () {
            usleep(1200000);

            return [
                'state' => 'success',
                'message' => 'Migrations complete',
                'details' => '3 new migrations',
            ];
        });

        // Clear caches
        $this->omni->newLoader('dotsCircle', ['text-rose-500', 'text-pink-500']);

        $this->omni->runTask('Clearing application caches', function () {
            usleep(800000);

            return [
                'state' => 'success',
                'message' => 'Caches cleared',
            ];
        });

        // Optimize
        $this->omni->newLoader('progress', ['text-emerald-500']);

        $this->omni->runTask('Optimizing application', function () {
            usleep(1000000);

            return [
                'state' => 'success',
                'message' => 'Application optimized',
                'details' => 'Config, routes, views cached',
            ];
        });

        $this->newLine();
        $this->omni->hrSuccess();
        $this->newLine();

        // Final status
        $this->omni->statusSuccess(
            'Deployment Complete',
            'Application successfully deployed to production',
            [
                'Version: v2.4.1',
                'Deployed at: '.date('Y-m-d H:i:s'),
                'Visit: https://myapp.com',
            ]
        );

        $this->newLine();

        // Summary table
        $this->omni->header('Metric', 'Value');
        $this->omni->row('Total Time', '~12 seconds');
        $this->omni->row('Files Changed', '15');
        $this->omni->row('Migrations Run', '3');
        $this->omni->row('Build Size', '2.4 MB');
        $this->omni->rowSuccess('Status', 'Live');

        return Command::SUCCESS;
    }
}
