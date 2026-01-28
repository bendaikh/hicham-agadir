<x-app-layout>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Gestion des Fournisseurs</h1>
            <p class="text-gray-500">Gérez vos fournisseurs d'aluminium et matières premières</p>
        </div>
        <a href="{{ route('suppliers.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nouveau Fournisseur
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-700 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">Total Fournisseurs</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ \App\Models\Supplier::count() }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">Fournisseurs Actifs</div>
            <div class="text-2xl font-bold text-green-600">{{ \App\Models\Supplier::count() }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">Avec solde dû</div>
            <div class="text-2xl font-bold text-orange-600">{{ \App\Models\Supplier::where('balance', '>', 0)->count() }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">Total Dettes</div>
            <div class="text-2xl font-bold text-red-600">{{ number_format(\App\Models\Supplier::sum('balance'), 2, ',', '.') }} MAD</div>
        </div>
    </div>

    <!-- Suppliers Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden" x-data="suppliersManager()">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-4 flex-1">
                <div class="relative flex-1 max-w-xs">
                    <input type="text" 
                           x-model="searchQuery" 
                           @input="filterSuppliers()"
                           placeholder="Rechercher un fournisseur..." 
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
                                <select @change="filterSuppliers()" 
                                        x-model="filterBalance"
                                        class="w-full px-3 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-600 dark:text-white rounded text-sm">
                                    <option value="">Tous</option>
                                    <option value="due">Avec solde dû</option>
                                    <option value="clear">Solde clair</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-2">Période</label>
                                <select @change="filterSuppliers()" 
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
            </div>
        </div>
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-gray-700/50 text-xs font-bold text-gray-500 uppercase">
                    <th class="px-6 py-4 text-left">Fournisseur</th>
                    <th class="px-6 py-4 text-left">Email</th>
                    <th class="px-6 py-4 text-left">Téléphone</th>
                    <th class="px-6 py-4 text-left">Adresse</th>
                    <th class="px-6 py-4 text-left">Solde</th>
                    <th class="px-6 py-4 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse (\App\Models\Supplier::latest()->get() as $supplier)
                    <tr class="text-sm hover:bg-gray-50 dark:hover:bg-gray-700/30 supplier-row"
                        data-name="{{ strtolower($supplier->name) }}"
                        data-email="{{ strtolower($supplier->email ?? '') }}"
                        data-phone="{{ $supplier->phone ?? '' }}"
                        data-balance="{{ $supplier->balance }}"
                        data-month="{{ $supplier->created_at->month }}"
                        data-year="{{ $supplier->created_at->year }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-bold">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                </div>
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $supplier->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $supplier->email ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $supplier->phone ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-500 truncate max-w-xs">{{ $supplier->address ?? '-' }}</td>
                        <td class="px-6 py-4 font-bold {{ $supplier->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ number_format($supplier->balance, 2, ',', '.') }} MAD
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                            Aucun fournisseur trouvé. <a href="{{ route('suppliers.create') }}" class="text-blue-600 hover:underline">Ajouter votre premier fournisseur</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        function suppliersManager() {
            return {
                searchQuery: '',
                showFilter: false,
                filterBalance: '',
                filterPeriod: '',
                
                filterSuppliers() {
                    const rows = document.querySelectorAll('.supplier-row');
                    
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
