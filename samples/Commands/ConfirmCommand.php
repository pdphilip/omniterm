<?php

namespace OmniTerm\Samples;

use Illuminate\Console\Command;
use OmniTerm\HasOmniTerm;

/**
 * Sample: Confirm Tasks
 *
 * Demonstrates the confirm dialog with synchronous callback execution.
 *
 * Run: php artisan omniterm:confirm
 */
class ConfirmCommand extends Command
{
    use HasOmniTerm;

    protected $signature = 'omniterm:confirm';

    protected $description = 'OmniTerm Sample: Confirm Dialog';

    public function handle(): int
    {
        $this->omni->titleBar('Confirm Dialog', 'sky');
        $this->omni->newLine();

        // Basic confirm - success
        $this->omni->divider('Basic confirm (press y to proceed):');
        $this->omni->newLine();

        $result = $this->omni->confirm('Run database migrations?', function () {
            usleep(2_000_000);
            $this->omni->info('doing X');
            // etc
        });

        $this->omni->newLine();

        if ($result) {
            $this->omni->statusSuccess('Confirmed', 'Migrations ran successfully');
        } else {
            $this->omni->disabled('Skipped');
        }

        $this->omni->newLine();
        $this->omni->newLine();

        // Warning outcome
        $this->omni->divider('Confirm with warning outcome:');
        $this->omni->newLine();

        $this->omni->confirm('Rebuild search index?', function () {
            $this->omni->info('Rebuilding index...');
            usleep(2_500_000);
            $this->omni->warning('Index rebuilt with 12 skipped records');
        });

        $this->omni->newLine();
        $this->omni->newLine();

        // Error outcome
        $this->omni->divider('Confirm with error outcome:');
        $this->omni->newLine();

        $this->omni->confirm('Sync to remote cluster?', function () {
            $this->omni->info('Connecting...');
            usleep(1_500_000);
            $this->omni->error('Connection refused on port 9200');
        });

        $this->omni->newLine();
        $this->omni->newLine();

        // Custom colors
        $this->omni->divider('Confirm with custom colors (sky/amber):');
        $this->omni->newLine();

        $this->omni->confirm('Deploy to staging?', function () {
            $this->omni->info('Deploying v2.4.1...');
            usleep(2_000_000);
            $this->omni->success('Deployed to staging');
        }, confirmColor: 'sky', declineColor: 'amber');

        $this->omni->newLine();
        $this->omni->success('Confirm demo complete!');

        return Command::SUCCESS;
    }
}
