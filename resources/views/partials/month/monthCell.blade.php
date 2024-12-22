{{-- Needs variables: $surveys, $events --}}
@php
    use Carbon\Carbon;
@endphp

@foreach ($surveys as $survey)
    {{-- going over all surveys see weekCellSurvey for a single survey --}}

    {{-- check if the survey is already over --}}
    @if (strtotime($survey->deadline) < time())
        <?php $classString = 'past-eventOrSurvey'; ?>
    @else
        <?php $classString = ''; ?>
    @endif

    @if (date('Y-m-d', strtotime($survey->deadline)) === date('Y-m-d', $weekDay->getTimestamp()))
        @if (!Auth::user() && $survey->is_private)
            {{-- check current session for user role && and the survey for private status --}}
            {{-- no userId means this a guest account, so he gets blocked here --}}

            {{-- if so show a grey placeholder for the guest --}}
            <div class="cal-event {{ $classString }} palette-Grey-500 bg word-break section-filter section-survey">
                <i class="fa-solid  fa-chart-bar"></i>
                &nbsp;&nbsp;
                {{-- and show him thats a private survey(=Interne Umfrage in german) only for users --}}
                <span class="event-name">
                    {{ __('mainLang.internalSurvey') }}
                </span>
            </div>
        @else
            {{-- meaning session()->has'userId' OR !$survey->is_private == 0 --}}
            {{-- so session has a valid user OR the guest can see this survey because it isn't private --}}
            <div class="cal-event {{ $classString }} palette-Purple-900 bg word-break section-filter section-survey">
                <i class="fa fa-chart-bar"></i>
                &nbsp;&nbsp;<span class="event-time">{{ date('H:i', strtotime($survey->deadline)) }}</span>
                {{-- provide a URL to the survey --}}
                <a href="{{ URL::route('survey.show', $survey->id) }}" class="event-name" data-bs-toggle="tooltip"
                    data-bs-placement="right" title="{{ __('mainLang.showDetails') }}">
                    {{-- instead of private survey show the actual title of the survey --}}
                    <span> {{ $survey->title }} </span>
                </a>
            </div>
        @endif
    @endif
@endforeach

@foreach ($events as $clubEvent)
    @if (Carbon::createFromTimestamp($weekDay->getTimestamp())->between(Carbon::createFromFormat('Y-m-d', $clubEvent->evnt_date_start)->subDay(),
            Carbon::createFromFormat('Y-m-d H:i:s', $clubEvent->evnt_date_end . ' ' . $clubEvent->evnt_time_end)->subHour(
                5)))
        {{-- Check if the event is still going on --}}
        @if (strtotime($clubEvent->evnt_date_end . ' ' . $clubEvent->evnt_time_end) < time())
            {{-- The event is already over --}}
            <?php $classString = 'past-eventOrSurvey'; ?>
        @else
            <?php $classString = ''; ?>
        @endif

        {{-- highlight with cal-month-my-event class if the signed in user has a shift in this event --}}


        {{-- Filter --}}
        @if ($clubEvent->showToSection->isEmpty())
            {{-- Workaround for older events: if filter is empty - use event club data instead --}}
            <div class="section-{!! $clubEvent->section->id !!}">
            @else
                {{-- Normal scenario: add a css class according to filter data --}}
                {{-- Formatting: "Section Name 123" => "section-name-123" --}}
                <div
                    class="section-filter
                        @foreach ($sections as $section)
                            {!! in_array($section->id, $clubEvent->showToSectionIds()) ? 'section-' . $section->id : false !!} @endforeach">
        @endif
        {{-- guests see private events as placeholders only, so check if user is logged in --}}
        @guest
            {{-- show only a placeholder for private events --}}
            @if ($clubEvent->evnt_is_private)
                <div class="cal-event {{ $classString }} palette-Grey-500 bg">
                    <i class="fa fa-eye-slash"></i>
                    &nbsp;&nbsp;
                    <span>{{ __('mainLang.internEventP') }}</span>
                </div>
                {{-- show everything for public events --}}
            @else
                <div
                    class="cal-event {{ $classString }} {{ \Lara\utilities\ViewUtility::getEventPaletteClass($clubEvent) }}"
                    @include('partials.shiftsProgressBar', $clubEvent)
                    >
                    @include('partials.event-marker', $clubEvent)
                    <span class="event-time">&nbsp;{{ date('H:i', strtotime($clubEvent->evnt_time_start)) }}</span>
                    <a @class(['event-cancelled' => $clubEvent->canceled == 1, 'event-name']) 
                        href="{{ URL::route('event.show', $clubEvent->id) }}" 
                        data-bs-toggle="tooltip"
                        data-bs-placement="right" title="{{ __('mainLang.showDetails') }}">
                        {{ $clubEvent->evnt_title }}
                    </a>
                </div>
            @endif


            {{-- show everything for members, but switch the color theme according to event type --}}
        @else
            <div id="cal-event-{{ $clubEvent->id }}"
                @include('partials.shiftsProgressBar', $clubEvent)
                data-total-shifts="{{$clubEvent->shifts->count()}}"
                data-empty-shifts="{{$clubEvent->shifts->filter(function ($shift) {
                    return is_null($shift->person_id) && $shift->optional == 0;
                 })->count()}}"
                data-opt-empty-shifts="{{$clubEvent->shifts->filter(function ($shift) {
                    return is_null($shift->person_id) && $shift->optional == 1;
                 })->count()}}"
                class="cal-event {{ $classString }} {{ \Lara\utilities\ViewUtility::getEventPaletteClass($clubEvent) }}">

                @include('partials.event-marker', $clubEvent)
                {{-- Show starting time with Preparation time in () --}}
                <span class="event-time">
                    &nbsp;{{ date('H:i', strtotime($clubEvent->evnt_time_start)) }}{{ $clubEvent->schedule->schdl_time_preparation_start != $clubEvent->evnt_time_start ? ' (' . date('H:i', strtotime($clubEvent->schedule->schdl_time_preparation_start)) . ')' : '' }}
                </span>
                {{--

                Disabling iCal until fully functional.

                @include("partials.publishStateIndicator")

                --}}

                <a @class(['event-cancelled'=> $clubEvent->canceled == 1, 'event-name'])
                href="{{ URL::route('event.show', $clubEvent->id) }}" 
                data-bs-toggle="tooltip"
                    data-bs-placement="right" title="{{ __('mainLang.showDetails') }}">
                    {{$clubEvent->evnt_title }}
                </a>

            </div>
        @endguest

        </div>
    @endif
@endforeach
