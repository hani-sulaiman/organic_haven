<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:title" content="" />
    <meta property="og:type" content="" />
    <meta property="og:url" content="" />
    <meta property="og:image" content="" />
    <link
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
  rel="stylesheet"
/>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/logo.png') }}" />

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/plugins/animate.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/admin/css/main.css?v=6.0') }}" />

    @vite(['resources/js/app.js'])
</head>

<body>
    <div id="app" data-layout="admin">

    </div>

</body>

</html>