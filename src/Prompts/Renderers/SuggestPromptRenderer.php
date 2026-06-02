<?php

declare(strict_types=1);

namespace OmniTerm\Prompts\Renderers;

use Laravel\Prompts\SuggestPrompt;
use Laravel\Prompts\Themes\Contracts\Scrolling;

class SuggestPromptRenderer extends OmniPromptRenderer implements Scrolling
{
    public function __invoke(SuggestPrompt $prompt): string
    {
        $width = $prompt->terminal()->cols() - 4;

        return match ($prompt->state) {
            'submit' => $this->submitted($prompt->label, $prompt->value()),
            'cancel' => $this->submitted($prompt->label, $prompt->value()),
            'error' => $this->frame([
                $this->header($prompt->label),
                $this->caretLine($prompt->valueWithCursor($width), 'rose'),
                $this->matches($prompt),
                $this->errorLine($prompt->error),
            ]),
            default => $this->frame([
                $this->header($prompt->label),
                $this->caretLine($prompt->valueWithCursor($width)),
                $this->matches($prompt),
                $this->hintLine($prompt->hint),
            ]),
        };
    }

    protected function matches(SuggestPrompt $prompt): string
    {
        $visible = $prompt->visible();

        if ($visible === []) {
            return '';
        }

        $rows = '';
        foreach ($visible as $index => $label) {
            $label = e((string) $label);
            $rows .= $prompt->highlighted === $index
                ? "<div class=\"flex space-x-1\"><span class=\"text-emerald-500\">&#8250;</span><span class=\"text-emerald-500\">{$label}</span></div>"
                : "<div class=\"flex space-x-1\"><span class=\"text-stone-600\"> </span><span class=\"text-stone-400\">{$label}</span></div>";
        }

        return $this->omni("<div class=\"mx-1 pl-2\">{$rows}</div>");
    }

    public function reservedLines(): int
    {
        return 5;
    }
}
