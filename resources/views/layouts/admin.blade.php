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

<body class="font-sans antialiased bg-[#DBC8C6]">

    <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden bg-[#DBC8C6]">

        {{-- 1. SIDEBAR --}}
        <div :class="sidebarOpen ? 'block fixed' : 'hidden lg:block'"
            class="shrink-0 w-72 transition-all duration-300 z-40 lg:z-auto lg:relative">
            @include('layouts.partials.admin-sidebar')
        </div>

        {{-- OVERLAY UNTUK MOBILE --}}
        <div x-show="sidebarOpen"
            @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-30 bg-black bg-opacity-50 lg:hidden cursor-pointer">
        </div>

        {{-- WRAPPER KONTEN --}}
        <div class="flex flex-col flex-1 overflow-hidden w-full">

            {{-- 2. NAVBAR --}}
            <div class="shrink-0">
                @include('layouts.partials.admin-navbar')
            </div>

            {{-- 3. MAIN CONTENT --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-8 bg-[#DBC8C6]">
                {{ $slot }}
            </main>
        </div>
    </div>
    @stack('scripts')
</body>

</html>