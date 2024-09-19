<?php
if (empty($color)) {
    $color = 'emerald';
}
?>
<div class="flex space-x-1 mb-1 mx-1">
    <span class="bg-{{$color}}-600 text-{{$color}}-100 px-1 ml-1">GOOD</span>
    <span class="flex-1">{{$message}}</span>
</div>