<header>
    <nav class="navbar navbar-expand-md bg-body-tertiary fixed-top">
        <div class="container-fluid">

            {{-- LARA LOGO --}}
            <a class="navbar-brand" href="{{ asset('/') }}">
                @if (App::environment('development') || App::environment('local'))
                    <img id="nav-logo-field" src="{{ asset('/logos/lara-logo-dev.png') }}" height="48" alt="LARA dev">
                @elseif (App::environment('berta'))
                    <img id="nav-logo-field" src="{{ asset('/logos/lara-logo-berta.png') }}" height="48"
                        alt="BERTA">
                @else
                    <img id="nav-logo-field" src="{{ asset('/logos/lara-logo-prod.png') }}" height="48"
                        alt="LARA">
                @endif
            </a>

            {{-- HAMBURGER / Mobile only --}}
            <button type="button" class="navbar-toggler collapsed btn btn-primary" data-bs-toggle="collapse"
                data-bs-target="#navbar" aria-expanded="false" aria-controls="navbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div id="navbar" class="collapse navbar-collapse">
                <ul class="navbar-nav mb-2 mb-lg-0 me-auto">

                    {{-- MONTH VIEW / public --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ asset('/calendar/month') }}">
                            {{ __('mainLang.month') }}
                        </a>
                    </li>

                    {{-- WEEK VIEW / public --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ asset('/calendar/week') }}">
                            {{ __('mainLang.week') }}
                        </a>
                    </li>

                    {{-- DAY VIEW / public --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ asset('/calendar/today') }}">
                            {{ __('mainLang.today') }}
                        </a>
                    </li>

                    {{-- MEMBER STATISTICS / members only --}}
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ action('StatisticsController@showStatistics') }}">
                                <i class="fa fa-square-poll-vertical" aria-hidden="true" data-bs-toggle="tooltip"
                                    data-bs-placement="left" title="{{ __('mainLang.statisticalEvaluation') }}"></i>
                                <span class="d-md-none ms-2">{{ __('mainLang.statisticalEvaluation') }}</span>
                            </a>
                        </li>
                    @endauth

                    {{-- LANGUAGE SWITCHER / public --}}
                    <li class="nav-item dropdown">
                        <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown" role="button"
                            aria-expanded="false">
                            <i class="fa fa-language" data-bs-toggle="tooltip" data-bs-placement="left"
                                title="{{ __('mainLang.language') }}"></i>
                            <span class="d-md-none ms-2">{{ __('mainLang.language') }}</span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            @foreach (config('languages') as $lang => $language)
                                <li class="languageSwitcher dropdown-item"><a class="nav-link"
                                        href="{{ route('lang.switch', $lang) }}"
                                        data-language="{{ $lang }}"><i class="fa fa-globe"
                                            aria-hidden="true"></i> {{ $language }}
                                    </a></li>
                            @endforeach
                        </ul>
                    </li>

                    {{-- Theme switcher --}}
                    @include('partials/themeSwitcher')

                    {{-- SETTINGS (GEAR ICON) / marketing, section management or admins only --}}
                    @is(Roles::PRIVILEGE_MARKETING, Roles::PRIVILEGE_CL, Roles::PRIVILEGE_ADMINISTRATOR)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" role="button"
                                aria-expanded="false">
                                <i class="fa fa-cog" data-bs-toggle="tooltip" data-bs-placement="left" title="{{__('mainLang.settings')}}"></i>
                                <span class="d-md-none ms-2">{{__('mainLang.settings')}}</span>
                            </a>

                            {{-- SHIFT TYPES AND TEMPLATE MANAGEMENT / marketing, section management or admins only --}}
                            <ul class="dropdown-menu">
                                <li class="dropdown-item">
                                    <a class="nav-link" href="{{ asset('shiftType') }}">
                                        <i class="fa fa-magic" aria-hidden="true"></i>
                                        {{ __('mainLang.manageShiftType') }}
                                    </a>
                                </li>
                                <li class="dropdown-item">
                                    <a class="nav-link" href="{{ route('template.overview') }}">
                                        <i class="fa fa-magic" aria-hidden="true"></i>
                                        {{ __('mainLang.manageTemplates') }}
                                    </a>
                                </li>

                                {{-- USER AND SECTION MANAGEMENT / section management or admins only --}}
                                @is(Roles::PRIVILEGE_CL, Roles::PRIVILEGE_ADMINISTRATOR)
                                    <li class="dropdown-item">
                                        <a class="nav-link" href="{{ route('user.index') }}">
                                            <i class="fa fa-users" aria-hidden="true"> </i>
                                            {{ __('users.userManagement') }}
                                        </a>
                                    </li>
                                    <li class="dropdown-item">
                                        <a class="nav-link" href="{{ asset('section') }}">
                                            <i class="fa fa-university" aria-hidden="true"></i>
                                            {{ __('mainLang.manageSections') }}
                                        </a>
                                    </li>
                                @endis
                                {{-- LARA ADMINISTRATION / admins only --}}
                                @is(Roles::PRIVILEGE_ADMINISTRATOR)
                                    <li role="separator" class="divider dropdown-item"></li>
                                    <li class="dropdown-item">
                                        <a class="nav-link" href="{{ asset('/logs') }}">
                                            <i class="fa fa-list-alt" aria-hidden="true"></i>
                                            Logs
                                        </a>
                                    </li>
                                    <li role="separator" class="divider dropdown-item"></li>
                                    <li class="dropdown-item">
                                        <a class="nav-link" href="{{ route('lara.update') }}">
                                            <i class="fa fa-chevron-circle-up" aria-hidden="true"></i>
                                            Lara update </a>
                                    </li>
                                @endis
                            </ul>
                        </li>
                    @endis


                    {{-- ICal feed links
            Disabling iCal until fully functional.

                                <li><a href="#" name="icalfeeds"><i class="fa fa-calendar" aria-hidden="true"></i> {{ __('mainLang.icalfeeds') }}</a></li>

            --}}
            
                    {{-- AUTHENTICATION --}}
                    @auth

                        {{-- CREATE BUTTONS / members only --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fa fa-plus text-primary"></i>
                                <span class="d-md-none ms-2">Create</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item"
                                        href="{{ URL::route('event.create') }}">{{ __('mainLang.createNewEvent') }}</a>
                                </li>
                                <li><a class="dropdown-item"
                                        href="{{ URL::route('survey.create') }}">{{ __('mainLang.createNewSurvey') }}</a>
                                </li>
                            </ul>
                        </li>
                    </ul>

                    {{-- MEMBER INFO / members only --}}
                    <ul class="navbar-nav mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link py-0" href="{{ route('user.personalpage') }}">
                                @php
                                    $attributes = Lara\Status::style(Auth::user()->status);
                                @endphp
                                <i class="{{ $attributes['status'] }}" style="{{ $attributes['style'] }}"
                                    data-bs-toggle="tooltip" data-bs-placement="left"
                                    title="{{ Lara\Status::localizeCurrent() }}"></i>
                                <span data-bs-toggle="tooltip" data-bs-placement="bottom"
                                    title="{{ __('mainLang.userPersonalPage') }}">
                                    <strong>{{ Auth::user()->name }}</strong>
                                    <br>
                                    @is(Roles::PRIVILEGE_ADMINISTRATOR)
                                        ({{ Auth::user()->section->title . ' / Admin' }})
                                    @elseis(Roles::PRIVILEGE_CL)
                                        ({{ Auth::user()->section->title . ' / Clubleitung' }})
                                    @elseis(Roles::PRIVILEGE_MARKETING)
                                        ({{ Auth::user()->section->title . ' / Marketing' }})
                                    @else
                                        ({{ Auth::user()->section->title }})
                                    @endis
                                </span>
                            </a>
                        </li>

                        {!! Form::open(['url' => 'logout', 'method' => 'POST', 'class' => 'd-flex align-items-center']) !!}
                        <button type="submit" class="btn btn-outline-danger ms-1 float-end" name="logout">
                            <i class='fa fa-door-open' data-bs-toggle="tooltip" data-bs-placement="left"
                                title="{{__('mainLang.logOut')}}"></i>
                        </button>
                        {!! Form::close() !!}
                    </ul>
                @else
                    </ul>
                {{-- LOGIN FORM / public --}}
                @include('partials/login')
                @endauth

            </div>
        </div>
    </nav>
</header>
