<x-app-layout>
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $client->name }}</h1>
                <p class="text-gray-500 mt-1">Détails du client</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('clients.edit', $client->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </a>
                <a href="{{ route('clients.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Retour
                </a>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="col-span-2 space-y-6">
                <!-- Contact Information -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Informations de Contact</h2>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Email</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $client->email ?? 'Non renseigné' }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Téléphone</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $client->phone ?? 'Non renseigné' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Adresse</label>
                        <p class="text-gray-900 dark:text-gray-100">{{ $client->address ?? 'Non renseignée' }}</p>
                    </div>
                </div>

                <!-- Invoices Section -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Factures</h2>
                        <a href="{{ route('invoices.index') }}" class="text-sm text-blue-600 hover:text-blue-700">Voir tout</a>
                    </div>

                    @php
                        $invoices = $client->invoices()->orderBy('created_at', 'desc')->limit(5)->get();
                    @endphp

                    @if($invoices->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="border-b border-gray-200 dark:border-gray-700">
                                    <tr>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Numéro</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Date</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Montant</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Statut</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach($invoices as $invoice)
                                        <tr>
                                            <td class="py-3 px-4 text-gray-900 dark:text-gray-100">
                                                <a href="{{ route('invoices.show', $invoice->id) }}" class="text-blue-600 hover:underline">
                                                    {{ $invoice->invoice_number }}
                                                </a>
                                            </td>
                                            <td class="py-3 px-4 text-gray-600 dark:text-gray-400">{{ $invoice->created_at->format('d/m/Y') }}</td>
                                            <td class="py-3 px-4 text-gray-900 dark:text-gray-100 font-semibold">{{ $invoice->items->sum('total_price') + ($invoice->items->sum('total_price') * 0.20) }} MAD</td>
                                            <td class="py-3 px-4">
                                                @if($invoice->status === 'draft')
                                                    <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-full text-xs font-medium">Brouillon</span>
                                                @elseif($invoice->status === 'sent')
                                                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 rounded-full text-xs font-medium">Envoyée</span>
                                                @elseif($invoice->status === 'payee')
                                                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-full text-xs font-medium">Payée</span>
                                                @else
                                                    <span class="inline-block px-3 py-1 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-full text-xs font-medium">Annulée</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">Aucune facture pour ce client</p>
                    @endif
                </div>

                <!-- Quotes Section -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Devis</h2>
                        <a href="{{ route('quotes.index') }}" class="text-sm text-blue-600 hover:text-blue-700">Voir tout</a>
                    </div>

                    @php
                        $quotes = $client->quotes()->orderBy('created_at', 'desc')->limit(5)->get();
                    @endphp

                    @if($quotes->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="border-b border-gray-200 dark:border-gray-700">
                                    <tr>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Numéro</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Date</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Montant</th>
                                        <th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Statut</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach($quotes as $quote)
                                        <tr>
                                            <td class="py-3 px-4 text-gray-900 dark:text-gray-100">
                                                <a href="{{ route('quotes.show', $quote->id) }}" class="text-blue-600 hover:underline">
                                                    {{ $quote->quote_number }}
                                                </a>
                                            </td>
                                            <td class="py-3 px-4 text-gray-600 dark:text-gray-400">{{ $quote->created_at->format('d/m/Y') }}</td>
                                            <td class="py-3 px-4 text-gray-900 dark:text-gray-100 font-semibold">{{ $quote->items->sum('total_price') }} MAD</td>
                                            <td class="py-3 px-4">
                                                @if($quote->status === 'draft')
                                                    <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-full text-xs font-medium">Brouillon</span>
                                                @elseif($quote->status === 'sent')
                                                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 rounded-full text-xs font-medium">Envoyée</span>
                                                @elseif($quote->status === 'accepted')
                                                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-full text-xs font-medium">Acceptée</span>
                                                @else
                                                    <span class="inline-block px-3 py-1 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-full text-xs font-medium">Refusée</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">Aucun devis pour ce client</p>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Account Summary -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-6">Résumé</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Solde</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($client->balance, 2) }} MAD</p>
                        </div>

                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Total des factures</p>
                            @php
                                $totalInvoices = $client->invoices()->sum(\DB::raw("(SELECT SUM(total_price) FROM invoice_items WHERE invoice_items.invoice_id = invoices.id)"));
                            @endphp
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($totalInvoices, 2) }} MAD</p>
                        </div>

                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Créé le</p>
                            <p class="text-gray-900 dark:text-gray-100">{{ $client->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Delete Button -->
                <form action="{{ route('clients.destroy', $client->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-3 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition font-semibold text-sm">
                        <svg class="w-4 h-4 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Supprimer le client
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
