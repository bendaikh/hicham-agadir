<x-app-layout>
    <div x-data="quoteForm()" class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Nouveau Devis</h1>
                <p class="text-gray-500">Créer un devis pour un client</p>
            </div>
            <a href="{{ route('quotes.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        </div>

        <form action="{{ route('quotes.store') }}" method="POST" @submit="prepareSubmit">
            @csrf
            
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
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="quote_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">N° Devis *</label>
                            <input type="text" name="quote_number" id="quote_number" required value="DEV-{{ str_pad(\App\Models\Quote::count() + 1, 6, '0', STR_PAD_LEFT) }}" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="expires_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date d'expiration</label>
                        <input type="date" name="expires_at" id="expires_at" value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div class="mt-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Statut *</label>
                        <select name="status" id="status" required class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="pending">En attente</option>
                            <option value="rejected">Rejeté</option>
                            <option value="expired">Expiré</option>
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
                        <button type="button" @click="addItem" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Ajouter
                        </button>
                    </div>

                    <!-- Items List -->
                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600">
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                    <!-- Article Select -->
                                    <div class="md:col-span-5">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Article *</label>
                                        <select x-model="item.article_id" @change="updateArticleInfo(index)" required class="w-full border-2 border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                            <option value="">Sélectionner un article</option>
                                            @foreach (\App\Models\Article::where('is_active', true)->orderBy('designation')->get() as $article)
                                                <option value="{{ $article->id }}" 
                                                        data-designation="{{ $article->full_designation }}"
                                                        data-unit="{{ $article->unit }}"
                                                        data-price="{{ $article->selling_price }}"
                                                        data-stock="{{ $article->stock_quantity }}">
                                                    {{ $article->reference }} - {{ $article->designation }} 
                                                    @if($article->thickness) ({{ $article->thickness }}) @endif
                                                    [Stock: {{ $article->stock_quantity }}]
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Quantity -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Quantité *</label>
                                        <input type="number" x-model.number="item.quantity" @input="calculateItemTotal(index)" min="1" required 
                                               class="w-full border-2 border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-600 dark:border-gray-500 dark:text-white" 
                                               placeholder="Qté">
                                    </div>

                                    <!-- Unit Price -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Prix unitaire (MAD) *</label>
                                        <input type="number" step="0.01" x-model.number="item.unit_price" @input="calculateItemTotal(index)" min="0" required 
                                               class="w-full border-2 border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-600 dark:border-gray-500 dark:text-white" 
                                               placeholder="0.00">
                                    </div>

                                    <!-- Total -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Total</label>
                                        <div class="w-full border-2 border-gray-300 rounded-xl px-3 py-2.5 text-sm bg-gray-50 font-bold text-gray-900 dark:bg-gray-600 dark:border-gray-500 dark:text-white" x-text="formatPrice(item.total)"></div>
                                    </div>

                                    <!-- Remove Button -->
                                    <div class="md:col-span-1">
                                        <button type="button" @click="removeItem(index)" class="w-full px-3 py-2.5 text-red-600 hover:bg-red-50 rounded-xl border border-red-200 transition">
                                            <svg class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Totals -->
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex justify-end">
                            <div class="w-full md:w-80 space-y-3">
                                <div class="flex justify-between text-sm text-gray-700 dark:text-gray-300">
                                    <span>Sous-total:</span>
                                    <span x-text="formatPrice(subtotal)"></span>
                                </div>
                                <div class="flex justify-between text-sm text-gray-700 dark:text-gray-300">
                                    <span>TVA (20%):</span>
                                    <span x-text="formatPrice(tax)"></span>
                                </div>
                                <div class="flex justify-between items-center pt-3 border-t-2 border-gray-300 dark:border-gray-600">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">Total:</span>
                                    <span class="text-2xl font-bold text-blue-600 dark:text-blue-400" x-text="formatPrice(grandTotal)"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden input for items JSON -->
                    <input type="hidden" name="items_json" :value="itemsJson">
                    <input type="hidden" name="total_amount" :value="grandTotal">
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3 pt-6">
                    <a href="{{ route('quotes.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition" :disabled="items.length === 0 || !isValid">
                        Créer le devis
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function quoteForm() {
            return {
                items: [
                    { article_id: '', quantity: 1, unit_price: 0, total: 0, article_info: '', unit: '' }
                ],
                
                get subtotal() {
                    return this.items.reduce((sum, item) => sum + (item.total || 0), 0);
                },
                
                get tax() {
                    return this.subtotal * 0.20;
                },
                
                get grandTotal() {
                    return this.subtotal + this.tax;
                },

                get isValid() {
                    return this.items.every(item => item.article_id && item.quantity > 0 && item.unit_price >= 0);
                },

                get itemsJson() {
                    return JSON.stringify(this.items.map(item => ({
                        article_id: item.article_id,
                        quantity: item.quantity,
                        unit_price: item.unit_price,
                        total_price: item.total
                    })));
                },

                addItem() {
                    this.items.push({ article_id: '', quantity: 1, unit_price: 0, total: 0, article_info: '', unit: '' });
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },

                updateArticleInfo(index) {
                    const select = document.querySelectorAll('select[x-model="item.article_id"]')[index];
                    const option = select.options[select.selectedIndex];
                    if (option && option.value) {
                        this.items[index].article_info = option.dataset.designation || '';
                        this.items[index].unit = option.dataset.unit || '';
                        if (!this.items[index].unit_price || this.items[index].unit_price === 0) {
                            this.items[index].unit_price = parseFloat(option.dataset.price) || 0;
                        }
                    } else {
                        this.items[index].article_info = '';
                        this.items[index].unit = '';
                    }
                    this.calculateItemTotal(index);
                },

                calculateItemTotal(index) {
                    const item = this.items[index];
                    item.total = (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0);
                },

                formatPrice(value) {
                    return new Intl.NumberFormat('fr-MA', { 
                        minimumFractionDigits: 2, 
                        maximumFractionDigits: 2 
                    }).format(value || 0) + ' MAD';
                },

                prepareSubmit(e) {
                    if (!this.isValid || this.items.length === 0) {
                        e.preventDefault();
                        alert('Veuillez remplir tous les champs obligatoires et ajouter au moins un article');
                        return false;
                    }
                    return true;
                }
            }
        }
    </script>
</x-app-layout>
