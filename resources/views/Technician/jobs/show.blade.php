<x-app-layout :hideNav="true">

    @push('styles')
    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    @endpush

    {{-- CONTAINER UTAMA --}}
    <div class="relative max-w-md mx-auto min-h-screen flex flex-col bg-gray-50 text-gray-800 shadow-2xl font-sans">

        {{-- HEADER SECTION --}}
        <header class="bg-[#0A2647] pt-5 pb-16 px-5 rounded-b-[2rem] shadow-xl sticky top-0 z-10">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-3">
                    <a href="{{ route('technician.dashboard', ['tab' => 'mytask']) }}"
                        class="w-8 h-8 rounded-full bg-white/10 backdrop-blur-sm border border-white/10 flex items-center justify-center text-white hover:bg-white/20 transition active:scale-95">
                        <i class="fa-solid fa-arrow-left text-sm"></i>
                    </a>
                    <h1 class="text-white font-bold text-base tracking-wide">Detail Pengerjaan</h1>
                </div>

                {{-- TIMER LOGIC --}}
                <div x-data="{
                        seconds: {{ $ticket->started_at ? (time() - strtotime($ticket->started_at)) : 0 }},
                        duration: '00:00:00',

                        init() {
                            this.formatTime();
                            setInterval(() => {
                                this.seconds++;
                                this.formatTime();
                            }, 1000);
                        },

                        formatTime() {
                            let hrs = Math.floor(this.seconds / 3600);
                            let mins = Math.floor((this.seconds % 3600) / 60);
                            let secs = Math.floor(this.seconds % 60);

                            let h = hrs.toString().padStart(2, '0');
                            let m = mins.toString().padStart(2, '0');
                            let s = secs.toString().padStart(2, '0');

                            this.duration = `${h}:${m}:${s}`;
                        }
                    }">

                    <div class="flex items-center gap-2 bg-blue-900/60 rounded-lg px-3 py-1.5 border border-blue-500/30 backdrop-blur-md">
                        <i class="fa-regular fa-clock text-blue-300 text-xs animate-pulse"></i>
                        <span class="font-mono font-bold text-white text-sm tracking-widest" x-text="duration">00:00:00</span>
                    </div>
                </div>
            </div>

            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] font-mono text-blue-200 bg-white/10 px-1.5 py-0.5 rounded">
                        {{ $ticket->ticket_number }}
                    </span>
                    <span class="text-[10px] font-bold uppercase {{ $ticket->priority == 'high' ? 'text-red-400' : 'text-blue-300' }}">
                        {{ $ticket->priority }}
                    </span>
                </div>
                <h2 class="font-bold text-white text-lg leading-tight truncate">{{ $ticket->title }}</h2>
                <p class="text-blue-200/80 text-xs flex items-center gap-1 mt-1">
                    <i class="fa-solid fa-location-dot"></i> {{ $ticket->asset->location->name ?? '-' }}
                </p>
            </div>
        </header>

        {{-- MAIN CONTENT --}}
        <main class="flex-1 px-4 -mt-8 pb-10 z-20 overflow-y-auto scrollbar-hide">

            {{-- ERROR ALERT --}}
            @if ($errors->any())
            <div class="mb-5 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r shadow-sm animate-bounce" role="alert">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                    <strong class="font-bold">Mohon Periksa Kembali!</strong>
                </div>
                <ul class="list-disc pl-8 text-xs mt-1 space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- 1. CARD MASALAH --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5 mb-5">
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-red-500"></i> Keluhan User
                    </h3>
                    <button class="text-[10px] text-blue-600 font-semibold bg-blue-50 px-2 py-1 rounded-full hover:bg-blue-100">
                        <i class="fa-solid fa-history mr-1"></i> History
                    </button>
                </div>
                <p class="text-gray-800 text-sm leading-relaxed mb-4 bg-gray-50 p-3 rounded-xl border border-gray-100">
                    "{{ $ticket->description }}"
                </p>
                @if($ticket->photo_evidence_before)
                <div class="relative rounded-xl overflow-hidden h-32 bg-gray-100 border border-gray-200 group w-full">
                    <img src="{{ asset('storage/'.$ticket->photo_evidence_before) }}" class="w-full h-full object-cover transition transform group-hover:scale-105">
                    <div class="absolute bottom-0 left-0 bg-black/60 text-white text-[10px] px-2 py-1 rounded-tr-lg">
                        Foto Awal
                    </div>
                </div>
                @endif
            </div>

            {{-- 2. FORM PENYELESAIAN --}}
            <form action="{{ route('technician.job.update', $ticket->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf @method('PATCH')
                <input type="hidden" name="action" value="finish">

                {{-- Input: Note Tindakan --}}
                <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tindakan Perbaikan <span class="text-red-500">*</span></label>
                    <textarea name="technician_note" rows="3"
                        class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 p-3 placeholder-gray-400 transition"
                        placeholder="Deskripsikan perbaikan yang telah dilakukan..." required>{{ old('technician_note', $ticket->technician_note) }}</textarea>
                </div>

                {{-- ========================================================== --}}
                {{-- TAMBAHAN: ROOT CAUSE ANALYSIS (HANYA MUNCUL JIKA HIGH)     --}}
                {{-- ========================================================== --}}
                @if($ticket->priority == 'high')
                <div class="bg-purple-50 p-4 rounded-2xl border border-purple-200 shadow-sm">
                    <div x-data="{ 
                        rcaMode: '{{ old('rca_option') }}', 
                        finalRca: '{{ old('root_cause') }}',
                        manualRca: '',
                        rcaList: {{ json_encode($historyRca ?? []) }},
                        showRcaDropdown: false
                    }" x-init="$watch('manualRca', value => finalRca = value)">

                        <label class="block text-sm font-bold text-purple-800 mb-1">
                            <i class="fa-solid fa-magnifying-glass-chart mr-1"></i> Akar Masalah (RCA)
                            <span class="text-red-500">*</span>
                        </label>
                        <p class="text-[10px] text-purple-600 mb-3 italic">Prioritas URGENT: Wajib disimpulkan.</p>

                        {{-- Hidden Input untuk dikirim ke Controller --}}
                        <input type="hidden" name="root_cause" x-model="finalRca">

                        {{-- Pilihan RCA Utama --}}
                        <div class="flex flex-col gap-2 mb-2">
                            @foreach($commonRca as $rca)
                            <label class="cursor-pointer">
                                <input type="radio" name="rca_option" value="{{ $rca }}" class="hidden"
                                    @click="rcaMode = 'common'; finalRca = '{{ $rca }}'; manualRca = ''"
                                    {{ old('root_cause') == $rca ? 'checked' : '' }}>
                                <div class="px-4 py-3 rounded-xl border text-xs font-bold transition-all shadow-sm"
                                    :class="finalRca === '{{ $rca }}' ? 'bg-purple-600 text-white border-purple-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-purple-100'">
                                    {{ $rca }}
                                </div>
                            </label>
                            @endforeach

                            {{-- Opsi Lainnya --}}
                            <label class="cursor-pointer">
                                <input type="radio" name="rca_option" value="Lainnya" class="hidden"
                                    @click="rcaMode = 'manual'; finalRca = manualRca">
                                <div class="px-4 py-3 rounded-xl border text-xs font-bold transition-all shadow-sm"
                                    :class="rcaMode === 'manual' ? 'bg-purple-600 text-white border-purple-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-purple-100'">
                                    + Masalah Lainnya...
                                </div>
                            </label>
                        </div>

                        {{-- Input Hybrid RCA (Muncul jika pilih Lainnya) --}}
                        <div x-show="rcaMode === 'manual'" x-transition class="mt-2 relative">
                            <div class="relative">
                                <input type="text" x-model="manualRca"
                                    @focus="showRcaDropdown = true"
                                    @click.away="showRcaDropdown = false"
                                    class="w-full pl-4 pr-10 py-3 rounded-xl border border-purple-300 focus:border-purple-600 focus:ring-1 focus:ring-purple-600 outline-none text-sm bg-white"
                                    placeholder="Ketik akar masalah baru..." autocomplete="off">

                                <button type="button" @click="showRcaDropdown = !showRcaDropdown"
                                    class="absolute inset-y-0 right-0 px-4 flex items-center text-purple-500">
                                    <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="showRcaDropdown ? 'rotate-180' : ''"></i>
                                </button>
                            </div>

                            {{-- Dropdown Riwayat RCA --}}
                            <div x-show="showRcaDropdown && rcaList.length > 0" class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-40 overflow-y-auto">
                                <template x-for="item in rcaList" :key="item">
                                    <div @click="manualRca = item; showRcaDropdown = false"
                                        class="px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 hover:text-purple-700 cursor-pointer border-b border-gray-50 transition-colors">
                                        <span x-text="item"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>
                </div>
                @endif
                {{-- ========================================================== --}}

                {{-- Input: Material --}}
                <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm" x-data="{ rows: [] }">
                    <label class="block text-sm font-bold text-gray-700 mb-3 flex justify-between items-center">
                        <span>Material Terpakai</span>
                        <button type="button" @click="rows.push({id: '', qty: 1})" class="text-[10px] bg-blue-50 text-blue-600 px-3 py-1.5 rounded-full font-bold hover:bg-blue-100 border border-blue-100 transition">
                            <i class="fa-solid fa-plus mr-1"></i> Tambah
                        </button>
                    </label>

                    <div x-show="rows.length === 0" class="text-center py-3 border-2 border-dashed border-gray-100 rounded-lg text-xs text-gray-400">
                        Tidak ada material yang digunakan.
                    </div>

                    <template x-for="(row, index) in rows" :key="index">
                        <div class="flex gap-2 mb-3 items-center">
                            <div class="relative flex-1">
                                <select :name="'spareparts['+index+'][id]'" x-model="row.id" class="w-full rounded-xl border-gray-200 text-xs bg-gray-50 pl-3 pr-8 py-2.5 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- Pilih Material --</option>
                                    @foreach($spareparts as $part)
                                    <option value="{{ $part->id }}">{{ $part->name }} (Sisa: {{ $part->stock }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="number" :name="'spareparts['+index+'][qty]'" x-model="row.qty" class="w-16 rounded-xl border-gray-200 bg-gray-50 text-xs text-center font-bold py-2.5" min="1" placeholder="Qty">
                            <button type="button" @click="rows.splice(index, 1)" class="text-red-400 hover:text-red-600 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition">
                                <i class="fa-solid fa-trash text-sm"></i>
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Input: Foto Bukti --}}
                <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Foto Bukti Selesai <span class="text-red-500">*</span></label>
                    @error('photo_after')
                    <p class="text-red-500 text-xs mb-2 font-semibold bg-red-50 px-2 py-1 rounded inline-block">
                        <i class="fa-solid fa-triangle-exclamation"></i> {{ $message }}
                    </p>
                    @enderror

                    <div x-data="{ fileName: null, previewUrl: null }" class="relative">
                        <label class="flex flex-col items-center justify-center w-full h-32 rounded-xl border-2 border-dashed {{ $errors->has('photo_after') ? 'border-red-300 bg-red-50' : 'border-blue-200 bg-blue-50/50' }} hover:bg-blue-50 transition cursor-pointer group overflow-hidden relative">
                            <img x-show="previewUrl" :src="previewUrl" class="absolute inset-0 w-full h-full object-cover z-10" />
                            <div class="flex flex-col items-center justify-center pt-5 pb-6 z-20" :class="previewUrl ? 'bg-black/40 w-full h-full text-white' : 'text-blue-400'">
                                <i class="fa-solid fa-camera text-2xl mb-1 group-hover:scale-110 transition-transform"></i>
                                <p class="text-[10px] font-medium" x-text="fileName ? 'Ganti Foto' : 'Ambil Foto'"></p>
                            </div>
                            <input type="file" name="photo_after" class="hidden"
                                @change="fileName = $event.target.files[0].name; previewUrl = URL.createObjectURL($event.target.files[0]);" />
                        </label>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="grid grid-cols-2 gap-3 pt-2 pb-6">
                    <button type="submit" name="status" value="pending_sparepart"
                        class="flex items-center justify-center gap-2 bg-amber-100 text-amber-700 py-3 rounded-xl font-bold text-sm hover:bg-amber-200 transition active:scale-95">
                        <i class="fa-solid fa-pause"></i> Pending
                    </button>

                    <button type="submit" name="status" value="resolved"
                        class="flex items-center justify-center gap-2 bg-[#0A2647] text-white py-3 rounded-xl font-bold text-sm shadow-lg shadow-blue-900/30 hover:bg-blue-900 transition active:scale-95">
                        <i class="fa-solid fa-check-double"></i> Selesai
                    </button>
                </div>
            </form>
        </main>
    </div>
</x-app-layout>