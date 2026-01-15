<x-app-layout>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Gestion des Devis</h1>
            <p class="text-gray-500">Créez et suivez vos devis clients</p>
        </div>
        <a href="{{ route('quotes.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nouveau Devis
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">Total Devis</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ \App\Models\Quote::count() }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">Acceptés</div>
            <div class="text-2xl font-bold text-green-600">{{ \App\Models\Quote::where('status', 'accepted')->count() }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">En attente</div>
            <div class="text-2xl font-bold text-orange-600">{{ \App\Models\Quote::where('status', 'pending')->count() }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">Taux de conversion</div>
            @php
                $totalQuotes = \App\Models\Quote::count();
                $acceptedQuotes = \App\Models\Quote::where('status', 'accepted')->count();
                $conversionRate = $totalQuotes > 0 ? round(($acceptedQuotes / $totalQuotes) * 100) : 0;
            @endphp
            <div class="text-2xl font-bold text-blue-600">{{ $conversionRate }}%</div>
        </div>
    </div>

    <!-- Quotes Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <input type="text" placeholder="Rechercher un devis..." class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <select class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les statuts</option>
                    <option value="pending">En attente</option>
                    <option value="accepted">Accepté</option>
                    <option value="rejected">Refusé</option>
                    <option value="expired">Expiré</option>
                </select>
            </div>
        </div>
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-gray-700/50 text-xs font-bold text-gray-500 uppercase">
                    <th class="px-6 py-4 text-left">N° Devis</th>
                    <th class="px-6 py-4 text-left">Client</th>
                    <th class="px-6 py-4 text-left">Date</th>
                    <th class="px-6 py-4 text-left">Expire le</th>
                    <th class="px-6 py-4 text-left">Montant</th>
                    <th class="px-6 py-4 text-left">Statut</th>
                    <th class="px-6 py-4 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse (\App\Models\Quote::with('client')->latest()->take(10)->get() as $quote)
                    <tr class="text-sm hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4 font-bold text-blue-600">#{{ $quote->quote_number }}</td>
                        <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $quote->client->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $quote->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $quote->expires_at ? \Carbon\Carbon::parse($quote->expires_at)->format('d/m/Y') : '-' }}</td>
                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-gray-100">{{ number_format($quote->total_amount, 2, ',', '.') }} MAD</td>
                        <td class="px-6 py-4">
                            @php
                                $statusClasses = match($quote->status) {
                                    'accepted' => 'bg-green-100 text-green-700',
                                    'pending' => 'bg-orange-100 text-orange-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                    'expired' => 'bg-gray-100 text-gray-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                                $statusLabel = match($quote->status) {
                                    'accepted' => 'Accepté',
                                    'pending' => 'En attente',
                                    'rejected' => 'Refusé',
                                    'expired' => 'Expiré',
                                    default => $quote->status
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusClasses }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('quotes.show', $quote) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Voir">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                @if($quote->status === 'pending')
                                <button class="p-2 text-green-600 hover:bg-green-50 rounded-lg" title="Convertir en facture">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </button>
                                @endif
                                <a href="{{ route('quotes.edit', $quote) }}" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg" title="Modifier">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Aucun devis trouvé. <a href="{{ route('quotes.create') }}" class="text-blue-600 hover:underline">Créer votre premier devis</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
