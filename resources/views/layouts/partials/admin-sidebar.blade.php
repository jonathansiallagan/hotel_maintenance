<aside class="flex flex-col w-72 h-screen bg-[#002760] border-r border-gray-200 shrink-0">

    {{-- 1. HEADER SIDEBAR (LOGO) --}}
    {{-- Tinggi 100px agar sejajar dengan Navbar --}}
    <div class="flex items-center gap-3 px-6 border-b border-gray-200 h-[70px]">

        <img src="{{ asset('images/Logo_Marianna.png') }}"
            class="w-10 h-10 object-contain"
            alt="Logo">

        <div class="flex flex-col justify-center">
            <span class="text-sm font-bold text-[#CDB9B7] leading-tight">
                Marianna Resort
            </span>
            <span class="text-sm font-bold text-[#CDB9B7] leading-tight">
                & Convention Tuktuk
            </span>
        </div>
    </div>

    {{-- 2. MENU NAVIGATION --}}
    <nav class="flex-1 overflow-y-auto py-6 px-6 flex flex-col gap-6">

        {{-- A. MENU UTAMA --}}

        {{-- Home --}}
        <a href="{{ route('admin.dashboard') }}"
            class="text-lg font-bold {{ request()->routeIs('admin.dashboard') ? 'text-[#D0BBB8]' : 'text-gray-400 hover:text-gray-600' }} transition-colors">
            Home
        </a>

        {{-- Tiket --}}
        <a href="{{ route('admin.tickets.index') }}"
            class="text-lg font-bold {{ request()->routeIs('admin.tickets*') ? 'text-[#D0BBB8]' : 'text-gray-400 hover:text-gray-600' }} transition-colors">
            Tiket
        </a>

        {{-- Jadwal Maintenance --}}
        <a href="{{ route('admin.maintenance.index') }}"
            class="text-lg font-bold {{ request()->routeIs('admin.maintenance*') ? 'text-[#D0BBB8]' : 'text-gray-400 hover:text-gray-600' }} transition-colors">
            Jadwal Maintenance
        </a>

        {{-- Laporan --}}
        <a href="{{ route('admin.reports.index') }}"
            class="text-lg font-bold {{ request()->routeIs('admin.reports*') ? 'text-[#D0BBB8]' : 'text-gray-400 hover:text-gray-600' }} transition-colors">
            Laporan
        </a>

        {{-- B. DROPDOWN MASTER DATA (GABUNGAN ASET & SPAREPART) --}}
        {{-- Menggunakan Alpine.js untuk efek buka-tutup --}}
        <div x-data="{ open: {{ request()->routeIs('admin.assets*') || request()->routeIs('admin.spareparts*') ? 'true' : 'false' }} }">

            {{-- Tombol Pemicu Dropdown --}}
            <button @click="open = !open"
                class="w-full flex items-center justify-between text-lg font-bold transition-colors group {{ request()->routeIs('admin.assets*') || request()->routeIs('admin.spareparts*') ? 'text-[#D0BBB8]' : 'text-gray-400 hover:text-gray-600' }}">
                <span>Master Data</span>
                {{-- Ikon Panah Kecil --}}
                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200"
                    :class="open ? 'rotate-180' : ''"></i>
            </button>

            {{-- Isi Dropdown --}}
            <div x-show="open"
                x-collapse
                class="flex flex-col gap-3 mt-4 ml-2 border-l-2 border-gray-100 pl-4"
                style="display: none;"> {{-- style none agar tidak flicker saat load --}}

                {{-- 1. Daftar Aset --}}
                <a href="{{ route('admin.assets.index') }}"
                    class="text-base font-bold {{ request()->routeIs('admin.assets*') ? 'text-[#D0BBB8]' : 'text-gray-400 hover:text-gray-600' }} transition-colors">
                    Daftar Aset
                </a>

                {{-- 2. Daftar Sparepart --}}
                {{-- Pastikan route ini ada di web.php, jika belum ada ganti '#' --}}
                <a href="{{ route('admin.spareparts.index') ?? '#' }}"
                    class="text-base font-bold {{ request()->routeIs('admin.spareparts*') ? 'text-[#D0BBB8]' : 'text-gray-400 hover:text-gray-600' }} transition-colors">
                    Stok Sparepart
                </a>
            </div>
        </div>

        {{-- C. ADMIN TOOLS (SISANYA) --}}
        <div class="pt-6 mt-auto border-t border-gray-100 flex flex-col gap-3">
            <p class="text-xs font-bold text-gray-300 uppercase mb-2">System</p>
            <a href="#" class="text-base font-bold text-gray-400 hover:text-gray-600">Users Management</a>
            {{-- Bisa tambah setting lain disini --}}
        </div>
    </nav>
</aside>