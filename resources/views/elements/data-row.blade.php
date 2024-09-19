<?php

$name = \Illuminate\Support\Str::headline($key);
if (! empty($skipTitle)) {
    $name = $key;
}
$disabledColor = 'zinc';
$errorColor = 'rose';
$successColor = 'emerald';
if (! empty($statusColors['disabled'])) {
    $disabledColor = $statusColors['disabled'];
}
if (! empty($statusColors['error'])) {
    $errorColor = $statusColors['error'];
}
if (! empty($successColors['success'])) {
    $successColor = $statusColors['success'];
}

if ($value === true) {
    $class = 'text-'.$successColor.'-500';
    $value = 'Yes';
}
if ($value === false) {
    $class = 'text-'.$errorColor.'-500';
    $value = 'No';
}

if ($value === 0) {
    $class = 'text-'.$disabledColor.'-600';
} elseif (is_int($value)) {
    $value = number_format($value);
}
if (empty($class)) {
    $class = '';
}
?>
<div>
    <div class="flex space-x-1 px-1">
        <span class="font-bold">{{ $name }}</span>
        <span class="flex-1 content-repeat-[.] text-gray"></span>
        @if(!empty($details))
            <span class="text-stone-400">[{{ $details }}]</span>
        @endif
        <span class="text-right">
        <span class="{{$class}} font-bold  px-1 ">{{$value}}</span>
        </span>
    </div>
    @if(!empty($help))
        @foreach ($help as $helperRow)
            @include('omniterm::elements.data-row-help',['value' => $helperRow,'class' => $class])
        @endforeach
    @endif
</div>
