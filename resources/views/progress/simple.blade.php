<div class="mx-3">
    <div class="flex w-{{$length + $valuesWidth}}">
        <span class="w-{{$valuesWidth}} pr-2"></span>
        <span class="{{$barFg}} w-{{$progress}} content-repeat-[▁]"></span>
        <span class="text-slate-500 w-{{$remaining}} content-repeat-[▁]"></span>
    </div>
    <div class="flex">
        <span class="w-{{$valuesWidth}} text-right pr-2"><span class="{{$labelFg}}">{{$current}}</span>/{{$max}}</span>
        <span class="{{$barBg}} {{$barFg}} w-{{$progress}} content-repeat-[▁]"></span>
        <span class="bg-slate-700 text-slate-500 w-{{$remaining}} content-repeat-[▁]"></span>
        <span class="{{$labelFg}} ml-2">{{$percentage}}%</span>
    </div>
</div>
