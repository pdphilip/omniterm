<?php
$color = $color ?? 'sky';
$t = $t ?? '';
?>
<div class="flex px-1">
    <span class="bg-gradient-to-l from-{{$color}}-600 to-{{$color}}-800 w-20"></span>
    <span class="bg-{{$color}}-600 text-{{$color}}-200 flex-1 text-center">{{$t}}</span>
    <span class="bg-gradient-to-r from-{{$color}}-600 to-{{$color}}-800 w-20"></span>
</div>
