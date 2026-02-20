<?php
$borderColor = 'text-gray';
$answer = 'y/n';
$answerColor = 'text-gray';

switch ($state) {
    case 'confirmed':
        $borderColor = "text-{$confirmColor}-500";
        $answer = 'y';
        $answerColor = "text-{$confirmColor}-500";
        break;
    case 'declined':
        $borderColor = "text-{$declineColor}-500";
        $answer = 'n';
        $answerColor = "text-{$declineColor}-500";
        break;
}
?>
<div>
    <div class="flex space-x-1 px-1">
        <span class="flex-1 content-repeat-[─] {{$borderColor}}"></span>
    </div>
    <div class="flex space-x-1 px-1">
        <span>[{{$question}}]</span>
        <span class="{{$answerColor}}">{{$answer}}</span>
    </div>
    @if($state === 'confirmed')
        <div></div>
    @endif
    @if($state === 'asking')
        <div class="flex space-x-1 px-1">
            <span class="flex-1 content-repeat-[─] {{$borderColor}}"></span>
        </div>
    @endif
</div>
