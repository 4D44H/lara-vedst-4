@php
    /***
    * @var \Lara\Shift $shift
    * @var boolean $hideComments
    * @var @deprecated boolean $commentsInSeparateLine
    */

if(Auth::user()){
    $autocomplete = 'autocomplete';
} else {
    $autocomplete = '';
}

if($hideComments){
    $commentClass = 'hide';
} else {
    $commentClass = '';
}
@endphp


<div class="d-flex flex-wrap gap-1 align-items-center justify-content-between p-2 shiftRow mt-2 mb-2 border-bottom  {!! ( isset($shift->person->prsn_ldap_id)
                                                  && Auth::user()
                                                  && $shift->person->prsn_ldap_id === Auth::user()->person->prsn_ldap_id) ? "my-shift" : false !!}">
    <div class="d-none">
        @include('partials.shifts.updateShiftFormOpener',['shift'=>$shift, 'autocomplete'=>$autocomplete])
        {{-- SPAMBOT HONEYPOT - this field will be hidden, so if it's filled, then it's a bot or a user tampering with page source --}}
        <div class="welcome-to-our-mechanical-overlords">
            <small>If you can read this this - refresh the page to update CSS styles or switch CSS support on.</small>
            <input type="text" id="{!! 'website' . $shift->id !!}" name="{!! 'website' . $shift->id !!}" value=""/>
        </div>
        <button type="submit" class="hidden hide"></button>
        {!! Form::close() !!}
    </div>

    {{-- Name and time of the shift --}}
    <div class="align-content-center" style="width:15%; min-width:92px">
        @include("partials.shiftTitle")
    </div>

    {{-- User status icon --}}
    <div style="width:30px">
        @include('partials.shifts.updateShiftFormOpener',['shift'=>$shift, 'autocomplete'=>$autocomplete])
        <div id="clubStatus{{ $shift->id }}">
            @include("partials.shiftStatus")
        </div>
        {!! Form::close() !!}
    </div>
    @if( isset($shift->getPerson->prsn_ldap_id) && !Auth::user())
        <div style="width:15%; min-width:100px;">
            <div class="form-group form-group-sm">
                @if($shift->getPerson->isNamePrivate() == 0)
                    {{-- Shift USERNAME--}}
                    <div id="{!! 'userName' . $shift->id !!}" class="form-control form-control-sm">
                        {!! $shift->getPerson->prsn_name !!}
                    </div>
                @else
                    <div id="{!! 'userName' . $shift->id !!}" class="form-control form-control-sm">
                        @if(isset($shift->person->user))
                            {{ __($shift->person->user->section->title . '.' . $shift->person->user->status) }}
                        @endif
                    </div>
                @endif
            </div>
        </div>
        {{-- SHIFT CLUB --}}
        <div style="width:10%; min-width:70px;">
            <div id="{!! 'club' . $shift->id !!}" class="form-group form-group-sm ">
                <div class="form-control form-control-sm">
                    {!! "(" . $shift->getPerson->getClub->clb_title . ")" !!}
                </div>
            </div>
        </div>
        <div style="width:32px;">
            <button class="showhide btn btn-sm btn-outline-secondary">
                @if($shift->comment === "") <i
                    class="far fa-comment"></i> @else <i class="fa-solid fa-comment"></i> @endif</button>
        </div>
        {{-- COMMENT SECTION --}}
        <div class="{{$commentColumnClass}}">
            <div class="form-group from-group-sm hidden-print text-break">
                        <span class="w-auto @if(isset($hideComments) && $hideComments) hide @endif"
                              id="{{'comment'.$shift->id}}"
                              name="{{'comment' . $shift->id}}">{!! !empty($shift->comment) ? $shift->comment : "-" !!}</span>
            </div>
        </div>
    @else
        {{-- show everything for members --}}
        {{-- SHIFT STATUS, USERNAME, DROPDOWN USERNAME and LDAP ID --}}
        <div style="width:15%; min-width:100px;">
            @include('partials.shifts.updateShiftFormOpener',['shift'=>$shift, 'autocomplete'=>$autocomplete])
            <div class="form-group form-group-sm">
                @include('partials.shifts.shiftName',['shift'=>$shift])
            </div>
            <button type="submit" class="hidden hide"></button>
            {!! Form::close() !!}
        </div>
        {{-- SHIFT CLUB and DROPDOWN CLUB --}}
        <div style="width:10%; min-width:70px;">
            @include('partials.shifts.updateShiftFormOpener',['shift'=>$shift, 'autocomplete'=>$autocomplete])
            <div class="form-group form-group-sm ">
                @include('partials.shifts.shiftClub',['shift'=>$shift])
            </div>
            <button type="submit" class="hidden hide"></button>
            {!! Form::close() !!}
        </div>
        {{-- COMMENT SECTION --}}
        {{-- Hidden comment field to be opened after the click on the icon
             see filter-scripts "Show/hide comments" function --}}
        <div style="width:33px;">
            <button class="showhide btn btn-sm btn-outline-secondary">@if($shift->comment === "") <i
                    class="far fa-comment"></i> @else <i class="fa-solid  fa-comment"></i> @endif</button>
        </div>
        <div class="flex-fill">
            @include('partials.shifts.updateShiftFormOpener',['shift'=>$shift, 'autocomplete'=>$autocomplete])
            <div class="form-group from-group-sm hidden-print text-break">

                {!! Form::text('comment' . $shift->id,
                                           $shift->comment,
                                           ['placeholder'=>Lang::get('mainLang.addCommentHere'),
                                                 'id'=>'comment' . $shift->id,
                                                  'name'=>'comment' . $shift->id,
                                                 'class'=>'form-control form-control-sm '. $commentClass])
                                !!}

            </div>
            <button type="submit" class="hidden hide"></button>
            {!! Form::close() !!}
        </div>
    @endif
</div>


