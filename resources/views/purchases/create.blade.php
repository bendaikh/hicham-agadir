<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Nouvel Achat</h1>
                <p class="text-gray-500">Enregistrer un achat de matières premières avec mise à jour du stock</p>
            </div>
            <a href="{{ route('purchases.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        </div>

        <div x-data="purchaseForm()" class="space-y-6">
            <form action="{{ route('purchases.store') }}" method="POST" @submit="prepareSubmit">
                @csrf
                
                <!-- Supplier and Date -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Informations générales</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fournisseur *</label>
                            <select name="supplier_id" id="supplier_id" required class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Sélectionner un fournisseur</option>
                                @foreach (\App\Models\Supplier::orderBy('name')->get() as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="purchase_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date d'achat *</label>
                            <input type="date" name="purchase_date" id="purchase_date" required value="{{ date('Y-m-d') }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes / Description</label>
                        <textarea name="description" id="description" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Notes additionnelles sur l'achat..."></textarea>
                    </div>
                </div>

                <!-- Articles Selection -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Articles à acheter</h3>
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
                                        <select x-model="item.article_id" @change="updateArticleInfo(index)" required class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                            <option value="">Sélectionner un article</option>
                                            @foreach (\App\Models\Article::where('is_active', true)->orderBy('designation')->get() as $article)
                                                <option value="{{ $article->id }}" 
                                                        data-designation="{{ $article->full_designation }}"
                                                        data-unit="{{ $article->unit }}"
                                                        data-price="{{ $article->selling_price }}">
                                                    {{ $article->reference }} - {{ $article->designation }} 
                                                    @if($article->thickness) ({{ $article->thickness }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Quantity -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Quantité *</label>
                                        <input type="number" x-model.number="item.quantity" @input="calculateItemTotal(index)" min="1" required 
                                               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" 
                                               placeholder="Qté">
                                    </div>

                                    <!-- Unit Price -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Prix d'achat (MAD) *</label>
                                        <input type="number" step="0.01" x-model.number="item.unit_price" @input="calculateItemTotal(index)" min="0" required 
                                               class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" 
                                               placeholder="0.00">
                                    </div>

                                    <!-- Total -->
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-500 mb-1">Total</label>
                                        <div class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-100 font-bold text-gray-900" x-text="formatPrice(item.total)"></div>
                                    </div>

                                    <!-- Remove Button -->
                                    <div class="md:col-span-1">
                                        <button type="button" @click="removeItem(index)" class="w-full p-2.5 text-red-500 hover:bg-red-50 rounded-xl transition" :disabled="items.length === 1">
                                            <svg class="w-5 h-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Article Info -->
                                <div x-show="item.article_info" class="mt-3 text-xs text-gray-500 bg-white rounded-lg px-3 py-2 border border-gray-100">
                                    <span x-text="item.article_info"></span>
                                    <span class="mx-2">|</span>
                                    <span>Unité: <strong x-text="item.unit"></strong></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Empty State -->
                    <div x-show="items.length === 0" class="text-center py-8 text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <p>Aucun article ajouté</p>
                        <button type="button" @click="addItem" class="mt-2 text-blue-600 hover:underline text-sm">Ajouter un article</button>
                    </div>
                </div>

                <!-- Totals -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-sm text-gray-500">Nombre d'articles: <span class="font-semibold text-gray-900" x-text="items.length"></span></div>
                            <div class="text-sm text-gray-500 mt-1">Quantité totale: <span class="font-semibold text-gray-900" x-text="totalQuantity"></span></div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">Montant Total HT</div>
                            <div class="text-3xl font-bold text-blue-600" x-text="formatPrice(grandTotal)"></div>
                        </div>
                    </div>

                    <!-- Hidden input for total -->
                    <input type="hidden" name="total_amount" x-model="grandTotal">
                    <input type="hidden" name="items_json" x-model="itemsJson">

                    <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-100">
                        <a href="{{ route('purchases.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                            Annuler
                        </a>
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition" :disabled="items.length === 0 || !isValid">
                            Enregistrer l'achat
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function purchaseForm() {
            return {
                items: [
                    { article_id: '', quantity: 1, unit_price: 0, total: 0, article_info: '', unit: '' }
                ],
                
                get grandTotal() {
                    return this.items.reduce((sum, item) => sum + (item.total || 0), 0);
                },

                get totalQuantity() {
                    return this.items.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0);
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
                        alert('Veuillez remplir tous les champs obligatoires');
                        return false;
                    }
                    return true;
                }
            }
        }
    </script>
</x-app-layout>
