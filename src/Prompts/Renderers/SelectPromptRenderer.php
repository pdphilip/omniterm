<?php

declare(strict_types=1);

namespace OmniTerm\Prompts\Renderers;

use Laravel\Prompts\SelectPrompt;
use Laravel\Prompts\Themes\Contracts\Scrolling;

class SelectPromptRenderer extends OmniPromptRenderer implements Scrolling
{
    public function __invoke(SelectPrompt $prompt): string
    {
        return match ($prompt->state) {
            'submit' => $this->submitted($prompt->label, (string) $prompt->label()),
            'cancel' => $this->submitted($prompt->label, (string) $prompt->label()),
            'error' => $this->frame([
                $this->header($prompt->label),
                $this->options($prompt),
                $this->errorLine($prompt->error),
            ]),
            default => $this->frame([
                $this->header($prompt->label),
                $this->options($prompt),
                $this->hintLine($prompt->hint),
            ]),
        };
    }

    protected function options(SelectPrompt $prompt): string
    {
        $keys = array_keys($prompt->options);
        $rows = '';

        foreach ($prompt->visible() as $key => $label) {
            $index = array_search($key, $keys, true);
            $label = e((string) $label);

            $rows .= $prompt->highlighted === $index
                ? '<div class="flex space-x-1">'
                    .'<span class="text-emerald-500">&#8250;</span>'
                    .'<span class="text-emerald-500">&#9679;</span>'
                    ."<span>{$label}</span></div>"
                : '<div class="flex space-x-1">'
                    .'<span class="text-stone-600"> </span>'
                    .'<span class="text-stone-600">&#9675;</span>'
                    ."<span class=\"text-stone-400\">{$label}</span></div>";
        }

        return $this->omni("<div class=\"mx-1 pl-1\">{$rows}</div>");
    }

    public function reservedLines(): int
    {
        return 4;
    }
}
