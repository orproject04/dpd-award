<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>{{ __('home.home') }} | {{ config('laravolt.ui.brand_name') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles -->
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    @stack('meta')

    @laravoltStyles
    @livewireStyles

    @stack('style')
    @stack('head')
</head>

<body style="width: 100%">
    <div class="mx-auto overflow-hidden pt-24">
        <!-- Layout untuk content -->
        @yield('content')
    </div>
</body>

</html>