<?php

declare(strict_types=1);

namespace OmniTerm\Prompts\Renderers;

use Laravel\Prompts\MultiSelectPrompt;
use Laravel\Prompts\Themes\Contracts\Scrolling;

class MultiSelectPromptRenderer extends OmniPromptRenderer implements Scrolling
{
    public function __invoke(MultiSelectPrompt $prompt): string
    {
        return match ($prompt->state) {
            'submit' => $this->submitted($prompt->label, implode(', ', $prompt->labels())),
            'cancel' => $this->submitted($prompt->label, implode(', ', $prompt->labels())),
            'error' => $this->frame([
                $this->header($prompt->label),
                $this->options($prompt),
                $this->errorLine($prompt->error),
            ]),
            default => $this->frame([
                $this->header($prompt->label),
                $this->options($prompt),
                $this->hintLine($prompt->hint !== '' ? $prompt->hint : 'Space to toggle, Enter to confirm.'),
            ]),
        };
    }

    protected function options(MultiSelectPrompt $prompt): string
    {
        $keys = array_keys($prompt->options);
        $isList = array_is_list($prompt->options);
        $selected = $prompt->value();
        $rows = '';

        foreach ($prompt->visible() as $key => $label) {
            $index = array_search($key, $keys, true);
            $value = $isList ? $prompt->options[$index] : $keys[$index];
            $isChecked = in_array($value, $selected, true);
            $isActive = $prompt->highlighted === $index;
            $label = e((string) $label);

            $pointer = $isActive
                ? '<span class="text-emerald-500">&#8250;</span>'
                : '<span class="text-stone-600"> </span>';
            $box = $isChecked
                ? '<span class="text-emerald-500">&#9642;</span>'
                : '<span class="text-stone-600">&#9643;</span>';
            $text = $isActive
                ? "<span>{$label}</span>"
                : "<span class=\"text-stone-400\">{$label}</span>";

            $rows .= "<div class=\"flex space-x-1\">{$pointer}{$box}{$text}</div>";
        }

        return $this->omni("<div class=\"mx-1 pl-1\">{$rows}</div>");
    }

    public function reservedLines(): int
    {
        return 4;
    }
}
