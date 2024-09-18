<?php

use OmniTerm\partials\Ascii;

if (empty($type)) {
    $type = 'sand';
}
if (empty($colors)) {
    $colors = ["text-amber-500"];
}
$characters = Ascii::loadSpinner($type);
$intervals = count($characters) - 1;
$colorIntervals = count($colors) - 1;
$j = 0;
while ($i > $intervals) {
    $i -= $intervals;
    $j++;
    if ($j > $colorIntervals) {
        $j = 0;
    }
}

$show = $characters[$i];
$show = str_replace(' ', '&nbsp;', $show);
$textColor = $colors[$j];
switch ($state) {
    case 'success':
        $textColor = "text-emerald-500";
        $show = "✔";
        break;
    case 'warning':
        $textColor = "text-amber-500";
        $show = "⚠";
        break;
    case 'failover':
        $textColor = "text-amber-500";
        $show = "◴";
        break;
    case 'error':
        $textColor = "text-rose-500";
        $show = "✘";
        break;
}
?>
<div class="m-1 flex">
    <span class="{{$textColor}} mx-1">{!! $show !!}</span>
    <span class="mx-1">{{$message}}</span>
    @if(!empty($details))
        <span class="mx-1 text-slate-600">{{$details}}</span>
    @endif
</div>