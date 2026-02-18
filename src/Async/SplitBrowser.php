<?php

declare(strict_types=1);

namespace OmniTerm\Async;

use Closure;
use Laravel\Prompts\Concerns\Scrolling;
use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;
use OmniTerm\Browser\SplitBrowserRenderer;
use OmniTerm\OmniTerm;
use OmniTerm\Rendering\Renderer;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Terminal;

class SplitBrowser extends Prompt
{
    use Scrolling;

    public bool $cancelled = false;

    /** @var array<int, string> */
    public array $items = [];

    protected array $entries;

    protected OmniTerm $omni;

    protected array $detailCache = [];

    public function __construct(
        public string $label,
        array $items,
        ?OmniTerm $omni = null,
        public int $scroll = 12,
        public string $hint = '',
    ) {
        $this->entries = $items;
        $this->items = array_keys($items);
        $this->omni = $omni ?? new OmniTerm;

        $this->required = false;
        $this->validate = null;

        if ($this->hint === '') {
            $this->hint = '↑/↓ Navigate  Enter Select  q/Esc Exit';
        }

        $this->initializeScrolling(0);

        $this->on('key', function (string $key) {
            if ($key === Key::UP || $key === Key::UP_ARROW) {
                $this->highlightPrevious(count($this->items));

                return;
            }

            if ($key === Key::DOWN || $key === Key::DOWN_ARROW) {
                $this->highlightNext(count($this->items));

                return;
            }

            if ($key === Key::ENTER) {
                $this->submit();

                return;
            }

            if ($key === Key::ESCAPE || $key === 'q') {
                $this->cancelled = true;
                $this->submit();
            }
        });
    }

    public function value(): mixed
    {
        if ($this->cancelled) {
            return null;
        }

        return $this->items[$this->highlighted] ?? null;
    }

    public function detail(): array
    {
        if ($this->highlighted === null) {
            return [];
        }

        $label = $this->items[$this->highlighted];

        if (isset($this->detailCache[$label])) {
            return $this->detailCache[$label];
        }

        $value = $this->entries[$label];

        if ($value instanceof Closure) {
            $lines = $this->captureOutput($value);
        } elseif (is_array($value) && ! array_is_list($value)) {
            $lines = $this->formatAssocArray($value);
        } elseif (is_array($value)) {
            $lines = $value;
        } else {
            $lines = [(string) $value];
        }

        $this->detailCache[$label] = $lines;

        return $lines;
    }

    public function visible(): array
    {
        return array_slice($this->items, $this->firstVisible, $this->scroll, true);
    }

    public static function browse(
        string $label,
        array $items,
        ?OmniTerm $omni = null,
        int $scroll = 12,
        string $hint = '',
    ): mixed {
        return (new self($label, $items, $omni, $scroll, $hint))->prompt();
    }

    protected function captureOutput(Closure $closure): array
    {
        $rightWidth = $this->computeRightPaneWidth();

        $oldColumns = getenv('COLUMNS');
        putenv('COLUMNS='.$rightWidth);

        $buffer = new BufferedOutput;
        Renderer::renderUsing($buffer);

        try {
            $closure($this->omni);
        } finally {
            Renderer::renderUsing(null);
            if ($oldColumns === false) {
                putenv('COLUMNS');
            } else {
                putenv('COLUMNS='.$oldColumns);
            }
        }

        $output = $buffer->fetch();
        if ($output === '') {
            return [];
        }

        return explode("\n", rtrim($output, "\n"));
    }

    protected function formatAssocArray(array $data): array
    {
        $keys = array_keys($data);
        $maxKeyLen = max(array_map(fn ($k) => mb_strwidth((string) $k), $keys));

        $lines = [];
        foreach ($data as $key => $value) {
            $paddedKey = str_pad((string) $key, $maxKeyLen);
            $lines[] = "\e[1m{$paddedKey}\e[0m  {$value}";
        }

        return $lines;
    }

    protected function computeRightPaneWidth(): int
    {
        $totalWidth = (new Terminal)->getWidth();
        $leftWidth = max(20, min(50, (int) ($totalWidth * 0.4)));

        return $totalWidth - $leftWidth - 3;
    }

    protected function renderTheme(): string
    {
        $renderer = new SplitBrowserRenderer;

        return $renderer($this);
    }

    protected function reduceScrollingToFitTerminal(): void
    {
        $reserved = 4;

        $this->scroll = max(1, min($this->scroll, $this->terminal()->lines() - $reserved));
    }
}
