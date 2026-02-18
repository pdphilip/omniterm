<div class="mx-1">
    <div class="flex w-{{$length + $valuesWidth + 3}}">
        <span class="w-{{$valuesWidth + 1}}"></span>
        <span class="{{$labelFg}}">╭</span>
        <span class="{{$labelFg}} flex-1 content-repeat-[─]"></span>
        <span class="{{$labelFg}}">╮</span>
    </div>
    <div class="flex">
        <span class="w-{{$valuesWidth}} text-right"><span class="{{$labelFg}}">{{$current}}</span>/{{$max}}</span>
        <span class="w-1"></span>
        <span class="{{$labelFg}}">│</span>
        <span class="{{$barBg}} {{$barFg}} w-{{$progress}} content-repeat-[▁]"></span>
        <span class="bg-slate-700 text-slate-500 w-{{$remaining}} content-repeat-[▁]"></span>
        <span class="{{$labelFg}}">│</span>
        <span class="{{$labelFg}} ml-2">{{$percentage}}%</span>
    </div>
    <div class="flex w-{{$length + $valuesWidth + 3}}">
        <span class="w-{{$valuesWidth + 1}} pr-2"></span>
        <span class="{{$labelFg}}">╰</span>
        <span class="{{$labelFg}} flex-1 content-repeat-[─]"></span>
        <span class="{{$labelFg}}">╯</span>
    </div>
</div>
