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

        <!-- Payment Information -->
        @php
            $totalPaid = $purchase->payments()->sum('amount');
            $remainingAmount = max(0, $purchase->total_amount - $totalPaid);
            $paymentPercentage = $purchase->total_amount > 0 ? ($totalPaid / $purchase->total_amount) * 100 : 0;
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Suivi des Paiements</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <div class="text-sm text-gray-500 mb-1">Montant Total</div>
                    <div class="font-bold text-xl text-gray-900 dark:text-gray-100">{{ number_format($purchase->total_amount, 2, ',', '.') }} MAD</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500 mb-1">Montant Payé</div>
                    <div class="font-bold text-xl text-green-600">{{ number_format($totalPaid, 2, ',', '.') }} MAD</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500 mb-1">Solde Restant</div>
                    <div class="font-bold text-xl {{ $remainingAmount > 0 ? 'text-orange-600' : 'text-green-600' }}">
                        {{ number_format($remainingAmount, 2, ',', '.') }} MAD
                    </div>
                </div>
            </div>

            <!-- Payment Progress Bar -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progression du paiement</span>
                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ number_format($paymentPercentage, 1, ',', '.') }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                    <div class="bg-green-500 h-3 rounded-full transition-all duration-300" style="width: {{ min($paymentPercentage, 100) }}%"></div>
                </div>
            </div>

            <!-- Payment Records -->
            @if($purchase->payments()->exists())
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">Transactions</h4>
                    <div class="space-y-2">
                        @foreach($purchase->payments()->latest()->get() as $payment)
                            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">
                                        @php
                                            $methodLabel = match($payment->payment_method) {
                                                'cash' => 'Espèces',
                                                'card' => 'Carte bancaire',
                                                'cheque' => 'Chèque',
                                                'bank_transfer' => 'Virement',
                                                'mobile_payment' => 'Paiement mobile',
                                                default => $payment->payment_method
                                            };
                                        @endphp
                                        {{ $methodLabel }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</div>
                                </div>
                                <div class="font-bold text-green-600">+{{ number_format($payment->amount, 2, ',', '.') }} MAD</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Add Payment Form -->
            @if($remainingAmount > 0)
                <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">Enregistrer un Paiement</h4>
                    <form action="{{ route('purchases.record-payment', $purchase) }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Montant à payer *</label>
                                <input type="number" name="amount" id="amount" step="0.01" min="0.01" :max="{{ $remainingAmount }}" placeholder="0.00" required
                                       class="w-full border-2 border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-gray-900">
                                <div class="text-xs text-gray-500 mt-1">Max: {{ number_format($remainingAmount, 2, ',', '.') }} MAD</div>
                            </div>
                            
                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Méthode de paiement *</label>
                                <select name="payment_method" id="payment_method" required
                                        class="w-full border-2 border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-gray-900">
                                    <option value="">Sélectionner une méthode</option>
                                    <option value="cash">Espèces</option>
                                    <option value="card">Carte bancaire</option>
                                    <option value="cheque">Chèque</option>
                                    <option value="bank_transfer">Virement</option>
                                    <option value="mobile_payment">Paiement mobile</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="payment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date du paiement *</label>
                                <input type="date" name="payment_date" id="payment_date" value="{{ date('Y-m-d') }}" required
                                       class="w-full border-2 border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-gray-900">
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2.5 bg-green-600 text-white rounded-xl font-semibold text-sm hover:bg-green-700 transition">
                                Enregistrer le Paiement
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
                    <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-green-700 dark:text-green-400">
                        <svg class="w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">Achat complètement payé!</span>
                    </div>
                </div>
            @endif
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('purchases.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                Retour à la liste
            </a>
        </div>
    </div>
</x-app-layout>
