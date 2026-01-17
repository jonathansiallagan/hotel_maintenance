<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
{{-- Background Color disesuaikan dengan gambar (Pinkish/Beige) --}}

<body class="font-sans antialiased bg-[#DBC8C6]">

    <div class="flex h-screen overflow-hidden">

        {{-- 1. SIDEBAR --}}
        @include('layouts.partials.admin-sidebar')

        {{-- WRAPPER KONTEN --}}
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">

            {{-- 2. NAVBAR --}}
            @include('layouts.partials.admin-navbar')

            {{-- 3. MAIN CONTENT --}}
            <main class="p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

</body>

</html>