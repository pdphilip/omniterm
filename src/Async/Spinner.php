<?php

declare(strict_types=1);

namespace OmniTerm\Async;

use OmniTerm\Helpers\Partials\AsciiHelper;

enum Spinner: string
{
    case Dots = 'dots';
    case Dots2 = 'dots2';
    case Dots3 = 'dots3';
    case DotsCircle = 'dotsCircle';
    case Sand = 'sand';
    case Clock = 'clock';
    case Material = 'material';
    case Pong = 'pong';
    case Progress = 'progress';
    case ProgressLoader = 'progressLoader';
    case Loader = 'loader';

    public function view(): string
    {
        return match ($this) {
            self::Loader => 'omniterm::loaders.loading',
            default => 'omniterm::loaders.spinner',
        };
    }

    public function frames(): array
    {
        return match ($this) {
            self::Loader => [],
            default => AsciiHelper::loadSpinner($this->value),
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Dots => 'Classic braille dots',
            self::Dots2 => 'Double braille pattern',
            self::Dots3 => 'Flowing dots',
            self::DotsCircle => 'Circular dot pattern',
            self::Sand => 'Filling hourglass effect',
            self::Clock => 'Clock face animation',
            self::Material => 'Material design loader',
            self::Pong => 'Bouncing ball',
            self::Progress => 'Progress indicator',
            self::ProgressLoader => 'Looping progress',
            self::Loader => 'Animated color blocks',
        };
    }
}
