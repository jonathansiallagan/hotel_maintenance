<x-admin-layout>
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h1 class="text-2xl font-bold text-gray-800">Manajemen User</h1>
            <div class="text-sm text-gray-500">
                Total: {{ $users->total() }} user
            </div>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 font-bold border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Nama</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Role</th>
                            <th class="px-6 py-4">Departemen</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Bergabung</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-sm font-bold text-gray-600">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                $roleColor = match($user->role) {
                                    'admin' => 'bg-red-50 text-red-600 border border-red-100',
                                    'technician' => 'bg-blue-50 text-blue-600 border border-blue-100',
                                    'staff' => 'bg-green-50 text-green-600 border border-green-100',
                                    default => 'bg-gray-50 text-gray-600'
                                };
                                @endphp
                                <span class="{{ $roleColor }} px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $user->department ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($user->email_verified_at)
                                <span class="bg-green-50 text-green-600 border border-green-100 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">
                                    Aktif
                                </span>
                                @else
                                <span class="bg-yellow-50 text-yellow-600 border border-yellow-100 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">
                                    Belum Verifikasi
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-sm">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <i class="fa-regular fa-users text-3xl mb-2 opacity-50"></i>
                                    <p class="text-sm">Belum ada data user.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($users->hasPages())
            <div class="mt-6 flex justify-center">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>
</x-admin-layout>