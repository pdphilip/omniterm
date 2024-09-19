<?php
if (empty($class)) {
    $class = 'text-gray';
}
?>
<div>
    <div class="flex space-x-1 px-1">
        <span class="{{ $class}} pr-1 font-bold">└────►</span>
        <span class="text-gray">{{$value}}</span>
    </div>
</div>