<?php

declare(strict_types=1);

namespace OmniTerm\Async;

use Closure;
use Laravel\Prompts\Concerns\Scrolling;
use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;
use OmniTerm\Browser\SplitBrowserRenderer;

class SplitBrowser extends Prompt
{
    use Scrolling;

    public bool $cancelled = false;

    protected array $detailCache = [];

    public function __construct(
        public string $label,
        public array $items,
        public Closure $detailCallback,
        public int $scroll = 12,
        public string $hint = '',
    ) {
        $this->required = false;
        $this->validate = null;

        if ($this->hint === '') {
            $this->hint = '↑/↓ Navigate  Enter Select  q/Esc Exit';
        }

        $this->items = array_values($this->items);

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

        if (isset($this->detailCache[$this->highlighted])) {
            return $this->detailCache[$this->highlighted];
        }

        $item = $this->items[$this->highlighted];
        $result = ($this->detailCallback)($item);
        $this->detailCache[$this->highlighted] = $result;

        return $result;
    }

    public function visible(): array
    {
        return array_slice($this->items, $this->firstVisible, $this->scroll, true);
    }

    public static function browse(
        string $label,
        array $items,
        Closure $detail,
        int $scroll = 12,
        string $hint = '',
    ): mixed {
        return (new self($label, $items, $detail, $scroll, $hint))->prompt();
    }

    protected function renderTheme(): string
    {
        $renderer = new SplitBrowserRenderer;

        return $renderer($this);
    }

    protected function reduceScrollingToFitTerminal(): void
    {
        $reserved = 4; // top border + bottom border + hint + buffer

        $this->scroll = max(1, min($this->scroll, $this->terminal()->lines() - $reserved));
    }
}
