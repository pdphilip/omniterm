# Changelog

All notable changes to `pdphilip/omniterm` will be documented in this file.

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
