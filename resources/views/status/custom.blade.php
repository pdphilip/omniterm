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
    'disabled' => 'bg-'.$disabledColor.'-500 text-'.$disabledColor.'-100',
    'warning' => 'bg-'.$warningColor.'-500 text-'.$warningColor.'-200',
    'error' => 'bg-'.$errorColor.'-500 text-'.$errorColor.'-200',
    'info' => 'bg-'.$infoColor.'-500 text-'.$infoColor.'-200',
    default => 'bg-'.$successColor.'-500 text-'.$successColor.'-100',
};

$color = match ($status) {
    'disabled' => 'text-'.$disabledColor.'-500',
    'warning' => 'text-'.$warningColor.'-500',
    'error' => 'text-'.$errorColor.'-500',
    'info' => 'text-'.$infoColor.'-500',
    default => 'text-'.$successColor.'-500',
};

$extraText = null;
if (! empty($extra)) {
    $extraText = $extra;
}

?>
<div>
    @include('omniterm::elements.hr',['color' => $color])
    <div class="flex space-x-1 mx-1">
        <span class="{{$class}} px-1 ml-1">{{$title}}</span>
        <span class="flex-1">{!! $details !!}</span>
        @if(!empty($help))
            @foreach ($help as $helperRow)
                @include('omniterm::status.custom-help',['value' => $helperRow,'class' => $color])
            @endforeach
        @endif
    </div>
    @include('omniterm::elements.hr',['color' => $color])
</div>