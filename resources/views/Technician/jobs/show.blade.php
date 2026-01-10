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

        {{-- HEADER SECTION (Navy Blue) --}}
        <header class="bg-[#0A2647] pt-5 pb-16 px-5 rounded-b-[2rem] shadow-xl sticky top-0 z-10">
            {{-- ... (Header tetap sama seperti kode Anda) ... --}}
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-3">
                    <a href="{{ route('technician.dashboard', ['tab' => 'mytask']) }}"
                        class="w-8 h-8 rounded-full bg-white/10 backdrop-blur-sm border border-white/10 flex items-center justify-center text-white hover:bg-white/20 transition active:scale-95">
                        <i class="fa-solid fa-arrow-left text-sm"></i>
                    </a>
                    <h1 class="text-white font-bold text-base tracking-wide">Detail Pengerjaan</h1>
                </div>

                <div x-data="{ 
                        start: new Date('{{ $ticket->started_at ?? now() }}'),
                        now: new Date(),
                        get duration() {
                            let diff = Math.floor((this.now - this.start) / 1000);
                            let h = Math.floor(diff / 3600).toString().padStart(2, '0');
                            let m = Math.floor((diff % 3600) / 60).toString().padStart(2, '0');
                            let s = (diff % 60).toString().padStart(2, '0');
                            return `${h}:${m}:${s}`;
                        }
                    }"
                    x-init="setInterval(() => { now = new Date() }, 1000)">
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

            {{-- SOLUSI NO. 3: ALERT ERROR VALIDASI FOTO KOSONG --}}
            {{-- Letakkan blok ini tepat di bawah tag <main> agar langsung terlihat --}}
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
            {{-- AKHIR SOLUSI NO. 3 --}}

            {{-- 1. CARD MASALAH (Tetap sama) --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5 mb-5">
                {{-- ... konten card masalah ... --}}
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

                {{-- Input: Bunda Tindakan --}}
                <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tindakan Perbaikan</label>
                    <textarea name="technician_note" rows="3"
                        class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm focus:bg-white focus:border-blue-500 focus:ring-blue-500 p-3 placeholder-gray-400 transition"
                        placeholder="Deskripsikan perbaikan..." required>{{ old('technician_note', $ticket->technician_note) }}</textarea>
                </div>

                {{-- SOLUSI NO. 2: INPUT MATERIAL (ALPINE JS FIX) --}}
                {{-- Pastikan x-data didefinisikan di div pembungkus utama ini --}}
                <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm"
                    x-data="{ rows: [] }">

                    <label class="block text-sm font-bold text-gray-700 mb-3 flex justify-between items-center">
                        <span>Material Terpakai</span>
                        {{-- Tombol Tambah --}}
                        <button type="button" @click="rows.push({id: '', qty: 1})" class="text-[10px] bg-blue-50 text-blue-600 px-3 py-1.5 rounded-full font-bold hover:bg-blue-100 border border-blue-100 transition">
                            <i class="fa-solid fa-plus mr-1"></i> Tambah
                        </button>
                    </label>

                    {{-- Pesan Kosong --}}
                    <div x-show="rows.length === 0" class="text-center py-3 border-2 border-dashed border-gray-100 rounded-lg text-xs text-gray-400">
                        Tidak ada material yang digunakan.
                    </div>

                    {{-- Looping Baris Material --}}
                    <template x-for="(row, index) in rows" :key="index">
                        <div class="flex gap-2 mb-3 items-center animate-fade-in-up">
                            <div class="relative flex-1">
                                <select :name="'spareparts['+index+'][id]'" x-model="row.id" class="w-full rounded-xl border-gray-200 text-xs bg-gray-50 pl-3 pr-8 py-2.5 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- Pilih Material --</option>
                                    @foreach($spareparts as $part)
                                    <option value="{{ $part->id }}">{{ $part->name }} (Sisa: {{ $part->stock }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <input type="number" :name="'spareparts['+index+'][qty]'" x-model="row.qty" class="w-16 rounded-xl border-gray-200 bg-gray-50 text-xs text-center font-bold py-2.5" min="1" placeholder="Qty">

                            {{-- Tombol Hapus --}}
                            <button type="button" @click="rows.splice(index, 1)" class="text-red-400 hover:text-red-600 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition">
                                <i class="fa-solid fa-trash text-sm"></i>
                            </button>
                        </div>
                    </template>
                </div>
                {{-- AKHIR SOLUSI NO. 2 --}}

                {{-- Input: Foto Bukti --}}
                <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Foto Bukti Selesai <span class="text-red-500">*</span></label>

                    {{-- Pesan Error Spesifik di Bawah Label --}}
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
                                @change="
                                    fileName = $event.target.files[0].name;
                                    previewUrl = URL.createObjectURL($event.target.files[0]);
                                " />
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