<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HMS Hotel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-sm bg-white rounded-xl shadow-lg overflow-hidden">
        {{-- Header Biru --}}
        <div class="bg-blue-600 p-6 text-center">
            <h1 class="text-2xl font-bold text-white tracking-wide">HMS HOTEL</h1>
            <p class="text-blue-100 text-sm mt-1">Facility Maintenance System</p>
        </div>

        {{-- Form Login --}}
        <div class="p-8">
            @if (session('status'))
            <div class="mb-4 text-sm text-green-600">
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-4">
                    <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Email / ID</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                        placeholder="user@hotel.com">
                    @error('email')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-6">
                    <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm"
                        placeholder="********">
                    @error('password')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Tombol Login --}}
                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 active:bg-blue-800 transition duration-300 shadow-md transform active:scale-95">
                    MASUK SEKARANG
                </button>

                {{-- Link Register (Penting untuk Tes) --}}
                <div class="mt-4 text-center">
                    <a href="{{ route('register') }}" class="text-xs text-blue-600 hover:underline">
                        Belum punya akun? Daftar disini
                    </a>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <div class="bg-gray-50 py-3 text-center border-t border-gray-100">
            <p class="text-[10px] text-gray-500">© 2026 Hotel Engineering Division</p>
        </div>
    </div>

</body>

</html>