<x-app-layout>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Gestion des Clients</h1>
            <p class="text-gray-500">Gérez votre portefeuille clients et leur historique</p>
        </div>
        @if (session('success'))
            <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg z-50" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                {{ session('success') }}
            </div>
        @endif
        <a href="{{ route('clients.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nouveau Client
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">Total Clients</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ \App\Models\Client::count() }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">Nouveaux ce mois</div>
            <div class="text-2xl font-bold text-green-600">{{ \App\Models\Client::whereMonth('created_at', now()->month)->count() }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">Avec solde dû</div>
            <div class="text-2xl font-bold text-orange-600">{{ \App\Models\Client::where('balance', '>', 0)->count() }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">Total Créances</div>
            <div class="text-2xl font-bold text-red-600">{{ number_format(\App\Models\Client::sum('balance'), 2, ',', '.') }} MAD</div>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden" x-data="clientsManager()">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-4 flex-1">
                <div class="relative flex-1 max-w-xs">
                    <input type="text" 
                           x-model="searchQuery" 
                           @input="filterClients()"
                           placeholder="Rechercher un client..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <button @click="showFilter = !showFilter" 
                            class="px-3 py-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </button>
                    
                    <!-- Filter Dropdown -->
                    <div x-show="showFilter" 
                         @click.outside="showFilter = false"
                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 z-20">
                        <div class="p-4 space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-2">Solde</label>
                                <select @change="filterClients()" 
                                        x-model="filterBalance"
                                        class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-600 dark:text-white rounded text-sm">
                                    <option value="">Tous</option>
                                    <option value="due">Avec solde dû</option>
                                    <option value="clear">Solde clair</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-2">Période</label>
                                <select @change="filterClients()" 
                                        x-model="filterPeriod"
                                        class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-600 dark:text-white rounded text-sm">
                                    <option value="">Tous</option>
                                    <option value="month">Ce mois</option>
                                    <option value="year">Cette année</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <a href="{{ route('clients.export') }}" 
                   class="px-3 py-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span class="text-sm">Exporter</span>
                </a>
            </div>
        </div>
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-gray-700/50 text-xs font-bold text-gray-500 uppercase">
                    <th class="px-6 py-4 text-left">Client</th>
                    <th class="px-6 py-4 text-left">Email</th>
                    <th class="px-6 py-4 text-left">Téléphone</th>
                    <th class="px-6 py-4 text-left">Solde</th>
                    <th class="px-6 py-4 text-left">Créé le</th>
                    <th class="px-6 py-4 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse (\App\Models\Client::latest()->get() as $client)
                    <tr class="text-sm hover:bg-gray-50 dark:hover:bg-gray-700/30 client-row" 
                        data-name="{{ strtolower($client->name) }}"
                        data-email="{{ strtolower($client->email ?? '') }}"
                        data-phone="{{ $client->phone ?? '' }}"
                        data-balance="{{ $client->balance }}"
                        data-month="{{ $client->created_at->month }}"
                        data-year="{{ $client->created_at->year }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                    {{ strtoupper(substr($client->name, 0, 2)) }}
                                </div>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $client->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $client->email ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $client->phone ?? '-' }}</td>
                        <td class="px-6 py-4 font-bold {{ $client->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ number_format($client->balance, 2, ',', '.') }} MAD
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $client->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('clients.show', $client) }}" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('clients.edit', $client) }}" class="p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline" onsubmit="return confirm('Confirmer la suppression ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Aucun client trouvé
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        function clientsManager() {
            return {
                searchQuery: '',
                showFilter: false,
                filterBalance: '',
                filterPeriod: '',
                
                filterClients() {
                    const rows = document.querySelectorAll('.client-row');
                    
                    rows.forEach(row => {
                        let show = true;
                        
                        // Search filter
                        if (this.searchQuery) {
                            const searchLower = this.searchQuery.toLowerCase();
                            const name = row.dataset.name;
                            const email = row.dataset.email;
                            const phone = row.dataset.phone;
                            
                            if (!name.includes(searchLower) && 
                                !email.includes(searchLower) && 
                                !phone.includes(searchLower)) {
                                show = false;
                            }
                        }
                        
                        // Balance filter
                        if (this.filterBalance && show) {
                            const balance = parseFloat(row.dataset.balance);
                            if (this.filterBalance === 'due' && balance <= 0) show = false;
                            if (this.filterBalance === 'clear' && balance > 0) show = false;
                        }
                        
                        // Period filter
                        if (this.filterPeriod && show) {
                            const now = new Date();
                            const rowMonth = parseInt(row.dataset.month);
                            const rowYear = parseInt(row.dataset.year);
                            
                            if (this.filterPeriod === 'month') {
                                if (rowMonth !== now.getMonth() + 1 || rowYear !== now.getFullYear()) {
                                    show = false;
                                }
                            } else if (this.filterPeriod === 'year') {
                                if (rowYear !== now.getFullYear()) {
                                    show = false;
                                }
                            }
                        }
                        
                        row.style.display = show ? '' : 'none';
                    });
                }
            };
        }
    </script>
</x-app-layout>
