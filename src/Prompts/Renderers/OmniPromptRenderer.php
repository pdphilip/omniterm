<?php

declare(strict_types=1);

namespace OmniTerm\Prompts\Renderers;

use Laravel\Prompts\Themes\Default\Renderer;
use OmniTerm\Rendering\Renderer as OmniRenderer;

/**
 * Base for OmniTerm's prompt renderers. The interactive value/cursor is left to
 * laravel/prompts (it already produces ANSI for those); everything around it -
 * label, options, chips, errors, hints - is emitted as OmniTerm HTML and
 * compiled to ANSI so the prompts match the rest of the toolkit's aesthetic.
 */
abstract class OmniPromptRenderer extends Renderer
{
    protected function omni(string $html): string
    {
        return (new OmniRenderer)->toAnsi($html);
    }

    protected function omniInline(string $html): string
    {
        return rtrim($this->omni($html), "\n");
    }

    protected function header(string $label, string $chip = ''): string
    {
        $label = e($label);

        return $this->omni(
            '<div class="mx-1"><div class="flex space-x-1">'
            ."<span class=\"font-bold\">{$label}</span>{$chip}"
            .'</div></div>'
        );
    }

    protected function caretLine(string $valueAnsi, string $color = 'emerald'): string
    {
        $caret = $this->omniInline("<span class=\"text-{$color}-500 font-bold\">&#10095;</span>");

        return ' '.$caret.' '.$valueAnsi;
    }

    protected function defaultChip(int|string|null $default): string
    {
        if ($default === null || $default === '') {
            return '';
        }

        $value = e((string) $default);

        return "<span class=\"text-stone-400\">[{$value}]</span>";
    }

    /**
     * @param  array<int, int|string>  $options
     */
    protected function optionsChip(array $options): string
    {
        if ($options === []) {
            return '';
        }

        $list = e(implode('/', array_map(static fn ($o) => (string) $o, $options)));

        return "<span class=\"text-stone-400\">[<span class=\"text-emerald-500\">{$list}</span>]</span>";
    }

    protected function errorLine(string $error): string
    {
        $error = e($error);

        return $this->omni("<div class=\"mx-1\"><span class=\"text-rose-500\">&#9888; {$error}</span></div>");
    }

    protected function hintLine(string $hint): string
    {
        if ($hint === '') {
            return '';
        }

        $hint = e($hint);

        return $this->omni("<div class=\"mx-1\"><span class=\"text-stone-500\">{$hint}</span></div>");
    }

    protected function submitted(string $label, string $value): string
    {
        $label = e($label);
        $value = e($value === '' ? '—' : $value);

        return $this->omni(
            '<div class="mx-1 flex space-x-1">'
            ."<span class=\"text-stone-400\">{$label}</span>"
            ."<span class=\"text-emerald-500 font-bold\">{$value}</span>"
            .'</div>'
        );
    }

    /**
     * Join the frame lines, dropping any that came back empty.
     *
     * @param  array<int, string>  $lines
     */
    protected function frame(array $lines): string
    {
        return implode(PHP_EOL, array_filter($lines, static fn ($line) => $line !== ''));
    }
}
