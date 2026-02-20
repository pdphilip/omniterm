<?php

namespace OmniTerm\Samples;

use Illuminate\Console\Command;
use OmniTerm\HasOmniTerm;

/**
 * Sample: Data Tables
 *
 * Demonstrates formatted key-value tables with status indicators.
 *
 * Run: php artisan omniterm:data-tables
 */
class DataTablesCommand extends Command
{
    use HasOmniTerm;

    protected $signature = 'omniterm:data-tables';

    protected $description = 'OmniTerm Sample: Data Tables';

    public function handle(): int
    {
        $this->omni->titleBar('Data Tables', 'teal');
        $this->omni->newLine();

        // Header row
        $this->omni->tableHeader('Setting', 'Value', 'Notes');

        // Basic rows with different value styling
        $this->omni->tableRow('App Name', 'MyApp', 'Production');
        $this->omni->tableRow('Environment', 'production', null, 'text-emerald-500');
        $this->omni->tableRow('Debug Mode', 'false', 'Recommended for production');
        $this->omni->tableRow('Timezone', 'UTC');

        $this->omni->newLine();
        $this->omni->hrInfo();
        $this->omni->newLine();

        // Status check table
        $this->omni->tableHeader('Service', 'Status');

        $this->omni->tableRowSuccess('Database Connection');
        $this->omni->tableRowSuccess('Redis Cache', 'Response: 0.5ms');
        $this->omni->tableRowEnabled('Queue Worker', '3 workers active');
        $this->omni->tableRowOk('Storage Permissions');

        $this->omni->tableRowWarning('Memory Usage', '78% (1.56GB / 2GB)');
        $this->omni->tableRowWarning('Disk Space', '85% used');

        $this->omni->tableRowError('SSL Certificate', 'Expires in 5 days');
        $this->omni->tableRowFailed('External API', 'Connection timeout');

        $this->omni->tableRowDisabled('Maintenance Mode');
        $this->omni->tableRowInfo('Last Deploy', '2024-01-15 14:30:00');

        $this->omni->newLine();
        $this->omni->hrWarning();
        $this->omni->newLine();

        // With help text
        $this->omni->tableHeader('Configuration', 'Status');

        $this->omni->tableRowError('API Key', 'Missing', [
            'Set OPENAI_API_KEY in your .env file',
            'Get a key at https://platform.openai.com/api-keys',
        ]);

        $this->omni->tableRowWarning('Cache Driver', 'Using file', [
            'Consider using Redis for better performance',
            'Set CACHE_DRIVER=redis in .env',
        ]);

        $this->omni->tableRowSuccess('Session Driver', 'database', [
            'Sessions stored securely in database',
        ]);

        return Command::SUCCESS;
    }
}
