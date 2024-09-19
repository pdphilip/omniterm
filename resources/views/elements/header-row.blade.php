<?php

?>
<div>
    <div class="flex space-x-1 px-1">
        <span class="font-bold text-gray">{{ $keyName }}</span>
        <span class="flex-1  text-gray"></span>
        @if($detailsName)
            <span class="text-stone-400">{{ $detailsName }}</span>
            <span class="text-gray font-bold  text-right ">/</span>
        @endif
        <span class="text-gray font-bold  text-right ">{{$valueName}}</span>
    </div>
    @include('omniterm::elements.hr')
</div>