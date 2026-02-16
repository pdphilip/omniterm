<?php

declare(strict_types=1);

namespace OmniTerm\Browser;

use OmniTerm\Async\SplitBrowser;

class SplitBrowserRenderer
{
    public function __invoke(SplitBrowser $prompt): string
    {
        if ($prompt->state === 'submit') {
            return $this->renderSubmitted($prompt);
        }

        return $this->renderActive($prompt);
    }

    private function renderActive(SplitBrowser $prompt): string
    {
        $totalWidth = $prompt->terminal()->cols();
        $leftWidth = $this->leftPaneWidth($totalWidth);
        $rightWidth = $totalWidth - $leftWidth - 3; // 3 border chars: │ │ │

        $visible = $prompt->visible();
        $detail = $prompt->detail();
        $rows = $prompt->scroll;

        $lines = [];
        $lines[] = $this->topBorder($prompt->label, $leftWidth, $rightWidth);

        for ($i = 0; $i < $rows; $i++) {
            $itemIndex = $prompt->firstVisible + $i;

            $leftContent = $this->renderLeftCell($prompt, $itemIndex, $leftWidth);
            $rightContent = $this->renderRightCell($detail, $i, $rightWidth);

            $lines[] = "\e[90m│\e[0m{$leftContent}\e[90m│\e[0m{$rightContent}\e[90m│\e[0m";
        }

        $lines[] = $this->bottomBorder($leftWidth, $rightWidth);
        $lines[] = "  \e[2m{$prompt->hint}\e[0m";

        return implode(PHP_EOL, $lines);
    }

    private function renderSubmitted(SplitBrowser $prompt): string
    {
        $value = $prompt->value();

        if ($value === null) {
            return "\e[2mCancelled.\e[0m";
        }

        return "\e[36m{$prompt->label}\e[0m \e[2m›\e[0m \e[1m{$value}\e[0m";
    }

    private function renderLeftCell(SplitBrowser $prompt, int $itemIndex, int $width): string
    {
        if (! isset($prompt->items[$itemIndex])) {
            return str_repeat(' ', $width);
        }

        $item = $prompt->items[$itemIndex];
        $isHighlighted = $itemIndex === $prompt->highlighted;

        if ($isHighlighted) {
            $text = " \e[36;1m› {$item}\e[0m";
        } else {
            $text = " \e[2m  {$item}\e[0m";
        }

        return $this->pad($text, $width);
    }

    private function renderRightCell(array $detail, int $lineIndex, int $width): string
    {
        if (! isset($detail[$lineIndex])) {
            return str_repeat(' ', $width);
        }

        $line = " {$detail[$lineIndex]}";

        return $this->pad($line, $width);
    }

    private function topBorder(string $label, int $leftWidth, int $rightWidth): string
    {
        $labelText = " {$label} ";
        $labelLen = mb_strwidth($labelText);
        $leftFill = max(0, $leftWidth - $labelLen);

        $left = "\e[90m╭{$labelText}".str_repeat('─', $leftFill).'┬'.str_repeat('─', $rightWidth)."╮\e[0m";

        return $left;
    }

    private function bottomBorder(int $leftWidth, int $rightWidth): string
    {
        return "\e[90m╰".str_repeat('─', $leftWidth).'┴'.str_repeat('─', $rightWidth)."╯\e[0m";
    }

    private function leftPaneWidth(int $totalWidth): int
    {
        $width = (int) ($totalWidth * 0.4);

        return max(20, min(50, $width));
    }

    private function pad(string $text, int $width): string
    {
        $visible = mb_strwidth($this->stripAnsi($text));
        $padding = max(0, $width - $visible);

        return $text.str_repeat(' ', $padding);
    }

    private function stripAnsi(string $text): string
    {
        return (string) preg_replace('/\e\[[0-9;]*m/', '', $text);
    }
}
