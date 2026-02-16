<div align="center">

# OmniTerm

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pdphilip/omniterm.svg?style=flat-square)](https://packagist.org/packages/pdphilip/omniterm)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/pdphilip/omniterm/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/pdphilip/omniterm/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/pdphilip/omniterm.svg?style=flat-square)](https://packagist.org/packages/pdphilip/omniterm)

**A terminal UI toolkit for Laravel Artisan commands**

Build rich CLI interfaces with styled output, progress bars, spinners, interactive browsers, and async task execution — all using familiar Tailwind CSS-style classes.

![Progress Bars](docs/gifs/progress-bars.gif)

</div>

---

## Installation

```bash
composer require pdphilip/omniterm
```

## Quick Start

Add the `OmniTerm` trait to your Artisan command and call `initOmni()`:

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

        $this->omni->titleBar('My App', 'sky');
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

Then update the namespace in each file to `App\Console\Commands\OmniTermSamples` and run:

```bash
php artisan omniterm:full-demo          # Complete demo (simulated deployment)
php artisan omniterm:status-messages    # Status messages
php artisan omniterm:progress-bars      # All progress bar styles
php artisan omniterm:spinners           # All 10 spinner animations
php artisan omniterm:data-tables        # Key-value tables
php artisan omniterm:visual-elements    # Boxes and horizontal rules
php artisan omniterm:async-tasks        # Async task execution
php artisan omniterm:interactive        # Interactive prompts
php artisan omniterm:tailwind-classes   # Every supported CSS class
php artisan omniterm:custom-colors      # Custom color schemes
php artisan omniterm:global-functions   # Using global functions
```

---

## Features

- [HTML Rendering Engine](#html-rendering-engine) — Write terminal UI with HTML and Tailwind CSS classes
- [Status Messages](#status-messages) — Styled feedback messages
- [Detailed Statuses](#detailed-statuses) — Status blocks with title, details, and help text
- [Data Tables](#data-tables) — Key-value rows with status indicators
- [Visual Elements](#visual-elements) — Title bars, boxes, and horizontal rules
- [Progress Bars](#progress-bars) — Framed, simple, and gradient styles
- [Live Tasks](#live-tasks) — Run tasks with animated spinners
- [Spinners & Loaders](#spinners--loaders) — 10 spinner animations for async operations
- [Interactive Browser](#interactive-browser) — Split-pane list with detail view
- [Interactive Prompts](#interactive-prompts) — Ask questions with autocomplete
- [Global Functions](#global-functions) — Render HTML directly to terminal

---

## HTML Rendering Engine

OmniTerm includes a built-in HTML-to-ANSI rendering engine. Write terminal output using HTML tags and Tailwind CSS-style classes — no external rendering dependencies needed.

```php
use function OmniTerm\render;

render('<div class="flex">
    <span class="bg-emerald-600 text-emerald-100 font-bold px-1">PASS</span>
    <span class="flex-1 text-zinc-400 px-1">Database connection verified</span>
    <span class="text-zinc-600 text-right w-12">12ms</span>
</div>');
```

### Supported Classes

#### Layout

| Class | Description |
|-------|-------------|
| `flex` | Flex container (horizontal layout) |
| `flex-1` | Fill remaining space in a flex row |
| `w-{n}` | Fixed width in characters (e.g. `w-20`) |
| `space-x-{n}` | Gap between flex children |

#### Spacing

| Class | Description |
|-------|-------------|
| `px-{n}` | Horizontal padding |
| `pl-{n}` / `pr-{n}` | Left / right padding |
| `m-{n}` | Margin on all sides |
| `mx-{n}` | Horizontal margin |
| `ml-{n}` / `mr-{n}` | Left / right margin |
| `mt-{n}` / `mb-{n}` | Top / bottom margin (blank lines) |

#### Typography

| Class | Description |
|-------|-------------|
| `font-bold` | Bold text |
| `text-center` | Center-align text |
| `text-right` | Right-align text |

#### Colors

| Class | Description |
|-------|-------------|
| `text-{color}-{shade}` | Text color (e.g. `text-sky-500`) |
| `text-{color}` | Text color, defaults to shade 500 |
| `bg-{color}-{shade}` | Background color (e.g. `bg-red-600`) |
| `text-[R,G,B]` | Arbitrary RGB text color (e.g. `text-[255,100,50]`) |
| `bg-[R,G,B]` | Arbitrary RGB background color |

All [Tailwind CSS colors](https://tailwindcss.com/docs/customizing-colors) are supported: slate, gray, zinc, neutral, stone, red, orange, amber, yellow, lime, green, emerald, teal, cyan, sky, blue, indigo, violet, purple, fuchsia, pink, rose — each with shades 50–950.

#### Gradients

| Class | Description |
|-------|-------------|
| `bg-gradient-to-r` | Left-to-right gradient |
| `bg-gradient-to-l` | Right-to-left gradient |
| `from-{color}-{shade}` | Gradient start color |
| `to-{color}-{shade}` | Gradient end color |
| `via-{color}-{shade}` | Gradient midpoint color |

```php
render('<div class="flex">
    <span class="flex-1 bg-gradient-to-r from-indigo-800 via-purple-500 to-pink-400 text-white text-center">
        Smooth gradient
    </span>
</div>');
```

#### Content

| Class | Description |
|-------|-------------|
| `content-repeat-[char]` | Repeat a character to fill width (e.g. `content-repeat-[─]`) |

### Color Mode Detection

OmniTerm automatically detects your terminal's color capabilities:

- **Truecolor (16M)** — Full RGB colors. Used by iTerm2, Kitty, WezTerm, most modern terminals.
- **256-color** — Automatic fallback for older terminals (e.g. Apple Terminal). Colors are mapped to the nearest match.

No configuration needed — it just works.

![Tailwind Classes](docs/gifs/tailwind-classes.gif)

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

![Status Messages](docs/gifs/status-messages.gif)

---

## Detailed Statuses

Status blocks with title, details, and optional help text:

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

Formatted key-value tables with status indicators:

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
```

![Data Tables](docs/gifs/data-tables.gif)

**With help text:**
```php
$this->omni->rowError('API Key', 'Missing', [
    'Set OPENAI_API_KEY in your .env file',
    'Get a key at https://openai.com'
]);
```

---

## Visual Elements

### Title Bar

Full-width colored title bar with gradient wings:

```php
$this->omni->titleBar('My Application', 'sky');
$this->omni->titleBar('Deployment', 'emerald');
```

### Boxes

```php
$this->omni->roundedBox('Welcome to MyApp', 'text-cyan-500', 'text-white');
$this->omni->box('Configuration', 'text-amber-500', 'text-gray');
```

**Output:**
```
 ╭──────────────────────────────────────╮
 │          Welcome to MyApp            │
 ╰──────────────────────────────────────╯
```

![Visual Elements](docs/gifs/visual-elements.gif)

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
// With color gradient (red → green as progress increases)
$this->omni->createProgressBar(100, withColors: true);
$this->omni->showProgress();

foreach ($items as $item) {
    // Process item...
    $this->omni->progressAdvance();
}

$this->omni->progressFinish();
```

```
          ╭────────────────────────────────────────╮
 50/100   │▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁▁                    │  50%
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

### Gradient Progress Bar

The bar, border, and label smoothly transition from amber to emerald as progress increases:

```php
$this->omni->createGradientProgressBar(60);
$this->omni->showProgress();

for ($i = 0; $i < 60; $i++) {
    $this->omni->progressAdvance();
}

$this->omni->progressFinish();
```

### Variable Increments

```php
$this->omni->createProgressBar(100, withColors: true);
$this->omni->showProgress();

$this->omni->progressAdvance(25);  // Jump by 25
$this->omni->progressAdvance(10);  // Jump by 10
$this->omni->progressFinish();
```

### Progress Bar Methods

| Method | Description |
|--------|-------------|
| `createProgressBar($total, $withColors)` | Framed style, optional color steps |
| `createSimpleProgressBar($total, $withColors)` | Minimal style |
| `createGradientProgressBar($total)` | Smooth amber → emerald gradient |
| `showProgress()` | Display the progress bar |
| `progressAdvance($by = 1)` | Increment progress |
| `progressFinish()` | Complete and show 100% |

---

## Live Tasks

Run a task with an animated spinner. The spinner runs while your callback executes in a background process:

```php
$this->omni->newLoader('sand');

$result = $this->omni->runTask('Processing data...', function () {
    // Your long-running task
    sleep(3);
    return ['state' => 'success', 'message' => 'Data processed'];
});
```

### Task with Tracked Rows

`liveTask()` gives you fine-grained control with live-updating counters:

```php
$task = $this->omni->liveTask('Syncing records', 'dots');
$task->row('Processed', 0);
$task->row('Skipped', 0);

$result = $task->run(function () use ($task) {
    foreach ($records as $record) {
        if ($record->sync()) {
            $task->increment('Processed');
        } else {
            $task->increment('Skipped');
        }
    }
    return ['state' => 'success', 'message' => 'Sync complete'];
});

$task->finish('All done');
```

### Task Return Values

Your callback should return an array:

```php
return [
    'state' => 'success',      // success, warning, or error
    'message' => 'Done!',      // Completion message
    'details' => 'Extra info',  // Optional details
];
```

**States:**
- `success` — Green checkmark
- `warning` — Amber warning
- `error` — Red X

---

## Spinners & Loaders

10 built-in spinner animations:

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

### Custom Spinner Colors

Colors cycle as the spinner animates:

```php
$this->omni->newLoader('dots', [
    'text-amber-500',
    'text-emerald-500',
    'text-rose-500',
    'text-sky-500',
]);
```

---

## Interactive Browser

A split-pane TUI component — scrollable list on the left, detail view on the right:

```
╭─ Select an Index ────────────────┬───────────────────────────────────╮
│ › users                          │ Documents: 1,234                  │
│   companies                      │ Store Size: 45.2mb                │
│   products                       │ Health: green                     │
│   blog_posts                     │                                   │
╰──────────────────────────────────┴───────────────────────────────────╯
  ↑/↓ Navigate  Enter Select  q/Esc Exit
```

```php
$selected = $this->omni->browse(
    label: 'Select an Index',
    items: ['users', 'companies', 'products'],
    detail: fn (string $item) => [
        "Documents: 1,234",
        "Store Size: 45.2mb",
        "Health: green",
    ],
    scroll: 12,
    hint: '↑/↓ Navigate  Enter Select  q/Esc Exit',
);

// Returns the selected item string, or null on Esc/q
```

**Parameters:**
- `label` — Title shown in the top-left border
- `items` — Array of string labels for the list
- `detail` — Closure that receives the selected item and returns an array of lines for the right pane
- `scroll` — Max visible rows (default: 12)
- `hint` — Footer text

---

## Interactive Prompts

Ask the user for input with optional autocomplete:

```php
$name = $this->omni->ask('What is your name?');

$color = $this->omni->ask('Choose a color:', ['red', 'green', 'blue']);
```

---

## Global Functions

Use these anywhere — no trait or initialization needed:

### `render()`

Render HTML to the terminal:

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

### `parse()`

Convert HTML to an ANSI string without printing:

```php
use function OmniTerm\parse;

$ansi = parse('<span class="text-sky-500">Hello</span>');
```

### `terminal()`

Get terminal dimensions:

```php
use function OmniTerm\terminal;

$width = terminal()->getWidth();
$height = terminal()->getHeight();
```

### `ask()`

Prompt for user input:

```php
use function OmniTerm\ask;

$answer = ask('Continue? (y/n)');
```

---

## Customizing Colors

Override the default status colors used by status messages, data rows, and horizontal rules:

```php
$this->initOmni();

$this->omni->successColor = 'green';   // Default: emerald
$this->omni->errorColor = 'red';       // Default: rose
$this->omni->warningColor = 'orange';  // Default: amber
$this->omni->infoColor = 'blue';       // Default: sky
$this->omni->disabledColor = 'gray';   // Default: zinc
```

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

        $this->omni->titleBar('Deployment', 'sky');

        // Configuration check
        $this->omni->header('Check', 'Status');
        $this->omni->rowSuccess('PHP Version', '8.2.0');
        $this->omni->rowSuccess('Composer', 'Installed');
        $this->omni->rowWarning('Node.js', '16.x (18.x recommended)');

        $this->omni->hrInfo();

        // Run migrations with spinner
        $this->omni->newLoader('sand');
        $this->omni->runTask('Running migrations', function () {
            sleep(2);
            return ['state' => 'success', 'message' => 'Migrations complete'];
        });

        // Build assets with gradient progress bar
        $this->omni->info('Building assets...');
        $this->omni->createGradientProgressBar(100);
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

- `symfony/console` — Terminal output and cursor control
- `laravel/prompts` — Interactive prompt primitives (used by SplitBrowser)

## Testing

```bash
composer test        # Lint + PHPStan + Pest
composer test:unit   # Pest only
composer types       # PHPStan only
composer format      # Laravel Pint
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
