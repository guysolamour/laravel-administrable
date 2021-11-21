<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        {{-- CSRF Token --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @yield('seo')


        <!-- GOOGLE FONTS -->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500|Poppins:400,500,600,700|Roboto:400,500" rel="stylesheet" />
        <link href="https://cdn.materialdesignicons.com/4.4.95/css/materialdesignicons.min.css" rel="stylesheet" />


        <!-- PLUGINS CSS STYLE -->
        <link href="{{ asset('vendor/sleek/assets/plugins/nprogress/nprogress.css') }}" rel="stylesheet" />



        <!-- SLEEK CSS -->
        <link id="sleek-css" rel="stylesheet" href="{{ asset('vendor/sleek/assets/css/sleek.css') }}" />

        <!-- FAVICON -->
        <link href="{{ asset('vendor/sleek/assets/img/favicon.png') }}" rel="shortcut icon" />



        <!--
            HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries
        -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
            <![endif]-->
            <script src="{{ asset('vendor/sleek/assets/plugins/nprogress/nprogress.js') }}"></script>
    </head>
    <body class="" id="body">
        @yield('content')
        <div class="copyright pl-0">
            <p class="text-center">&copy; {{ date('Y') }} Tous droits reservés
            </p>
        </div>
    </div>
</body>
</html>
