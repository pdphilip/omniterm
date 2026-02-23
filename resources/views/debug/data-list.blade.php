<?php

use OmniTerm\Helpers\Partials\AsciiHelper;

$frame = AsciiHelper::roundedBox();
$titleUpper = strtoupper($title);
$tee = '├';
$corner = '╰';
$pipe = '│';
$fsp = "\u{2007}";
?>
<div class="mx-1 {{ $borderColor }}">
    <div class="flex">
        <span>{{ $frame['tl'] }}{{ $frame['t'] }}</span>
        <span class="px-1 font-bold">{{ $titleUpper }}</span>
        <span class="flex-1 content-repeat-[{{ $frame['t'] }}]"></span>
        <span>{{ $frame['tr'] }}</span>
    </div>
</div>
@foreach($rows as $row)
    @if($row['type'] === 'section' && $row['depth'] === 0)
        <div class="mx-1 flex">
            <span class="{{ $borderColor }}">{{ $frame['l'] }}</span>
            <span class="pl-1 {{ $borderColor }} font-bold">{{ $row['key'] }}</span>
            <span class="flex-1 content-repeat-[{{ $frame['t'] }}] text-stone-700"></span>
            <span class="{{ $borderColor }}">{{ $frame['r'] }}</span>
        </div>
    @elseif($row['type'] === 'section')
        <div class="mx-1 flex">
            <span class="{{ $borderColor }}">{{ $frame['l'] }}</span>
            <span class="pl-1 text-stone-600">@foreach($row['ancestors'] as $hasMore){{ $hasMore ? $pipe . $fsp . $fsp : $fsp . $fsp . $fsp }}@endforeach{{ $row['last'] ? $corner : $tee }}{{ $frame['t'] }}</span>
            <span class="{{ $borderColor }} px-1 font-bold">{{ $row['key'] }}</span>
            <span class="flex-1 content-repeat-[{{ $frame['t'] }}] text-stone-700"></span>
            <span class="{{ $borderColor }}">{{ $frame['r'] }}</span>
        </div>
    @elseif($row['type'] === 'row')
        <div class="mx-1 flex space-x-1">
            <span class="{{ $borderColor }}">{{ $frame['l'] }}</span>
            @if($row['depth'] > 0)
                <span class="text-stone-600">@foreach($row['ancestors'] as $hasMore){{ $hasMore ? $pipe . $fsp . $fsp : $fsp . $fsp . $fsp }}@endforeach{{ $row['last'] ? $corner : $tee }}</span>
                <span class="text-stone-400">{{ $row['key'] }}</span>
            @else
                <span class="text-stone-400">{{ $row['key'] }}</span>
            @endif
            <span class="flex-1 content-repeat-[.] text-stone-700"></span>
            @if($row['valueType'] === 'boolean')
                @if($row['value'])
                    <span class="text-emerald-400 font-bold">true</span>
                @else
                    <span class="text-rose-400 font-bold">false</span>
                @endif
            @elseif($row['valueType'] === 'null')
                <span class="text-stone-500 italic">null</span>
            @elseif($row['valueType'] === 'number')
                <span class="text-sky-400">{{ $row['value'] }}</span>
            @elseif($row['valueType'] === 'empty')
                <span class="text-stone-500">{{ $row['value'] }}</span>
            @else
                <span class="text-amber-300">"{{ $row['value'] }}"</span>
            @endif
            <span class="{{ $borderColor }}">{{ $frame['r'] }}</span>
        </div>
    @endif
@endforeach
<div class="mx-1 {{ $borderColor }}">
    <div class="flex">
        <span>{{ $frame['bl'] }}</span>
        <span class="flex-1 content-repeat-[{{ $frame['b'] }}]"></span>
        <span>{{ $frame['br'] }}</span>
    </div>
</div>
