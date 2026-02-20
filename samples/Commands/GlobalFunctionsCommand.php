<?php

namespace OmniTerm\Samples;

use Illuminate\Console\Command;
use OmniTerm\HasOmniTerm;

class GlobalFunctionsCommand extends Command
{
    use HasOmniTerm;

    protected $signature = 'omniterm:global-functions';

    protected $description = 'OmniTerm Sample: Render & Live View Functions';

    public function handle(): int
    {
        $width = $this->omni->terminal()->getWidth();
        $height = $this->omni->terminal()->getHeight();

        $this->omni->titleBar('Render Functions', 'cyan');
        $this->omni->newLine();

        $this->omni->render('<div class="mx-1"><span class="text-amber-500">Terminal Size:</span> '.$width.' x '.$height.'</div>');
        $this->omni->newLine();

        $this->omni->render('<div class="mx-1 text-sky-500">1. render() - Basic HTML to terminal:</div>');
        $this->omni->newLine();

        $this->omni->render('<div class="mx-2 text-green-500 font-bold">  Bold green text</div>');
        $this->omni->render('<div class="mx-2"><span class="bg-red-600 text-white px-1">ERROR</span> With a badge</div>');
        $this->omni->render('<div class="mx-2"><span class="text-yellow-500">Warning:</span> <span class="text-gray">Some message</span></div>');

        $this->omni->newLine();

        $this->omni->render('<div class="mx-1 text-sky-500">2. render() - Flexbox layouts:</div>');
        $this->omni->newLine();

        $this->omni->render('
            <div class="flex mx-2">
                <span class="text-emerald-500">Left</span>
                <span class="flex-1 text-center text-amber-500">Center</span>
                <span class="text-rose-500">Right</span>
            </div>
        ');

        $this->omni->newLine();

        $this->omni->render('
            <div class="flex mx-2">
                <span class="font-bold">Status</span>
                <span class="flex-1 content-repeat-[.] text-gray"></span>
                <span class="text-emerald-500 font-bold">OK</span>
            </div>
        ');

        $this->omni->newLine();

        $this->omni->render('<div class="mx-1 text-sky-500">3. liveView() - Live updating display:</div>');
        $this->omni->newLine();

        $live = $this->omni->liveView('<div class="mx-2">  Countdown: <span class="text-amber-500">Starting...</span></div>');

        for ($i = 5; $i >= 0; $i--) {
            usleep(500000);
            $color = $i > 2 ? 'text-amber-500' : 'text-rose-500';
            $live->reRender("<div class=\"mx-2\">  Countdown: <span class=\"{$color} font-bold\">{$i}</span></div>");
        }

        $live->reRender('<div class="mx-2">  Countdown: <span class="text-emerald-500 font-bold">Done!</span></div>');
        $this->omni->endLiveView();

        $this->omni->newLine();

        $this->omni->render('<div class="mx-1 text-sky-500">4. liveView() - Custom progress:</div>');
        $this->omni->newLine();

        $live = $this->omni->liveView();

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

            usleep(100000);
        }

        $this->omni->endLiveView();
        $this->omni->newLine();

        $this->omni->render('<div class="mx-1 text-gray">────────────────────────────────────</div>');
        $this->omni->render('<div class="mx-1 text-emerald-500 font-bold">Render functions demo complete!</div>');

        return Command::SUCCESS;
    }
}
