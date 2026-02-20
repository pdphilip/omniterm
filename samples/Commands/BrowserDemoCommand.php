<?php

namespace OmniTerm\Samples;

use Illuminate\Console\Command;
use OmniTerm\HasOmniTerm;
use OmniTerm\OmniTerm;

/**
 * Sample: Split Browser
 *
 * Demonstrates the interactive split-pane browser component.
 *
 * Run: php artisan omniterm:browser-demo
 */
class BrowserDemoCommand extends Command
{
    use HasOmniTerm;

    protected $signature = 'omniterm:browser-demo';

    protected $description = 'Demo: Interactive split-pane browser';

    public function handle(): int
    {
        $this->omni->titleBar('Split Browser', 'orange');
        $this->omni->newLine();

        $servers = [
            'web-01' => function (OmniTerm $omni) {
                $omni->statusSuccess('Healthy', 'All checks passing');
                $omni->tableHeader('Metric', 'Value');
                $omni->tableRowSuccess('CPU', '23%');
                $omni->tableRow('Memory', '4.2 GB / 8 GB');
                $omni->tableRow('Uptime', '14 days');
                $omni->tableRow('IP', '10.0.1.10');
                $omni->tableRow('OS', 'Ubuntu 22.04');
                $omni->tableRow('Load', '0.45 0.32 0.28');
            },
            'web-02' => function (OmniTerm $omni) {
                $omni->statusWarning('High Load', 'CPU above 60%');
                $omni->tableHeader('Metric', 'Value');
                $omni->tableRowWarning('CPU', '67%');
                $omni->tableRow('Memory', '6.1 GB / 8 GB');
                $omni->tableRow('Uptime', '14 days');
                $omni->tableRow('IP', '10.0.1.11');
                $omni->tableRow('OS', 'Ubuntu 22.04');
                $omni->tableRow('Load', '1.82 1.45 1.12');
            },
            'db-primary' => ['status' => 'running', 'cpu' => '45%', 'memory' => '28.3 GB / 32 GB', 'uptime' => '42 days', 'ip' => '10.0.2.10', 'os' => 'Ubuntu 22.04', 'load' => '2.10 1.89 1.76'],
            'db-replica' => ['status' => 'running', 'cpu' => '12%', 'memory' => '16.1 GB / 32 GB', 'uptime' => '42 days', 'ip' => '10.0.2.11', 'os' => 'Ubuntu 22.04', 'load' => '0.55 0.42 0.38'],
            'cache-01' => ['status' => 'running', 'cpu' => '8%', 'memory' => '3.8 GB / 16 GB', 'uptime' => '90 days', 'ip' => '10.0.3.10', 'os' => 'Alpine 3.18', 'load' => '0.12 0.08 0.05'],
            'queue-01' => function (OmniTerm $omni) {
                $omni->statusError('Critical', 'CPU at 89%, memory near limit');
                $omni->tableHeader('Metric', 'Value');
                $omni->tableRowError('CPU', '89%');
                $omni->tableRowWarning('Memory', '7.6 GB / 8 GB');
                $omni->tableRow('Uptime', '7 days');
                $omni->tableRow('IP', '10.0.4.10');
                $omni->tableRow('OS', 'Ubuntu 22.04');
                $omni->tableRow('Load', '3.45 2.98 2.67');
            },
            'queue-02' => function (OmniTerm $omni) {
                $omni->statusDisabled('Stopped', 'Server is offline');
                $omni->tableHeader('Metric', 'Value');
                $omni->tableRowDisabled('CPU', '0%');
                $omni->tableRow('Memory', '0 GB / 8 GB');
                $omni->tableRow('Uptime', '-');
                $omni->tableRow('IP', '10.0.4.11');
            },
            'monitor' => ['status' => 'running', 'cpu' => '15%', 'memory' => '2.1 GB / 4 GB', 'uptime' => '120 days', 'ip' => '10.0.5.10', 'os' => 'Alpine 3.18', 'load' => '0.22 0.18 0.15'],
            'cdn-edge-01' => ['status' => 'running', 'cpu' => '34%', 'memory' => '1.8 GB / 4 GB', 'uptime' => '30 days', 'ip' => '10.0.6.10', 'os' => 'Alpine 3.18', 'load' => '0.78 0.65 0.52'],
            'backup-01' => ['status' => 'running', 'cpu' => '5%', 'memory' => '1.2 GB / 4 GB', 'uptime' => '180 days', 'ip' => '10.0.7.10', 'os' => 'Ubuntu 22.04', 'load' => '0.05 0.03 0.02'],
        ];

        $selected = $this->omni->browse('Server Dashboard', $servers);

        $this->omni->newLine();

        if ($selected === null) {
            $this->line('No server selected.');
        } else {
            $this->info("Selected: {$selected}");
        }

        return self::SUCCESS;
    }
}
