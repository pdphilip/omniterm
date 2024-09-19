<?php
$disabledColor = 'zinc';
$warningColor = 'amber';
$errorColor = 'rose';
$infoColor = 'sky';
$successColor = 'emerald';

if (! empty($statusColors['disabled'])) {
    $disabledColor = $statusColors['disabled'];
}
if (! empty($statusColors['warning'])) {
    $warningColor = $statusColors['warning'];
}
if (! empty($statusColors['error'])) {
    $errorColor = $statusColors['error'];
}
if (! empty($statusColors['info'])) {
    $infoColor = $statusColors['info'];
}
if (! empty($successColors['success'])) {
    $successColor = $statusColors['success'];
}
$class = match ($status) {
    'disabled' => 'text-'.$disabledColor.'-500',
    'warning' => 'text-'.$warningColor.'-500 ',
    'error', 'failed' => 'text-'.$errorColor.'-500',
    'info' => 'text-'.$infoColor.'-500',
    default => 'text-'.$successColor.'-500',
};

$statusValue = match ($status) {
    'disabled' => 'DISABLED',
    'warning' => 'WARNING',
    'error' => 'ERROR',
    'failed' => 'FAILED',
    'info' => 'INFO',
    'enabled' => 'ENABLED',
    'success' => 'SUCCESS',
    default => 'OK',
};

?>
<div>
    <div class="flex space-x-1 px-1">
        <span class="font-bold">{{ $key }}</span>
        <span class="flex-1 content-repeat-[.] text-gray"></span>
        @if(!empty($details))
            <span class="text-stone-400">[{{ $details }}]</span>
        @endif
        <span class="text-right">
        <span class="{{$class}} font-bold  px-1 ">{{$statusValue}}</span>
        </span>
    </div>
    @if(!empty($help))
        @foreach ($help as $helperRow)
            @include('omniterm::elements.data-row-help',['value' => $helperRow,'class' => $class])
        @endforeach
    @endif
</div>
