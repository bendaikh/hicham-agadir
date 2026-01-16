<x-app-layout>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Gestion des Achats</h1>
            <p class="text-gray-500">Suivez vos achats et gérez le stock automatiquement</p>
        </div>
        <a href="{{ route('purchases.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nouvel Achat
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
            <div class="text-sm text-gray-500 mb-1">Total Achats</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format(\App\Models\Purchase::sum('total_amount'), 2, ',', '.') }} MAD</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">Ce mois</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format(\App\Models\Purchase::whereMonth('created_at', now()->month)->sum('total_amount'), 2, ',', '.') }} MAD</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">En attente</div>
            <div class="text-2xl font-bold text-orange-600">{{ \App\Models\Purchase::where('status', 'pending')->count() }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">Nombre d'achats</div>
            <div class="text-2xl font-bold text-green-600">{{ \App\Models\Purchase::count() }}</div>
        </div>
    </div>

    <!-- Purchases Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <input type="text" placeholder="Rechercher un achat..." class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <select class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les statuts</option>
                    <option value="pending">En attente</option>
                    <option value="completed">Complété</option>
                    <option value="cancelled">Annulé</option>
                </select>
            </div>
        </div>
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-gray-700/50 text-xs font-bold text-gray-500 uppercase">
                    <th class="px-6 py-4 text-left">ID</th>
                    <th class="px-6 py-4 text-left">Fournisseur</th>
                    <th class="px-6 py-4 text-left">Date</th>
                    <th class="px-6 py-4 text-left">Montant</th>
                    <th class="px-6 py-4 text-left">Statut</th>
                    <th class="px-6 py-4 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse (\App\Models\Purchase::with(['supplier', 'items'])->latest()->take(10)->get() as $purchase)
                    <tr class="text-sm hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4">
                            <a href="{{ route('purchases.show', $purchase) }}" class="font-bold text-blue-600 hover:underline">#ACH-{{ str_pad($purchase->id, 4, '0', STR_PAD_LEFT) }}</a>
                            @if($purchase->items->count() > 0)
                                <div class="text-xs text-gray-400 mt-1">{{ $purchase->items->count() }} article(s)</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $purchase->supplier->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-gray-100">{{ number_format($purchase->total_amount, 2, ',', '.') }} MAD</td>
                        <td class="px-6 py-4">
                            @php
                                $statusClasses = match($purchase->status) {
                                    'completed' => 'bg-green-100 text-green-700',
                                    'pending' => 'bg-orange-100 text-orange-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                                $statusLabel = match($purchase->status) {
                                    'completed' => 'Complété',
                                    'pending' => 'En attente',
                                    'cancelled' => 'Annulé',
                                    default => $purchase->status
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusClasses }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('purchases.show', $purchase) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('purchases.edit', $purchase) }}" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Aucun achat trouvé. <a href="{{ route('purchases.create') }}" class="text-blue-600 hover:underline">Enregistrer votre premier achat</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
