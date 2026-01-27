<x-app-layout>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Analyses & Rapports</h1>
            <p class="text-gray-500">Visualisez les performances de votre entreprise</p>
        </div>
        <div class="flex items-center gap-3">
            <select class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option>Ce mois</option>
                <option>Ce trimestre</option>
                <option>Cette année</option>
                <option>Personnalisé</option>
            </select>
            <button class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Exporter
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @php
            $totalSales = \App\Models\Invoice::sum('total_amount');
            $totalPurchases = \App\Models\Purchase::sum('total_amount');
            $grossProfit = $totalSales - $totalPurchases;
            $profitMargin = $totalSales > 0 ? round(($grossProfit / $totalSales) * 100, 1) : 0;
        @endphp
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 rounded-2xl shadow-lg text-white">
            <div class="flex items-center justify-between mb-4">
                <svg class="w-8 h-8 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <span class="text-xs font-semibold bg-white/20 px-2 py-1 rounded-full">+12.5%</span>
            </div>
            <div class="text-sm opacity-80 mb-1">Chiffre d'affaires</div>
            <div class="text-2xl font-bold">{{ number_format($totalSales, 0, ',', '.') }} MAD</div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-2xl shadow-lg text-white">
            <div class="flex items-center justify-between mb-4">
                <svg class="w-8 h-8 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs font-semibold bg-white/20 px-2 py-1 rounded-full">+8.3%</span>
            </div>
            <div class="text-sm opacity-80 mb-1">Bénéfice brut</div>
            <div class="text-2xl font-bold">{{ number_format($grossProfit, 0, ',', '.') }} MAD</div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-6 rounded-2xl shadow-lg text-white">
            <div class="flex items-center justify-between mb-4">
                <svg class="w-8 h-8 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <div class="text-sm opacity-80 mb-1">Marge bénéficiaire</div>
            <div class="text-2xl font-bold">{{ $profitMargin }}%</div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-6 rounded-2xl shadow-lg text-white">
            <div class="flex items-center justify-between mb-4">
                <svg class="w-8 h-8 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div class="text-sm opacity-80 mb-1">Total Clients</div>
            <div class="text-2xl font-bold">{{ \App\Models\Client::count() }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Sales vs Purchases Chart -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-6">Ventes vs Achats</h3>
            <div class="h-64 flex items-end justify-around px-4">
                @for ($i = 1; $i <= 6; $i++)
                    <div class="flex flex-col items-center gap-2">
                        <div class="flex gap-1 items-end">
                            <div class="w-6 bg-blue-500 rounded-t" style="height: {{ rand(40, 120) }}px"></div>
                            <div class="w-6 bg-red-400 rounded-t" style="height: {{ rand(30, 80) }}px"></div>
                        </div>
                        <span class="text-xs text-gray-400">{{ ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'][$i-1] }}</span>
                    </div>
                @endfor
            </div>
            <div class="flex justify-center gap-6 mt-4">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-blue-500 rounded"></div>
                    <span class="text-xs text-gray-500">Ventes</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-red-400 rounded"></div>
                    <span class="text-xs text-gray-500">Achats</span>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-6">Produits les plus vendus</h3>
            <div class="space-y-4">
                @foreach ([
                    ['name' => 'Fenêtres Coulissantes', 'sales' => 85, 'amount' => 125000],
                    ['name' => 'Portes Battantes', 'sales' => 62, 'amount' => 98000],
                    ['name' => 'Profilés Standard', 'sales' => 156, 'amount' => 45000],
                    ['name' => 'Vitrage 6mm', 'sales' => 94, 'amount' => 38000],
                    ['name' => 'Accessoires divers', 'sales' => 210, 'amount' => 21000],
                ] as $index => $product)
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between mb-1">
                                <span class="font-medium text-gray-900 dark:text-gray-100 text-sm">{{ $product['name'] }}</span>
                                <span class="text-sm text-gray-500">{{ $product['sales'] }} ventes</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($product['amount'] / 125000) * 100 }}%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ number_format($product['amount'], 0, ',', '.') }} MAD</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Activity -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-6">Activité Récente</h3>
            <div class="space-y-4">
                @forelse (\App\Models\Invoice::with('client')->latest()->take(5)->get() as $invoice)
                    <div class="flex items-center gap-4 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-xl">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Facture #{{ $invoice->invoice_number }}</p>
                            <p class="text-xs text-gray-500">{{ $invoice->client->name ?? 'Client' }} - {{ $invoice->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="text-sm font-bold text-green-600">+{{ number_format($invoice->total_amount, 0, ',', '.') }} MAD</span>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400">
                        <p>Aucune activité récente</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-6">Statistiques Rapides</h3>
            <div class="space-y-6">
                <div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-500">Factures payées</span>
                        <span class="font-bold text-gray-900 dark:text-gray-100">{{ \App\Models\Invoice::where('status', 'payee')->count() }}/{{ \App\Models\Invoice::count() }}</span>
                    </div>
                    @php
                        $totalInv = \App\Models\Invoice::count();
                        $paidInv = \App\Models\Invoice::where('status', 'payee')->count();
                        $paidPercent = $totalInv > 0 ? round(($paidInv / $totalInv) * 100) : 0;
                    @endphp
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $paidPercent }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-500">Devis acceptés</span>
                        <span class="font-bold text-gray-900 dark:text-gray-100">{{ \App\Models\Quote::where('status', 'accepted')->count() }}/{{ \App\Models\Quote::count() }}</span>
                    </div>
                    @php
                        $totalQ = \App\Models\Quote::count();
                        $accQ = \App\Models\Quote::where('status', 'accepted')->count();
                        $accPercent = $totalQ > 0 ? round(($accQ / $totalQ) * 100) : 0;
                    @endphp
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $accPercent }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-500">Achats ce mois</span>
                        <span class="font-bold text-gray-900 dark:text-gray-100">{{ \App\Models\Purchase::whereMonth('created_at', now()->month)->count() }}</span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: 65%"></div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                <button class="w-full py-3 text-sm font-bold text-blue-600 border border-blue-200 rounded-xl hover:bg-blue-50 transition">
                    Générer un rapport complet
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
