<?php

namespace OmniTerm\Samples;

use Illuminate\Console\Command;
use OmniTerm\HasOmniTerm;

/**
 * Sample: Tailwind Classes
 *
 * Demonstrates every Tailwind CSS class supported by the OmniTerm renderer.
 *
 * Run: php artisan omniterm:tailwind-classes
 */
class TailwindClassesCommand extends Command
{
    use HasOmniTerm;

    protected $signature = 'omniterm:tailwind-classes';

    protected $description = 'OmniTerm Sample: Every supported Tailwind CSS class';

    public function handle(): int
    {
        $this->omni->titleBar('Tailwind CSS Class Reference', 'violet');

        $this->section('flex — Flex Container');
        $this->omni->line('<div class="flex"><span class="w-20 text-zinc-500">Child A</span><span class="w-20 text-zinc-500">Child B</span><span class="w-20 text-zinc-500">Child C</span></div>');

        $this->section('flex-1 — Fill Remaining Space');
        $this->omni->line('<div class="flex"><span class="w-12 text-sky-500">Label</span><span class="flex-1 text-zinc-400">This span fills all remaining space in the row</span></div>');

        $this->section('w-{n} — Fixed Width (characters)');
        $this->omni->line('<div class="flex"><span class="w-10 bg-sky-900 text-sky-300 text-center">w-10</span><span class="w-20 bg-sky-800 text-sky-300 text-center">w-20</span><span class="w-30 bg-sky-900 text-sky-300 text-center">w-30</span></div>');

        $this->section('px-{n} — Horizontal Padding');
        $this->omni->line('<div class="flex"><span class="bg-indigo-800 text-indigo-200 px-1">px-1</span><span> </span><span class="bg-indigo-800 text-indigo-200 px-3">px-3</span><span> </span><span class="bg-indigo-800 text-indigo-200 px-5">px-5</span></div>');

        $this->section('pl-{n} / pr-{n} — Left / Right Padding');
        $this->omni->line('<div class="flex"><span class="bg-teal-800 text-teal-200 pl-5 pr-1">pl-5 pr-1</span><span> </span><span class="bg-teal-800 text-teal-200 pl-1 pr-5">pl-1 pr-5</span></div>');

        $this->section('m-{n} — Margin (all sides)');
        $this->omni->line('<div class="flex"><span class="text-zinc-500">before</span><span class="bg-rose-800 text-rose-200 m-2">m-2</span><span class="text-zinc-500">after</span></div>');

        $this->section('mx-{n} — Horizontal Margin');
        $this->omni->line('<div class="flex"><span class="text-zinc-500">before</span><span class="bg-amber-800 text-amber-200 mx-4">mx-4</span><span class="text-zinc-500">after</span></div>');

        $this->section('ml-{n} / mr-{n} — Left / Right Margin');
        $this->omni->line('<div class="flex"><span class="bg-emerald-800 text-emerald-200 ml-8">ml-8</span><span class="bg-emerald-800 text-emerald-200 ml-2 mr-8">ml-2 mr-8</span><span class="text-zinc-500">after</span></div>');

        $this->section('mt-{n} / mb-{n} — Top / Bottom Margin (blank lines)');
        $this->omni->line('<div class="text-zinc-500">Line before</div>');
        $this->omni->line('<div class="mt-1 mb-1 text-violet-400">  This div has mt-1 and mb-1 (1 blank line above and below)</div>');
        $this->omni->line('<div class="text-zinc-500">Line after</div>');

        $this->section('space-x-{n} — Gap Between Flex Children');
        $this->omni->line('<div class="flex space-x-1"><span class="bg-sky-800 text-sky-200 px-1">A</span><span class="bg-sky-800 text-sky-200 px-1">B</span><span class="bg-sky-800 text-sky-200 px-1">C</span></div>');
        $this->omni->line('<div class="flex space-x-4"><span class="bg-sky-800 text-sky-200 px-1">A</span><span class="bg-sky-800 text-sky-200 px-1">B</span><span class="bg-sky-800 text-sky-200 px-1">C</span></div>');
        $this->omni->line('<div class="flex"><span class="text-zinc-600">↑ space-x-1    ↑ space-x-4</span></div>');

        $this->section('font-bold — Bold Text');
        $this->omni->line('<div class="flex"><span class="text-zinc-400">Normal text → </span><span class="text-zinc-400 font-bold">Bold text</span></div>');

        $this->section('text-center — Center Align');
        $this->omni->line('<div class="flex"><span class="flex-1 text-center text-cyan-400 bg-cyan-950">Centered in flex-1</span></div>');

        $this->section('text-right — Right Align');
        $this->omni->line('<div class="flex"><span class="flex-1 text-right text-pink-400 bg-pink-950">Right-aligned in flex-1</span></div>');

        $this->section('text-{color}-{shade} — Text Colors');
        $this->omni->line('<div class="flex space-x-2"><span class="text-red-500">red-500</span><span class="text-orange-500">orange-500</span><span class="text-amber-500">amber-500</span><span class="text-yellow-500">yellow-500</span><span class="text-lime-500">lime-500</span><span class="text-green-500">green-500</span><span class="text-emerald-500">emerald-500</span></div>');
        $this->omni->line('<div class="flex space-x-2"><span class="text-teal-500">teal-500</span><span class="text-cyan-500">cyan-500</span><span class="text-sky-500">sky-500</span><span class="text-blue-500">blue-500</span><span class="text-indigo-500">indigo-500</span><span class="text-violet-500">violet-500</span><span class="text-purple-500">purple-500</span></div>');
        $this->omni->line('<div class="flex space-x-2"><span class="text-fuchsia-500">fuchsia-500</span><span class="text-pink-500">pink-500</span><span class="text-rose-500">rose-500</span><span class="text-slate-500">slate-500</span><span class="text-gray-500">gray-500</span><span class="text-zinc-500">zinc-500</span></div>');

        $this->section('text-{color} — Shorthand (defaults to shade 500)');
        $this->omni->line('<div class="flex space-x-2"><span class="text-red">red</span><span class="text-sky">sky</span><span class="text-emerald">emerald</span><span class="text-amber">amber</span><span class="text-violet">violet</span></div>');

        $this->section('text-{color}-{shade} — Shade Range');
        $this->omni->line('<div class="flex"><span class="text-sky-50 px-1">50</span><span class="text-sky-100 px-1">100</span><span class="text-sky-200 px-1">200</span><span class="text-sky-300 px-1">300</span><span class="text-sky-400 px-1">400</span><span class="text-sky-500 px-1">500</span><span class="text-sky-600 px-1">600</span><span class="text-sky-700 px-1">700</span><span class="text-sky-800 px-1">800</span><span class="text-sky-900 px-1">900</span><span class="text-sky-950 px-1">950</span></div>');

        $this->section('bg-{color}-{shade} — Background Colors');
        $this->omni->line('<div class="flex"><span class="bg-red-600 text-red-100 px-2">red</span><span class="bg-sky-600 text-sky-100 px-2">sky</span><span class="bg-emerald-600 text-emerald-100 px-2">emerald</span><span class="bg-amber-600 text-amber-100 px-2">amber</span><span class="bg-violet-600 text-violet-100 px-2">violet</span><span class="bg-rose-600 text-rose-100 px-2">rose</span></div>');

        $this->section('bg-{color}-{shade} — Shade Range');
        $this->omni->line('<div class="flex"><span class="bg-indigo-50 text-indigo-900 px-1">50</span><span class="bg-indigo-100 text-indigo-900 px-1">100</span><span class="bg-indigo-200 text-indigo-900 px-1">200</span><span class="bg-indigo-300 text-indigo-900 px-1">300</span><span class="bg-indigo-400 text-indigo-100 px-1">400</span><span class="bg-indigo-500 text-indigo-100 px-1">500</span><span class="bg-indigo-600 text-indigo-100 px-1">600</span><span class="bg-indigo-700 text-indigo-100 px-1">700</span><span class="bg-indigo-800 text-indigo-100 px-1">800</span><span class="bg-indigo-900 text-indigo-100 px-1">900</span><span class="bg-indigo-950 text-indigo-100 px-1">950</span></div>');

        $this->section('content-repeat-[char] — Repeat Character to Fill Width');
        $this->omni->line('<div class="flex"><span class="flex-1 text-zinc-600 content-repeat-[─]">─</span></div>');
        $this->omni->line('<div class="flex"><span class="flex-1 text-emerald-600 content-repeat-[═]">═</span></div>');
        $this->omni->line('<div class="flex"><span class="flex-1 text-rose-600 content-repeat-[·]">·</span></div>');

        $this->section('bg-gradient-to-r — Left-to-Right Gradient');
        $this->omni->line('<div class="flex"><span class="flex-1 bg-gradient-to-r from-red-500 to-amber-300 text-white font-bold text-center px-2">from-red-500 to-amber-300</span></div>');
        $this->omni->line('<div class="flex"><span class="flex-1 bg-gradient-to-r from-sky-600 to-emerald-400 text-white font-bold text-center px-2">from-sky-600 to-emerald-400</span></div>');

        $this->section('bg-gradient-to-l — Right-to-Left Gradient');
        $this->omni->line('<div class="flex"><span class="flex-1 bg-gradient-to-l from-violet-600 to-rose-400 text-white font-bold text-center px-2">from-violet-600 to-rose-400</span></div>');

        $this->section('via-{color} — Three-Stop Gradient');
        $this->omni->line('<div class="flex"><span class="flex-1 bg-gradient-to-r from-rose-500 via-amber-400 to-emerald-500 text-white font-bold text-center px-2">from-rose-500 via-amber-400 to-emerald-500</span></div>');
        $this->omni->line('<div class="flex"><span class="flex-1 bg-gradient-to-r from-indigo-800 via-purple-500 to-pink-400 text-white font-bold text-center px-2">from-indigo-800 via-purple-500 to-pink-400</span></div>');

        $this->section('Container Gradient — On Flex Row');
        $this->omni->line('<div class="flex bg-gradient-to-r from-slate-900 to-slate-700"><span class="w-20 text-sky-400 font-bold px-2">Server</span><span class="flex-1 text-emerald-300">Online — 128 days uptime</span></div>');

        $this->section('bg-[R,G,B] / text-[R,G,B] — Arbitrary RGB Colors');
        $this->omni->line('<div class="flex"><span class="bg-[255,100,50] text-[255,255,255] px-2">bg-[255,100,50]</span><span> </span><span class="text-[0,200,150]">text-[0,200,150]</span><span> </span><span class="bg-[40,40,80] text-[200,180,255] px-2">Custom purple</span></div>');

        $this->section('Combined — Practical Examples');

        // Status row
        $this->omni->line('<div class="flex"><span class="bg-emerald-600 text-emerald-100 font-bold px-1">PASS</span><span class="flex-1 text-zinc-400 px-1">Database connection verified</span><span class="text-zinc-600 text-right w-12">12ms</span></div>');
        $this->omni->line('<div class="flex"><span class="bg-rose-600 text-rose-100 font-bold px-1">FAIL</span><span class="flex-1 text-zinc-400 px-1">Redis connection timed out</span><span class="text-zinc-600 text-right w-12">5000ms</span></div>');
        $this->omni->line('<div class="flex"><span class="bg-amber-600 text-amber-100 font-bold px-1">WARN</span><span class="flex-1 text-zinc-400 px-1">Cache driver using array fallback</span><span class="text-zinc-600 text-right w-12">0ms</span></div>');

        $this->newLine();

        // Boxed header
        $this->omni->line('<div class="flex"><span class="flex-1 text-sky-700 content-repeat-[─]">─</span></div>');
        $this->omni->line('<div class="flex"><span class="bg-sky-800 text-sky-200 font-bold flex-1 px-2 text-center">Server Dashboard</span></div>');
        $this->omni->line('<div class="flex"><span class="flex-1 text-sky-700 content-repeat-[─]">─</span></div>');
        $this->omni->line('<div class="flex mx-1"><span class="w-20 text-zinc-500 px-1">CPU</span><span class="flex-1 text-emerald-400">23%</span></div>');
        $this->omni->line('<div class="flex mx-1"><span class="w-20 text-zinc-500 px-1">Memory</span><span class="flex-1 text-amber-400">67%</span></div>');
        $this->omni->line('<div class="flex mx-1"><span class="w-20 text-zinc-500 px-1">Disk</span><span class="flex-1 text-rose-400">89%</span></div>');
        $this->omni->line('<div class="flex"><span class="flex-1 text-sky-700 content-repeat-[─]">─</span></div>');

        $this->newLine();

        return Command::SUCCESS;
    }

    private function section(string $title): void
    {
        $this->newLine();
        $this->omni->line('<div class="flex"><span class="text-violet-400 font-bold px-1">'.$title.'</span></div>');
    }
}
