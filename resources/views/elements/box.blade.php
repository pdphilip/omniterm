<?php
if (empty($frameColor)) {
    $frameColor = 'gray';
}
if (empty($titleColor)) {
    $titleColor = 'gray';
}
?>
<div class="mx-1 {{$frameColor}}">
    <div class="flex">
        <span>╭</span>
        <span class="flex-1 content-repeat-[─]"></span>
        <span>╮</span>
    </div>
    <div>
        <span>│</span>
        <span class="flex-1"></span>
        <span>│</span>
    </div>
    <div>
        <span>│</span>
        <span class="flex-1 text-center {{$titleColor}}">{{$title}}</span>
        <span>│</span>
    </div>
    <div>
        <span>│</span>
        <span class="flex-1"></span>
        <span>│</span>
    </div>
    <div class="flex">
        <span>╰</span>
        <span class="flex-1 content-repeat-[─]"></span>
        <span>╯</span>
    </div>
</div>
