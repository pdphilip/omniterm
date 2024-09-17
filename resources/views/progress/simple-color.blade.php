<?php
$length = $screenWidth - (30);
$progress = floor($current / $max * $length);
$remaining = $length - $progress;
$percentage = round(($current / $max) * 100);
$colors = ['rose', 'red', 'orange', 'amber', 'yellow', 'lime', 'cyan', 'teal', 'emerald', 'green', 'green'];
$i = (int) floor(($current / $max) * 10);
$selectedColor = $colors[$i];

$progressColor = "bg-$selectedColor-600";
$borderColor = "text-$selectedColor-400";
if ($max == $current) {
    $progressColor = "bg-green-600 ";
    $borderColor = "text-green-400";
}
?>
<div class="mx-1">
    <div class="flex w-{{$length + 12}}">
        <span class="w-11"></span>
        <span class="{{$borderColor}}  w-{{$progress}} content-repeat-[▁] text-right"></span>
        <span class="text-slate-500  w-{{$remaining}} content-repeat-[▁] text-right"></span>
    </div>
    <div class="flex">
        <span class="w-11 text-right pr-2"><span class="{{$borderColor}}">{{$current}}</span>/{{$max}}</span>
        <span class="{{$progressColor}} {{$borderColor}} w-{{$progress}} content-repeat-[▁] text-right"></span>
        <span class="bg-slate-700 text-slate-500  w-{{$remaining}} content-repeat-[▁] text-right"></span>
        <span class="ml-2">{{$percentage}}%</span>
    </div>
</div>