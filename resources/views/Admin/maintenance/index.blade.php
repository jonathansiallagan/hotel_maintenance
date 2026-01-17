<x-admin-layout>
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Jadwal Maintenance Rutin</h2>
            <p class="text-sm text-gray-500">Generator tiket otomatis untuk perawatan berkala.</p>
        </div>

        {{-- Tombol Tambah (Pemicu Modal nanti) --}}
        <button onclick="document.getElementById('createModal').showModal()"
            class="bg-[#D0BBB8] hover:bg-[#bda3a0] text-white px-5 py-2.5 rounded-lg font-bold text-sm shadow-sm transition flex items-center gap-2">
            <i class="fa-solid fa-calendar-plus"></i> Buat Jadwal Baru
        </button>
    </div>

    {{-- TABEL JADWAL --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-xs">
                <tr>
                    <th class="px-6 py-4">Aset</th>
                    <th class="px-6 py-4">Tugas</th>
                    <th class="px-6 py-4">Frekuensi</th>
                    <th class="px-6 py-4">Eksekusi Berikutnya</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($schedules as $schedule)
                <tr>
                    <td class="px-6 py-4 font-bold text-gray-800">{{ $schedule->asset->name }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $schedule->title }}</td> 
                    <td class="px-6 py-4">
                        <span class="bg-purple-50 text-purple-700 px-2 py-1 rounded text-xs font-bold uppercase">
                            {{ $schedule->frequency }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 text-gray-700">
                            <i class="fa-regular fa-clock text-gray-400"></i>
                            {{ $schedule->next_due_date->format('d M Y') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <form action="{{ route('admin.maintenance.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700 font-bold text-xs">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-8 text-gray-400">Belum ada jadwal maintenance.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL CREATE (Native HTML Dialog) --}}
    <dialog id="createModal" class="p-0 rounded-2xl shadow-2xl w-full max-w-lg backdrop:bg-black/50">
        <div class="bg-white p-6">
            <h3 class="text-lg font-bold mb-4">Tambah Jadwal Rutin</h3>

            {{-- TAMPILKAN ERROR JIKA ADA --}}
            @if ($errors->any())
            <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm mb-4">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            {{-- Script kecil agar modal tetap terbuka jika ada error --}}
            <script>
                document.getElementById('createModal').showModal()
            </script>
            @endif

            <form action="{{ route('admin.maintenance.store') }}" method="POST" class="space-y-4">
                @csrf

                {{-- 1. ASET --}}
                <div>
                    <label class="block text-sm font-bold mb-1">Pilih Aset</label>
                    <select name="asset_id" class="w-full border rounded-lg p-2 bg-white" required>
                        @foreach($assets as $asset)
                        <option value="{{ $asset->id }}">{{ $asset->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 2. JUDUL TUGAS (Perbaikan: name="title") --}}
                <div>
                    <label class="block text-sm font-bold mb-1">Nama Tugas</label>
                    <input type="text" name="title" placeholder="Contoh: Ganti Oli, Bersihkan Filter"
                        class="w-full border rounded-lg p-2" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- 3. FREKUENSI --}}
                    <div>
                        <label class="block text-sm font-bold mb-1">Frekuensi</label>
                        <select name="frequency" class="w-full border rounded-lg p-2 bg-white">
                            <option value="weekly">Mingguan</option>
                            <option value="monthly">Bulanan</option>
                            <option value="quarterly">3 Bulanan</option>
                            <option value="yearly">Tahunan</option>
                        </select>
                    </div>

                    {{-- 4. PRIORITAS (WAJIB DITAMBAHKAN) --}}
                    <div>
                        <label class="block text-sm font-bold mb-1">Prioritas</label>
                        <select name="priority" class="w-full border rounded-lg p-2 bg-white">
                            <option value="low">Low</option>
                            <option value="medium" selected>Normal</option>
                            <option value="high">Urgent</option>
                        </select>
                    </div>
                </div>

                {{-- 5. TANGGAL MULAI --}}
                <div>
                    <label class="block text-sm font-bold mb-1">Mulai Tanggal</label>
                    <input type="date" name="start_date" class="w-full border rounded-lg p-2" required>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t mt-4">
                    <button type="button" onclick="document.getElementById('createModal').close()" class="px-4 py-2 text-gray-500 font-bold hover:bg-gray-100 rounded-lg">Batal</button>
                    <button type="submit" class="bg-[#D0BBB8] hover:bg-[#bda3a0] text-white px-4 py-2 rounded-lg font-bold shadow-sm">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </dialog>
</x-admin-layout>