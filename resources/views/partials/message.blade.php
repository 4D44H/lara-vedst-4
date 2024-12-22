{{-- STATUS MESSAGE: if there are any errors, show them here --}}
@if(session()->has('message'))
    @php
        $messageType = session('msgType') ? session('msgType') : 'info'
    @endphp
    <div class="alert alert-centered alert-dismissable alert-fixed alert-{{$messageType}}">
        <button type="button" class="btn-close float-end ms-2" data-dismiss="alert" aria-label="Close"></button>
        {{ session('message') }}
        @php
            session()->forget(['message', 'msgType']);
        @endphp
    </div>
@endif


