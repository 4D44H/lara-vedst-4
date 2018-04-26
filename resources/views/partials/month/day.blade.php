@if ($weekDay->format('Y-m-d') === date('Y-m-d'))
    {{-- The actual date of today marked in dark gray--}}
    <div class="thisMonth today-marker-today scroll-marker custom-md-85">
@elseif ($weekDay->format('W') === date('W'))
    {{-- Other day in this week --}}
    <div class="thisMonth today-marker custom-md-85">
@elseif ($weekDay->format('m') === $month)
    <div class="thisMonth custom-md-85">
@else
    <div class="otherMonth custom-md-85">
@endif
    <div class="">
        @auth
            <a href="{{ Request::getBasePath() }}/event/{{ strftime("%Y/%m/%d", $weekDay->getTimestamp()) }}/0/create"
               data-toggle="tooltip" 
               data-placement="top"
               title="{{ trans('mainLang.createEventOnThisDate')}}">
                {{$weekDay->format('d')}}
            </a>
        @else
            {{$weekDay->format('d')}}
        @endauth
        <small class="visible-xs visible-sm">
            {{-- Display day of the month--}}
            {{ strftime("%a", $weekDay->getTimestamp()) }}
        </small>
    </div>
    <div class="" style="padding-right:4px;">
        @include( 'partials.month.monthCell')
    </div>
</div>
