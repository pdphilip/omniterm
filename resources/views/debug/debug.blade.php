<?php

use OmniTerm\Helpers\Partials\AsciiHelper;

$frame = AsciiHelper::roundedBox();
$color = 'violet';
?>
<div class="mx-1 text-{{ $color }}-400">
    <div class="flex">
        <span>{{ $frame['tl'] }}{{ $frame['t'] }}{{ $frame['t'] }}</span>
        <span class="px-1 font-bold text-{{ $color }}-300">DEBUG{{ $label ? ': ' . $label : '' }}</span>
        <span class="flex-1 content-repeat-[{{ $frame['t'] }}]"></span>
        <span>{{ $frame['tr'] }}</span>
    </div>
    <div class="flex">
        <span>{{ $frame['bl'] }}</span>
        <span class="flex-1 content-repeat-[{{ $frame['b'] }}]"></span>
        <span>{{ $frame['br'] }}</span>
    </div>
</div>
@foreach($rows as $row)
    @if($row['type'] === 'section')
        <div class="flex pl-{{ $row['depth'] * 2 + 1 }} pr-1">
            <span class="text-stone-600">───</span>
            <span class="text-{{ $color }}-300 px-1 font-bold">{{ $row['key'] }}</span>
            <span class="flex-1 content-repeat-[─] text-stone-600"></span>
        </div>
    @elseif($row['type'] === 'end')
        <div class="flex pl-{{ $row['depth'] * 2 + 1 }} pr-1">
            <span class="flex-1 content-repeat-[─] text-stone-700"></span>
        </div>
    @elseif($row['type'] === 'row')
        <div class="flex space-x-1 pl-{{ $row['depth'] * 2 + 2 }} pr-1">
            @if($row['key'] !== null)
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
        </div>
    @endif
@endforeach
