<x-app-layout>
    <div class="flex items-center justify-between mb-8" x-data="reportsManager()" x-init="init()" :class="{ 'opacity-50': isLoading }">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Analyses & Rapports</h1>
            <p class="text-gray-500">Visualisez les performances de votre entreprise</p>
        </div>
        <div class="flex items-center gap-3">
            <select x-model="selectedPeriod" @change="updatePeriod()" :disabled="isLoading" class="border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                <option value="month">Ce mois</option>
                <option value="quarter">Ce trimestre</option>
                <option value="year">Cette année</option>
                <option value="custom">Personnalisé</option>
            </select>
            <div x-show="selectedPeriod === 'custom'" class="flex items-center gap-2" style="display: none;">
                <input type="date" x-model="startDate" @change="updatePeriod()" class="border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <span class="text-gray-400">à</span>
                <input type="date" x-model="endDate" @change="updatePeriod()" class="border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button @click="exportReport()" :disabled="isLoading" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg font-semibold text-xs text-gray-700 dark:text-gray-100 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-600 transition disabled:opacity-50 disabled:cursor-not-allowed">
                <svg x-show="!isLoading" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <svg x-show="isLoading" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                </svg>
                Exporter
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @php
            $now = now();
            
            // Calculate date range based on period
            if ($period === 'month') {
                $startDate = $now->clone()->startOfMonth();
                $endDate = $now->clone()->endOfMonth();
            } elseif ($period === 'quarter') {
                $startDate = $now->clone()->startOfQuarter();
                $endDate = $now->clone()->endOfQuarter();
            } elseif ($period === 'year') {
                $startDate = $now->clone()->startOfYear();
                $endDate = $now->clone()->endOfYear();
            } else {
                $startDate = $startDateCustom ?? $now->clone()->startOfMonth();
                $endDate = $endDateCustom ?? $now->clone()->endOfMonth();
            }
            
            $totalSales = \App\Models\Invoice::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
            $totalPurchases = \App\Models\Purchase::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
            $grossProfit = $totalSales - $totalPurchases;
            $profitMargin = $totalSales > 0 ? round(($grossProfit / $totalSales) * 100, 1) : 0;
            
            // Calculate growth rate
            $prevStartDate = $startDate->clone()->subMonths(1);
            $prevEndDate = $endDate->clone()->subMonths(1);
            $prevSales = \App\Models\Invoice::whereBetween('created_at', [$prevStartDate, $prevEndDate])->sum('total_amount');
            $salesGrowth = $prevSales > 0 ? round((($totalSales - $prevSales) / $prevSales) * 100, 1) : 0;
        @endphp
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 rounded-2xl shadow-lg text-white">
            <div class="flex items-center justify-between mb-4">
                <svg class="w-8 h-8 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                <span class="text-xs font-semibold bg-white/20 px-2 py-1 rounded-full">{{ $salesGrowth > 0 ? '+' : '' }}{{ $salesGrowth }}%</span>
            </div>
            <div class="text-sm opacity-80 mb-1">Chiffre d'affaires</div>
            <div class="text-2xl font-bold">{{ number_format($totalSales, 0, ',', '.') }} MAD</div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-2xl shadow-lg text-white">
            <div class="flex items-center justify-between mb-4">
                <svg class="w-8 h-8 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs font-semibold bg-white/20 px-2 py-1 rounded-full">{{ $totalSales > 0 ? round(($grossProfit / $totalSales) * 100, 1) : 0 }}%</span>
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
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-6">Ventes vs Achats (par mois)</h3>
            <div class="h-64 flex items-end justify-around px-4">
                @php
                    $months = [];
                    $sales = [];
                    $purchases = [];
                    
                    for ($i = 5; $i >= 0; $i--) {
                        $date = now()->subMonths($i);
                        $months[] = $date->format('M');
                        $sales[] = \App\Models\Invoice::whereYear('created_at', $date->year)
                            ->whereMonth('created_at', $date->month)
                            ->sum('total_amount');
                        $purchases[] = \App\Models\Purchase::whereYear('created_at', $date->year)
                            ->whereMonth('created_at', $date->month)
                            ->sum('total_amount');
                    }
                    
                    $maxVal = max(max($sales), max($purchases));
                    $maxVal = $maxVal > 0 ? $maxVal : 100;
                @endphp
                @foreach ($months as $idx => $month)
                    <div class="flex flex-col items-center gap-2">
                        <div class="flex gap-1 items-end" style="height: 120px;">
                            <div class="w-6 bg-blue-500 rounded-t" style="height: {{ ($sales[$idx] / $maxVal) * 100 }}px"></div>
                            <div class="w-6 bg-red-400 rounded-t" style="height: {{ ($purchases[$idx] / $maxVal) * 100 }}px"></div>
                        </div>
                        <span class="text-xs text-gray-400">{{ $month }}</span>
                    </div>
                @endforeach
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
                @php
                    $topProducts = \App\Models\Article::with('invoiceItems')
                        ->withCount('invoiceItems')
                        ->orderBy('invoice_items_count', 'desc')
                        ->limit(5)
                        ->get();
                    
                    $maxAmount = $topProducts->max(fn($p) => $p->invoiceItems->sum('total_price'));
                    $maxAmount = $maxAmount > 0 ? $maxAmount : 1;
                @endphp
                @forelse ($topProducts as $index => $product)
                    @php
                        $totalSold = $product->invoiceItems->sum('total_price');
                    @endphp
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between mb-1">
                                <span class="font-medium text-gray-900 dark:text-gray-100 text-sm">{{ $product->designation }}</span>
                                <span class="text-sm text-gray-500">{{ $product->invoice_items_count }} ventes</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($totalSold / $maxAmount) * 100 }}%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalSold, 0, ',', '.') }} MAD</span>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400">
                        <p>Aucun produit vendu</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Activity -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-6">Activité Récente</h3>
            <div class="space-y-4">
                @php
                    $now = now();
                    if ($period === 'month') {
                        $startDate = $now->clone()->startOfMonth();
                        $endDate = $now->clone()->endOfMonth();
                    } elseif ($period === 'quarter') {
                        $startDate = $now->clone()->startOfQuarter();
                        $endDate = $now->clone()->endOfQuarter();
                    } elseif ($period === 'year') {
                        $startDate = $now->clone()->startOfYear();
                        $endDate = $now->clone()->endOfYear();
                    } else {
                        $startDate = $startDateCustom ?? $now->clone()->startOfMonth();
                        $endDate = $endDateCustom ?? $now->clone()->endOfMonth();
                    }
                @endphp
                @forelse (\App\Models\Invoice::with('client')->whereBetween('created_at', [$startDate, $endDate])->latest()->take(8)->get() as $invoice)
                    <div class="flex items-center gap-4 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-xl">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Facture #{{ $invoice->invoice_number }}</p>
                            <p class="text-xs text-gray-500">{{ $invoice->client->name ?? 'Client' }} - {{ $invoice->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="text-sm font-bold text-green-600">+{{ number_format($invoice->total_amount, 0, ',', '.') }} MAD</span>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400">
                        <p>Aucune activité pour cette période</p>
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
                    @php
                        $monthPurchases = \App\Models\Purchase::whereMonth('created_at', now()->month)->count();
                        $totalPurchases = \App\Models\Purchase::count();
                        $purchasePercent = $totalPurchases > 0 ? round(($monthPurchases / $totalPurchases) * 100) : 0;
                    @endphp
                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $purchasePercent }}%"></div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                <button @click="exportReport()" :disabled="isLoading" class="w-full py-3 text-sm font-bold text-blue-600 border border-blue-200 dark:border-blue-900 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Générer un rapport complet
                </button>
            </div>
        </div>
    </div>

    <script>
        function reportsManager() {
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            
            return {
                selectedPeriod: 'month',
                startDate: firstDay.toISOString().split('T')[0],
                endDate: today.toISOString().split('T')[0],
                isLoading: false,
                
                init() {
                    // Get current period from URL if exists
                    const params = new URLSearchParams(window.location.search);
                    const period = params.get('period');
                    if (period) {
                        this.selectedPeriod = period;
                        
                        // Restore custom dates if provided
                        if (period === 'custom') {
                            const startDate = params.get('start_date');
                            const endDate = params.get('end_date');
                            if (startDate) this.startDate = startDate;
                            if (endDate) this.endDate = endDate;
                        }
                    }
                },
                
                updatePeriod() {
                    if (this.isLoading) return; // Prevent multiple clicks
                    
                    // Validate custom dates
                    if (this.selectedPeriod === 'custom') {
                        if (!this.startDate || !this.endDate) {
                            alert('Veuillez sélectionner une date de début et de fin');
                            return;
                        }
                        if (new Date(this.startDate) > new Date(this.endDate)) {
                            alert('La date de début doit être avant la date de fin');
                            return;
                        }
                    }
                    
                    // Show loading state
                    this.isLoading = true;
                    
                    // Add slight delay to show transition
                    setTimeout(() => {
                        const params = new URLSearchParams();
                        params.set('period', this.selectedPeriod);
                        
                        if (this.selectedPeriod === 'custom') {
                            params.set('start_date', this.startDate);
                            params.set('end_date', this.endDate);
                        }
                        
                        window.location.href = `/reports?${params.toString()}`;
                    }, 200);
                },
                
                exportReport() {
                    if (this.isLoading) return; // Prevent multiple clicks
                    
                    // Validate custom dates
                    if (this.selectedPeriod === 'custom') {
                        if (!this.startDate || !this.endDate) {
                            alert('Veuillez sélectionner une date de début et de fin');
                            return;
                        }
                        if (new Date(this.startDate) > new Date(this.endDate)) {
                            alert('La date de début doit être avant la date de fin');
                            return;
                        }
                    }
                    
                    // Show loading state
                    this.isLoading = true;
                    
                    const params = new URLSearchParams();
                    params.set('period', this.selectedPeriod);
                    
                    if (this.selectedPeriod === 'custom') {
                        params.set('start_date', this.startDate);
                        params.set('end_date', this.endDate);
                    }
                    
                    window.location.href = `/reports/export?${params.toString()}`;
                }
            };
        }
    </script>
</x-app-layout>
