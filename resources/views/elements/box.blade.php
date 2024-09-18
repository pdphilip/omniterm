<?php
if (empty($frameColor)) {
    $frameColor = '';
}
if (empty($titleColor)) {
    $titleColor = '';
}
?>
<div class="mx-1 {{$frameColor}}">
    <div class="flex">
        <span>╭</span>
        <span class="flex-1 content-repeat-[─]"></span>
        <span>╮</span>
    </div>
    <div class="flex">
        <span>│</span>
        <span class="flex-1">&nbsp;</span>
        <span>│</span>
    </div>
    <div class="flex">
        <span>│</span>
        <span class="flex-1 text-center {{$titleColor}}">{{$title}}</span>
        <span>│</span>
    </div>
    <div class="flex">
        <span>│</span>
        <span class="flex-1">&nbsp;</span>
        <span>│</span>
    </div>
    <div class="flex">
        <span>╰</span>
        <span class="flex-1 content-repeat-[─]"></span>
        <span>╯</span>
    </div>
</div>
