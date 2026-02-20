<?php

declare(strict_types=1);

namespace OmniTerm\Helpers;

use OmniTerm\Helpers\Partials\AsciiHelper;
use OmniTerm\LiveHtmlRenderer;
use OmniTerm\Rendering\Colors;

class ProgressBar
{
    protected LiveHtmlRenderer $renderer;

    protected int $current = 0;

    protected int $screenWidth;

    protected bool $isFramed = false;

    protected string $colorMode = 'static';

    protected string $staticColor = 'sky';

    protected array $stepColors = ['red', 'orange', 'amber', 'yellow', 'lime', 'teal', 'cyan', 'sky'];

    protected string $gradientFrom = 'amber';

    protected string $gradientTo = 'emerald';

    protected string $doneColor = 'emerald';

    public function __construct(protected int $total)
    {
        $this->total = max(1, $total);
        $this->renderer = new LiveHtmlRenderer;
        $this->screenWidth = $this->renderer->getScreenWidth();
    }

    public function framed(): static
    {
        $this->isFramed = true;

        return $this;
    }

    public function color(string $color): static
    {
        $this->colorMode = 'static';
        $this->staticColor = $color;

        return $this;
    }

    public function steps(?array $colors = null): static
    {
        $this->colorMode = 'steps';
        if ($colors !== null) {
            $this->stepColors = $colors;
        }

        return $this;
    }

    public function gradient(?string $from = null, ?string $to = null): static
    {
        $this->colorMode = 'gradient';
        if ($from !== null) {
            $this->gradientFrom = $from;
        }
        if ($to !== null) {
            $this->gradientTo = $to;
        }

        return $this;
    }

    public function completeColor(string $color): static
    {
        $this->doneColor = $color;

        return $this;
    }

    public function start(): void
    {
        $this->renderer->reRender($this->buildHtml());
    }

    public function advance(int $by = 1): void
    {
        $this->current = min($this->current + $by, $this->total);
        $this->renderer->reRender($this->buildHtml());
    }

    public function finish(): void
    {
        $this->current = $this->total;
        $this->renderer->reRender($this->buildHtml());
    }

    protected function buildHtml(): string
    {
        $dimensions = AsciiHelper::progressBarDimensions($this->screenWidth, $this->total);
        $length = $dimensions['length'];
        $valuesWidth = $dimensions['valuesWidth'];
        $progress = (int) floor($this->current / $this->total * $length);
        $remaining = $length - $progress;
        $percentage = (int) round(($this->current / $this->total) * 100);
        $colors = $this->resolveColors($percentage);

        $view = $this->isFramed ? 'omniterm::progress.framed' : 'omniterm::progress.simple';

        return (string) view($view, [
            'barBg' => $colors['barBg'],
            'barFg' => $colors['barFg'],
            'labelFg' => $colors['labelFg'],
            'progress' => $progress,
            'remaining' => $remaining,
            'length' => $length,
            'valuesWidth' => $valuesWidth,
            'current' => number_format($this->current),
            'max' => number_format($this->total),
            'percentage' => $percentage,
        ]);
    }

    protected function resolveColors(int $percentage): array
    {
        if ($this->current >= $this->total) {
            return $this->doneColors();
        }

        return match ($this->colorMode) {
            'gradient' => $this->gradientColors($percentage),
            'steps' => $this->stepColors($percentage),
            default => $this->staticColors(),
        };
    }

    protected function doneColors(): array
    {
        $c = $this->doneColor;
        $labelFg = $this->colorMode === 'gradient' ? "text-{$c}-300" : '';

        return [
            'barBg' => "bg-{$c}-600",
            'barFg' => "text-{$c}-300",
            'labelFg' => $labelFg,
        ];
    }

    protected function staticColors(): array
    {
        $c = $this->staticColor;

        return [
            'barBg' => "bg-{$c}-600",
            'barFg' => "text-{$c}-400",
            'labelFg' => '',
        ];
    }

    protected function stepColors(int $percentage): array
    {
        $count = count($this->stepColors);
        $index = min((int) floor(($percentage / 100) * $count), $count - 1);
        $c = $this->stepColors[$index];

        return [
            'barBg' => "bg-{$c}-600",
            'barFg' => "text-{$c}-400",
            'labelFg' => '',
        ];
    }

    protected function gradientColors(int $percentage): array
    {
        $barRgb = Colors::colorAt($percentage, $this->gradientFrom, 600, $this->gradientTo, 600);
        $fgRgb = Colors::colorAt($percentage, $this->gradientFrom, 400, $this->gradientTo, 400);
        $labelRgb = Colors::colorAt($percentage, $this->gradientFrom, 500, $this->gradientTo, 500);

        return [
            'barBg' => "bg-[{$barRgb[0]},{$barRgb[1]},{$barRgb[2]}]",
            'barFg' => "text-[{$fgRgb[0]},{$fgRgb[1]},{$fgRgb[2]}]",
            'labelFg' => "text-[{$labelRgb[0]},{$labelRgb[1]},{$labelRgb[2]}]",
        ];
    }
}
