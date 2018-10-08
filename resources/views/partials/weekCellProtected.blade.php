<div class="card bg-warning">

	{{-- Check if the event is still going on --}}
    @php
        $classString = "card-header";
        $clubEventClass = "palette-". $clubEvent->section->color;
        switch ($clubEvent->evnt_type){
        case 0:
        $clubEventClass .="-700 bg";
        break;
        case 1:
        $clubEventClass .=" palette-Purple-500 bg";
        break;
        case 2:
        case 3:
        case 10:
        case 11:
        $clubEventClass .= "-900 bg";
        break;
        case 4:
        case 5:
        case 6:
        case 9:
        $clubEventClass .= "-500 bg";
        break;
        case 7:
        case 8:
        $clubEventClass .= "-300 bg";
        break;
        default:
        $clubEventClass .= "-500 bg";
        }
    @endphp
	{{-- Set card color --}}
    <div class="{{$classString}} {{$clubEventClass}}" >
			<h4 class="card-title">
				@include("partials.event-marker")
				&nbsp;
				<a class="{{$clubEventClass}}" href="{{ URL::route('event.show', $clubEvent->id) }}">
					<span class="name">{{ $clubEvent->evnt_title }}</span>
				</a>
			</h4>

			{{ utf8_encode(strftime("%a, %d. %b", strtotime($clubEvent->evnt_date_start))) }}
			&nbsp;
			DV: {{ date("H:i", strtotime($clubEvent->getSchedule->schdl_time_preparation_start)) }}
			&nbsp;
			<i class="far fa-clock"></i> {{ date("H:i", strtotime($clubEvent->evnt_time_start)) }}
			-
			{{ date("H:i", strtotime($clubEvent->evnt_time_end)) }}
			&nbsp;
			<i class="fas fa-map-marker">&nbsp;</i>{{ $clubEvent->section->title }}

		</div>

		{{-- Show password input if schedule needs one --}}
		@if( $clubEvent->getSchedule->schdl_password != '')
		    <div class="{{ $classString }} hidden-print">
		        {!! Form::password('password' . $clubEvent->getSchedule->id, ['required',
		                                             'class'=>'col-md-12 col-xs-12 black-text',
		                                             'id'=>'password' . $clubEvent->getSchedule->id,
		                                             'placeholder'=>Lang::get('mainLang.enterPasswordHere')]) !!}
		        <br />
		    </div>
		@endif

		<div class="card-body no-padding">

			@if (!is_null($clubEvent->getSchedule))

				{{-- Show shifts --}}
				@foreach($shifts = $clubEvent->getSchedule->shifts as $shift)
				    <div class="row{!! $shift !== $shifts->last() ? ' divider': false !!}">
				        {!! Form::open(  array( 'route' => ['shift.update', $shift->id],
				                                'id' => $shift->id,
				                                'method' => 'put',
				                                'class' => 'shift')  ) !!}

				        {{-- SPAMBOT HONEYPOT - this field will be hidden, so if it's filled, then it's a bot or a user tampering with page source --}}
				        <div id="welcome-to-our-mechanical-overlords">
				            <small>If you can read this this - refresh the page to update CSS styles or switch CSS support on.</small>
				            <input type="text" id="{!! 'website' . $shift->id !!}" name="{!! 'website' . $shift->id !!}" value="" />
				        </div>

				        {{-- SHIFT TITLE --}}
				        <div class="col-2 padding-right-minimal">
				            @include("partials.shiftTitle")
				        </div>

				        {{-- if shift occupied by member and the user is not logged in - show only the info without inputs --}}
				        @if(isset($shift->getPerson->prsn_ldap_id))

				        	{{-- SHIFT STATUS, USERNAME, DROPDOWN USERNAME and LDAP ID --}}
				            <div class="col-5 input-append btn-group padding-left-minimal">

							    <div class="col-2 no-padding" id="clubStatus{{ $shift->id }}">
							        @include("partials.shiftStatus")
							    </div>

							    <div class="col-10 no-padding" id="{!! 'userName' . $shift->id !!}" >
                                    @if($shift->getPerson->isNamePrivate() == 0)
							            {!! $shift->getPerson->prsn_name !!}
                                    @else
                                        @if(isset($shift->person->user))
                                            {{ trans($shift->person->user->section->title . '.' . $shift->person->user->status) }}
                                        @endif
                                    @endif
							    </div>

							    {{-- no need to show LDAP ID or TIMESTAMP in this case --}}

							</div>

							{{-- SHIFT CLUB --}}
							<div id="{!! 'club' . $shift->id !!}" class="col-xs-3 col-md-3 no-padding">
							    {!! "(" . $shift->getPerson->getClub->clb_title . ")" !!}
							</div>

							{{-- SHIFT COMMENT --}}
							{{-- Show only the icon first --}}
							<div class="col-xs-1 col-md-1 no-padding">
							    @if( $shift->comment == "" )
							        <button type="button" class="showhide btn-small btn-secondary hidden-print" data-dismiss="alert">
							            <i class="fas fa-comment-alt"></i>
							        </button>
							    @else
							        <button type="button" class="showhide btn-small btn-secondary hidden-print" data-dismiss="alert">
							            <i class="far fa-comment"></i>
							        </button>
							    @endif
							</div>

							{{-- Hidden comment field to be opened after the click on the icon
							     see vedst-scripts "Show/hide comments" function --}}
							<div id="{!! 'comment' . $shift->id !!}"
							     class="col-xs-10 col-md-10 hidden-print hide offset-2 word-break"
							     name="{!! 'comment' . $shift->id !!}">
							    {!! !empty($shift->comment) ? $shift->comment : "-" !!}
							</div>

				        @else

				            {{-- SHIFT STATUS, USERNAME, DROPDOWN USERNAME and LDAP ID --}}
				            <div class="col-5 input-append btn-group padding-left-minimal">
				                @include("partials.shiftName")
				            </div>

				            {{-- SHIFT CLUB and DROPDOWN CLUB --}}
				            <div class="col-3 no-padding">
				                @include("partials.shiftClub")
				            </div>

				            {{-- SMALL COMMENT ICON --}}
				            <div class="col-1 no-padding">
						        @if( $shift->comment == "" )
						            <button type="button" class="showhide btn-small btn-secondary hidden-print" data-dismiss="alert">
						                <i class="fas fa-comment-alt"></i>
						            </button>
						        @else
						            <button type="button" class="showhide btn-small btn-secondary hidden-print" data-dismiss="alert">
						                <i class="fas fa-comment"></i>
						            </button>
						        @endif
							</div>

							{{-- Hidden comment field to be opened after the click on the icon
								 see vedst-scripts "Show/hide comments" function --}}
							{!! Form::text('comment' . $shift->id,
							               $shift->comment,
							               array('placeholder'=>Lang::get('mainLang.addCommentHere'),
							                     'id'=>'comment' . $shift->id,
							                     'class'=>'col-xs-10 col-md-10 hidden-print hide offset-2 offset-2 word-break' ))
							!!}

				        @endif

				        {!! Form::submit( 'save', array('id' => 'btn-submit-changes' . $shift->id, 'hidden') ) !!}
				        {!! Form::close() !!}

				    </div>



				@endforeach

			@endif

		</div>

</div>
