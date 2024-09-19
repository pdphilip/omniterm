<?php

use OmniTerm\Helpers\Partials\AsciiHelper;

$dimensions = AsciiHelper::progressBarDimensions($screenWidth, $max);

$length = $dimensions['length'];
$valuesWidth = $dimensions['valuesWidth'];

$progress = floor($current / $max * $length);
$remaining = $length - $progress;
$percentage = round(($current / $max) * 100);
$progressColor = "bg-sky-600 text-sky-400";
if ($max == $current) {
    $progressColor = "bg-emerald-600 text-emerald-300";
}
$current = number_format($current);
$max = number_format($max);

?>
<div class="mx-1">
    <div class="flex w-{{$length + $valuesWidth + 3}}">
        <span class="w-{{$valuesWidth + 1}}"></span>
        <span>╭</span>
        <span class="flex-1 content-repeat-[─]"></span>
        <span>╮</span>
    </div>
    <div class="flex">
        <span class="w-{{$valuesWidth}} text-right">{{$current}}/{{$max}}</span>
        <span class="w-1"></span>
        <span class="w-1">│</span>
        <span class="{{$progressColor}}  w-{{$progress}} content-repeat-[▁] text-right"></span>
        <span class="bg-slate-700 text-slate-500  w-{{$remaining}} content-repeat-[▁] text-right"></span>
        <span class="w-1">│</span>
        <span class="ml-2">{{$percentage}}%</span>
    </div>
    <div class="flex w-{{$length + $valuesWidth + 3}}">
        <span class="w-{{$valuesWidth + 1}} pr-2"></span>
        <span>╰</span>
        <span class="flex-1 content-repeat-[─]"></span>
        <span>╯</span>
    </div>
</div>
