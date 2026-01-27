<x-app-layout>
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Modifier Devis</h1>
                <p class="text-gray-500">Mettre à jour le devis {{ $quote->quote_number }}</p>
            </div>
            <a href="{{ route('quotes.index') }}" style="width: 24px; height: 24px; display: inline-block; flex-shrink: 0;">
                <svg style="width: 100%; height: 100%; stroke: currentColor; stroke-width: 2; color: #6B7280;" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        </div>

        <form action="{{ route('quotes.update', $quote->id) }}" method="POST" id="quoteForm">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Client and Quote Info -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informations du Devis</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="client_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Client</label>
                            <select name="client_id" id="client_id" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Client comptoir</option>
                                @foreach (\App\Models\Client::all() as $client)
                                    <option value="{{ $client->id }}" {{ $quote->client_id == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="quote_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">N° Devis *</label>
                            <input type="text" name="quote_number" id="quote_number" required value="{{ $quote->quote_number }}" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="expires_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date d'expiration</label>
                        <input type="date" name="expires_at" id="expires_at" value="{{ $quote->expires_at->format('Y-m-d') }}" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="mt-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Statut *</label>
                        <select name="status" id="status" required class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="pending" {{ $quote->status === 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="rejected" {{ $quote->status === 'rejected' ? 'selected' : '' }}>Rejeté</option>
                            <option value="expired" {{ $quote->status === 'expired' ? 'selected' : '' }}>Expiré</option>
                        </select>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            💡 Utilisez le bouton "Convertir en facture" pour créer une facture à partir de ce devis.
                        </p>
                    </div>
                </div>

                <!-- Articles Selection -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Articles</h3>
                        <button type="button" onclick="addQuoteItem()" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                            <svg style="width: 16px; height: 16px; margin-right: 4px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Ajouter
                        </button>
                    </div>

                    <!-- Items List -->
                    <div id="quoteItems" class="space-y-4">
                        @foreach ($quote->items as $item)
                        <div class="quote-item bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                <!-- Article Select -->
                                <div class="md:col-span-5">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Article *</label>
                                    <select name="article_id[]" required class="article-select w-full border-2 border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-600 dark:border-gray-500 dark:text-white" onchange="updateQuoteTotals()">
                                        <option value="">Sélectionner un article</option>
                                        @foreach (\App\Models\Article::where('is_active', true)->orderBy('designation')->get() as $article)
                                            <option value="{{ $article->id }}" data-price="{{ $article->selling_price }}" {{ $item->article_id == $article->id ? 'selected' : '' }}>
                                                {{ $article->reference }} - {{ $article->designation }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Quantity -->
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Quantité *</label>
                                    <input type="number" name="quantity[]" min="1" value="{{ $item->quantity }}" required class="quantity-input w-full border-2 border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-600 dark:border-gray-500 dark:text-white" onchange="updateQuoteTotals()" placeholder="Qté">
                                </div>

                                <!-- Unit Price -->
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Prix unitaire (MAD) *</label>
                                    <input type="number" step="0.01" name="unit_price[]" min="0" value="{{ $item->unit_price }}" required class="unit-price-input w-full border-2 border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-600 dark:border-gray-500 dark:text-white" onchange="updateQuoteTotals()" placeholder="0.00">
                                </div>

                                <!-- Total -->
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Total</label>
                                    <div class="item-total w-full border-2 border-gray-300 rounded-xl px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-600 font-bold text-gray-900 dark:border-gray-500 dark:text-white">{{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }} MAD</div>
                                </div>

                                <!-- Remove Button -->
                                <div class="md:col-span-1">
                                    <button type="button" onclick="removeQuoteItem(this)" class="w-full px-3 py-2.5 text-red-600 hover:bg-red-50 rounded-xl border border-red-200 transition dark:text-red-400 dark:hover:bg-red-900/20" style="display: flex; align-items: center; justify-content: center;">
                                        <svg style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Totals -->
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex justify-end">
                            <div class="w-full md:w-80 space-y-3">
                                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                    <span>Sous-total:</span>
                                    <span id="subtotal">0.00 MAD</span>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                    <span>TVA (20%):</span>
                                    <span id="tax">0.00 MAD</span>
                                </div>
                                <div class="flex justify-between items-center pt-3 border-t-2 border-gray-200 dark:border-gray-600">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">Total:</span>
                                    <span id="grandTotal" class="text-2xl font-bold text-blue-600 dark:text-blue-400">0.00 MAD</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden input for items JSON -->
                    <input type="hidden" name="items_json" id="items_json">
                    <input type="hidden" name="total_amount" id="total_amount">
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3 pt-6">
                    <a href="{{ route('quotes.index') }}" class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition">
                        Enregistrer les modifications
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function addQuoteItem() {
            const container = document.getElementById('quoteItems');
            const firstItem = container.querySelector('.quote-item');
            const newItem = firstItem.cloneNode(true);
            
            // Reset all inputs in the cloned item
            newItem.querySelectorAll('input, select').forEach(el => {
                if (el.type === 'number') {
                    el.value = el.name.includes('quantity') ? '1' : '0';
                } else if (el.tagName === 'SELECT') {
                    el.value = '';
                }
            });
            
            newItem.querySelector('.item-total').textContent = '0.00 MAD';
            container.appendChild(newItem);
        }

        function removeQuoteItem(button) {
            const items = document.querySelectorAll('.quote-item');
            if (items.length > 1) {
                button.closest('.quote-item').remove();
                updateQuoteTotals();
            }
        }

        function updateQuoteTotals() {
            let subtotal = 0;
            
            document.querySelectorAll('.quote-item').forEach(item => {
                const quantity = parseFloat(item.querySelector('.quantity-input').value) || 0;
                const unitPrice = parseFloat(item.querySelector('.unit-price-input').value) || 0;
                const total = quantity * unitPrice;
                
                item.querySelector('.item-total').textContent = total.toFixed(2) + ' MAD';
                subtotal += total;
            });
            
            const tax = subtotal * 0.20;
            const grandTotal = subtotal + tax;
            
            document.getElementById('subtotal').textContent = subtotal.toFixed(2) + ' MAD';
            document.getElementById('tax').textContent = tax.toFixed(2) + ' MAD';
            document.getElementById('grandTotal').textContent = grandTotal.toFixed(2) + ' MAD';
            document.getElementById('total_amount').value = grandTotal.toFixed(2);
            
            // Prepare items JSON
            const items = [];
            document.querySelectorAll('.quote-item').forEach(item => {
                const articleId = item.querySelector('.article-select').value;
                if (articleId) {
                    const quantity = parseFloat(item.querySelector('.quantity-input').value) || 0;
                    const unitPrice = parseFloat(item.querySelector('.unit-price-input').value) || 0;
                    items.push({
                        article_id: articleId,
                        quantity: quantity,
                        unit_price: unitPrice,
                        total_price: quantity * unitPrice
                    });
                }
            });
            document.getElementById('items_json').value = JSON.stringify(items);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateQuoteTotals();
        });
    </script>
</x-app-layout>
