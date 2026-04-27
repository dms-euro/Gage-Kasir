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
    <style>
        @media (max-width: 768px) {
            .content {
                padding-bottom: 70px;
            }
        }

        @media (max-width: 768px) {
            ::-webkit-scrollbar {
                width: 0px;
                background: transparent;
            }
        }
    </style>
</head>

<body class="py-5 md:py-0">

    <div class="hidden md:block">
        @include('layouts.topbar')
    </div>

    <div class="flex overflow-hidden">

        <div class="hidden md:block">
            @include('layouts.sidebar')
        </div>

        <div class="content w-full min-h-screen pb-20 md:pb-0">
            @yield('content')
        </div>

    </div>

    @include('layouts.mobile-nav')

    @include('layouts.scripts')
    @stack('scripts')

</body>
@include('layouts.alert')

</html>
