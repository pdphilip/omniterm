# Changelog

All notable changes to `pdphilip/omniterm` will be documented in this file.

## v3.0.0 - 2026-03-24

### Breaking Changes

- **Dropped Laravel 10 support** — minimum is now Laravel 11
- **Dropped Symfony 6 support** — minimum is now Symfony 7

### Added

- **Laravel 13 support** — compatible with Laravel 11, 12, and 13
- **Symfony 8 support** — for Laravel 13's Symfony dependency
- Composer test scripts for per-version testing: `composer test:l11`, `composer test:l12`, `composer test:l13`, `composer test:all`

### Changed

- `illuminate/contracts` constraint: `^11.0||^12.0||^13.0`
- `symfony/console` constraint: `^7.0||^8.0`
- `laravel/prompts` constraint: `^0.2||^0.3`
- Dev dependencies updated to support Pest 4, PHPStan 2, Larastan 3, Testbench 11
- CI matrix updated: PHP 8.3/8.4, Laravel 11/12/13

**Full Changelog**: https://github.com/pdphilip/omniterm/compare/v2.1.1...v3.0.0

## v2.1.1 - 2026-03-08

### Added

- `ask()` now accepts a `$default` parameter — when provided, pressing Enter returns the default value instead of an empty string
- Question prompt displays `(default: X)` hint in muted text when a default is set
- `AskTest` with 7 tests covering question view rendering (options, defaults, prompt character)
- Service provider registered in test suite `TestCase` for view-dependent tests

### Changed

- `ask()` signature: `ask(string $question, array $options = [], mixed $default = null): mixed`
- Question blade view renders the default hint alongside options

**Full Changelog**: https://github.com/pdphilip/omniterm/compare/v2.1.0...v2.1.1

## v2.1.0 - 2026-02-23

### Added

#### HTML Tag Support

The rendering engine now handles semantic HTML tags with correct terminal output. Tags apply sensible defaults (block/inline, bold, italic, etc.) that CSS classes can override.

**Block tags:** `<p>`, `<ul>`, `<ol>`, `<li>`, `<dl>`, `<dt>`, `<dd>`, `<pre>`, `<code>`, `<table>`, `<section>`, `<article>`, `<header>`, `<footer>`, `<nav>`, `<aside>`, `<main>`

**Inline tags:** `<b>`/`<strong>` (bold), `<i>`/`<em>` (italic), `<s>` (strikethrough), `<a>` (underline + hyperlink), `<th>` (bold)

**Self-closing:** `<br>` (line break), `<hr>` (horizontal rule with color support)

#### Table Rendering

`<table>` elements render with rounded box-drawing borders. Auto-sized columns with proportional shrinking when content exceeds terminal width. Header rows get a mid-border separator.

```html
<table>
    <thead><tr><th>Name</th><th>Status</th></tr></thead>
    <tr><td>Build</td><td>Done</td></tr>
</table>
```

#### Code Blocks

`<code>` preserves whitespace and supports line numbers via `line` and `start-line` attributes.

```html
<code line="1" start-line="10">
    $foo = 'bar';
    echo $foo;
</code>
```

#### Hyperlinks

`<a href="...">` wraps content with OSC 8 terminal hyperlink sequences. Terminals that support it show clickable links; others show the text as-is with underline.

#### Whitespace Preservation

`<pre>` and `<code>` preserve whitespace and newlines. The `preserveWhitespace` property inherits through nested elements.

#### Data List & Debug

- `dataList($data, $title, $borderColor)` renders any data structure as a tree with box-drawing connectors
- `debug($var, $label)` dumps data as a tree and exits (the terminal `dd()`)

#### Other

- `newLine($count)` method for outputting blank lines
- `tableRow()` and `tableRowAsStatus()` now accept `mixed` values (auto-cast to string)
- `Ansi::dim()` method
- `Ansi::hyperlink()`, `Ansi::stripAnsi()` methods

### Changed

- Rendering engine: `isBlockTag()` now recognizes `class="block"` on inline tags, not just block tag names
- Rendering engine: inline shortcut skipped when `listStyle` or `spaceY` is active (fixes list/space-y rendering with inline children)
- Box-drawing characters consolidated into `AsciiHelper` as single source of truth (`roundedTable()` method)
- `SplitBrowserRenderer` uses `AsciiHelper` and `Ansi` methods instead of raw escape sequences
- `Ansi::visibleLength()` and `Ansi::truncate()` handle OSC 8 hyperlink sequences

### Fixed

- List styles (`list-disc`, `list-decimal`, `list-square`) now render markers correctly when children are inline elements
- `space-y` now adds spacing between inline children in block containers
- `<span class="block">` correctly treated as block element in parent layout decisions

## v2.0.1 - 2026-02-20

### Added

- `feedback()` public method for custom-colored feedback messages with any title and color
- Custom `$title` parameter on `success()`, `error()`, `warning()`, `info()`, `disabled()` methods
- `confirm()` method with Y/N prompt and callback execution
- `ConfirmTask` class for interactive confirmation dialogs
- 26 CSS classes added to the rendering engine: `italic`, `underline`, `line-through`, `font-normal`, `invisible`, `block`, `list-disc`, `list-decimal`, `list-square`, `list-none`, `text-left`, `text-right`, `text-center`, `justify-between`, `justify-around`, `justify-center`, `justify-evenly`, `uppercase`, `lowercase`, `capitalize`, `snakecase`, `truncate`, `min-w-{n}`, `max-w-{n}`, `w-auto`, `w-full`
- `FeedbackTest` with 5 tests covering feedback view rendering
- New sample commands: `omniterm:confirm`, `omniterm:tailwind-classes`

### Changed

- Feedback methods (`success`, `error`, `warning`, `info`, `disabled`) now delegate to a single `feedback()` method using one shared blade view
- Removed individual blade views (`success.blade.php`, `error.blade.php`, `warning.blade.php`, `info.blade.php`, `disabled.blade.php`) in favor of unified `feedback.blade.php`
- Type hints and readability improvements across async classes

## v2.0.0 - 2026-02-15

### Breaking Changes

- **Trait renamed:** `OmniTerm\OmniTerm` → `OmniTerm\HasOmniTerm`
  ```php
  // Before
  use OmniTerm\OmniTerm;
  class MyCommand extends Command {
      use OmniTerm;
  }

  // After
  use OmniTerm\HasOmniTerm;
  class MyCommand extends Command {
      use HasOmniTerm;
  }
  ```

- **Core class renamed:** `OmniTerm\Helpers\OmniHelpers` → `OmniTerm\OmniTerm` — the flagship class name now belongs to the core component, accessed via `$this->omni`

- **Spinner enum replaces strings:** Loader/spinner types are now a backed enum instead of magic strings
  ```php
  // Before
  $this->omni->newLoader('sand');
  $this->omni->liveTask('Title', 'dots');

  // After
  use OmniTerm\Async\Spinner;
  $this->omni->newLoader(Spinner::Sand);
  $this->omni->liveTask('Title', Spinner::Dots);
  ```

- **Progress bar fluent builder:** Old convenience methods replaced with a chainable builder API
  ```php
  // Before
  $this->omni->createProgressBar(100, withColors: true);
  $this->omni->showProgress();
  $this->omni->progressAdvance();
  $this->omni->progressFinish();

  // After
  $bar = $this->omni->progressBar(100)->framed()->steps();
  $bar->start();
  $bar->advance();
  $bar->finish();
  ```

- **SplitBrowser API redesigned:** Items are now an associative array (keys = labels, values = closures, arrays, or scalars) instead of a flat list with a separate detail callback
  ```php
  // Before
  $this->omni->browse('Label', ['a', 'b'], fn ($item) => ["Detail for $item"]);

  // After
  $this->omni->browse('Label', [
      'a' => function (OmniTerm $omni) {
          $omni->statusSuccess('Healthy', 'All checks passing');
      },
      'b' => ['status' => 'running', 'cpu' => '45%'],
  ]);
  ```

- **Global functions removed:** `render()`, `liveRender()`, `parse()`, `terminal()`, `asyncFunction()` — use `$this->omni->` methods instead

### Added

- `Spinner` backed enum (`OmniTerm\Async\Spinner`) with 10 animation types — resolves view names, animation frames, and labels via `view()`, `frames()`, `label()` methods
- `TaskResult` value object for structured spinner/live task results
- `SpinnerTask` class encapsulating spinner + async task execution
- `LiveTask::runTask()` — one-shot method combining run + finish
- `OmniTerm::task()` — one-liner for spinner task execution
- Progress bar builder with `framed()`, `steps()`, `gradient()`, `color()` chainable methods
- SplitBrowser closure rendering — detail pane captures full omni output (status blocks, tables, etc.) via `Renderer::renderUsing()` with `COLUMNS` width constraint
- SplitBrowser associative array auto-formatting (bold keys, aligned values)
- ANSI-aware string truncation in SplitBrowser renderer preventing layout overflow
- New sample commands: `omniterm:browser-demo`, `omniterm:live-task-demo`, `omniterm:title-bars`

### Removed

- `OmniTerm\Helpers\OmniHelpers` class (renamed to `OmniTerm\OmniTerm`)
- `OmniTerm\OmniTerm` trait (renamed to `OmniTerm\HasOmniTerm`)
- Global helper functions (`use function OmniTerm\render`, etc.)
- Direct `AsciiHelper` calls from blade views (frames now passed as data)
