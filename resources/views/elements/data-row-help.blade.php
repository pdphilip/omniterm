<?php

$class = match ($status) {
    'disabled' => 'text-stone-600',
    'warning' => 'text-amber-500',
    'error' => 'text-rose-500',
    default => 'text-gray',
};
?>
<div>
    <div class="flex space-x-1 px-1">
        <span class="flex-1  text-gray"></span>
        <span class="text-gray">{{$value}}</span>
        <span class="{{ $class}} pr-1 font-bold">◄────┘</span>
    </div>
</div>