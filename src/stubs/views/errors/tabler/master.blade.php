<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>@yield('title')</title>

    <!-- CSS files -->
    <link href="{{ asset('vendor/tabler/css/tabler.min.css') }}" rel="stylesheet" />

    <style>
        body {
            display: none;
        }
    </style>
</head>

<body class="antialiased border-top-wide border-primary d-flex flex-column">
    <div class="flex-fill d-flex align-items-center justify-content-center">
        @yield('content')
    </div>

    <script>
        document.body.style.display = "block"
    </script>
</body>

</html>
