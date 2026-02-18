<?php

namespace OmniTerm\Samples;

use Illuminate\Console\Command;
use OmniTerm\HasOmniTerm;

/**
 * Sample: Status Messages
 *
 * Demonstrates simple one-line status messages with colored badges.
 *
 * Run: php artisan omniterm:status-messages
 */
class StatusMessagesCommand extends Command
{
    use HasOmniTerm;

    protected $signature = 'omniterm:status-messages';

    protected $description = 'OmniTerm Sample: Status Messages';

    public function handle(): int
    {
        $this->omni->titleBar('Status Messages', 'sky');
        $this->newLine();

        // Simple status messages
        $this->omni->success('Operation completed successfully');
        $this->omni->error('Something went wrong');
        $this->omni->warning('Proceed with caution');
        $this->omni->info('Here is some information');
        $this->omni->disabled('This feature is disabled');

        $this->newLine();
        $this->omni->hrInfo();
        $this->newLine();

        // Detailed status blocks with title, details, and help text
        $this->omni->statusSuccess(
            'Build Complete',
            'All 128 files compiled successfully',
            ['Output directory: /dist', 'Total size: 2.4MB']
        );

        $this->newLine();

        $this->omni->statusError(
            'Connection Failed',
            'Could not establish database connection',
            ['Check DATABASE_URL in .env', 'Ensure MySQL service is running']
        );

        $this->newLine();

        $this->omni->statusWarning(
            'Deprecation Notice',
            'Method User::getFullName() is deprecated',
            ['Use User::fullName() instead', 'Will be removed in v3.0']
        );

        $this->newLine();

        $this->omni->statusInfo(
            'Queue Status',
            '42 jobs pending, 3 workers active'
        );

        $this->newLine();

        $this->omni->statusDisabled(
            'Maintenance Mode',
            'Application is in maintenance mode',
            ['Run "php artisan up" to exit maintenance mode']
        );

        return Command::SUCCESS;
    }
}
