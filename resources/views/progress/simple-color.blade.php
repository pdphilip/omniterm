<?php

use OmniTerm\Helpers\Partials\AsciiHelper;

$dimensions = AsciiHelper::progressBarDimensions($screenWidth, $max);

$length = $dimensions['length'];
$valuesWidth = $dimensions['valuesWidth'];
$progress = floor($current / $max * $length);
$remaining = $length - $progress;
$percentage = round(($current / $max) * 100);
$colors = ['red', 'orange', 'amber', 'yellow', 'lime', 'teal', 'cyan', 'sky'];
$i = (int) floor(($current / $max) * 20);
$selectedColor = $colors[$i] ?? 'sky';

$progressColor = "bg-$selectedColor-600";
$borderColor = "text-$selectedColor-400";
if ($max == $current) {
    $progressColor = "bg-emerald-600 ";
    $borderColor = "text-emerald-400";
}
?>
<div class="mx-1">
    <div class="flex w-{{$length + $valuesWidth}}">
        <span class="w-{{$valuesWidth}} pr-2"></span>
        <span class="{{$borderColor}}  w-{{$progress}} content-repeat-[▁] text-right"></span>
        <span class="text-slate-500  w-{{$remaining}} content-repeat-[▁] text-right"></span>
    </div>
    <div class="flex">
        <span class="w-{{$valuesWidth}} text-right pr-2"><span class="{{$borderColor}}">{{$current}}</span>/{{$max}}</span>
        <span class="{{$progressColor}} {{$borderColor}} w-{{$progress}} content-repeat-[▁] text-right"></span>
        <span class="bg-slate-700 text-slate-500  w-{{$remaining}} content-repeat-[▁] text-right"></span>
        <span class="ml-2">{{$percentage}}%</span>
    </div>
</div>