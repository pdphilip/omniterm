<?php
$color = 'text-rose-500'
?>
<div class="flex space-x-1 px-1">
    <span class="flex-1 content-repeat-[─] {{$color}}"></span>
</div>
<div class="flex mb-1 mx-1">
    <span class="bg-rose-600 text-rose-100 px-1">OMNI error: {{$method}}</span>
    <span class="text-rose-400 pl-1">{{$error}}</span>
</div>
@if(!empty($help))
    <div class="flex mx-1">
        <span class="text-rose-600 pr-1 font-bold">└──►</span>
        <span class="text-gray">{{$help}}</span>
    </div>
@endif
<div class="flex space-x-1 px-1">
    <span class="flex-1 content-repeat-[─] {{$color}}"></span>
</div>