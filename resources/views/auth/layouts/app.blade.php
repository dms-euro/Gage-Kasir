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
    <title>@yield('title', 'Auth')</title>
    <link rel="stylesheet" href="{{ asset('templates/Compiled/dist/css/app.css') }}" />
</head>

<body class="login">
    @yield('content')
    @include('auth.layouts.js')
</body>
@stack('scripts')
@include('layouts.alert')

</html>
