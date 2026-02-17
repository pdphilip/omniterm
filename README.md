<div align="center">

# OmniTerm

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pdphilip/omniterm.svg?style=flat-square)](https://packagist.org/packages/pdphilip/omniterm)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/pdphilip/omniterm/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/pdphilip/omniterm/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/pdphilip/omniterm.svg?style=flat-square)](https://packagist.org/packages/pdphilip/omniterm)

**Terminal UI toolkit for Laravel**

Rich CLI output using HTML and Tailwind CSS classes, rendered as ANSI in your terminal.

![Progress Bars](docs/gifs/progress-bars.gif)

</div>

---

## About

OmniTerm is a terminal rendering engine for Laravel. Write your CLI output as HTML with Tailwind CSS classes and OmniTerm compiles it to ANSI escape sequences.

The Tailwind-for-terminal concept was pioneered by [Termwind](https://github.com/nunomaduro/termwind). OmniTerm builds on that idea with its own rendering engine that adds:

- **16 million color support** - full RGB truecolor, with automatic 256-color fallback for older terminals
- **Gradients** - `bg-gradient-to-r`, `from-{color}`, `via-{color}`, `to-{color}` for smooth per-character color transitions
- **Arbitrary RGB classes** - `text-[R,G,B]` and `bg-[R,G,B]` for computed/dynamic colors
- **Content repeat** - `content-repeat-[char]` to fill widths with box-drawing characters

On top of the rendering engine, OmniTerm ships with a set of pre-built components for common CLI patterns: status messages, data tables, progress bars, spinners, live tasks, and an interactive split-pane browser.

## Requirements

- PHP 8.2+
- Laravel 10, 11, or 12

## Installation

```bash
composer require pdphilip/omniterm
```

---

## Built-in Components

Add the `OmniTerm` trait to any Artisan command:

```php
use OmniTerm\OmniTerm;

class MyCommand extends Command
{
    use OmniTerm;

    public function handle()
    {
        $this->omni->success('Ready');
    }
}
```

### Status Messages

One-line status badges:

```php
$this->omni->success('Task completed');     // Green GOOD badge
$this->omni->error('Something went wrong'); // Red FAIL badge
$this->omni->warning('Check your config');  // Amber WARN badge
$this->omni->info('Processing...');         // Blue INFO badge
$this->omni->disabled('Feature off');       // Gray OFF badge
```

Detailed status blocks with title, message, and help lines:

```php
$this->omni->statusSuccess('Migration Complete', 'All 42 records processed', ['Run cache:clear']);
$this->omni->statusError('Connection Failed', 'Could not reach database', ['Check .env', 'Ensure MySQL is running']);
```

![Status Messages](./docs/gifs/status-messages.gif)

### Data Tables

Key-value rows with status indicators:

```php
$this->omni->tableHeader('Setting', 'Value', 'Notes');
$this->omni->tableRow('Database', 'mysql', 'Production server');
$this->omni->tableRowSuccess('Connection', 'Active');
$this->omni->tableRowError('SSL Certificate', 'Expired');
$this->omni->tableRowWarning('Memory', '85% used');
```

![Data Tables](./docs/gifs/data-tables.gif)

### Visual Elements

Title bars, boxes, and horizontal rules:

```php
$this->omni->titleBar('My Application', 'sky');
$this->omni->roundedBox('Welcome', 'text-cyan-500', 'text-white');
$this->omni->hr();
$this->omni->hrSuccess();
```

![Visual Elements](./docs/gifs/visual-elements.gif)

### Progress Bars

Three styles: framed, simple, and gradient. The gradient bar smoothly transitions from amber to emerald as progress increases.

```php
$this->omni->createGradientProgressBar(100);
$this->omni->showProgress();

foreach ($items as $item) {
    // work...
    $this->omni->progressAdvance();
}

$this->omni->progressFinish();
```

Other variants:

```php
$this->omni->createProgressBar(100, withColors: true);   // Framed with color steps
$this->omni->createSimpleProgressBar(50);                 // Minimal bar
```

![Progress Bars](./docs/gifs/progress-bars.gif)

### Live Tasks

Run a callback in a background process with an animated spinner:

```php
$this->omni->newLoader('sand');

$result = $this->omni->runTask('Processing data...', function () {
    sleep(3);
    return ['state' => 'success', 'message' => 'Done'];
});
```

For fine-grained control with live-updating counters:

```php
$task = $this->omni->liveTask('Syncing records', 'dots');
$task->row('Processed', 0);
$task->row('Skipped', 0);

$result = $task->run(function () use ($task) {
    foreach ($records as $record) {
        $record->sync()
            ? $task->increment('Processed')
            : $task->increment('Skipped');
    }
    return ['state' => 'success', 'message' => 'Sync complete'];
});

$task->finish('All done');
```

10 spinner types: `dots`, `dots2`, `dots3`, `dotsCircle`, `sand`, `clock`, `material`, `pong`, `progress`, `progressLoader`.

### Interactive Browser

Split-pane TUI: scrollable list on the left, detail view on the right.

```
+-- Select an Index ----------------+-----------------------------------+
| > users                           | Documents: 1,234                  |
|   companies                       | Store Size: 45.2mb                |
|   products                        | Health: green                     |
+------------------------------------+-----------------------------------+
  Up/Down Navigate  Enter Select  q/Esc Exit
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
);
// Returns selected item, or null on Esc
```

### Interactive Prompts

```php
$name = $this->omni->ask('What is your name?');
$color = $this->omni->ask('Choose a color:', ['red', 'green', 'blue']);
```

---

## DIY - The Rendering Engine

The built-in components are just Blade templates compiled through OmniTerm's rendering engine. You can use the same engine directly to build anything.

### `render()`

Write HTML with Tailwind classes, get ANSI output:

```php
use function OmniTerm\render;

render('<div class="flex">
    <span class="bg-emerald-600 text-white font-bold px-1">PASS</span>
    <span class="flex-1 text-zinc-400 px-1">Database connection verified</span>
    <span class="text-zinc-600 text-right w-12">12ms</span>
</div>');
```

### `liveRender()`

Redraws in place, for live-updating displays:

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

Terminal dimensions:

```php
use function OmniTerm\terminal;

$width = terminal()->getWidth();
$height = terminal()->getHeight();
```

### Supported Classes

![Tailwind Classes](docs/gifs/tailwind-classes.gif)

#### Layout

| Class         | Effect                    |
|---------------|---------------------------|
| `flex`        | Horizontal layout         |
| `flex-1`      | Fill remaining space      |
| `w-{n}`       | Fixed width in characters |
| `space-x-{n}` | Gap between children      |

#### Spacing

| Class                        | Effect                        |
|------------------------------|-------------------------------|
| `px-{n}`, `pl-{n}`, `pr-{n}` | Horizontal padding            |
| `mx-{n}`, `ml-{n}`, `mr-{n}` | Horizontal margin             |
| `mt-{n}`, `mb-{n}`           | Vertical margin (blank lines) |

#### Typography

| Class         | Effect       |
|---------------|--------------|
| `font-bold`   | Bold         |
| `text-center` | Center-align |
| `text-right`  | Right-align  |

#### Colors

All [Tailwind colors](https://tailwindcss.com/docs/customizing-colors) with shades 50-950: slate, gray, zinc, neutral, stone, red, orange, amber, yellow, lime, green, emerald, teal, cyan, sky, blue, indigo, violet, purple, fuchsia, pink,
rose.

| Class                  | Effect                                       |
|------------------------|----------------------------------------------|
| `text-{color}-{shade}` | Text color, e.g. `text-sky-500`              |
| `bg-{color}-{shade}`   | Background color, e.g. `bg-red-600`          |
| `text-[R,G,B]`         | Arbitrary RGB text, e.g. `text-[255,100,50]` |
| `bg-[R,G,B]`           | Arbitrary RGB background                     |

#### Gradients

Per-character color interpolation across an element's width.

| Class                  | Effect                 |
|------------------------|------------------------|
| `bg-gradient-to-r`     | Left-to-right gradient |
| `bg-gradient-to-l`     | Right-to-left gradient |
| `from-{color}-{shade}` | Start color            |
| `via-{color}-{shade}`  | Midpoint color         |
| `to-{color}-{shade}`   | End color              |

```php
render('<div class="flex">
    <span class="flex-1 bg-gradient-to-r from-indigo-800 via-purple-500 to-pink-400 text-white text-center">
        Smooth gradient
    </span>
</div>');
```

#### Content

| Class                   | Effect                         |
|-------------------------|--------------------------------|
| `content-repeat-[char]` | Repeat character to fill width |

### Color Mode Detection

OmniTerm auto-detects your terminal's color capability:

- **Truecolor (16M)** - full RGB. iTerm2, Kitty, WezTerm, most modern terminals.
- **256-color** - automatic fallback for older terminals (e.g. Apple Terminal). Colors mapped to nearest match.

No configuration needed.

### Using Blade Templates

Since OmniTerm is a Laravel package, you can write your CLI output as Blade views and render them through the engine. This is how all the built-in components work:

```php
// resources/views/cli/deploy-status.blade.php
<div class="flex">
    <span class="bg-{{ $color }}-600 text-white font-bold px-1">{{ $badge }}</span>
    <span class="flex-1 text-zinc-400 px-1">{{ $message }}</span>
</div>

// In your command
render(view('cli.deploy-status', [
    'badge' => 'DEPLOY',
    'color' => 'emerald',
    'message' => 'Production updated',
]));
```

---

## Samples

OmniTerm includes sample commands for every feature. Copy them into your app:

```bash
mkdir -p app/Console/Commands/OmniTermSamples
cp vendor/pdphilip/omniterm/samples/Commands/*.php app/Console/Commands/OmniTermSamples/
```

Update the namespace in each file to `App\Console\Commands\OmniTermSamples`, then:

```bash
php artisan omniterm:full-demo          # Complete deployment simulation
php artisan omniterm:status-messages    # Status messages
php artisan omniterm:progress-bars      # All progress bar styles
php artisan omniterm:spinners           # All 10 spinner animations
php artisan omniterm:data-tables        # Key-value tables
php artisan omniterm:visual-elements    # Boxes and horizontal rules
php artisan omniterm:async-tasks        # Async task execution
php artisan omniterm:tailwind-classes   # Every supported CSS class
php artisan omniterm:interactive        # Interactive prompts
php artisan omniterm:custom-colors      # Custom color schemes
php artisan omniterm:global-functions   # Using global functions
```

---

## Testing

```bash
composer test        # Lint + PHPStan + Pest
composer test:unit   # Pest only
composer types       # PHPStan only
composer format      # Laravel Pint
```

## License

MIT. See [License File](LICENSE.md).
