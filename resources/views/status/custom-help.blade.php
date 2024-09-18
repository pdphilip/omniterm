<?php

$class = match ($status) {
    'disabled' => 'text-stone-500',
    'warning' => 'text-amber-500',
    'error' => 'text-rose-500',
    default => 'text-gray',
};
?>
<div>
    <div class="flex space-x-1 px-1">
        <span class="{{ $class}} pr-1 font-bold">└────►</span>
        <span class="text-gray">{{$value}}</span>
    </div>
</div>