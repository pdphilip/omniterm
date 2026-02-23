<?php

declare(strict_types=1);

namespace OmniTerm\Browser;

use OmniTerm\Async\SplitBrowser;
use OmniTerm\Helpers\Partials\AsciiHelper;
use OmniTerm\Rendering\Ansi;

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
        $rightWidth = $totalWidth - $leftWidth - 3;

        $visible = $prompt->visible();
        $detail = $prompt->detail();
        $rows = $prompt->scroll;

        $box = AsciiHelper::roundedTable();
        $dim = Ansi::dim();
        $reset = Ansi::reset();
        $v = $box['v'];

        $lines = [];
        $lines[] = $this->topBorder($box, $prompt->label, $leftWidth, $rightWidth);

        for ($i = 0; $i < $rows; $i++) {
            $itemIndex = $prompt->firstVisible + $i;

            $leftContent = $this->renderLeftCell($prompt, $itemIndex, $leftWidth);
            $rightContent = $this->renderRightCell($detail, $i, $rightWidth);

            $lines[] = "{$dim}{$v}{$reset}{$leftContent}{$dim}{$v}{$reset}{$rightContent}{$dim}{$v}{$reset}";
        }

        $lines[] = $this->bottomBorder($box, $leftWidth, $rightWidth);
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

        return $this->fitToWidth($text, $width);
    }

    private function renderRightCell(array $detail, int $lineIndex, int $width): string
    {
        if (! isset($detail[$lineIndex])) {
            return str_repeat(' ', $width);
        }

        $line = " {$detail[$lineIndex]}";

        return $this->fitToWidth($line, $width);
    }

    private function topBorder(array $box, string $label, int $leftWidth, int $rightWidth): string
    {
        $labelText = " {$label} ";
        $leftFill = max(0, $leftWidth - mb_strwidth($labelText));
        $h = $box['h'];

        return Ansi::dim()."{$box['tl']}{$labelText}".str_repeat($h, $leftFill)."{$box['top']}".str_repeat($h, $rightWidth)."{$box['tr']}".Ansi::reset();
    }

    private function bottomBorder(array $box, int $leftWidth, int $rightWidth): string
    {
        $h = $box['h'];

        return Ansi::dim()."{$box['bl']}".str_repeat($h, $leftWidth)."{$box['bottom']}".str_repeat($h, $rightWidth)."{$box['br']}".Ansi::reset();
    }

    private function leftPaneWidth(int $totalWidth): int
    {
        $width = (int) ($totalWidth * 0.4);

        return max(20, min(50, $width));
    }

    private function fitToWidth(string $text, int $width): string
    {
        $visible = mb_strwidth($this->stripAnsi($text));

        if ($visible <= $width) {
            return $text.str_repeat(' ', $width - $visible);
        }

        return $this->ansiTruncate($text, $width);
    }

    private function ansiTruncate(string $text, int $maxWidth): string
    {
        $visible = 0;
        $result = '';
        $i = 0;
        $bytes = strlen($text);

        while ($i < $bytes && $visible < $maxWidth) {
            if ($text[$i] === "\e") {
                $end = strpos($text, 'm', $i);
                if ($end !== false) {
                    $result .= substr($text, $i, $end - $i + 1);
                    $i = $end + 1;
                } else {
                    break;
                }
            } else {
                $char = mb_substr(substr($text, $i), 0, 1);
                $charWidth = mb_strwidth($char);
                $charBytes = strlen($char);

                if ($visible + $charWidth > $maxWidth) {
                    break;
                }

                $result .= $char;
                $visible += $charWidth;
                $i += $charBytes;
            }
        }

        return $result."\e[0m".str_repeat(' ', max(0, $maxWidth - $visible));
    }

    private function stripAnsi(string $text): string
    {
        return (string) preg_replace('/\e\[[0-9;]*m/', '', $text);
    }
}
