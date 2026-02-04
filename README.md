<div align="center">

# OmniTerm

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pdphilip/omniterm.svg?style=flat-square)](https://packagist.org/packages/pdphilip/omniterm)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/pdphilip/omniterm/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/pdphilip/omniterm/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/pdphilip/omniterm.svg?style=flat-square)](https://packagist.org/packages/pdphilip/omniterm)

**A terminal UI toolkit for Laravel Artisan commands**

Build beautiful CLI interfaces with styled output, progress bars, spinners, and async task execution.

</div>

---

## Installation

```bash
composer require pdphilip/omniterm
```

## Quick Start

Add the `OmniTerm` trait to your Artisan command:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OmniTerm\OmniTerm;

class MyCommand extends Command
{
    use OmniTerm;

    protected $signature = 'my:command';

    public function handle()
    {
        $this->initOmni();

        $this->omni->success('Operation completed successfully!');
    }
}
```

## Try the Samples

OmniTerm includes sample commands to demo all features. Copy them to your app:

```bash
mkdir -p app/Console/Commands/OmniTermSamples
cp vendor/pdphilip/omniterm/samples/Commands/*.php app/Console/Commands/OmniTermSamples/
```

Then run:

```bash
php artisan omniterm:full-demo        # Complete demo (simulated deployment)
php artisan omniterm:status-messages  # Status message examples
php artisan omniterm:progress-bars    # Progress bar styles
php artisan omniterm:spinners         # All 10 spinner animations
php artisan omniterm:data-tables      # Key-value tables
php artisan omniterm:async-tasks      # Async task execution
```

See [samples/README.md](samples/README.md) for the full list.

---

## Features

- [Status Messages](#status-messages) - Styled feedback messages
- [Detailed Statuses](#detailed-statuses) - Status blocks with title, details, and help text
- [Data Tables](#data-tables) - Key-value rows with status indicators
- [Visual Elements](#visual-elements) - Boxes and horizontal rules
- [Progress Bars](#progress-bars) - Framed and simple styles with color support
- [Spinners & Loaders](#spinners--loaders) - Animated spinners for async tasks
- [Async Task Execution](#async-task-execution) - Run tasks with visual feedback
- [Interactive Prompts](#interactive-prompts) - Ask questions with autocomplete
- [Global Functions](#global-functions) - Render HTML directly to terminal

---

## Status Messages

Simple one-line status messages:

```php
$this->omni->success('Task completed');     // Green "GOOD" badge
$this->omni->error('Something went wrong'); // Red "FAIL" badge
$this->omni->warning('Check your config');  // Amber "WARN" badge
$this->omni->info('Processing...');         // Blue "INFO" badge
$this->omni->disabled('Feature disabled');  // Gray "OFF" badge
```

**Output:**
```
 GOOD  Task completed
 FAIL  Something went wrong
 WARN  Check your config
 INFO  Processing...
 OFF   Feature disabled
```

---

## Detailed Statuses

Status blocks with horizontal rules, title, details, and optional help text:

```php
$this->omni->statusSuccess(
    'Migration Complete',
    'All 42 records processed successfully',
    ['Tip: Run "php artisan cache:clear" to see changes']
);

$this->omni->statusError(
    'Connection Failed',
    'Could not connect to database',
    ['Check your .env file', 'Ensure MySQL is running']
);

$this->omni->statusWarning('Cache Stale', 'Cache is older than 24 hours');
$this->omni->statusInfo('Build Started', 'Compiling assets...');
$this->omni->statusDisabled('Feature Off', 'Enable in config/app.php');
```

**Custom status:**
```php
$this->omni->status('success', 'Title', 'Details text', ['Help line 1', 'Help line 2']);
```

---

## Data Tables

Create formatted key-value tables with status indicators:

```php
// Header row
$this->omni->header('Setting', 'Value', 'Notes');

// Basic row
$this->omni->row('Database', 'mysql', 'Production server');

// Status rows
$this->omni->rowSuccess('Connection', 'Active');
$this->omni->rowError('SSL Certificate', 'Expired');
$this->omni->rowWarning('Memory', '85% used');
$this->omni->rowInfo('Version', '8.2.0');
$this->omni->rowDisabled('Debug Mode');

// Additional status types
$this->omni->rowEnabled('Feature X');
$this->omni->rowOk('Health Check');
$this->omni->rowFailed('Sync Task', 'Retrying in 5s');
```

**Output:**
```
 Setting             Value              Notes
 Database .............................. mysql [Production server]
 Connection ............................ SUCCESS
 SSL Certificate ....................... FAILED
 Memory ................................ WARNING [85% used]
 Version ............................... INFO [8.2.0]
 Debug Mode ............................ DISABLED
```

**With help text:**
```php
$this->omni->rowError('API Key', 'Missing', [
    'Set OPENAI_API_KEY in your .env file',
    'Get a key at https://openai.com'
]);
```

---

## Visual Elements

### Boxes

```php
// Rounded corners (default)
$this->omni->roundedBox('Welcome to MyApp', 'text-cyan-500', 'text-white');

// Square corners
$this->omni->box('Configuration', 'text-amber-500', 'text-gray');
```

**Output:**
```
 ╭──────────────────────────────────────╮
 │                                      │
 │          Welcome to MyApp            │
 │                                      │
 ╰──────────────────────────────────────╯
```

### Horizontal Rules

```php
$this->omni->hr();              // Gray line
$this->omni->hr('text-blue');   // Custom color

// Semantic colors
$this->omni->hrSuccess();       // Green
$this->omni->hrError();         // Red
$this->omni->hrWarning();       // Amber
$this->omni->hrInfo();          // Blue
$this->omni->hrDisabled();      // Gray
```

---

## Progress Bars

### Framed Progress Bar

```php
$this->omni->createProgressBar(100, withColors: true);
$this->omni->showProgress();

foreach ($items as $item) {
    // Process item...
    $this->omni->progressAdvance();
}

$this->omni->progressFinish();
```

**Output (with colors - changes from red to green as progress increases):**
```
                    ╭────────────────────────────────────────╮
         0/100     │▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁│  0%
                    ╰────────────────────────────────────────╯
```

### Simple Progress Bar

```php
$this->omni->createSimpleProgressBar(50, withColors: false);
$this->omni->showProgress();

for ($i = 0; $i < 50; $i++) {
    $this->omni->progressAdvance();
}

$this->omni->progressFinish();
```

### Progress Bar Options

| Method | Description |
|--------|-------------|
| `createProgressBar($total, $withColors)` | Framed style with optional color gradient |
| `createSimpleProgressBar($total, $withColors)` | Minimal style |
| `showProgress()` | Display the progress bar |
| `progressAdvance($by = 1)` | Increment progress |
| `progressFinish()` | Complete and show 100% |

---

## Spinners & Loaders

OmniTerm includes 10 spinner animations for visual feedback during async operations.

### Available Spinner Types

| Type | Preview | Description |
|------|---------|-------------|
| `dots` | ⠋ ⠙ ⠹ ⠸ | Classic braille dots |
| `dots2` | ⢀⠀ ⡀⠀ | Double braille pattern |
| `dots3` | ⠉⠉ ⠈⠙ | Flowing dots |
| `dotsCircle` | ⢎⠁ ⠎⠑ | Circular dot pattern |
| `sand` | ⠁ ⠂ ⣿ | Filling hourglass effect |
| `clock` | 🕛 🕐 🕑 | Clock face animation |
| `material` | ▁█▁ | Material design loader |
| `pong` | ▐⠂ ▌ | Bouncing ball |
| `progress` | ▰▱▱ | Progress indicator |
| `progressLoader` | ▰▱▱ | Looping progress |

### Basic Spinner Usage

```php
$this->omni->newLoader('sand');

$result = $this->omni->runTask('Processing data...', function () {
    // Your long-running task
    sleep(3);
    return ['state' => 'success', 'message' => 'Data processed'];
});
```

### Spinner with Custom Colors

Colors cycle through as the spinner animates:

```php
$this->omni->newLoader('dots', [
    'text-amber-500',
    'text-emerald-500',
    'text-rose-500',
    'text-sky-500'
]);

$this->omni->runTask('Building...', function () {
    // Task code
    return ['state' => 'success'];
});
```

### Task Return Values

Your task callback should return an array with optional keys:

```php
$this->omni->runTask('My Task', function () {
    // Do work...

    return [
        'state' => 'success',   // success, warning, or error
        'message' => 'Done!',   // Completion message
        'details' => 'Extra info', // Optional details
    ];
});
```

**States:**
- `success` - Shows green checkmark ✔
- `warning` - Shows amber warning ⚠
- `error` - Shows red X ✘

---

## Async Task Execution

OmniTerm can run tasks asynchronously using process forking, displaying a spinner while the task executes in the background.

```php
$this->omni->newLoader('material', ['text-sky-500', 'text-emerald-500'], 1000);

$result = $this->omni->runTask('Syncing with remote server...', function () {
    // This runs in a forked process
    $data = file_get_contents('https://api.example.com/sync');

    if ($data) {
        return [
            'state' => 'success',
            'message' => 'Sync complete',
            'details' => 'Downloaded 1.2MB'
        ];
    }

    return [
        'state' => 'error',
        'message' => 'Sync failed'
    ];
});

if ($result['state'] === 'success') {
    // Continue processing
}
```

**Note:** Async execution requires the `pcntl` extension. On systems without it (like Windows), tasks run synchronously with a fallback display.

---

## Interactive Prompts

Ask the user for input with optional autocomplete suggestions:

```php
// Simple question
$name = $this->omni->ask('What is your name?');

// With autocomplete options
$color = $this->omni->ask('Choose a color:', ['red', 'green', 'blue']);
```

---

## Global Functions

OmniTerm provides functions for direct HTML-to-terminal rendering:

### `render()`

Render HTML with Tailwind CSS classes to the terminal:

```php
use function OmniTerm\render;

render('<div class="text-green-500 font-bold">Success!</div>');
render('<span class="bg-red-600 text-white px-2">ERROR</span> Something failed');
```

### `liveRender()`

Create a live-updating display that redraws in place:

```php
use function OmniTerm\liveRender;

$live = liveRender('<div>Starting...</div>');

for ($i = 1; $i <= 100; $i++) {
    $live->reRender("<div>Progress: {$i}%</div>");
    usleep(50000);
}
```

### `asyncFunction()`

Create an async renderer for custom async operations:

```php
use function OmniTerm\asyncFunction;

$async = asyncFunction(function () {
    // Background task
    return ['done' => true];
});

$result = $async->run(function () use ($async) {
    $async->render('<div>Working...</div>');
}, 10000); // Update every 10ms
```

### `ask()`

Prompt for user input:

```php
use function OmniTerm\ask;

$answer = ask('Continue? (y/n)');
```

### `terminal()`

Get terminal dimensions:

```php
use function OmniTerm\terminal;

$width = terminal()->getWidth();
$height = terminal()->getHeight();
```

---

## Customizing Colors

Override the default status colors:

```php
$this->initOmni();

$this->omni->successColor = 'green';   // Default: emerald
$this->omni->errorColor = 'red';       // Default: rose
$this->omni->warningColor = 'orange';  // Default: amber
$this->omni->infoColor = 'blue';       // Default: sky
$this->omni->disabledColor = 'gray';   // Default: zinc
```

Colors use Tailwind CSS color names (without the number suffix).

---

## Complete Example

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OmniTerm\OmniTerm;

class DeployCommand extends Command
{
    use OmniTerm;

    protected $signature = 'app:deploy';
    protected $description = 'Deploy the application';

    public function handle()
    {
        $this->initOmni();

        // Header
        $this->omni->roundedBox('Deployment Starting', 'text-cyan-500');

        // Configuration check
        $this->omni->header('Check', 'Status');
        $this->omni->rowSuccess('PHP Version', '8.2.0');
        $this->omni->rowSuccess('Composer', 'Installed');
        $this->omni->rowWarning('Node.js', '16.x (18.x recommended)');

        $this->omni->hrInfo();

        // Run migrations with spinner
        $this->omni->newLoader('sand', ['text-amber-500', 'text-emerald-500']);

        $result = $this->omni->runTask('Running migrations', function () {
            // Artisan::call('migrate', ['--force' => true]);
            sleep(2); // Simulated work
            return ['state' => 'success', 'message' => 'Migrations complete'];
        });

        // Build assets with progress bar
        $this->omni->info('Building assets...');
        $this->omni->createProgressBar(100, true);
        $this->omni->showProgress();

        for ($i = 0; $i < 100; $i++) {
            usleep(20000);
            $this->omni->progressAdvance();
        }

        $this->omni->progressFinish();

        // Final status
        $this->omni->statusSuccess(
            'Deployment Complete',
            'Application deployed successfully',
            ['Visit https://myapp.com to verify']
        );

        return Command::SUCCESS;
    }
}
```

---

## Requirements

- PHP 8.2+
- Laravel 10, 11, or 12

## Dependencies

- [nunomaduro/termwind](https://github.com/nunomaduro/termwind) - HTML to terminal rendering

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
