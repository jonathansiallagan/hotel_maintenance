<header class="bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-40 shadow-sm shrink-0"
    style="height: 70px;">

    {{-- JUDUL HALAMAN --}}
    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">
        Monitoring Dashboard
    </h1>

    {{-- KANAN: Search & Notif --}}
    <div class="flex items-center gap-6">

        {{-- Search Bar --}}
        <div class="relative" x-data="{
            open: false,
            query: '',
            results: [],
            search() {
                if (this.query.length < 2) {
                    this.results = [];
                    return;
                }
                fetch(`/admin/search?q=${encodeURIComponent(this.query)}`)
                    .then(response => response.json())
                    .then(data => {
                        this.results = data.results;
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        this.results = [];
                    });
            },
            getIcon(type) {
                const icons = {
                    'ticket': 'fa-ticket-alt',
                    'asset': 'fa-cogs',
                    'sparepart': 'fa-wrench'
                };
                return 'fas ' + (icons[type] || 'fa-search');
            },
            getStatusClass(status) {
                const classes = {
                    'open': 'bg-yellow-100 text-yellow-800',
                    'in_progress': 'bg-blue-100 text-blue-800',
                    'pending_sparepart': 'bg-orange-100 text-orange-800',
                    'resolved': 'bg-green-100 text-green-800',
                    'available': 'bg-green-100 text-green-800',
                    'out_of_stock': 'bg-red-100 text-red-800',
                    'active': 'bg-green-100 text-green-800',
                    'inactive': 'bg-gray-100 text-gray-800',
                    'maintenance': 'bg-yellow-100 text-yellow-800'
                };
                return classes[status] || 'bg-gray-100 text-gray-800';
            }
        }">
            <form @submit.prevent="search()" class="relative">
                <input type="text"
                    x-model="query"
                    @input="search()"
                    @focus="open = true"
                    @blur="setTimeout(() => open = false, 200)"
                    placeholder="Cari tiket, aset..."
                    class="w-64 pl-4 pr-10 py-2 rounded-full border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-400 transition-shadow">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400 text-xs"></i>
                </div>
            </form>

            {{-- Search Results Dropdown --}}
            <div x-show="open && results.length > 0"
                x-transition
                class="absolute top-full mt-2 w-96 bg-white rounded-lg shadow-lg border border-gray-200 z-50 max-h-96 overflow-y-auto">
                <div class="p-2">
                    <template x-for="result in results" :key="result.id">
                        <a :href="result.url"
                            class="block p-3 hover:bg-gray-50 rounded-md border-b border-gray-100 last:border-b-0">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <i :class="getIcon(result.type)" class="text-lg text-gray-500"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate" x-text="result.title"></p>
                                    <p class="text-xs text-gray-500 truncate" x-text="result.description"></p>
                                    <span :class="getStatusClass(result.status)"
                                        class="inline-block px-2 py-1 text-xs rounded-full mt-1 capitalize"
                                        x-text="result.status"></span>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
        </div>

        <!-- {{-- Notification Bell --}}
        <div class="relative" x-data="{
            open: false,
            notifications: [],
            unreadCount: 0,
            loadNotifications() {
                fetch('/admin/notifications')
                    .then(response => response.json())
                    .then(data => {
                        this.notifications = data.notifications;
                        this.unreadCount = data.notifications.length;
                    })
                    .catch(error => {
                        console.error('Notifications error:', error);
                        this.notifications = [];
                        this.unreadCount = 0;
                    });
            }
        }">
            <button @click="open = !open; if(open) loadNotifications()"
                class="relative text-black hover:text-gray-600 transition">
                <i class="fa-regular fa-bell text-2xl"></i>
                {{-- Badge Merah --}}
                <span x-show="unreadCount > 0"
                    x-text="unreadCount"
                    class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white font-medium">
                </span>
            </button>

            {{-- Notification Dropdown --}}
            <div x-show="open"
                @click.away="open = false"
                x-transition
                class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50 max-h-96 overflow-y-auto">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900">Notifikasi</h3>
                </div>
                <div class="divide-y divide-gray-200">
                    <template x-for="notification in notifications" :key="notification.id">
                        <a :href="notification.url"
                            class="block p-4 hover:bg-gray-50">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <i :class="notification.icon + ' ' + notification.color" class="text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                                    <p class="text-xs text-gray-500" x-text="notification.message"></p>
                                    <p class="text-xs text-gray-400 mt-1" x-text="notification.time"></p>
                                </div>
                            </div>
                        </a>
                    </template>
                    <div x-show="notifications.length === 0" class="p-4 text-center text-gray-500 text-sm">
                        Tidak ada notifikasi baru
                    </div>
                </div>
            </div>
        </div> -->

        {{-- Profil Dropdown (Opsional, agar bisa Logout) --}}
        <div class="relative ml-2" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                <img src="https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff" class="w-8 h-8 rounded-full border border-gray-200">
            </button>

            {{-- Dropdown Menu --}}
            <div x-show="open" @click.away="open = false"
                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-100"
                style="display: none;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Log Out
                    </button>
                </form>
            </div>
        </div>

    </div>
</header>