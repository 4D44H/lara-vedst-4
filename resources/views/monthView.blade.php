@extends('layouts.master')
@section('title')
        {{ $date['monthName'] . " " . $date['year'] }}
@stop
@section('content')

    <div class="container">
        {{-- prev/next month --}}
        <div class="col-xs-12 col-md-5 btn-group">

            <a class="btn btn-default hidden-print" 
               href="{{ Request::getBasePath() }}/calendar/{{ date("Y/m", 
                        strtotime("previous month", $date['startStamp'])) }}">
                &lt;&lt;
            </a>

                <span class="btn btn-lg disabled" style="text-align: center !important;">   
                    {{ $date['monthName'] . " " . $date['year'] }} 
                </span>

            <a class="btn btn-default hidden-print" 
               href="{{ Request::getBasePath() }}/calendar/{{ date("Y/m", 
                        strtotime("next month", $date['startStamp'])) }}">
                &gt;&gt;
            </a>

        </div>

        {{-- placeholder for a middle button --}}
        <div class="col-xs-12 col-md-3">
            &nbsp;
        </div>

        {{-- filter --}}
        <div class="col-xs-12 col-md-4 pull-right">
            @include('partials.filter')
        </div>
    </div>
        <br class="hidden-xs">

{{-- month table --}}
<div class="panel">
    <table class="table table-bordered ">
          
        <thead>
            <tr>
                <th>KW</th>
                <th>Mo</th>
                <th>Di</th>
                <th>Mi</th>
                <th>Do</th>
                <th>Fr</th>
                <th>Sa</th>
                <th>So</th>
            </tr>
        </thead>
  
        <tbody>
        @for($i = 1; $i <= $date['daysOfMonth'] + ($date['startDay'] - 1) + (7 - $date['endDay']); $i++)
               
                {{-- if monday then start new line --}}
                @if(date("N", strtotime($i - $date['startDay'] . " day", $date['startStamp'])) == 1)
                        @if ( date('W', strtotime($i - $date['startDay'] . ' day', $date['startStamp'])) === date("W") )   
                            <tr class="light-grey">
                        @else
                            <tr>
                        @endif
                            <td>
                                <a href="{!! Request::getBasePath() !!}/calendar/{!! $date['year'] !!}/KW{{ date('W', strtotime($i - $date['startDay'] . ' day', $date['startStamp'])) }}">
                                    {{ date("W", strtotime($i - $date['startDay'] . " day", $date['startStamp'])) }}.
                                </a>
                            </td>
                            
                @endif
       


                {{-- Show table  --}}
                @if($i - $date['startDay'] >= 0 AND $i-$date['startDay'] < $date['daysOfMonth'])
               
            

                        @if( date("Y-m-d", strtotime($i - $date['startDay']." day", $date['startStamp'])) === date( "Y-m-d" ) )
                            <td class="thisMonth today-marker" width=14%>
                        @else
                            <td class="thisMonth" width=14%>
                        @endif      
                                @include('partials.monthCell', $date)
                        </td>
                @else 
                        <td class="otherMonth" width=14%>
                                {{-- date("j", strtotime($i-$date['startDay']." day", $date['startStamp'])) --}}
                        </td>
                @endif
                
                {{-- if sunday then end this line --}}
                @if(date("N",date("j", strtotime($i-$date['startDay']." day", $date['startStamp']))) == 7)
                        </tr>
                @endif
               
        @endfor
        </tbody>        
           
    </table>
</div>

{{-- Legend --}}
@include("partials.legend")

{{-- filter hack --}}
<span id="own-filter-marker" hidden>&nbsp;</span>

@stop