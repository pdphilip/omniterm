<?php

namespace OmniTerm\Samples;

use Illuminate\Console\Command;

use function OmniTerm\liveRender;
use function OmniTerm\render;
use function OmniTerm\terminal;

/**
 * Sample: Global Functions
 *
 * Demonstrates using OmniTerm's global functions without the trait.
 *
 * Run: php artisan omniterm:global-functions
 */
class GlobalFunctionsCommand extends Command
{
    protected $signature = 'omniterm:global-functions';

    protected $description = 'OmniTerm Sample: Global Helper Functions';

    public function handle(): int
    {
        // Terminal info
        $width = terminal()->getWidth();
        $height = terminal()->getHeight();

        render(view('omniterm::elements.title-bar', ['t' => '', 'color' => 'cyan']));
        render(view('omniterm::elements.title-bar', ['t' => 'Global Functions', 'color' => 'cyan']));
        render(view('omniterm::elements.title-bar', ['t' => '', 'color' => 'cyan']));
        $this->newLine();

        // Terminal dimensions
        render('<div class="mx-1"><span class="text-amber-500">Terminal Size:</span> '.$width.' x '.$height.'</div>');
        $this->newLine();

        // render() - Basic HTML rendering
        render('<div class="mx-1 text-sky-500">1. render() - Basic HTML to terminal:</div>');
        $this->newLine();

        render('<div class="mx-2 text-green-500 font-bold">  Bold green text</div>');
        render('<div class="mx-2"><span class="bg-red-600 text-white px-1">ERROR</span> With a badge</div>');
        render('<div class="mx-2"><span class="text-yellow-500">Warning:</span> <span class="text-gray">Some message</span></div>');

        $this->newLine();

        // Flexbox layout
        render('<div class="mx-1 text-sky-500">2. render() - Flexbox layouts:</div>');
        $this->newLine();

        render('
            <div class="flex mx-2">
                <span class="text-emerald-500">Left</span>
                <span class="flex-1 text-center text-amber-500">Center</span>
                <span class="text-rose-500">Right</span>
            </div>
        ');

        $this->newLine();

        render('
            <div class="flex mx-2">
                <span class="font-bold">Status</span>
                <span class="flex-1 content-repeat-[.] text-gray"></span>
                <span class="text-emerald-500 font-bold">OK</span>
            </div>
        ');

        $this->newLine();

        // liveRender() - Live updating display
        render('<div class="mx-1 text-sky-500">3. liveRender() - Live updating display:</div>');
        $this->newLine();

        $live = liveRender('<div class="mx-2">  Countdown: <span class="text-amber-500">Starting...</span></div>');

        for ($i = 5; $i >= 0; $i--) {
            usleep(500000); // 500ms
            $color = $i > 2 ? 'text-amber-500' : 'text-rose-500';
            $live->reRender("<div class=\"mx-2\">  Countdown: <span class=\"{$color} font-bold\">{$i}</span></div>");
        }

        $live->reRender('<div class="mx-2">  Countdown: <span class="text-emerald-500 font-bold">Done!</span></div>');

        $this->newLine();

        // Progress with liveRender
        render('<div class="mx-1 text-sky-500">4. liveRender() - Custom progress:</div>');
        $this->newLine();

        $live = liveRender();

        for ($i = 0; $i <= 20; $i++) {
            $filled = str_repeat('█', $i);
            $empty = str_repeat('░', 20 - $i);
            $percent = $i * 5;

            $color = match (true) {
                $percent < 30 => 'text-rose-500',
                $percent < 60 => 'text-amber-500',
                $percent < 90 => 'text-sky-500',
                default => 'text-emerald-500',
            };

            $live->reRender("
                <div class=\"mx-2 flex\">
                    <span class=\"{$color}\">{$filled}</span>
                    <span class=\"text-gray\">{$empty}</span>
                    <span class=\"ml-1\">{$percent}%</span>
                </div>
            ");

            usleep(100000); // 100ms
        }

        $this->newLine();

        // Summary
        render('<div class="mx-1 text-gray">────────────────────────────────────</div>');
        render('<div class="mx-1 text-emerald-500 font-bold">Global functions demo complete!</div>');

        return Command::SUCCESS;
    }
}
