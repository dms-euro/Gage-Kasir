<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <meta charset="utf-8">
    <link href="dist/images/logo.svg" rel="shortcut icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="Enigma admin is super flexible, powerful, clean & modern responsive tailwind admin template with unlimited possibilities.">
    <meta name="keywords"
        content="admin template, Enigma Admin Template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="LEFT4CODE">
    <title>@yield('title', 'Sistem')</title>
    <link rel="stylesheet" href="{{ asset('templates/Compiled/dist/css/app.css') }}" />
    {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.css" rel="stylesheet" /> --}}
</head>

<body class="py-5 md:py-0">
    @include('layouts.topbar')
    <div class="flex overflow-hidden">

        @include('layouts.sidebar')

        <div class="content">
            @yield('content')
        </div>

    </div>

    @include('layouts.scripts')
    @stack('scripts')

</body>
    @include('layouts.alert')
</html>
