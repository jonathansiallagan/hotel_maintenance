<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - HMS Hotel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-sm bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-[#BFAFB0] p-4 text-center">
            <h1 class="text-lg font-bold text-[#6F6C6C]">Buat Akun Baru</h1>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- Nama --}}
                <div class="mb-3">
                    <label class="block text-gray-700 text-xs font-bold mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label class="block text-gray-700 text-xs font-bold mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label class="block text-gray-700 text-xs font-bold mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                    @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="mb-5">
                    <label class="block text-gray-700 text-xs font-bold mb-1">Ulangi Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                </div>

                <button type="submit" class="w-full bg-[#BFAFB0] text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition">
                    DAFTAR
                </button>

                <div class="mt-4 text-center">
                    <a href="{{ route('login') }}" class="text-xs text-blue-600 hover:underline">
                        Sudah punya akun? Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>