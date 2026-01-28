<x-app-layout>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Gestion des Paiements</h1>
            <p class="text-gray-500">Suivez les paiements de vos clients et fournisseurs</p>
        </div>
        <a href="{{ route('payments.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nouveau Paiement
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 bg-green-50 rounded-lg">
                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-sm text-gray-500 mb-1">Total Encaissé</div>
            <div class="text-2xl font-bold text-green-600">{{ number_format(\App\Models\Payment::sum('amount'), 2, ',', '.') }} MAD</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 bg-blue-50 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="text-sm text-gray-500 mb-1">Ce mois</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format(\App\Models\Payment::whereMonth('created_at', now()->month)->sum('amount'), 2, ',', '.') }} MAD</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 bg-purple-50 rounded-lg">
                    <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </div>
            </div>
            <div class="text-sm text-gray-500 mb-1">Nombre de transactions</div>
            <div class="text-2xl font-bold text-purple-600">{{ \App\Models\Payment::count() }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 bg-orange-50 rounded-lg">
                    <svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-sm text-gray-500 mb-1">Aujourd'hui</div>
            <div class="text-2xl font-bold text-orange-600">{{ number_format(\App\Models\Payment::whereDate('created_at', today())->sum('amount'), 2, ',', '.') }} MAD</div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden" x-data="paymentsManager()">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-4 flex-1">
                <div class="relative flex-1 max-w-xs">
                    <input type="text" 
                           x-model="searchQuery" 
                           @input="filterPayments()"
                           placeholder="Rechercher une transaction..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <select x-model="filterMethod" @change="filterPayments()" class="border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Toutes les méthodes</option>
                    <option value="cash">Espèces</option>
                    <option value="card">Carte bancaire</option>
                    <option value="transfer">Virement</option>
                    <option value="check">Chèque</option>
                </select>
            </div>
        </div>
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-gray-700/50 text-xs font-bold text-gray-500 uppercase">
                    <th class="px-6 py-4 text-left">ID</th>
                    <th class="px-6 py-4 text-left">Type</th>
                    <th class="px-6 py-4 text-left">Date</th>
                    <th class="px-6 py-4 text-left">Montant</th>
                    <th class="px-6 py-4 text-left">Méthode</th>
                    <th class="px-6 py-4 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse (\App\Models\Payment::latest()->get() as $payment)
                    <tr class="text-sm hover:bg-gray-50 dark:hover:bg-gray-700/30 payment-row"
                        data-type="{{ strtolower(class_basename($payment->payable_type)) }}"
                        data-method="{{ strtolower($payment->payment_method) }}">
                        <td class="px-6 py-4 font-bold text-blue-600">#PAY-{{ str_pad($payment->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-4 text-gray-900 dark:text-gray-100">
                            {{ class_basename($payment->payable_type) }}
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 font-bold text-green-600">+{{ number_format($payment->amount, 2, ',', '.') }} MAD</td>
                        <td class="px-6 py-4">
                            @php
                                $methodClasses = match($payment->payment_method) {
                                    'cash' => 'bg-green-100 text-green-700',
                                    'card' => 'bg-blue-100 text-blue-700',
                                    'transfer' => 'bg-purple-100 text-purple-700',
                                    'check' => 'bg-orange-100 text-orange-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                                $methodLabel = match($payment->payment_method) {
                                    'cash' => 'Espèces',
                                    'card' => 'Carte',
                                    'transfer' => 'Virement',
                                    'check' => 'Chèque',
                                    default => $payment->payment_method
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $methodClasses }}">
                                {{ $methodLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('payments.show', $payment) }}" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition inline-block">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Aucun paiement trouvé. <a href="{{ route('payments.create') }}" class="text-blue-600 hover:underline">Enregistrer votre premier paiement</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        function paymentsManager() {
            return {
                searchQuery: '',
                filterMethod: '',
                
                filterPayments() {
                    const rows = document.querySelectorAll('.payment-row');
                    
                    rows.forEach(row => {
                        let show = true;
                        
                        // Search filter
                        if (this.searchQuery) {
                            const searchLower = this.searchQuery.toLowerCase();
                            const type = row.dataset.type;
                            
                            if (!type.includes(searchLower)) {
                                show = false;
                            }
                        }
                        
                        // Method filter
                        if (this.filterMethod && show) {
                            const method = row.dataset.method;
                            if (method !== this.filterMethod) {
                                show = false;
                            }
                        }
                        
                        row.style.display = show ? '' : 'none';
                    });
                }
            };
        }
    </script>
</x-app-layout>
