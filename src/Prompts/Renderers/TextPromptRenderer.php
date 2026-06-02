<?php

declare(strict_types=1);

namespace OmniTerm\Prompts\Renderers;

use Laravel\Prompts\TextPrompt;

class TextPromptRenderer extends OmniPromptRenderer
{
    public function __invoke(TextPrompt $prompt): string
    {
        $width = $prompt->terminal()->cols() - 4;

        return match ($prompt->state) {
            'submit' => $this->submitted($prompt->label, $prompt->value()),
            'cancel' => $this->submitted($prompt->label, $prompt->value()),
            'error' => $this->frame([
                $this->header($prompt->label),
                $this->caretLine($prompt->valueWithCursor($width), 'rose'),
                $this->errorLine($prompt->error),
            ]),
            default => $this->frame([
                $this->header($prompt->label),
                $this->caretLine($prompt->valueWithCursor($width)),
                $this->hintLine($prompt->hint),
            ]),
        };
    }
}
