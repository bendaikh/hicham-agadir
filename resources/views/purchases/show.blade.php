<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Achat #ACH-{{ str_pad($purchase->id, 4, '0', STR_PAD_LEFT) }}</h1>
                <p class="text-gray-500">{{ $purchase->supplier->name ?? 'N/A' }} - {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('purchases.edit', $purchase) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-semibold text-xs hover:bg-gray-200 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </a>
                <a href="{{ route('purchases.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Purchase Info -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <div class="text-sm text-gray-500 mb-1">Fournisseur</div>
                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $purchase->supplier->name ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500 mb-1">Date d'achat</div>
                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500 mb-1">Statut</div>
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
                </div>
                <div>
                    <div class="text-sm text-gray-500 mb-1">Montant Total</div>
                    <div class="font-bold text-xl text-blue-600">{{ number_format($purchase->total_amount, 2, ',', '.') }} MAD</div>
                </div>
            </div>

            @if($purchase->description)
                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <div class="text-sm text-gray-500 mb-1">Notes</div>
                    <div class="text-gray-700 dark:text-gray-300">{{ $purchase->description }}</div>
                </div>
            @endif
        </div>

        <!-- Purchase Items -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Articles ({{ $purchase->items->count() }})</h3>
            </div>
            
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-gray-700/50 text-xs font-bold text-gray-500 uppercase">
                        <th class="px-6 py-4 text-left">Article</th>
                        <th class="px-6 py-4 text-center">Quantité</th>
                        <th class="px-6 py-4 text-right">Prix unitaire</th>
                        <th class="px-6 py-4 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($purchase->items as $item)
                        <tr class="text-sm">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $item->article->reference ?? 'N/A' }} - {{ $item->article->designation ?? 'Article supprimé' }}
                                </div>
                                @if($item->article)
                                    <div class="text-xs text-gray-500">
                                        @if($item->article->type) {{ $item->article->type }} @endif
                                        @if($item->article->color) - {{ $item->article->color }} @endif
                                        @if($item->article->thickness) - {{ $item->article->thickness }} @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center font-semibold">
                                {{ $item->quantity }}
                                <span class="text-gray-400 text-xs font-normal">{{ $item->article->unit ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4 text-right text-gray-600">{{ number_format($item->unit_price, 2, ',', '.') }} MAD</td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-gray-100">{{ number_format($item->total_price, 2, ',', '.') }} MAD</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                Aucun article dans cet achat
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-blue-50 dark:bg-blue-900/20">
                        <td colspan="3" class="px-6 py-4 text-right font-semibold text-gray-700 dark:text-gray-300">Total</td>
                        <td class="px-6 py-4 text-right font-bold text-xl text-blue-600">{{ number_format($purchase->total_amount, 2, ',', '.') }} MAD</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('purchases.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                Retour à la liste
            </a>
        </div>
    </div>
</x-app-layout>
