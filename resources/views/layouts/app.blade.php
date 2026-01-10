@props(['hideNav' => false])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Slot untuk CSS tambahan --}}
    @stack('styles')
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">

        {{-- LOGIC: Karena sudah didefinisikan di @props, kita bisa langsung cek boolean-nya --}}
        @if (!$hideNav)
        @include('layouts.navigation')
        @endif

        @if (isset($header))
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        <main>
            {{ $slot }}
        </main>
    </div>

    {{-- Slot untuk Javascript tambahan --}}
    @stack('scripts')
</body>

</html>