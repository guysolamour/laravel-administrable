<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    {{-- Required meta tags --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="fr">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @yield('seo')


    {{-- Bootstrap --}}
    <link rel="stylesheet" href="{{ asset('css/vendor/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vendor/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vendor/sweetalert.css') }}">

    {{-- Flashy --}}
    <link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href='//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700' rel='stylesheet'>

    @stack('css')
</head>
<body>

<main id="app">
    @include(front_view_path('partials._header'))
    @yield('content')
    @include(front_view_path('partials._footer'))
</main>



<script src="{{ asset('js/vendor/jquery.min.js') }}"></script>
<script src="{{ asset('js/vendor/popper.min.js') }}"></script>

<script src="{{ asset('js/vendor/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/vendor/sweetalert.min.js') }}"></script>

<script src="{{ asset('js/vendor/helpers.js') }}"></script>

{{-- <script src="{{ asset('js/vendor/fontawesome.js') }}"></script> --}}
<script src="{{ asset('js/vendor/larails-alert.js') }}"></script>
@stack('js')
@flashy()

@include('cookie-consent::index')

{{-- add livenews extension here --}}

</body>
</html>
