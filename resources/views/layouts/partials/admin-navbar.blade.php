<header class="bg-[#002760] sticky top-0 z-40 shadow-sm shrink-0"
    x-data="{ mobileSearchOpen: false }">

    {{-- BARIS UTAMA (Tinggi Tetap 70px) --}}
    <div class="flex items-center justify-between w-full px-4 border-b border-gray-200 md:px-8 h-[70px]">

        {{-- BAGIAN KIRI: Hamburger & Judul --}}
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen"
                class="lg:hidden text-gray-500 focus:outline-none hover:text-gray-700 transition">
                <i class="fa-solid fa-bars text-2xl"></i>
            </button>

            <h1 class="text-xl md:text-2xl font-bold text-white tracking-tight truncate">
                Monitoring
            </h1>
        </div>

        {{-- BAGIAN KANAN --}}
        <div class="flex items-center gap-3 md:gap-6">

            {{-- 1. SEARCH BAR - DESKTOP --}}
            <div class="relative hidden md:block" x-data="{
                open: false,
                query: '',
                results: [],
                search() {
                    if (this.query.length < 2) { this.results = []; return; }
                    fetch(`{{ route('admin.search') }}?q=${encodeURIComponent(this.query)}`).then(r => r.json()).then(d => { this.results = d.results; });
                }
            }">
                <form @submit.prevent="search()" class="relative">
                    <input type="text" x-model="query" @input="search()" @focus="open = true" @blur="setTimeout(() => open = false, 200)"
                        placeholder="Cari tiket, aset, sparepart..."
                        class="w-64 pl-4 pr-10 py-2 rounded-full border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-shadow">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-gray-400 text-xs"></i>
                    </div>
                </form>
                {{-- Hasil Desktop --}}
                <div x-show="open && results.length > 0" class="absolute top-full mt-2 w-96 bg-[#002760] rounded-lg shadow-lg border border-gray-200 z-50 max-h-96 overflow-y-auto right-0" style="display: none;">
                    <div class="p-2">
                        <template x-for="result in results" :key="result.id">
                            <a :href="result.url" class="block p-3 hover:bg-gray-50 rounded-md border-b border-gray-50 last:border-0">
                                <p x-text="result.title" class="text-sm font-bold text-gray-800"></p>
                                <p x-text="result.type" class="text-xs text-gray-500 uppercase mt-1"></p>
                            </a>
                        </template>
                    </div>
                </div>
            </div>

            {{-- 2. ICON SEARCH - MOBILE --}}
            {{-- PERBAIKAN: Timeout diubah jadi 300ms agar menunggu animasi selesai --}}
            <button @click="mobileSearchOpen = !mobileSearchOpen; 
                            if(mobileSearchOpen) { 
                                setTimeout(() => { document.getElementById('mobileSearchInput').focus(); }, 300); 
                            }"
                class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none p-2 rounded-full hover:bg-gray-100 transition">
                <i class="fa-solid fa-magnifying-glass text-xl"></i>
            </button>

            {{-- 3. PROFIL --}}
            <div class="relative ml-1" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff" class="w-8 h-8 rounded-full border border-gray-2 00">
                </button>
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-100" style="display: none;">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Log Out</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- FLOATING SEARCH BAR - MOBILE --}}
    <div x-show="mobileSearchOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        @click.away="mobileSearchOpen = false"
        class="absolute top-[70px] left-0 w-full bg-[#002760] border-b border-gray-200 p-4 shadow-md md:hidden z-30"
        style="display: none;">

        <div x-data="{
            open: false,
            query: '',
            results: [],
            search() {
                if (this.query.length < 2) { this.results = []; return; }
                fetch(`{{ route('admin.search') }}?q=${encodeURIComponent(this.query)}`).then(r => r.json()).then(d => { this.results = d.results; });
            }
        }">
            <form @submit.prevent="search()" class="relative w-full">
                <input type="text"
                    id="mobileSearchInput"
                    x-model="query"
                    @input="search()"
                    @focus="open = true"
                    placeholder="Cari kode tiket, sparepart..."
                    class="w-full pl-4 pr-10 py-2 rounded-full border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-shadow">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400 text-xs"></i>
                </div>
            </form>

            <div x-show="open && results.length > 0" class="mt-2 bg-[#002760] rounded-lg shadow-sm border border-gray-100 max-h-60 overflow-y-auto">
                <div class="p-2">
                    <template x-for="result in results" :key="result.id">
                        <a :href="result.url" class="block p-3 hover:bg-gray-50 rounded-md border-b border-gray-50 last:border-0">
                            <p x-text="result.title" class="text-sm font-bold text-gray-800"></p>
                            <div class="flex justify-between items-center mt-1">
                                <p x-text="result.type" class="text-xs text-gray-500 uppercase"></p>
                                <p x-text="result.status" class="text-xs font-medium px-2 py-0.5 rounded bg-gray-100 text-gray-600"></p>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
        </div>
    </div>

</header>