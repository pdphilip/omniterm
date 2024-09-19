<?php

use OmniTerm\Helpers\Partials\AsciiHelper;

if (empty($borderColor)) {
    $borderColor = '';
}
if (empty($textColor)) {
    $textColor = '';
}

$frame = AsciiHelper::roundedBox();
if (! empty($type) && $type == 'square') {
    $frame = AsciiHelper::squareBox();
}

?>
<div class="mx-1 {{$borderColor}}">
    <div class="flex">
        <span>{{ $frame['tl'] }}</span>
        <span class="flex-1 content-repeat-[{{ $frame['t'] }}]"></span>
        <span>{{ $frame['tr'] }}</span>
    </div>
    <div class="flex">
        <span>{{ $frame['l'] }}</span>
        <span class="flex-1">&nbsp;</span>
        <span>{{ $frame['r'] }}</span>
    </div>
    <div class="flex">
        <span>{{ $frame['l'] }}</span>
        <span class="flex-1 text-center {{$textColor}}">{{$title}}</span>
        <span>{{ $frame['r'] }}</span>
    </div>
    <div class="flex">
        <span>{{ $frame['l'] }}</span>
        <span class="flex-1">&nbsp;</span>
        <span>{{ $frame['r'] }}</span>
    </div>
    <div class="flex">
        <span>{{ $frame['bl'] }}</span>
        <span class="flex-1 content-repeat-[{{ $frame['b'] }}]"></span>
        <span>{{ $frame['br'] }}</span>
    </div>
</div>
