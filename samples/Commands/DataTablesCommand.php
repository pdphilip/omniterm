<?php

namespace OmniTerm\Samples;

use Illuminate\Console\Command;
use OmniTerm\OmniTerm;

/**
 * Sample: Data Tables
 *
 * Demonstrates formatted key-value tables with status indicators.
 *
 * Run: php artisan omniterm:data-tables
 */
class DataTablesCommand extends Command
{
    use OmniTerm;

    protected $signature = 'omniterm:data-tables';

    protected $description = 'OmniTerm Sample: Data Tables';

    public function handle(): int
    {
        $this->initOmni();

        $this->omni->roundedBox('System Configuration', 'text-cyan-500');
        $this->newLine();

        // Header row
        $this->omni->header('Setting', 'Value', 'Notes');

        // Basic rows with different value styling
        $this->omni->row('App Name', 'MyApp', 'Production');
        $this->omni->row('Environment', 'production', null, 'text-emerald-500');
        $this->omni->row('Debug Mode', 'false', 'Recommended for production');
        $this->omni->row('Timezone', 'UTC');

        $this->newLine();
        $this->omni->hrInfo();
        $this->newLine();

        // Status check table
        $this->omni->header('Service', 'Status');

        $this->omni->rowSuccess('Database Connection');
        $this->omni->rowSuccess('Redis Cache', 'Response: 0.5ms');
        $this->omni->rowEnabled('Queue Worker', '3 workers active');
        $this->omni->rowOk('Storage Permissions');

        $this->omni->rowWarning('Memory Usage', '78% (1.56GB / 2GB)');
        $this->omni->rowWarning('Disk Space', '85% used');

        $this->omni->rowError('SSL Certificate', 'Expires in 5 days');
        $this->omni->rowFailed('External API', 'Connection timeout');

        $this->omni->rowDisabled('Maintenance Mode');
        $this->omni->rowInfo('Last Deploy', '2024-01-15 14:30:00');

        $this->newLine();
        $this->omni->hrWarning();
        $this->newLine();

        // With help text
        $this->omni->header('Configuration', 'Status');

        $this->omni->rowError('API Key', 'Missing', [
            'Set OPENAI_API_KEY in your .env file',
            'Get a key at https://platform.openai.com/api-keys',
        ]);

        $this->omni->rowWarning('Cache Driver', 'Using file', [
            'Consider using Redis for better performance',
            'Set CACHE_DRIVER=redis in .env',
        ]);

        $this->omni->rowSuccess('Session Driver', 'database', [
            'Sessions stored securely in database',
        ]);

        return Command::SUCCESS;
    }
}
