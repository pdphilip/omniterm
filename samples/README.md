# OmniTerm Samples

Sample Artisan commands demonstrating all OmniTerm features.

## Installation

Copy the sample commands to your Laravel application:

```bash
cp -r vendor/pdphilip/omniterm/samples/Commands/* app/Console/Commands/OmniTermSamples/
```

Or create the directory and copy manually:

```bash
mkdir -p app/Console/Commands/OmniTermSamples
cp vendor/pdphilip/omniterm/samples/Commands/*.php app/Console/Commands/OmniTermSamples/
```

## Available Samples

| Command | Description |
|---------|-------------|
| `omniterm:status-messages` | Status messages (success, error, warning, etc.) |
| `omniterm:data-tables` | Key-value tables with status indicators |
| `omniterm:visual-elements` | Boxes and horizontal rules |
| `omniterm:progress-bars` | Progress bar styles and options |
| `omniterm:spinners` | All 10 spinner animations |
| `omniterm:async-tasks` | Async task execution with feedback |
| `omniterm:interactive` | Interactive prompts with autocomplete |
| `omniterm:global-functions` | Using global functions without the trait |
| `omniterm:custom-colors` | Customizing status colors |
| `omniterm:full-demo` | Complete demo (simulated deployment) |

## Running Samples

After copying the commands, run them with Artisan:

```bash
# Run individual samples
php artisan omniterm:status-messages
php artisan omniterm:data-tables
php artisan omniterm:visual-elements
php artisan omniterm:progress-bars
php artisan omniterm:spinners
php artisan omniterm:async-tasks
php artisan omniterm:interactive
php artisan omniterm:global-functions
php artisan omniterm:custom-colors

# Run the full demo (shows all features)
php artisan omniterm:full-demo

# Run a specific spinner type
php artisan omniterm:spinners --type=sand
php artisan omniterm:spinners --type=material
```

## Sample Descriptions

### Status Messages
Demonstrates simple one-line status messages and detailed status blocks with title, details, and help text.

### Data Tables
Shows how to create formatted key-value tables with headers, basic rows, and status indicator rows (success, error, warning, etc.).

### Visual Elements
Displays rounded and square boxes with custom colors, plus all horizontal rule variants.

### Progress Bars
Runs through all four progress bar styles:
- Framed with color gradient
- Framed monochrome
- Simple with colors
- Simple monochrome

### Spinners
Cycles through all 10 spinner types:
- `dots`, `dots2`, `dots3`, `dotsCircle`
- `sand`, `clock`, `material`, `pong`
- `progress`, `progressLoader`

Use `--type=NAME` to demo a specific spinner.

### Async Tasks
Shows async task execution with different outcomes (success, warning, error) and how to use returned data.

### Interactive
Demonstrates asking questions with and without autocomplete options.

### Global Functions
Shows how to use `render()`, `liveRender()`, and `terminal()` without the OmniTerm trait.

### Custom Colors
Demonstrates overriding default status colors with custom color schemes.

### Full Demo
A comprehensive example simulating an application deployment, combining:
- Status checks with data tables
- Async tasks with spinners
- Progress bars
- Status messages
- Visual elements
