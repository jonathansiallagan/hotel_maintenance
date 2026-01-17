<x-admin-layout>
    <div class="max-w-6xl mx-auto space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-gray-800">{{ $asset->name }}</h1>
                    @php
                    $statusColors = [
                    'active' => 'bg-green-100 text-green-700 border-green-200',
                    'inactive' => 'bg-gray-100 text-gray-700 border-gray-200',
                    'maintenance' => 'bg-amber-100 text-amber-700 border-amber-200'
                    ];
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $statusColors[$asset->status] ?? 'bg-gray-100' }}">
                        {{ ucfirst($asset->status) }}
                    </span>
                </div>
                <p class="text-sm text-gray-500">{{ $asset->serial_number ?? 'No Serial Number' }}</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.assets.print_qr', $asset->id) }}"
                    target="_blank"
                    class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition">
                    <i class="fa-solid fa-qrcode"></i> QR Code
                </a>
                <a href="{{ route('admin.assets.edit', $asset->id) }}"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition">
                    <i class="fa-solid fa-edit"></i> Edit
                </a>
                <a href="{{ route('admin.assets.index') }}"
                    class="bg-[#D0BBB8] hover:bg-[#bda3a0] text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- KOLOM KIRI: DETAIL ASSET --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- INFO ASSET --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-blue-500"></i>
                        Informasi Aset
                    </h3>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">Nama Aset</label>
                                <p class="text-gray-800 font-medium">{{ $asset->name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">Serial Number</label>
                                <p class="text-gray-800 font-medium">{{ $asset->serial_number ?? '-' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">Kategori</label>
                                <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded text-sm font-bold">
                                    {{ $asset->category->name ?? '-' }}
                                </span>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-600 mb-1">Lokasi</label>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-location-dot text-red-500"></i>
                                    <span class="text-gray-700">{{ $asset->location->name ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        @if($asset->description)
                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-1">Deskripsi</label>
                            <p class="text-gray-700 leading-relaxed">{{ $asset->description }}</p>
                        </div>
                        @endif

                        {{-- GAMBAR ASSET --}}
                        @if($asset->image)
                        <div>
                            <label class="block text-sm font-bold text-gray-600 mb-3">Foto Aset</label>
                            <div class="relative group max-w-md">
                                <img src="{{ asset('storage/' . $asset->image) }}"
                                    alt="Foto {{ $asset->name }}"
                                    class="w-full h-48 object-cover rounded-lg border border-gray-200 cursor-pointer hover:opacity-90 transition"
                                    onclick="openImageModal('{{ asset('storage/' . $asset->image) }}', 'Foto Aset: {{ htmlspecialchars($asset->name, ENT_QUOTES) }}')">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-expand text-white opacity-0 group-hover:opacity-100 transition text-xl"></i>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- STATISTIK TIKET --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-chart-bar text-green-500"></i>
                        Statistik Laporan Kerusakan
                    </h3>

                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-800">{{ $ticketStats['total'] }}</div>
                            <div class="text-xs text-gray-500 uppercase">Total</div>
                        </div>

                        <div class="text-center p-4 bg-red-50 rounded-lg">
                            <div class="text-2xl font-bold text-red-600">{{ $ticketStats['open'] }}</div>
                            <div class="text-xs text-gray-500 uppercase">Open</div>
                        </div>

                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $ticketStats['in_progress'] }}</div>
                            <div class="text-xs text-gray-500 uppercase">Progress</div>
                        </div>

                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $ticketStats['resolved'] }}</div>
                            <div class="text-xs text-gray-500 uppercase">Resolved</div>
                        </div>

                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-600">{{ $ticketStats['closed'] }}</div>
                            <div class="text-xs text-gray-500 uppercase">Closed</div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- KOLOM KANAN: INFORMASI TAMBAHAN --}}
            <div class="space-y-6">

                {{-- INFO SISTEM --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-cogs text-purple-500"></i>
                        Sistem
                    </h3>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">UUID:</span>
                            <span class="font-mono text-xs text-gray-800">{{ $asset->uuid }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Dibuat:</span>
                            <span class="text-gray-800">{{ $asset->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Diupdate:</span>
                            <span class="text-gray-800">{{ $asset->updated_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- RIWAYAT TIKET --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-history text-orange-500"></i>
                Riwayat Laporan Kerusakan
            </h3>

            @if($asset->tickets && $asset->tickets->count() > 0)
            <div class="space-y-4">
                @foreach($asset->tickets as $ticket)
                <div class="border border-gray-100 rounded-lg p-4 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-xs text-gray-400">#{{ $ticket->id }}</span>
                            @php
                            $statusColors = [
                            'open' => 'bg-red-100 text-red-700',
                            'in_progress' => 'bg-blue-100 text-blue-700',
                            'pending_sparepart' => 'bg-amber-100 text-amber-700',
                            'resolved' => 'bg-green-100 text-green-700',
                            'closed' => 'bg-gray-100 text-gray-700'
                            ];
                            @endphp
                            <span class="px-2 py-1 rounded text-xs font-bold {{ $statusColors[$ticket->status] ?? 'bg-gray-100' }}">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-500">{{ $ticket->created_at->format('d M Y H:i') }}</div>
                    </div>

                    <div class="mb-2">
                        <div class="font-bold text-gray-800">{{ $ticket->title }}</div>
                        <div class="text-sm text-gray-600 line-clamp-2">{{ $ticket->description }}</div>
                    </div>

                    <div class="flex justify-between items-center text-sm">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-user text-gray-400"></i>
                                <span class="text-gray-600">{{ $ticket->user->name }}</span>
                            </div>
                            @if($ticket->technician)
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-user-gear text-gray-400"></i>
                                <span class="text-gray-600">{{ $ticket->technician->name }}</span>
                            </div>
                            @endif
                        </div>

                        <a href="{{ route('admin.tickets.show', $ticket->id) }}"
                            class="text-blue-600 hover:text-blue-800 font-bold text-xs">
                            Lihat Detail →
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 text-gray-400">
                <i class="fa-regular fa-folder-open text-4xl mb-2 opacity-50"></i>
                <p>Belum ada laporan kerusakan untuk aset ini.</p>
            </div>
            @endif
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