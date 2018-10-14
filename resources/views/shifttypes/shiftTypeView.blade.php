{{-- Needs variable: $current_shiftType, $shiftTypes, $shifts --}}
@extends('layouts.master')

@section('title')
	{{ trans('mainLang.management') }}: #{{ $current_shiftType->id }} - {!! $current_shiftType->title !!}
@stop

@section('content')

@is(Roles::PRIVILEGE_ADMINISTRATOR, Roles::PRIVILEGE_CL, Roles::PRIVILEGE_MARKETING)
	<div class="card card.text-white.bg-info">
		<div class="card-header">
			<h4 class="card-title">#{{ $current_shiftType->id }}: "{!! $current_shiftType->title !!}" </h4>
		</div>
		<div class="card card-body no-padding">
			<table class="table table-hover">
				{!! Form::open(  array( 'route' => ['shiftType.update', $current_shiftType->id],
		                                'id' => $current_shiftType->id,
		                                'method' => 'PUT',
		                                'class' => 'shiftType')  ) !!}
					<tr>
						<td width="20%" class="padding-left-16px">
							<i>{{ trans('mainLang.shiftType') }}:</i>
						</td>
						<td>
							{!! Form::text('title' . $current_shiftType->id,
							   $current_shiftType->title,
							   array('id'=>'title' . $current_shiftType->id)) !!}
						</td>
					</tr>
					<tr>
						<td width="20%" class="padding-left-16px">
							<i>{{ trans('mainLang.begin') }}:</i>
						</td>
						<td>
							{!! Form::input('time','start' . $current_shiftType->id,
							   $current_shiftType->start,
							   array('id'=>'start' . $current_shiftType->id)) !!}
						</td>
					</tr>
					<tr>
						<td width="20%" class="padding-left-16px">
							<i>{{ trans('mainLang.end') }}:</i>
						</td>
						<td>
							{!! Form::input('time','end' . $current_shiftType->id,
							   $current_shiftType->end,
							   array('id'=>'end' . $current_shiftType->id)) !!}
						</td>
					</tr>
					<tr>
						<td width="20%" class="padding-left-16px">
							<i>{{ trans('mainLang.weight') }}:</i>
						</td>
						<td>
							{!! Form::text('statistical_weight' . $current_shiftType->id,
							   $current_shiftType->statistical_weight,
							   array('id'=>'statistical_weight' . $current_shiftType->id)) !!} <br/>
						</td>
					</tr>
					<tr>
						<td>
							&nbsp;
						</td>
						<td>
							<button type="reset" class="btn btn-small btn-secondary">{{ trans('mainLang.reset') }}</button>
					    	<button type="submit" class="btn btn-small btn-success">{{ trans('mainLang.update') }}</button>
						</td>
					</tr>
				{!! Form::close() !!}

				@if( $current_shiftType->shifts->count() == 0 )
					<tr>
						<td width="100%" colspan="2" class="padding-left-16px">
							{{ trans('mainLang.shiftTypeNeverUsed') }}
							<a href="../shiftType/{{ $current_shiftType->id }}"
							   class="btn btn-small btn-danger"
							   data-toggle="tooltip"
			                   data-placement="bottom"
			                   title="&#39;&#39;{!! $current_shiftType->title !!}&#39;&#39; (#{{ $current_shiftType->id }}) löschen"
							   data-method="delete"
							   data-token="{{csrf_token()}}"
							   rel="nofollow"
							   data-confirm="{{ trans('mainLang.deleteConfirmation') }} &#39;&#39;{!! $current_shiftType->title !!}&#39;&#39; (#{{ $current_shiftType->id }})? {{ trans('mainLang.warningNotReversible') }}">
								   	{{ trans('mainLang.delete') }}
							</a>
							?
						</td>
					</tr>
				@else
					<tr>
						<td width="100%" colspan="2" class="padding-left-16px">
					      	{{ trans('mainLang.shiftTypeUsedInFollowingEvents') }}
					    </td>
                    </tr>
					<tr>
						<td width="100%" colspan="2" class="no-padding">
							<table class="table table-hover table-sm" id="events-rows">
								<thead>
									<tr class="active">
										<th class="col-md-1 col-xs-1 text-center">
											#
										</th>
										<th class="col-md-2 col-xs-2 text-center">
											{{ trans('mainLang.event') }}
										</th>
										<th class="col-md-2 col-xs-2 text-center">
											{{ trans('mainLang.section') }}
										</th>
										<th class="col-md-2 col-xs-2 text-center">
											{{ trans('mainLang.date') }}
										</th>
										<th class="col-md-2 col-xs-2 text-center">
											{{ trans('mainLang.actions') }}
										</th>
									</tr>
								</thead>
								<tbody>
									@foreach($shifts as $shift)
                                        {{-- ignore shifts without an event, example: shifts from bd-template  --}}
                                        @if(is_null($shift->schedule) || is_null($shift->schedule->evnt_id))
                                            @continue
                                        @endif
                                        @php
                                            $isAllowedToEdit=\Lara\Utilities::requirePermission("admin") || $shift->schedule->event->section->title == Session::get('userClub');
                                        @endphp

										<tr class="{!! "shiftType-event-row" . $shift->id !!} @if(!$isAllowedToEdit) active @endif" name="{!! "shiftType-event-row" . $shift->id !!}">
											<td class="text-center">
										      	{!! $shift->schedule->event->id !!}
											</td>
											<td class="text-center">
                                                @if($isAllowedToEdit)
												    <a href="/event/{!! $shift->schedule->event->id !!}">{!! $shift->schedule->event->evnt_title !!}</a>
                                                @else
                                                    {{ $shift->schedule->event->evnt_title }}
                                                @endif
											</td>
											<td class="text-center">
												{!! $shift->schedule->event->section->title !!}
											</td>
											<td class="text-center">
												{!! strftime("%a, %d. %b %Y", strtotime($shift->schedule->event->evnt_date_start)) !!} um
												{!! date("H:i", strtotime($shift->schedule->event->evnt_time_start)) !!}
											</td>
											<td class="text-center">
                                                @if($isAllowedToEdit)
                                                    @include('shifttypes.shiftTypeSelect',['shift'=>$shift,'shiftTypes' => $shiftTypes,'route'=>'shiftTypeOverride','shiftTypeId'=>$current_shiftType->id,'selectorClass'=>'shiftTypeSelector'])
                                                 @endif
											</td>
										</tr>
									@endforeach

								</tbody>
							</table>
						</td>
                    </tr>
                    <tr>
                        <td width="100%" colspan="2" class="padding-left-16px">
                            {{ trans('mainLang.shiftTypeUsedInFollowingTemplates') }}
                        </td>
                    </tr>
                    <tr>
                        <td width="100%" colspan="2" class="no-padding">
                            <table class="table table-hover table-sm" id="events-rows">
                                <thead>
                                <tr class="active">
                                    <th class="col-md-3 col-xs-3 text-center">
                                        #
                                    </th>
                                    <th class="col-md-3 col-xs-3 text-center">
                                        {{ trans('mainLang.template') }}
                                    </th>
                                    <th class="col-md-3 col-xs-3 text-center">
                                        {{ trans('mainLang.section') }}
                                    </th>
                                    <th class="col-md-3 col-xs-3 text-center">
                                        {{ trans('mainLang.actions') }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($templates as $template)
                                        @foreach($template->shifts as $shift)
                                            @if($shift->shifttype_id != $current_shiftType->id)
                                                @continue
                                            @endif
                                            @php
                                                $isAllowedToEdit=\Lara\Utilities::requirePermission("admin") ||  Auth::user()->getSectionsIdForRoles(Roles::PRIVILEGE_MARKETING)->contains($template->section->id);
                                            @endphp
                                        <tr class="@if(!$isAllowedToEdit) active @endif">
                                            <td class="text-center">
                                                {{ $shift->id}}
                                            </td>
                                            <td class="text-center">
                                                @if($isAllowedToEdit)
                                                    <a href="{{ route('template.edit', $template->id) }}">
                                                        {{ $template->title }}
                                                    </a>
                                                @else
                                                    {{ $template->title }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ $template->section->title }}
                                            </td>
                                            <td class="text-center">
                                              @if($isAllowedToEdit)
                                                    @include('shifttypes.shiftTypeSelect',['shift'=>$shift,'shiftTypes' => $shiftTypes, 'route'=>'shiftTypeOverride','shiftTypeId'=>$current_shiftType->id, 'selectorClass'=>'shiftTypeSelector'])
                                              @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
					</tr>
				@endif
			</table>
		</div>
	</div>

	<div class="text-center">
		{{ $shifts->links() }}
	</div>

	<br/>
@else
	@include('partials.accessDenied')
@endis
@stop



