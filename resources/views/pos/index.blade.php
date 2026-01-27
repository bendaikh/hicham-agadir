<x-app-layout>
    @php
        $articles = \App\Models\Article::where('is_active', true)->where('stock_quantity', '>', 0)->get();
        // Get categories from settings, fallback to article categories if settings not available
        $categories = \App\Models\Setting::getArticleCategories();
        // Also get categories from existing articles to show all
        $articleCategories = \App\Models\Article::where('is_active', true)->distinct()->pluck('category')->filter()->toArray();
        $categories = array_unique(array_merge($categories, $articleCategories));
    @endphp
    
    <div x-data="posCart()" class="flex flex-col lg:flex-row gap-6 min-h-[calc(100vh-200px)]">
        <!-- Left: Product Selection -->
        <div class="flex-1 flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100">Terminal POS</h1>
                    <p class="text-sm text-gray-500">Vente rapide de produits - {{ $articles->count() }} articles disponibles</p>
                </div>
                
                <!-- Mobile Cart Button -->
                <button @click="cartOpen = true" class="lg:hidden relative flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl font-semibold text-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Panier</span>
                    <span x-show="cartItems.length > 0" class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center" x-text="cartItems.length"></span>
                </button>
            </div>

            <!-- Categories -->
            <div class="flex gap-2 mb-4 sm:mb-6 overflow-x-auto pb-2 -mx-4 px-4 sm:mx-0 sm:px-0">
                <button @click="selectedCategory = 'all'" :class="selectedCategory === 'all' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-2 border-gray-300 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-400'" class="px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold whitespace-nowrap transition">Tous</button>
                @foreach ($categories as $category)
                    <button @click="selectedCategory = '{{ $category }}'" :class="selectedCategory === '{{ $category }}' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-2 border-gray-300 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-400'" class="px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold whitespace-nowrap transition">{{ $category }}</button>
                @endforeach
            </div>

            <!-- Search -->
            <div class="relative mb-4 sm:mb-6">
                <input type="text" id="searchInput" placeholder="Rechercher un produit..." class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Products Grid -->
            <div class="flex-1 overflow-y-auto -mx-4 px-4 sm:mx-0 sm:px-0">
                @if($articles->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
                        @foreach ($articles as $article)
                            <div x-show="selectedCategory === 'all' || selectedCategory === '{{ $article->category }}'" 
                                 @click="addToCart({{ $article->id }}, '{{ addslashes($article->designation) }}', {{ $article->selling_price }}, '{{ $article->unit }}', '{{ $article->image_url ?? '' }}', {{ $article->stock_quantity }})" 
                                 class="product-card bg-white dark:bg-gray-800 p-3 sm:p-4 rounded-lg border-2 border-gray-200 dark:border-gray-700 hover:border-blue-500 hover:shadow-md dark:hover:border-blue-500 dark:hover:bg-gray-700 cursor-pointer transition-all duration-200 group active:scale-95" 
                                 data-name="{{ strtolower($article->designation . ' ' . $article->reference . ' ' . $article->type . ' ' . $article->color) }}">
                                <div class="h-16 sm:h-24 bg-gradient-to-br from-slate-100 to-slate-50 dark:from-gray-700 dark:to-gray-600 rounded-xl mb-2 sm:mb-3 flex items-center justify-center relative overflow-hidden">
                                    @if($article->image)
                                        <img src="{{ $article->image_url }}" alt="{{ $article->designation }}" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-8 sm:w-10 h-8 sm:h-10 text-slate-300 group-hover:text-blue-400 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    @endif
                                    @if($article->isLowStock())
                                        <span class="absolute top-1 right-1 px-1.5 py-0.5 bg-orange-500 text-white text-[10px] rounded font-bold">Stock bas</span>
                                    @endif
                                </div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-xs sm:text-sm mb-1 truncate" title="{{ $article->full_designation }}">
                                    {{ $article->designation }}
                                </h3>
                                <div class="text-[10px] sm:text-xs text-gray-500 mb-1 truncate">
                                    {{ $article->reference }}
                                    @if($article->thickness) - {{ $article->thickness }} @endif
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-blue-600 font-bold text-sm sm:text-base">{{ number_format($article->selling_price, 2, ',', '.') }}</span>
                                    <span class="text-[10px] sm:text-xs text-gray-400">MAD/{{ $article->unit }}</span>
                                </div>
                                <div class="mt-1 sm:mt-2 flex items-center justify-between">
                                    <span class="text-[10px] sm:text-xs text-gray-400">Stock: {{ $article->stock_quantity }}</span>
                                    @if($article->surface_area)
                                        <span class="text-[10px] sm:text-xs text-gray-400">{{ number_format($article->surface_area, 2, ',', '.') }} M²</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Aucun article en stock</h3>
                        <p class="text-gray-500 mb-4">Commencez par créer des articles et effectuer des achats pour alimenter le stock.</p>
                        <div class="flex items-center justify-center gap-3">
                            <a href="{{ route('articles.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition">
                                Créer un article
                            </a>
                            <a href="{{ route('purchases.create') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                                Effectuer un achat
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Desktop Cart -->
        <div class="hidden lg:flex w-80 xl:w-96 bg-white dark:bg-gray-800 rounded-lg border-2 border-gray-200 dark:border-gray-700 flex-col shadow-lg">
            <div class="p-4 border-b-2 border-gray-200 dark:border-gray-700 bg-gradient-to-r from-slate-50 to-white dark:from-gray-700 dark:to-gray-800 rounded-t-lg">
                <div class="flex items-center justify-between">
                    <h2 class="font-bold text-gray-900 dark:text-gray-100">Panier <span x-show="cartItems.length > 0" class="text-sm font-normal text-gray-500" x-text="'(' + cartItems.length + ')'"></span></h2>
                    <button @click="clearCart()" x-show="cartItems.length > 0" class="text-red-500 text-sm font-semibold hover:underline">Vider</button>
                </div>
            </div>

            <!-- Client Selection -->
            <div class="p-4 border-b-2 border-gray-200 dark:border-gray-700">
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Client</label>
                <select x-model="selectedClient" class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Client comptoir</option>
                    @foreach (\App\Models\Client::all() as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-4">
                <div x-show="cartItems.length === 0" class="text-center py-12 text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-sm font-medium">Panier vide</p>
                    <p class="text-xs mt-1">Cliquez sur un produit pour l'ajouter</p>
                </div>
                
                <template x-for="(item, index) in cartItems" :key="'cart-' + item.id + '-' + index">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 mb-3 border-2 border-gray-200 dark:border-gray-600">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-600 flex-shrink-0">
                                <img x-show="item.image" :src="item.image" :alt="item.name" class="w-full h-full object-cover">
                                <div x-show="!item.image" class="w-full h-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-sm text-gray-900 dark:text-gray-100 truncate" x-text="item.name"></h4>
                                <p class="text-xs text-gray-500" x-text="formatPrice(item.price) + ' / ' + item.unit"></p>
                                <div class="flex items-center gap-2 mt-2">
                                    <button @click="decreaseQuantity(index)" class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold">-</button>
                                    <input type="number" x-model.number="item.quantity" @input="updateQuantity(index)" min="1" :max="item.stock" class="w-12 text-center text-sm font-semibold border-0 bg-transparent focus:outline-none">
                                    <button @click="increaseQuantity(index)" class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold">+</button>
                                    <button @click="removeItem(index)" class="ml-auto text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-1" x-text="'Stock: ' + item.stock"></p>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-t-2 border-gray-200 dark:border-gray-600 flex justify-between items-center">
                            <span class="text-xs text-gray-600 dark:text-gray-300">Sous-total</span>
                            <span class="font-bold text-blue-600 dark:text-blue-400" x-text="formatPrice(item.price * item.quantity)"></span>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Totals -->
            <div class="p-4 border-t-2 border-gray-200 dark:border-gray-700 space-y-3 bg-slate-50 dark:bg-gray-700">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-300">Sous-total</span>
                    <span class="font-semibold text-gray-900 dark:text-white" x-text="formatPrice(subtotal)"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-300">TVA (20%)</span>
                    <span class="font-semibold text-gray-900 dark:text-white" x-text="formatPrice(tax)"></span>
                </div>
                <div class="flex justify-between text-lg font-bold border-t-2 border-gray-300 dark:border-gray-600 pt-3 text-gray-900 dark:text-white">
                    <span>Total</span>
                    <span class="text-blue-600 dark:text-blue-400" x-text="formatPrice(total)"></span>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="p-4 border-t-2 border-gray-200 dark:border-gray-700" x-show="cartItems.length > 0">
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wide">Méthodes de Paiement</label>
                <div class="space-y-2">
                    <template x-for="(payment, index) in payments" :key="index">
                        <div class="flex items-center gap-2">
                            <select x-model="payment.method" class="flex-1 border-2 border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="cash">Espèces</option>
                                <option value="card">Carte bancaire</option>
                                <option value="cheque">Chèque</option>
                                <option value="bank_transfer">Virement</option>
                                <option value="mobile_payment">Paiement mobile</option>
                            </select>
                            <input type="number" x-model.number="payment.amount" @input="updatePaymentsTotal()" step="0.01" min="0" :max="total - paymentsTotal + payment.amount" placeholder="0.00" class="w-28 border-2 border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-semibold">
                            <button @click="removePayment(index)" x-show="payments.length > 1" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </template>
                    <button @click="addPayment()" class="w-full px-3 py-2 text-xs text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-gray-700 rounded-lg border-2 border-blue-300 dark:border-blue-500 font-bold transition">
                        + Ajouter une méthode
                    </button>
                    <div class="flex justify-between items-center pt-2 border-t-2 border-gray-300 dark:border-gray-600">
                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Total payé:</span>
                        <span class="text-sm font-bold" :class="paymentsTotal >= total ? 'text-green-600 dark:text-green-400' : 'text-orange-600 dark:text-orange-400'" x-text="formatPrice(paymentsTotal)"></span>
                    </div>
                    <div x-show="paymentsTotal < total" class="text-xs text-orange-600 dark:text-orange-400 font-bold">
                        Reste à payer: <span x-text="formatPrice(total - paymentsTotal)" class="font-bold"></span>
                    </div>
                    <div x-show="paymentsTotal > total" class="text-xs text-green-600 dark:text-green-400 font-bold">
                        Monnaie: <span x-text="formatPrice(paymentsTotal - total)" class="font-bold"></span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="p-4 border-t-2 border-gray-200 dark:border-gray-700 grid grid-cols-2 gap-3">
                <button @click="savePending()" :disabled="cartItems.length === 0 || processing" class="px-4 py-3 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white rounded-lg font-bold text-sm hover:bg-gray-300 dark:hover:bg-gray-500 transition active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!processing">En attente</span>
                    <span x-show="processing" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Enregistrement...
                    </span>
                </button>
                <button @click="checkout()" :disabled="cartItems.length === 0 || processing" class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold text-sm shadow-lg shadow-blue-600/40 transition active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!processing">Encaisser</span>
                    <span x-show="processing" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Traitement...
                    </span>
                </button>
            </div>
        </div>

        <!-- Mobile Cart Drawer -->
        <div x-show="cartOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="lg:hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50"
             @click="cartOpen = false">
        </div>
        
        <div x-show="cartOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="lg:hidden fixed bottom-0 inset-x-0 bg-white dark:bg-gray-800 rounded-t-2xl z-50 max-h-[85vh] flex flex-col border-t-2 border-gray-200 dark:border-gray-700"
             @click.stop>
            
            <!-- Handle -->
            <div class="flex justify-center py-3">
                <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
            </div>
            
            <div class="p-4 border-b-2 border-gray-200 dark:border-gray-700 flex items-center justify-between bg-gradient-to-r from-slate-50 to-white dark:from-gray-700 dark:to-gray-800">
                <h2 class="font-bold text-gray-900 dark:text-white text-lg">Panier</h2>
                <button @click="cartOpen = false" class="p-2 rounded-xl hover:bg-gray-100">
                    <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Client Selection -->
            <div class="p-4 border-b-2 border-gray-200 dark:border-gray-700">
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Client</label>
                <select x-model="selectedClient" class="w-full border-2 border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Client comptoir</option>
                    @foreach (\App\Models\Client::all() as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-4">
                <div x-show="cartItems.length === 0" class="text-center py-8 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-sm">Panier vide</p>
                </div>
                
                <template x-for="(item, index) in cartItems" :key="'cart-mobile-' + item.id + '-' + index">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 mb-3 border-2 border-gray-200 dark:border-gray-600">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-200 dark:bg-gray-600 flex-shrink-0">
                                <img x-show="item.image" :src="item.image" :alt="item.name" class="w-full h-full object-cover">
                                <div x-show="!item.image" class="w-full h-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-sm text-gray-900 truncate" x-text="item.name"></h4>
                                <p class="text-xs text-gray-500" x-text="formatPrice(item.price) + ' / ' + item.unit"></p>
                                <div class="flex items-center gap-2 mt-2">
                                    <button @click="decreaseQuantity(index)" class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold">-</button>
                                    <input type="number" x-model.number="item.quantity" @input="updateQuantity(index)" min="1" :max="item.stock" class="w-12 text-center text-sm font-semibold border-0 bg-transparent focus:outline-none">
                                    <button @click="increaseQuantity(index)" class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 font-bold">+</button>
                                    <button @click="removeItem(index)" class="ml-auto text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1" x-text="'Stock: ' + item.stock"></p>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-t-2 border-gray-200 dark:border-gray-600 flex justify-between items-center">
                            <span class="text-xs text-gray-600 dark:text-gray-400">Sous-total</span>
                            <span class="font-bold text-blue-600 dark:text-blue-400" x-text="formatPrice(item.price * item.quantity)"></span>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Totals -->
            <div class="p-4 border-t-2 border-gray-200 dark:border-gray-700 space-y-2 bg-slate-50 dark:bg-gray-700">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-300">Sous-total</span>
                    <span class="font-semibold text-gray-900 dark:text-white" x-text="formatPrice(subtotal)"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-300">TVA (20%)</span>
                    <span class="font-semibold text-gray-900 dark:text-white" x-text="formatPrice(tax)"></span>
                </div>
                <div class="flex justify-between text-lg font-bold border-t-2 border-gray-300 dark:border-gray-600 pt-2 text-gray-900 dark:text-white">
                    <span>Total</span>
                    <span class="text-blue-600 dark:text-blue-400" x-text="formatPrice(total)"></span>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="p-4 border-t-2 border-gray-200 dark:border-gray-700" x-show="cartItems.length > 0">
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wide">Méthodes de Paiement</label>
                <div class="space-y-2">
                    <template x-for="(payment, index) in payments" :key="index">
                        <div class="flex items-center gap-2">
                            <select x-model="payment.method" class="flex-1 border-2 border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="cash">Espèces</option>
                                <option value="card">Carte bancaire</option>
                                <option value="cheque">Chèque</option>
                                <option value="bank_transfer">Virement</option>
                                <option value="mobile_payment">Paiement mobile</option>
                            </select>
                            <input type="number" x-model.number="payment.amount" @input="updatePaymentsTotal()" step="0.01" min="0" :max="total - paymentsTotal + payment.amount" placeholder="0.00" class="w-28 border-2 border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white font-semibold">
                            <button @click="removePayment(index)" x-show="payments.length > 1" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </template>
                    <button @click="addPayment()" class="w-full px-3 py-2 text-xs text-blue-600 hover:bg-blue-50 rounded-xl border border-blue-200 font-semibold transition">
                        + Ajouter une méthode
                    </button>
                    <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                        <span class="text-sm font-semibold text-gray-700">Total payé:</span>
                        <span class="text-sm font-bold" :class="paymentsTotal >= total ? 'text-green-600' : 'text-orange-600'" x-text="formatPrice(paymentsTotal)"></span>
                    </div>
                    <div x-show="paymentsTotal < total" class="text-xs text-orange-600 font-medium">
                        Reste à payer: <span x-text="formatPrice(total - paymentsTotal)"></span>
                    </div>
                    <div x-show="paymentsTotal > total" class="text-xs text-blue-600 font-medium">
                        Monnaie: <span x-text="formatPrice(paymentsTotal - total)"></span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="p-4 border-t-2 border-gray-200 dark:border-gray-700 grid grid-cols-2 gap-3 pb-safe">
                <button @click="savePending()" :disabled="cartItems.length === 0 || processing" class="px-4 py-3 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white rounded-lg font-bold text-sm hover:bg-gray-300 dark:hover:bg-gray-500 transition active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!processing">En attente</span>
                    <span x-show="processing" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Enregistrement...
                    </span>
                </button>
                <button @click="checkout()" :disabled="cartItems.length === 0 || processing" class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold text-sm shadow-lg shadow-blue-600/40 transition active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!processing">Encaisser</span>
                    <span x-show="processing" class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Traitement...
                    </span>
                </button>
            </div>
        </div>
    </div>

    <script>
        function posCart() {
            return {
                cartOpen: false,
                selectedCategory: 'all',
                selectedClient: '',
                cartItems: [],
                payments: [{ method: 'cash', amount: 0 }],
                processing: false,
                
                get subtotal() {
                    return this.cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },
                
                get tax() {
                    return this.subtotal * 0.20;
                },
                
                get total() {
                    return this.subtotal + this.tax;
                },
                
                get paymentsTotal() {
                    return this.payments.reduce((sum, payment) => sum + (payment.amount || 0), 0);
                },
                
                addPayment() {
                    this.payments.push({ method: 'cash', amount: 0 });
                },
                
                removePayment(index) {
                    if (this.payments.length > 1) {
                        this.payments.splice(index, 1);
                        this.updatePaymentsTotal();
                    }
                },
                
                updatePaymentsTotal() {
                    // This method is called when payment amounts change
                },
                
                addToCart(id, name, price, unit, image, stock) {
                    const existingItem = this.cartItems.find(item => item.id === id);
                    
                    if (existingItem) {
                        if (existingItem.quantity < stock) {
                            existingItem.quantity++;
                        } else {
                            alert('Stock insuffisant! Stock disponible: ' + stock);
                        }
                    } else {
                        this.cartItems.push({
                            id: id,
                            name: name,
                            price: parseFloat(price),
                            unit: unit,
                            image: image || '',
                            quantity: 1,
                            stock: stock
                        });
                    }
                    
                    // Auto-fill first payment with total
                    setTimeout(() => {
                        if (this.payments.length > 0 && this.total > 0) {
                            this.payments[0].amount = this.total;
                        }
                    }, 100);
                    
                    // Open cart on mobile
                    if (window.innerWidth < 1024) {
                        this.cartOpen = true;
                    }
                },
                
                increaseQuantity(index) {
                    const item = this.cartItems[index];
                    if (item.quantity < item.stock) {
                        item.quantity++;
                    } else {
                        alert('Stock insuffisant! Stock disponible: ' + item.stock);
                    }
                },
                
                decreaseQuantity(index) {
                    const item = this.cartItems[index];
                    if (item.quantity > 1) {
                        item.quantity--;
                    } else {
                        this.removeItem(index);
                    }
                },
                
                updateQuantity(index) {
                    const item = this.cartItems[index];
                    if (item.quantity < 1) {
                        item.quantity = 1;
                    } else if (item.quantity > item.stock) {
                        item.quantity = item.stock;
                        alert('Stock insuffisant! Quantité ajustée au stock disponible.');
                    }
                },
                
                removeItem(index) {
                    this.cartItems.splice(index, 1);
                },
                
                clearCart() {
                    if (confirm('Voulez-vous vider le panier?')) {
                        this.cartItems = [];
                        this.payments = [{ method: 'cash', amount: 0 }];
                    }
                },
                
                async checkout() {
                    if (this.cartItems.length === 0) {
                        alert('Le panier est vide!');
                        return;
                    }

                    if (this.paymentsTotal < this.total) {
                        alert('Le montant payé est insuffisant!');
                        return;
                    }

                    this.processing = true;

                    try {
                        const response = await fetch('{{ route("pos.checkout") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                client_id: this.selectedClient || null,
                                items: this.cartItems.map(item => ({
                                    id: item.id,
                                    quantity: item.quantity,
                                    price: item.price
                                })),
                                subtotal: this.subtotal,
                                tax: this.tax,
                                total: this.total,
                                payments: this.payments.filter(p => p.amount > 0).map(p => ({
                                    method: p.method,
                                    amount: parseFloat(p.amount)
                                }))
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            alert('✅ ' + data.message + '\nFacture: ' + data.invoice_number);
                            this.cartItems = [];
                            this.selectedClient = '';
                            this.payments = [{ method: 'cash', amount: 0 }];
                            // Redirect to invoice
                            window.location.href = '/invoices/' + data.invoice_id;
                        } else {
                            alert('❌ ' + data.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('❌ Erreur lors de l\'encaissement. Veuillez réessayer.');
                    } finally {
                        this.processing = false;
                    }
                },
                
                async savePending() {
                    if (this.cartItems.length === 0) {
                        alert('Le panier est vide!');
                        return;
                    }

                    this.processing = true;

                    try {
                        const response = await fetch('{{ route("pos.pending") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                client_id: this.selectedClient || null,
                                items: this.cartItems.map(item => ({
                                    id: item.id,
                                    quantity: item.quantity,
                                    price: item.price
                                })),
                                subtotal: this.subtotal,
                                tax: this.tax,
                                total: this.total
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            alert('✅ ' + data.message + '\nFacture: ' + data.invoice_number);
                            this.cartItems = [];
                            this.selectedClient = '';
                        } else {
                            alert('❌ ' + data.message);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('❌ Erreur lors de l\'enregistrement. Veuillez réessayer.');
                    } finally {
                        this.processing = false;
                    }
                },
                
                formatPrice(value) {
                    return new Intl.NumberFormat('fr-MA', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(value || 0) + ' MAD';
                }
            }
        }
        
        // Simple search functionality
        document.getElementById('searchInput')?.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.product-card').forEach(card => {
                const name = card.dataset.name || '';
                if (name.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</x-app-layout>
