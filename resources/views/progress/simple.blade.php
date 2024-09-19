<?php

use OmniTerm\Helpers\Partials\AsciiHelper;

$dimensions = AsciiHelper::progressBarDimensions($screenWidth, $max);

$length = $dimensions['length'];
$valuesWidth = $dimensions['valuesWidth'];

$progress = floor($current / $max * $length);
$remaining = $length - $progress;
$percentage = round(($current / $max) * 100);
$progressColor = "bg-sky-600 ";
$borderColor = "text-sky-500";
if ($max == $current) {
    $borderColor = "text-emerald-300";
    $progressColor = "bg-emerald-600 ";
}
?>
<div class="mx-1">
    <div class="flex w-{{$length + $valuesWidth}}">
        <span class="w-{{$valuesWidth}} pr-2"></span>
        <span class="{{$borderColor}}  w-{{$progress}} content-repeat-[▁] text-right"></span>
        <span class="text-slate-500  w-{{$remaining}} content-repeat-[▁] text-right"></span>
    </div>
    <div class="flex">
        <span class="w-{{$valuesWidth}} text-right pr-2">{{$current}}/{{$max}}</span>
        <span class="{{$progressColor}} {{$borderColor}} w-{{$progress}} content-repeat-[▁] text-right"></span>
        <span class="bg-slate-700 text-slate-500  w-{{$remaining}} content-repeat-[▁] text-right"></span>
        <span class="ml-2">{{$percentage}}%</span>
    </div>
</div>
