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
<div class="mx-3">
    <div class="flex w-{{$length + $valuesWidth}}">
        <span class="w-{{$valuesWidth}} pr-2"></span>
        <span class="{{$barFg}} w-{{$progress}} content-repeat-[▁]"></span>
        <span class="text-slate-500 w-{{$remaining}} content-repeat-[▁]"></span>
    </div>
    <div class="flex">
        <span class="w-{{$valuesWidth}} text-right pr-2"><span class="{{$labelFg}}">{{$current}}</span>/{{$max}}</span>
        <span class="{{$barBg}} {{$barFg}} w-{{$progress}} content-repeat-[▁]"></span>
        <span class="bg-slate-700 text-slate-500 w-{{$remaining}} content-repeat-[▁]"></span>
        <span class="{{$labelFg}} ml-2">{{$percentage}}%</span>
    </div>
</div>
