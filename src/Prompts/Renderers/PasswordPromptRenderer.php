<?php

declare(strict_types=1);

namespace OmniTerm\Prompts\Renderers;

use Laravel\Prompts\PasswordPrompt;

class PasswordPromptRenderer extends OmniPromptRenderer
{
    public function __invoke(PasswordPrompt $prompt): string
    {
        $width = $prompt->terminal()->cols() - 4;

        return match ($prompt->state) {
            'submit' => $this->submitted($prompt->label, str_repeat('•', mb_strlen($prompt->value()))),
            'cancel' => $this->submitted($prompt->label, ''),
            'error' => $this->frame([
                $this->header($prompt->label),
                $this->caretLine($prompt->maskedWithCursor($width), 'rose'),
                $this->errorLine($prompt->error),
            ]),
            default => $this->frame([
                $this->header($prompt->label),
                $this->caretLine($prompt->maskedWithCursor($width)),
                $this->hintLine($prompt->hint),
            ]),
        };
    }
}
