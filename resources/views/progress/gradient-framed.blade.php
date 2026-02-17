<?php

use OmniTerm\Helpers\Partials\AsciiHelper;
use OmniTerm\Rendering\Colors;

$dimensions = AsciiHelper::progressBarDimensions($screenWidth, $max);

$length = $dimensions['length'];
$valuesWidth = $dimensions['valuesWidth'];
$progress = floor($current / $max * $length);
$remaining = $length - $progress;
$percentage = round(($current / $max) * 100);

$rgb = Colors::colorAt($percentage, 'amber', 500, 'emerald', 500);
$barRgb = Colors::colorAt($percentage, 'amber', 600, 'emerald', 600);
$textRgb = Colors::colorAt($percentage, 'amber', 400, 'emerald', 400);

$barBg = "bg-[{$barRgb[0]},{$barRgb[1]},{$barRgb[2]}]";
$barFg = "text-[{$textRgb[0]},{$textRgb[1]},{$textRgb[2]}]";
$labelFg = "text-[{$rgb[0]},{$rgb[1]},{$rgb[2]}]";

$current = number_format($current);
$max = number_format($max);
?>
<div class="mx-1">
    <div class="flex w-{{$length + $valuesWidth + 3}}">
        <span class="w-{{$valuesWidth + 1}}"></span>
        <span class="{{$labelFg}}">╭</span>
        <span class="{{$labelFg}} flex-1 content-repeat-[─]"></span>
        <span class="{{$labelFg}}">╮</span>
    </div>
    <div class="flex">
        <span class="w-{{$valuesWidth}} text-right"><span class="{{$labelFg}}">{{$current}}</span>/{{$max}}</span>
        <span class="w-1"></span>
        <span class="{{$labelFg}}">│</span>
        <span class="{{$barBg}} {{$barFg}} w-{{$progress}} content-repeat-[▁]"></span>
        <span class="bg-slate-700 text-slate-500 w-{{$remaining}} content-repeat-[▁]"></span>
        <span class="{{$labelFg}}">│</span>
        <span class="{{$labelFg}} ml-2">{{$percentage}}%</span>
    </div>
    <div class="flex w-{{$length + $valuesWidth + 3}}">
        <span class="w-{{$valuesWidth + 1}} pr-2"></span>
        <span class="{{$labelFg}}">╰</span>
        <span class="{{$labelFg}} flex-1 content-repeat-[─]"></span>
        <span class="{{$labelFg}}">╯</span>
    </div>
</div>
