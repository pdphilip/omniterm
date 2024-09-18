<?php
$length = $screenWidth - (30);
$progress = floor($current / $max * $length);
$remaining = $length - $progress;
$percentage = round(($current / $max) * 100);
$progressColor = "bg-red-600 text-red-400";
if ($progress > 5) {
    $progressColor = "bg-orange-600 text-orange-400";
}
if ($progress > 10) {
    $progressColor = "bg-amber-600 text-amber-400";
}
if ($progress > 15) {
    $progressColor = "bg-yellow-600 text-yellow-400";
}
if ($progress > 20) {
    $progressColor = "bg-lime-600 text-lime-400";
}
if ($progress > 25) {
    $progressColor = "bg-teal-600 text-teal-400";
}
if ($progress > 30) {
    $progressColor = "bg-cyan-600 text-cyan-400";
}
if ($progress > 35) {
    $progressColor = "bg-sky-600 text-sky-400";
}
if ($max == $current) {
    $progressColor = "bg-emerald-600 text-emerald-300";
}
?>
<div class="mx-1">
    <div class="flex w-{{$length + 12}}">
        <span class="w-10"></span><span>╭</span><span class="flex-1 content-repeat-[─]"></span><span>╮</span>
    </div>
    <div class="flex">
        <span class="w-9 text-right">{{$current}}/{{$max}}</span>
        <span class="w-1"></span>
        <span class="w-1">│</span>
        <span class="{{$progressColor}}  w-{{$progress}} content-repeat-[▁] text-right"></span>
        <span class="bg-slate-700 text-slate-500  w-{{$remaining}} content-repeat-[▁] text-right"></span>
        <span class="w-1">│</span>
        <span class="ml-2">{{$percentage}}%</span>
    </div>
    <div class="flex w-{{$length + 12}}">
        <span class="w-10"></span><span>╰</span><span class="flex-1 content-repeat-[─]"></span><span>╯</span>
    </div>
</div>
