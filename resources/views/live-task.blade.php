<?php
$intervals = count($frames) - 1;
$colorIntervals = count($colors) - 1;
$i = $frame;
$j = 0;
while ($i > $intervals) {
    $i -= $intervals;
    $j++;
    if ($j > $colorIntervals) {
        $j = 0;
    }
}

$show = $frames[$i];
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
<div>
    <div class="m-1 flex">
        <span class="{{$textColor}} mx-1">{!! $show !!}</span>
        <span class="mx-1">{{$title}}</span>
    </div>
    @if(count($rows))
        <div class="flex space-x-1 px-1">
            <span class="flex-1 content-repeat-[─] text-gray"></span>
        </div>
        @foreach($rows as $label => $row)
                <?php
                $displayValue = $row['value'];
                $class = $row['color'];
                $details = $row['details'];
                if ($displayValue == 0) {
                    $class = 'text-stone-600';
                } elseif (is_int($displayValue)) {
                    $displayValue = number_format($displayValue);
                }
                ?>
            <div class="flex space-x-1 px-1">
                <span class="font-bold">{{ $label }}</span>
                <span class="flex-1 content-repeat-[.] text-gray"></span>
                @if(!empty($details))
                    <span class="text-stone-400">[{{ $details }}]</span>
                @endif
                <span class="text-right">
                <span class="{{$class}} font-bold px-1">{{$displayValue}}</span>
        </span>
            </div>
        @endforeach
    @endif
</div>
