<x-app-layout>
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $article->designation ?? $article->reference }}</h1>
                <p class="text-gray-500 mt-1">Référence: {{ $article->reference }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('articles.edit', $article->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </a>
                <a href="{{ route('articles.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
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
                <!-- Article Details -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Détails de l'Article</h2>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Référence</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $article->reference }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Désignation</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $article->designation ?? 'Non renseignée' }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Prix (HT)</label>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($article->selling_price ?? $article->price_per_unit, 2) }} MAD</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Unité</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $article->unit ?? 'Unité' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Description</label>
                        <p class="text-gray-900 dark:text-gray-100">{{ $article->description ?? 'Aucune description' }}</p>
                    </div>

                    @if($article->image)
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Image</label>
                            <img src="{{ $article->image }}" alt="{{ $article->name }}" class="max-w-xs h-40 object-cover rounded-lg">
                        </div>
                    @endif
                </div>

                <!-- Stock Information -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Stock</h2>
                    
                    <div class="grid grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Quantité en Stock</label>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $article->stock_quantity }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Quantité Réservée</label>
                            <p class="text-2xl font-bold text-yellow-600">{{ $article->reserved_quantity ?? 0 }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Disponible</label>
                            <p class="text-2xl font-bold text-green-600">{{ ($article->stock_quantity - ($article->reserved_quantity ?? 0)) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Stock Movements -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Historique des Mouvements</h2>

                    @php
                        $movements = $article->stockMovements()->orderBy('created_at', 'desc')->limit(10)->get();
                    @endphp

                    @if($movements->count() > 0)
                        <div class="space-y-3">
                            @foreach($movements as $movement)
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900 dark:text-gray-100">
                                            @if($movement->movement_type === 'in')
                                                <span class="text-green-600">+ Entrée</span>
                                            @elseif($movement->movement_type === 'out')
                                                <span class="text-red-600">- Sortie</span>
                                            @elseif($movement->movement_type === 'reserved')
                                                <span class="text-yellow-600">📌 Réservée</span>
                                            @else
                                                <span class="text-blue-600">🔓 Libérée</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $movement->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $movement->quantity }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $movement->reference ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">Aucun mouvement de stock</p>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Summary -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-6">Résumé</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Catégorie</p>
                            <p class="text-gray-900 dark:text-gray-100">{{ $article->category ?? 'Non catégorisé' }}</p>
                        </div>

                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Prix TTC</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format(($article->selling_price ?? $article->price_per_unit) * 1.20, 2) }} MAD</p>
                        </div>

                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Créé le</p>
                            <p class="text-gray-900 dark:text-gray-100">{{ $article->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Delete Button -->
                <form action="{{ route('articles.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-3 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition font-semibold text-sm">
                        <svg class="w-4 h-4 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Supprimer l'article
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
