<x-admin-layout>
    <div class="max-w-6xl mx-auto space-y-6">

        {{-- ALERT SUKSES --}}
        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2" role="alert">
            <i class="fa-solid fa-circle-check text-lg"></i>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
        @endif

        {{-- ALERT ERROR --}}
        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2" role="alert">
            <i class="fa-solid fa-triangle-exclamation text-lg"></i>
            <span class="font-bold text-sm">{{ session('error') }}</span>
        </div>
        @endif

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-gray-800">Tiket #{{ $ticket->id }}</h1>
                    @php
                    $statusColors = [
                    'open' => 'bg-red-100 text-red-700 border-red-200',
                    'in_progress' => 'bg-blue-100 text-blue-700 border-blue-200',
                    'pending_sparepart' => 'bg-amber-100 text-amber-700 border-amber-200',
                    'resolved' => 'bg-green-100 text-green-700 border-green-200',
                    'closed' => 'bg-gray-100 text-gray-700 border-gray-200'
                    ];
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $statusColors[$ticket->status] ?? 'bg-gray-100' }}">
                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                    </span>
                </div>
                <p class="text-sm text-gray-500">{{ $ticket->created_at->format('l, d F Y H:i') }}</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.tickets.print', $ticket->id) }}"
                    target="_blank"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition">
                    <i class="fa-solid fa-print"></i> Cetak
                </a>
                <a href="{{ route('admin.tickets.index') }}"
                    class="bg-[#D0BBB8] hover:bg-[#bda3a0] text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- KOLOM KIRI: DETAIL TIKET --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- INFO TIKET --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-blue-500"></i>
                        Informasi Tiket
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-1">Judul</label>
                            <p class="text-gray-800 font-medium">{{ $ticket->title }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-1">Deskripsi Masalah</label>
                            <p class="text-gray-700 leading-relaxed">{{ $ticket->description }}</p>
                        </div>

                        @if($ticket->technician_note)
                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-1">Catatan Teknisi</label>
                            <p class="text-gray-700 leading-relaxed bg-blue-50 p-3 rounded-lg">{{ $ticket->technician_note }}</p>
                        </div>
                        @endif

                        {{-- FOTO EVIDENCE --}}
                        @if($ticket->photo_evidence_before || $ticket->photo_evidence_after)
                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-3">Foto Evidence</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($ticket->photo_evidence_before)
                                <div class="space-y-2">
                                    <div class="text-xs font-bold text-gray-500 uppercase">Sebelum Perbaikan</div>
                                    <div class="relative group">
                                        <img src="{{ asset('storage/' . $ticket->photo_evidence_before) }}"
                                            alt="Foto sebelum perbaikan"
                                            class="w-full h-48 object-cover rounded-lg border border-gray-200 cursor-pointer hover:opacity-90 transition"
                                            onclick="openImageModal('{{ asset('storage/' . $ticket->photo_evidence_before) }}', 'Foto Sebelum Perbaikan')">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-expand text-white opacity-0 group-hover:opacity-100 transition text-xl"></i>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($ticket->photo_evidence_after)
                                <div class="space-y-2">
                                    <div class="text-xs font-bold text-gray-500 uppercase">Setelah Perbaikan</div>
                                    <div class="relative group">
                                        <img src="{{ asset('storage/' . $ticket->photo_evidence_after) }}"
                                            alt="Foto setelah perbaikan"
                                            class="w-full h-48 object-cover rounded-lg border border-gray-200 cursor-pointer hover:opacity-90 transition"
                                            onclick="openImageModal('{{ asset('storage/' . $ticket->photo_evidence_after) }}', 'Foto Setelah Perbaikan')">
                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-expand text-white opacity-0 group-hover:opacity-100 transition text-xl"></i>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($ticket->status === 'resolved' && $ticket->root_cause)
                        <div class="mt-6 bg-gradient-to-br from-purple-50 to-white rounded-2xl p-5 border border-purple-100 shadow-sm">
                            <div class="flex items-center gap-2 mb-4 border-b border-purple-100 pb-3">
                                <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center">
                                    <i class="fa-solid fa-microscope"></i>
                                </div>
                                <h3 class="text-sm font-bold text-purple-800 uppercase tracking-wider">
                                    Analisis Akar Masalah (RCA)
                                </h3>
                            </div>

                            <div class="bg-white p-4 rounded-xl border border-purple-100 shadow-sm relative overflow-hidden">
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-purple-500"></div>
                                <p class="text-sm font-bold text-gray-800 leading-relaxed">
                                    "{{ $ticket->root_cause }}"
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- RIWAYAT AKTIVITAS --}}
                @if($ticket->activities && $ticket->activities->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-history text-green-500"></i>
                        Riwayat Aktivitas
                    </h3>

                    <div class="space-y-4">
                        @foreach($ticket->activities as $activity)
                        <div class="flex gap-4 pb-4 border-b border-gray-100 last:border-b-0 last:pb-0">
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-sm font-bold text-gray-600 flex-shrink-0">
                                {{ substr($activity->user->name ?? 'Sys', 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-gray-800">{{ $activity->user->name ?? 'Sistem' }}</span>
                                    <span class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-600 text-sm">{{ $activity->description }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- SPAREPART YANG DIGUNAKAN --}}
                @if($ticket->spareparts && $ticket->spareparts->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-tools text-orange-500"></i>
                        Sparepart yang Digunakan
                    </h3>

                    <div class="space-y-3">
                        @php $totalSparepartCost = 0; @endphp
                        @foreach($ticket->spareparts as $sparepart)
                        @php
                        $subtotal = $sparepart->pivot->quantity * $sparepart->price_per_unit;
                        $totalSparepartCost += $subtotal;
                        @endphp
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg bg-white border border-gray-200 overflow-hidden flex-shrink-0">
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <i class="fa-solid fa-box-archive"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="font-bold text-gray-800">{{ $sparepart->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $sparepart->sku_code ?? '-' }} • {{ $sparepart->category->name ?? 'Tidak ada kategori' }}</div>
                                    <div class="text-xs text-gray-600 mt-1">
                                        <span class="font-medium">Harga per unit:</span> Rp {{ number_format($sparepart->price_per_unit, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <div class="text-sm font-bold text-gray-800">{{ $sparepart->pivot->quantity }} {{ $sparepart->unit }}</div>
                                    <div class="text-xs text-gray-500">Subtotal: Rp {{ number_format($subtotal, 0, ',', '.') }}</div>
                                </div>
                                @if(!in_array($ticket->status, ['resolved', 'closed']))
                                <form action="{{ route('admin.tickets.remove-sparepart', [$ticket->id, $sparepart->id]) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sparepart ini dari tiket? Stok akan dikembalikan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @endforeach

                        @if($ticket->spareparts->count() > 0)
                        <div class="mt-4 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-orange-800">Total Biaya Sparepart:</span>
                                <span class="font-bold text-orange-800 text-lg">Rp {{ number_format($totalSparepartCost, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

            </div>

            {{-- KOLOM KANAN: INFO TAMBAHAN --}}
            <div class="space-y-6">

                {{-- INFO PELAPOR --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-user text-purple-500"></i>
                        Pelapor
                    </h3>

                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-lg font-bold text-purple-600">
                            {{ substr($ticket->user->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-bold text-gray-800">{{ $ticket->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $ticket->user->email }}</div>
                            <div class="text-xs text-gray-400 uppercase">{{ $ticket->user->role }}</div>
                        </div>
                    </div>
                </div>

                {{-- INFO ASET --}}
                @if($ticket->asset)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-cube text-orange-500"></i>
                        Aset Terkait
                    </h3>

                    <div class="space-y-3">
                        <div>
                            <div class="font-bold text-gray-800">{{ $ticket->asset->name }}</div>
                            <div class="text-sm text-gray-500">{{ $ticket->asset->serial_number ?? 'No S/N' }}</div>
                        </div>

                        <div class="flex items-center gap-2 text-sm">
                            <i class="fa-solid fa-tags text-blue-500"></i>
                            <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-bold">
                                {{ $ticket->asset->category->name ?? '-' }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2 text-sm">
                            <i class="fa-solid fa-location-dot text-red-500"></i>
                            <span class="text-gray-600">{{ $ticket->asset->location->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
                @endif

                {{-- INFO TEKNISI --}}
                @if($ticket->technician)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-user-gear text-green-500"></i>
                        Teknisi Ditugaskan
                    </h3>

                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-lg font-bold text-green-600">
                            {{ substr($ticket->technician->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-bold text-gray-800">{{ $ticket->technician->name }}</div>
                            <div class="text-sm text-gray-500">{{ $ticket->technician->email }}</div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- UPDATE STATUS (FORM) --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-edit text-blue-500"></i>
                        Update Status
                    </h3>

                    @if(in_array($ticket->status, ['resolved', 'closed']))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-check"></i>
                            <span class="font-bold text-sm">Tiket ini sudah selesai dan tidak dapat diubah lagi.</span>
                        </div>
                    </div>
                    @endif

                    <form action="{{ route('admin.tickets.update', $ticket->id) }}" method="POST" class="space-y-4" {{ in_array($ticket->status, ['resolved', 'closed']) ? 'onsubmit="return false;"' : '' }}>
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-2">Status Tiket</label>
                            <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8] bg-white {{ in_array($ticket->status, ['resolved', 'closed']) ? 'bg-gray-100 cursor-not-allowed' : '' }}" {{ in_array($ticket->status, ['resolved', 'closed']) ? 'disabled' : 'required' }}>
                                <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="pending_sparepart" {{ $ticket->status == 'pending_sparepart' ? 'selected' : '' }}>Pending Sparepart</option>
                                <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-2">Catatan Teknisi (Opsional)</label>
                            <textarea name="technician_note" rows="3" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8] resize-none {{ in_array($ticket->status, ['resolved', 'closed']) ? 'bg-gray-100 cursor-not-allowed' : '' }}" {{ in_array($ticket->status, ['resolved', 'closed']) ? 'disabled' : '' }} placeholder="Tambahkan catatan jika diperlukan...">{{ $ticket->technician_note }}</textarea>
                        </div>

                        <button type="submit" class="w-full {{ in_array($ticket->status, ['resolved', 'closed']) ? 'bg-gray-400 cursor-not-allowed' : 'bg-[#D0BBB8] hover:bg-[#bda3a0]' }} text-white py-2 rounded-lg font-bold text-sm transition" {{ in_array($ticket->status, ['resolved', 'closed']) ? 'disabled' : '' }}>
                            {{ in_array($ticket->status, ['resolved', 'closed']) ? 'Tiket Sudah Selesai' : 'Update Status' }}
                        </button>
                    </form>
                </div>

                {{-- KELOLA SPAREPART --}}
                @if(!in_array($ticket->status, ['resolved', 'closed']))
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-tools text-orange-500"></i>
                        Kelola Sparepart
                    </h3>

                    {{-- Form Tambah Sparepart --}}
                    <form action="{{ route('admin.tickets.add-sparepart', $ticket->id) }}" method="POST" class="space-y-4 mb-4">
                        @csrf
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-2">Sparepart</label>
                                <select name="sparepart_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8] bg-white" required>
                                    <option value="">Pilih Sparepart</option>
                                    @foreach(\App\Models\Sparepart::where('stock', '>', 0)->get() as $sparepart)
                                    <option value="{{ $sparepart->id }}">{{ $sparepart->name }} (Stok: {{ $sparepart->stock }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-2">Jumlah</label>
                                <input type="number" name="quantity" min="1" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8]" placeholder="1" required>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2 rounded-lg font-bold text-sm transition">
                            <i class="fa-solid fa-plus mr-2"></i>Tambah Sparepart
                        </button>
                    </form>
                </div>
                @endif

            </div>

        </div>

    </div>

    {{-- MODAL UNTUK MELIHAT GAMBAR BESAR --}}
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-opacity duration-300" onclick="closeImageModal()">
        <div class="relative max-w-4xl max-h-full">
            <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg">
            <button onclick="closeImageModal()" class="absolute top-4 right-4 bg-white bg-opacity-80 hover:bg-opacity-100 text-gray-800 rounded-full w-10 h-10 flex items-center justify-center transition">
                <i class="fa-solid fa-times"></i>
            </button>
            <div id="modalCaption" class="absolute bottom-4 left-4 bg-black bg-opacity-75 text-white px-4 py-2 rounded-lg text-sm font-bold"></div>
        </div>
    </div>

    <script>
        function openImageModal(src, caption) {
            document.getElementById('modalImage').src = src;
            document.getElementById('modalCaption').textContent = caption;
            const modal = document.getElementById('imageModal');
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100', 'pointer-events-auto');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.remove('opacity-100', 'pointer-events-auto');
            modal.classList.add('opacity-0', 'pointer-events-none');
            document.body.style.overflow = 'auto';
        }

        // Close modal when pressing Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>

</x-admin-layout>