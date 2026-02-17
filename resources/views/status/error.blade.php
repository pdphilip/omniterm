<?php
if (empty($color)) {
    $color = 'rose';
}
?>
<div class="flex mb-1 mx-1">
    <span class="bg-{{$color}}-600 text-{{$color}}-100 px-1">ERROR</span>
    <span class="pl-1 flex-1">{{$message}}</span>
</div>