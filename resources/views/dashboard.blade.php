<x-app-layout>
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">
        <!-- Card: Total Sales -->
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="hidden sm:flex items-center text-green-500 text-xs sm:text-sm font-semibold">
                    <span>+12.5%</span>
                    <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-9 9-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <div class="text-xs sm:text-sm text-gray-500 mb-1">Total des ventes</div>
            <div class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ number_format($totalSales, 0, ',', '.') }} <span class="text-xs sm:text-sm font-medium text-gray-400">MAD</span>
            </div>
        </div>

        <!-- Card: Total Purchases -->
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div class="hidden sm:flex items-center text-red-500 text-xs sm:text-sm font-semibold">
                    <span>-4.2%</span>
                    <svg class="h-4 w-4 ml-1 transform rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-9 9-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <div class="text-xs sm:text-sm text-gray-500 mb-1">Total des achats</div>
            <div class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ number_format($totalPurchases, 0, ',', '.') }} <span class="text-xs sm:text-sm font-medium text-gray-400">MAD</span>
            </div>
        </div>

        <!-- Card: Gross Profit -->
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                    </svg>
                </div>
                <div class="hidden sm:flex items-center text-green-500 text-xs sm:text-sm font-semibold">
                    <span>+8.1%</span>
                    <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-9 9-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <div class="text-xs sm:text-sm text-gray-500 mb-1">Bénéfice brut</div>
            <div class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ number_format($grossProfit, 0, ',', '.') }} <span class="text-xs sm:text-sm font-medium text-gray-400">MAD</span>
            </div>
        </div>

        <!-- Card: Pending Payments -->
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                    <svg class="h-5 w-5 sm:h-6 sm:w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div class="hidden sm:flex items-center text-gray-400 text-xs sm:text-sm font-semibold">
                    <span>+2.4%</span>
                </div>
            </div>
            <div class="text-xs sm:text-sm text-gray-500 mb-1">Paiements en attente</div>
            <div class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ number_format($pendingPayments, 0, ',', '.') }} <span class="text-xs sm:text-sm font-medium text-gray-400">MAD</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 mb-6 sm:mb-8">
        <!-- Sales Chart Container -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100">Performance des Ventes</h3>
                    <p class="text-xs sm:text-sm text-gray-500">Croissance des revenus sur les 6 derniers mois</p>
                </div>
                <div class="flex bg-gray-100 dark:bg-gray-700 p-1 rounded-lg self-start">
                    <button class="px-3 sm:px-4 py-1.5 text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400">Semaine</button>
                    <button class="px-3 sm:px-4 py-1.5 text-xs sm:text-sm font-medium bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-md shadow-sm">Mois</button>
                </div>
            </div>
            <!-- Chart -->
            <div class="h-48 sm:h-64 flex items-end justify-between px-2 sm:px-4 pb-8">
                @for ($i = 1; $i <= 6; $i++)
                    <div class="w-8 sm:w-12 bg-gradient-to-t from-blue-500/10 to-cyan-500/20 rounded-t-lg relative group hover:from-blue-500/20 hover:to-cyan-500/30 transition-all cursor-pointer" style="height: {{ rand(30, 90) }}%">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-blue-500 to-cyan-400 rounded-full"></div>
                        <div class="absolute -bottom-8 left-1/2 -translate-x-1/2 text-[10px] sm:text-xs text-gray-400 uppercase font-medium">
                            {{ ['JAN', 'FÉV', 'MAR', 'AVR', 'MAI', 'JUN'][$i-1] }}
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Manufacturing Status Container -->
        <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 sm:mb-6">Statut de Fabrication</h3>
            
            <div class="space-y-4 sm:space-y-6">
                @foreach ([
                    ['label' => 'Fenêtres Coulissantes', 'value' => 78, 'color' => 'from-blue-500 to-cyan-400'],
                    ['label' => 'Portes Battantes', 'value' => 42, 'color' => 'from-slate-400 to-slate-500'],
                    ['label' => 'Persiennes Industrielles', 'value' => 91, 'color' => 'from-green-500 to-emerald-400'],
                    ['label' => 'Cloisons Vitrées', 'value' => 25, 'color' => 'from-orange-500 to-amber-400'],
                ] as $item)
                    <div>
                        <div class="flex justify-between text-xs sm:text-sm mb-2">
                            <span class="font-medium text-gray-700 dark:text-gray-300 truncate mr-2">{{ $item['label'] }}</span>
                            <span class="font-bold text-gray-900 dark:text-gray-100">{{ $item['value'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-gradient-to-r {{ $item['color'] }} h-2 rounded-full transition-all duration-500" style="width: {{ $item['value'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <button class="w-full mt-6 sm:mt-8 py-2.5 sm:py-3 text-xs sm:text-sm font-bold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                Voir les Journaux de Production
            </button>
        </div>
    </div>

    <!-- Recent Invoices Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-4 sm:p-6 flex items-center justify-between border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100">Factures Récentes</h3>
            <a href="{{ route('invoices.index') }}" class="text-blue-600 text-xs sm:text-sm font-bold hover:underline">Voir Tout</a>
        </div>
        
        <!-- Mobile Cards View -->
        <div class="sm:hidden divide-y divide-gray-100 dark:divide-gray-700">
            @forelse ($recentInvoices as $invoice)
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-bold text-blue-600 text-sm">#INV-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</span>
                        @php
                            $statusClasses = match($invoice->status) {
                                'paid' => 'bg-green-100 text-green-700',
                                'pending' => 'bg-orange-100 text-orange-700',
                                'overdue' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                            $statusLabel = match($invoice->status) {
                                'paid' => 'Payée',
                                'pending' => 'En attente',
                                'overdue' => 'En retard',
                                default => $invoice->status
                            };
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusClasses }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $invoice->client->name }}</p>
                            <p class="text-xs text-gray-500">{{ $invoice->created_at->format('d M, Y') }}</p>
                        </div>
                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ number_format($invoice->total_amount, 0, ',', '.') }} MAD</span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500 text-sm">
                    Aucune facture récente trouvée.
                </div>
            @endforelse
        </div>
        
        <!-- Desktop Table View -->
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-gray-700/50 text-xs font-bold text-gray-500 uppercase">
                        <th class="px-6 py-4">ID Facture</th>
                        <th class="px-6 py-4">Nom du Client</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Montant</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($recentInvoices as $invoice)
                        <tr class="text-sm hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-blue-600">#INV-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $invoice->client->name }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $invoice->created_at->format('d M, Y') }}</td>
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-gray-100">{{ number_format($invoice->total_amount, 2, ',', '.') }} MAD</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClasses = match($invoice->status) {
                                        'paid' => 'bg-green-100 text-green-700',
                                        'pending' => 'bg-orange-100 text-orange-700',
                                        'overdue' => 'bg-red-100 text-red-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                    $statusLabel = match($invoice->status) {
                                        'paid' => 'Payée',
                                        'pending' => 'En attente',
                                        'overdue' => 'En retard',
                                        default => $invoice->status
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusClasses }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-400">
                                <button class="hover:text-gray-600 p-1 rounded hover:bg-gray-100 transition-colors">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                Aucune facture récente trouvée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
