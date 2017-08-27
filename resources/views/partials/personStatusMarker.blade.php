@if($person->prsn_status === 'candidate' )
    <i class="fa fa-adjust"
       name="status-icon"
       style="color:yellowgreen;"
       data-toggle="tooltip"
       data-placement="top"
       title="{{ trans('mainLang.candidate') }}"></i>
@elseif ( $person->prsn_status === 'veteran' )
    <i class="fa fa-star"
       name="status-icon"
       style="color:gold;"
       data-toggle="tooltip"
       data-placement="top"
       title="{{ trans('mainLang.veteran') }}"></i>
@elseif ( $person->prsn_status === 'member')
    <i class="fa fa-circle"
       name="status-icon"
       style="color:forestgreen;"
       data-toggle="tooltip"
       data-placement="top"
       title="{{ trans('mainLang.active') }}"></i>
@elseif ( $person->prsn_status === 'resigned' )
    <i class="fa fa-star-o"
       name="status-icon"
       style="color:gold;"
       data-toggle="tooltip"
       data-placement="top"
       title="{{ trans('mainLang.ex-member') }}"></i>
@elseif ( $person->prsn_status === 'guest' )
    <i class="fa fa-times-circle-o"
       name="status-icon"
       style="color:yellowgreen;"
       data-toggle="tooltip"
       data-placement="top"
       title="{{ trans('mainLang.ex-candidate') }}"></i>
@elseif ( empty($person->prsn_status) )
    <i class="fa fa-circle"
       name="status-icon"
       style="color:lightgrey;"
       data-toggle="tooltip"
       data-placement="top"
       title="{{ trans('mainLang.external') }}"></i>
@endif
