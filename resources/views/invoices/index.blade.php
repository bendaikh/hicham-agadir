<x-app-layout>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Gestion des Factures</h1>
            <p class="text-gray-500">Créez et gérez vos factures professionnelles</p>
        </div>
        <a href="{{ route('invoices.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nouvelle Facture
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">Total Facturé</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format(\App\Models\Invoice::sum('total_amount'), 2, ',', '.') }} MAD</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">Factures Payées</div>
            <div class="text-2xl font-bold text-green-600">{{ \App\Models\Invoice::where('status', 'payee')->count() }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">Envoyées</div>
            <div class="text-2xl font-bold text-blue-600">{{ \App\Models\Invoice::where('status', 'envoyee')->count() }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">Brouillons</div>
            <div class="text-2xl font-bold text-gray-600">{{ \App\Models\Invoice::where('status', 'brouillon')->count() }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">Annulées</div>
            <div class="text-2xl font-bold text-red-600">{{ \App\Models\Invoice::where('status', 'annulee')->count() }}</div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <input type="text" placeholder="Rechercher une facture..." class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <select class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tous les statuts</option>
                    <option value="payee">Payée</option>
                    <option value="envoyee">Envoyée</option>
                    <option value="brouillon">Brouillon</option>
                    <option value="annulee">Annulée</option>
                </select>
            </div>
        </div>
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-gray-700/50 text-xs font-bold text-gray-500 uppercase">
                    <th class="px-6 py-4 text-left">ID Facture</th>
                    <th class="px-6 py-4 text-left">Nom du Client</th>
                    <th class="px-6 py-4 text-left">Date</th>
                    <th class="px-6 py-4 text-left">Montant</th>
                    <th class="px-6 py-4 text-left">Statut</th>
                    <th class="px-6 py-4 text-left">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse (\App\Models\Invoice::with('client')->latest()->get() as $invoice)
                    <tr class="text-sm hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-4 font-bold text-blue-600">#{{ $invoice->invoice_number }}</td>
                        <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $invoice->client->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $invoice->created_at->format('d M, Y') }}</td>
                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-gray-100">{{ number_format($invoice->total_amount, 2, ',', '.') }} MAD</td>
                        <td class="px-6 py-4">
                            @php
                                $statusClasses = match($invoice->status) {
                                    'payee' => 'bg-green-100 text-green-700',
                                    'envoyee' => 'bg-blue-100 text-blue-700',
                                    'brouillon' => 'bg-gray-100 text-gray-700',
                                    'annulee' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                                $statusLabel = match($invoice->status) {
                                    'payee' => 'Payée',
                                    'envoyee' => 'Envoyée',
                                    'brouillon' => 'Brouillon',
                                    'annulee' => 'Annulée',
                                    default => $invoice->status
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusClasses }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('invoices.show', $invoice) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Voir">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('invoices.edit', $invoice) }}" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg" title="Modifier">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <button class="p-2 text-gray-400 hover:text-gray-600" title="Plus">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Aucune facture trouvée. <a href="{{ route('invoices.create') }}" class="text-blue-600 hover:underline">Créer votre première facture</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
