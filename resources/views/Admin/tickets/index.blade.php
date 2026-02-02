<x-admin-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Daftar Tiket Masuk</h2>
        <p class="text-sm text-gray-500">Monitoring seluruh laporan kerusakan & maintenance.</p>
    </div>

    {{-- FILTER STATUS --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <form action="{{ route('admin.tickets.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-bold text-gray-600 mb-2">Filter Status</label>
                <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8] bg-white">
                    <option value="">Semua Status</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="pending_sparepart" {{ request('status') == 'pending_sparepart' ? 'selected' : '' }}>Pending Sparepart</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-bold text-gray-600 mb-2">Cari Tiket</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari judul, deskripsi, atau nama aset..."
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#D0BBB8]">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-[#D0BBB8] hover:bg-[#bda3a0] text-white px-4 py-2 rounded-lg font-bold text-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                <a href="{{ route('admin.tickets.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-bold text-sm transition flex items-center gap-2">
                    <i class="fa-solid fa-xmark"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-xs">
                <tr>
                    <th class="px-6 py-4">ID & Tanggal</th>
                    <th class="px-6 py-4">Aset</th>
                    <th class="px-6 py-4">Masalah</th>
                    <th class="px-6 py-4">Pelapor</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($tickets as $ticket)
                <tr id="ticket-{{ $ticket->id }}" class="hover:bg-gray-50/50">
                    <td class="px-6 py-4">
                        {{-- Tampilkan Ticket Number, jika kosong pakai ID --}}
                        <div class="font-bold text-gray-800">{{ $ticket->ticket_number }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $ticket->created_at->format('d M Y H:i') }}</div>
                    </td>

                    {{-- ASET: nama aset (atas) dan lokasi (bawah) --}}
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800">{{ $ticket->asset->name ?? 'Tanpa Aset' }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $ticket->asset->location->name ?? '-' }}</div>
                    </td>

                    {{-- MASALAH: judul (atas) dan deskripsi (bawah) --}}
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800">{{ Str::limit($ticket->title, 80) }}</div>
                        <div class="text-xs text-gray-500 truncate max-w-[200px]">{{ Str::limit($ticket->description, 140) }}</div>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-[10px] font-bold">
                                {{ substr($ticket->user->name ?? 'Sys', 0, 1) }}
                            </div>
                            <span class="text-gray-600">{{ $ticket->user->name ?? 'Sistem' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
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
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-100">
            {{ $tickets->links() }}
        </div>
    </div>
</x-admin-layout>