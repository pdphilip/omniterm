<?php
if (empty($class)) {
    $class = 'text-gray';
}
if (str_contains($class, 'bg-')) {
    //remove any existing text-XXX-xxx classes
    $class = preg_replace('/text-[a-z]+-[0-9]+/', '', $class);
    //replace bg- with text-
    $class = str_replace('bg-', 'text-', $class);
}

?>
<div>
    <div class="flex space-x-1 px-1">
        <span class="flex-1  text-gray"></span>
        <span class="text-gray">{{$value}}</span>
        <span class="{{ $class}} pr-1 font-bold">◄────┘</span>
    </div>
</div>