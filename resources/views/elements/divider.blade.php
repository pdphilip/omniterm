<?php
?>
<div>
    <div class="flex space-x-1 px-1">
        <span class="flex-1 content-repeat-[─] text-gray"></span>
        @if(!empty($label))
            <span class="text-stone-400 mx-2">{{ $label }}</span>
        @endif
        <span class="flex-1 content-repeat-[─] text-gray"></span>
    </div>
</div>
