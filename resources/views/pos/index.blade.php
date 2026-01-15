<x-app-layout>
    <div x-data="{ cartOpen: false }" class="flex flex-col lg:flex-row gap-6 min-h-[calc(100vh-200px)]">
        <!-- Left: Product Selection -->
        <div class="flex-1 flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100">Terminal POS</h1>
                    <p class="text-sm text-gray-500">Vente rapide de produits aluminium</p>
                </div>
                
                <!-- Mobile Cart Button -->
                <button @click="cartOpen = true" class="lg:hidden relative flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl font-semibold text-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Panier</span>
                    <span class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">0</span>
                </button>
            </div>

            <!-- Categories -->
            <div class="flex gap-2 mb-4 sm:mb-6 overflow-x-auto pb-2 -mx-4 px-4 sm:mx-0 sm:px-0">
                <button class="px-3 sm:px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-xl text-xs sm:text-sm font-semibold whitespace-nowrap shadow-lg shadow-blue-500/25">Tous</button>
                <button class="px-3 sm:px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs sm:text-sm font-semibold whitespace-nowrap hover:bg-gray-50 hover:border-blue-300 transition">Profilés</button>
                <button class="px-3 sm:px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs sm:text-sm font-semibold whitespace-nowrap hover:bg-gray-50 hover:border-blue-300 transition">Fenêtres</button>
                <button class="px-3 sm:px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs sm:text-sm font-semibold whitespace-nowrap hover:bg-gray-50 hover:border-blue-300 transition">Portes</button>
                <button class="px-3 sm:px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs sm:text-sm font-semibold whitespace-nowrap hover:bg-gray-50 hover:border-blue-300 transition">Accessoires</button>
                <button class="px-3 sm:px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs sm:text-sm font-semibold whitespace-nowrap hover:bg-gray-50 hover:border-blue-300 transition">Vitrage</button>
            </div>

            <!-- Search -->
            <div class="relative mb-4 sm:mb-6">
                <input type="text" placeholder="Rechercher un produit..." class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Products Grid -->
            <div class="flex-1 overflow-y-auto -mx-4 px-4 sm:mx-0 sm:px-0">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
                    @foreach ([
                        ['name' => 'Profilé Coulissant 7024', 'price' => 185.00, 'unit' => 'ml', 'stock' => 250],
                        ['name' => 'Profilé Battant Standard', 'price' => 145.00, 'unit' => 'ml', 'stock' => 180],
                        ['name' => 'Fenêtre 120x120 cm', 'price' => 2400.00, 'unit' => 'unité', 'stock' => 12],
                        ['name' => 'Porte Battante 90x210', 'price' => 3200.00, 'unit' => 'unité', 'stock' => 8],
                        ['name' => 'Vitrage 4mm Clair', 'price' => 120.00, 'unit' => 'm²', 'stock' => 500],
                        ['name' => 'Vitrage 6mm Fumé', 'price' => 180.00, 'unit' => 'm²', 'stock' => 300],
                        ['name' => 'Poignée Standard', 'price' => 85.00, 'unit' => 'unité', 'stock' => 150],
                        ['name' => 'Serrure Multi-point', 'price' => 450.00, 'unit' => 'unité', 'stock' => 45],
                    ] as $product)
                        <div class="bg-white dark:bg-gray-800 p-3 sm:p-4 rounded-2xl border border-gray-100 dark:border-gray-700 hover:border-blue-300 hover:shadow-lg cursor-pointer transition-all duration-200 group">
                            <div class="h-16 sm:h-24 bg-gradient-to-br from-slate-100 to-slate-50 dark:from-gray-700 dark:to-gray-600 rounded-xl mb-2 sm:mb-3 flex items-center justify-center">
                                <svg class="w-8 sm:w-10 h-8 sm:h-10 text-slate-300 group-hover:text-blue-400 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-xs sm:text-sm mb-1 truncate">{{ $product['name'] }}</h3>
                            <div class="flex items-center justify-between">
                                <span class="text-blue-600 font-bold text-sm sm:text-base">{{ number_format($product['price'], 0, ',', '.') }}</span>
                                <span class="text-[10px] sm:text-xs text-gray-400">MAD/{{ $product['unit'] }}</span>
                            </div>
                            <div class="mt-1 sm:mt-2 text-[10px] sm:text-xs text-gray-400">Stock: {{ $product['stock'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Desktop Cart -->
        <div class="hidden lg:flex w-80 xl:w-96 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 flex-col shadow-xl">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-slate-50 to-white rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <h2 class="font-bold text-gray-900 dark:text-gray-100">Panier</h2>
                    <button class="text-red-500 text-sm font-semibold hover:underline">Vider</button>
                </div>
            </div>

            <!-- Client Selection -->
            <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                <label class="block text-xs text-gray-500 mb-2">Client</label>
                <select class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50">
                    <option>Client comptoir</option>
                    @foreach (\App\Models\Client::all() as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-4">
                <div class="text-center py-12 text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-sm font-medium">Panier vide</p>
                    <p class="text-xs mt-1">Cliquez sur un produit pour l'ajouter</p>
                </div>
            </div>

            <!-- Totals -->
            <div class="p-4 border-t border-gray-100 dark:border-gray-700 space-y-3 bg-slate-50/50">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Sous-total</span>
                    <span class="font-medium">0,00 MAD</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">TVA (20%)</span>
                    <span class="font-medium">0,00 MAD</span>
                </div>
                <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-3">
                    <span>Total</span>
                    <span class="text-blue-600">0,00 MAD</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="p-4 border-t border-gray-100 dark:border-gray-700 grid grid-cols-2 gap-3">
                <button class="px-4 py-3 bg-slate-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-slate-200 transition">
                    En attente
                </button>
                <button class="px-4 py-3 bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-xl font-semibold text-sm hover:from-blue-700 hover:to-cyan-600 shadow-lg shadow-blue-500/25 transition">
                    Encaisser
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
             class="lg:hidden fixed bottom-0 inset-x-0 bg-white dark:bg-gray-800 rounded-t-3xl z-50 max-h-[85vh] flex flex-col"
             @click.stop>
            
            <!-- Handle -->
            <div class="flex justify-center py-3">
                <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
            </div>
            
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-bold text-gray-900 text-lg">Panier</h2>
                <button @click="cartOpen = false" class="p-2 rounded-xl hover:bg-gray-100">
                    <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Client Selection -->
            <div class="p-4 border-b border-gray-100">
                <label class="block text-xs text-gray-500 mb-2">Client</label>
                <select class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>Client comptoir</option>
                    @foreach (\App\Models\Client::all() as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-4">
                <div class="text-center py-8 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-sm">Panier vide</p>
                </div>
            </div>

            <!-- Totals -->
            <div class="p-4 border-t border-gray-100 space-y-2 bg-slate-50">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Sous-total</span>
                    <span class="font-medium">0,00 MAD</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">TVA (20%)</span>
                    <span class="font-medium">0,00 MAD</span>
                </div>
                <div class="flex justify-between text-lg font-bold border-t pt-2">
                    <span>Total</span>
                    <span class="text-blue-600">0,00 MAD</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="p-4 border-t border-gray-100 grid grid-cols-2 gap-3 pb-safe">
                <button class="px-4 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm">
                    En attente
                </button>
                <button class="px-4 py-3 bg-gradient-to-r from-blue-600 to-cyan-500 text-white rounded-xl font-semibold text-sm shadow-lg shadow-blue-500/25">
                    Encaisser
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
