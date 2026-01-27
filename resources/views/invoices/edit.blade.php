<x-app-layout>
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Modifier Facture</h1>
                <p class="text-gray-500 dark:text-gray-400">Facture #{{ $invoice->invoice_number }}</p>
            </div>
            <a href="{{ route('invoices.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 px-4 py-3 rounded-lg mb-6">
                <strong>Erreur de validation:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('invoices.update', $invoice->id) }}" method="POST" id="invoiceForm" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Client and Invoice Info -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informations de la Facture</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Client</label>
                        <select name="client_id" id="client_id" class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">Client comptoir</option>
                            @foreach (\App\Models\Client::all() as $client)
                                <option value="{{ $client->id }}" @selected($invoice->client_id == $client->id)>{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="invoice_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">N° Facture *</label>
                        <input type="text" name="invoice_number" id="invoice_number" required value="{{ $invoice->invoice_number }}" class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                </div>

                <div class="mt-4">
                    <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date d'échéance</label>
                    <input type="date" name="due_date" id="due_date" value="{{ $invoice->due_date?->format('Y-m-d') }}" class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>

                <div class="mt-4">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Statut *</label>
                    <select name="status" id="status" required class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="brouillon" @selected($invoice->status == 'brouillon')>Brouillon</option>
                        <option value="envoyee" @selected($invoice->status == 'envoyee')>Envoyée</option>
                        <option value="payee" @selected($invoice->status == 'payee')>Payée</option>
                        <option value="annulee" @selected($invoice->status == 'annulee')>Annulée</option>
                    </select>
                </div>
            </div>

            <!-- Articles Selection -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Articles</h3>
                    <button type="button" onclick="addInvoiceItem()" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Ajouter
                    </button>
                </div>

                <!-- Items Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="text-left px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300">Article</th>
                                <th class="text-center px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300">Quantité</th>
                                <th class="text-right px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300">Prix unitaire</th>
                                <th class="text-right px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300">Total</th>
                                <th class="text-center px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300">Action</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceItems">
                            @foreach ($invoice->items as $item)
                                <tr class="invoice-item border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-3">
                                        <select name="article_id[]" required class="article-select w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white" onchange="updateInvoiceTotals()">
                                            <option value="">Sélectionner un article</option>
                                            @foreach (\App\Models\Article::where('is_active', true)->orderBy('designation')->get() as $article)
                                                <option value="{{ $article->id }}" data-price="{{ $article->selling_price }}" @selected($item->article_id == $article->id)>
                                                    {{ $article->reference }} - {{ $article->designation }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="number" name="quantity[]" min="1" value="{{ $item->quantity }}" required class="quantity-input w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white text-center" onchange="updateInvoiceTotals()" placeholder="1">
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <input type="number" step="0.01" name="unit_price[]" min="0" value="{{ $item->unit_price }}" required class="unit-price-input w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white text-right" onchange="updateInvoiceTotals()" placeholder="0.00">
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="item-total font-semibold text-gray-900 dark:text-white">{{ number_format($item->total_price, 2) }} MAD</div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" onclick="removeInvoiceItem(this)" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-semibold text-sm">
                                            Supprimer
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-end">
                        <div class="w-full md:w-80 space-y-3">
                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                <span>Sous-total:</span>
                                <span id="subtotal" class="font-medium">{{ number_format($invoice->items->sum(fn($item) => $item->total_price), 2) }} MAD</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                <span>TVA (20%):</span>
                                <span id="tax" class="font-medium">{{ number_format($invoice->items->sum(fn($item) => $item->total_price) * 0.20, 2) }} MAD</span>
                            </div>
                            <div class="flex justify-between items-center pt-3 border-t-2 border-gray-200 dark:border-gray-700">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">Total:</span>
                                <span id="grandTotal" class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($invoice->total_amount, 2) }} MAD</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden inputs -->
                <input type="hidden" name="items_json" id="items_json">
                <input type="hidden" name="total_amount" id="total_amount" value="{{ $invoice->total_amount }}">
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('invoices.index') }}" class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    Annuler
                </a>
                <button type="submit" onclick="return validateAndSubmitForm();" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition">
                    Mettre à jour la facture
                </button>
            </div>
        </form>
    </div>

    <script>
        function validateAndSubmitForm() {
            updateInvoiceTotals();
            const itemsJson = document.getElementById('items_json').value;
            const items = JSON.parse(itemsJson || '[]');
            
            if (items.length === 0) {
                alert('Vous devez ajouter au moins un article à la facture.');
                return false;
            }
            return true;
        }

        function addInvoiceItem() {
            const tbody = document.getElementById('invoiceItems');
            const firstRow = tbody.querySelector('.invoice-item');
            const newRow = firstRow.cloneNode(true);
            
            // Reset inputs
            newRow.querySelectorAll('input, select').forEach(el => {
                if (el.type === 'number') {
                    el.value = el.name.includes('quantity') ? '1' : '0';
                } else if (el.tagName === 'SELECT') {
                    el.value = '';
                }
            });
            
            newRow.querySelector('.item-total').textContent = '0.00 MAD';
            tbody.appendChild(newRow);
        }

        function removeInvoiceItem(button) {
            const items = document.querySelectorAll('.invoice-item');
            if (items.length > 1) {
                button.closest('tr').remove();
                updateInvoiceTotals();
            }
        }

        function updateInvoiceTotals() {
            let subtotal = 0;
            
            document.querySelectorAll('.invoice-item').forEach(row => {
                const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const unitPrice = parseFloat(row.querySelector('.unit-price-input').value) || 0;
                const total = quantity * unitPrice;
                
                row.querySelector('.item-total').textContent = total.toFixed(2) + ' MAD';
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
            document.querySelectorAll('.invoice-item').forEach(row => {
                const articleId = row.querySelector('.article-select').value;
                if (articleId) {
                    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                    const unitPrice = parseFloat(row.querySelector('.unit-price-input').value) || 0;
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
            updateInvoiceTotals();
        });
    </script>
</x-app-layout>
