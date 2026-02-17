<?php
if (empty($color)) {
    $color = 'sky';
}
?>
<div class="flex mb-1 mx-1">
    <span class="bg-{{$color}}-600 text-{{$color}}-100 px-1">INFO</span>
    <span class="pl-1 flex-1">{{$message}}</span>
</div>