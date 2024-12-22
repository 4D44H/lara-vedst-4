@php
    /**
     * @var \Lara\StatisticsInformation $info
     */
@endphp
<div class="progress text-center" style="height: 20px;">
    {{-- Progress bar for shifts in own club --}}
    <div class="progress-bar bg-primary" style="width: {{$info->shifts_percent_intern}}%;" role="progressbar"
         aria-label="{{__('mainLang.shiftsInOwnSection')}}"
         data-bs-toggle="tooltip" aria-valuenow="{{$info->shifts_percent_intern}}" aria-valuemin="0" aria-valuemax="100"
         title="{{__('mainLang.shiftsInOwnSection')}}">{{$info->shifts_percent_intern}}%</div>
    {{-- Progress bar for shifts in other club (marked in grey) --}}
    <div class="progress-bar bg-info"
         style="width: {{$info->shifts_percent_extern}}%;" role="progressbar"
         aria-label="{{__('mainLang.shiftsInOtherSection')}}"
         data-bs-toggle="tooltip" aria-valuenow="{{$info->shifts_percent_extern}}" aria-valuemin="0" aria-valuemax="100"
         title="{{__('mainLang.shiftsInOtherSection')}}">
        {{$info->shifts_percent_extern}}%
    </div>
    <div class="progress-bar bg-tertiary" role="progressbar"
         style="width: {{100 - $info->shifts_percent_extern - $info->shifts_percent_intern}}%;">
    </div>
</div>
