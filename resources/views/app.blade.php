<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/logo.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/user/css/main.css?v=6.0') }}" />
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app" data-layout="user"></div>
</body>
</html>