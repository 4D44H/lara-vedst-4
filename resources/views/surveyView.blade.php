@extends('layouts.master')

@section('title')
    Umfrage xyz
@stop

@section('content')
    <h1>Hier soll eine einzelne Umfrage angezeigt werden</h1>
    <div>
        {{ $survey->title }}
    </div>
    <div>
        {{ $survey->description }}
    </div>
    <div>
        Die Umfrage läuft bis:
        {{ $survey->deadline }}
    </div>

    <div>
        Fragen:
    </div>
    @foreach($questions as $question)
        <div>
            {{$question->content}}
            @foreach($answers[$question->id] as $answer)
                <div>
                    {{ $answer->name }}:
                    {{ $answer->content }}
                </div>
            @endforeach
        </div>
    @endforeach

@stop