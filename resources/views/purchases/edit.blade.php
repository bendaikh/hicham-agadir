<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Modifier l'Achat</h1>
                <p class="text-gray-500">Facture #ACH-{{ str_pad($purchase->id, 4, '0', STR_PAD_LEFT) }}</p>
            </div>
            <a href="{{ route('purchases.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        </div>

        <form action="{{ route('purchases.update', $purchase) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Supplier and Date -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informations générales</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fournisseur *</label>
                        <select name="supplier_id" id="supplier_id" required class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Sélectionner un fournisseur</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected($purchase->supplier_id == $supplier->id)>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="purchase_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date d'achat *</label>
                        <input type="date" name="purchase_date" id="purchase_date" required value="{{ $purchase->purchase_date->format('Y-m-d') }}" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>

                <div class="mt-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes / Description</label>
                    <textarea name="description" id="description" rows="2" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Notes additionnelles sur l'achat...">{{ $purchase->description }}</textarea>
                </div>

                <div class="mt-4">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Statut *</label>
                    <select name="status" id="status" required class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="pending" @selected($purchase->status === 'pending')>En attente</option>
                        <option value="completed" @selected($purchase->status === 'completed')>Complété</option>
                        <option value="cancelled" @selected($purchase->status === 'cancelled')>Annulé</option>
                    </select>
                </div>
            </div>

            <!-- Articles Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Articles achetés</h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700/50 text-xs font-bold text-gray-500 uppercase">
                                <th class="px-4 py-3 text-left">Article</th>
                                <th class="px-4 py-3 text-center">Quantité</th>
                                <th class="px-4 py-3 text-right">Prix unitaire</th>
                                <th class="px-4 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($purchase->items as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-4 py-3 text-sm">
                                        <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $item->article->reference }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->article->designation }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">{{ number_format($item->unit_price, 2, ',', '.') }} MAD</td>
                                    <td class="px-4 py-3 text-right text-sm font-bold text-blue-600">{{ number_format($item->total_price, 2, ',', '.') }} MAD</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500 text-sm">Aucun article ajouté à cet achat</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Totals -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <div class="text-sm text-gray-500">Nombre d'articles: <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $purchase->items->count() }}</span></div>
                        <div class="text-sm text-gray-500 mt-1">Quantité totale: <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $purchase->items->sum('quantity') }}</span></div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Montant Total</div>
                        <div class="text-3xl font-bold text-blue-600">{{ number_format($purchase->total_amount, 2, ',', '.') }} MAD</div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                    <a href="{{ route('purchases.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition">
                        Mettre à jour
                    </button>
                </div>
            </div>
        </form>

        <!-- Delete Button -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet achat?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-3 bg-red-100 text-red-700 rounded-xl font-semibold text-sm hover:bg-red-200 transition">
                    <svg class="w-4 h-4 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Supprimer cet achat
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
