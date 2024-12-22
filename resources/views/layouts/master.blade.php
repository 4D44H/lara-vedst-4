<!DOCTYPE html>
<html>
	<head>
		<title>Lara | @yield('title', 'Default Title')</title>
        <meta charset="UTF-8">
		<meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
        <meta http-equiv="Cache-control" content="no-cache">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        {{-- to avoid flickering, this is loaded before the stylesheet: --}}
        <script src="{{asset(WebpackBuiltFiles::$assets['darkmode.js'])}}" ></script>
        <link rel="stylesheet" type="text/css" href="{{ asset(WebpackBuiltFiles::$assets['lara.css']) }}">
        {{--
        <link rel="stylesheet" type="text/css" href="{{ mix('/static.css') }}">
        --}}
        @if (App::environment('development') || App::environment('local'))
            <link rel="shortcut icon" type="image/png" href="{{ asset('/favicon-demo-48x48.png') }}">
        @elseif (App::environment('berta'))
            <link rel="shortcut icon" type="image/png" href="{{ asset('/favicon-berta-48x48.png') }}">
        @else
    	<link rel="shortcut icon" type="image/png" href="{{ asset('/favicon-48x48.png') }}">
        @endif

        @yield('moreStylesheets')
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="{{asset(WebpackBuiltFiles::$assets['legacy.js'])}}" ></script>
        <![endif]-->
	</head>

    <body @if(session("language","de")  == "pirate")
              style="background-image:url( {{asset('/background-pirate.jpg')}} ) !important;
                     background-size:initial;
                     background-position:center;"
          @endif>

        <!-- Set the language to the same as the server -->
        <script type="text/javascript">
            localStorage.setItem('language', "{{ session("language","de") }}");
        </script>


		@include('partials.navigation')

		<div class="message" id="centerpoint">
			@include('partials.message')
		</div>

        <section id="main-container" class="container-fluid p-4">
            @yield('content')
        </section>

        <!-- Back to Top button -->
        <a id="back-to-top"
           href="#"
           class="btn btn-primary btn-lg back-to-top hidden-print d-md-none d-lg-none d-sm-block d-block"
           role="button"
           title="{{ __("mainLang.backToTop")  }}"
           data-bs-toggle="tooltip"
           data-bs-placement="right">
            <i class="fa-solid  fa-chevron-up"></i>
        </a>

        <br>

     	<section class="footer">
            @include('partials.footer')
        </section>

        <script> var enviroment = '{{App::environment()}}'; </script>
        <script src="{{asset(WebpackBuiltFiles::$assets['lara.js'])}}" ></script>
        @yield('moreScripts')
        {{--
            <script src="{{ mix('/manifest.js') }}"></script>
            <script src="{{ mix('/vendor.js') }}"></script>
            <script src="{{ mix('/static.js') }}"></script>
            <script src="{{ mix('/lara.js') }}"></script>
        --}}
    </body>
</html>
