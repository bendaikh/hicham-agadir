<x-app-layout>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Gestion des Articles</h1>
            <p class="text-gray-500">Catalogue des produits PMMA et matières premières</p>
        </div>
        <a href="{{ route('articles.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nouvel Article
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-700 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">Total Articles</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ \App\Models\Article::count() }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">Valeur du Stock</div>
            <div class="text-2xl font-bold text-blue-600">{{ number_format(\App\Models\Article::selectRaw('SUM(stock_quantity * selling_price) as total')->value('total') ?? 0, 2, ',', '.') }} MAD</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">Stock Faible</div>
            <div class="text-2xl font-bold text-orange-600">{{ \App\Models\Article::whereColumn('stock_quantity', '<=', 'min_stock')->count() }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="text-sm text-gray-500 mb-1">En Stock</div>
            <div class="text-2xl font-bold text-green-600">{{ \App\Models\Article::where('stock_quantity', '>', 0)->count() }}</div>
        </div>
    </div>

    <!-- Articles Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <input type="text" placeholder="Rechercher un article..." class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <select class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Toutes catégories</option>
                    <option value="PMMA">PMMA</option>
                    <option value="Vitrage">Vitrage</option>
                    <option value="Profilé">Profilé</option>
                    <option value="Accessoire">Accessoire</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-gray-700/50 text-xs font-bold text-gray-500 uppercase">
                        <th class="px-6 py-4 text-left">Image</th>
                        <th class="px-6 py-4 text-left">Référence</th>
                        <th class="px-6 py-4 text-left">Désignation</th>
                        <th class="px-6 py-4 text-left">Dimensions</th>
                        <th class="px-6 py-4 text-center">Unité</th>
                        <th class="px-6 py-4 text-right">Prix Vente</th>
                        <th class="px-6 py-4 text-center">Stock</th>
                        <th class="px-6 py-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($articles as $article)
                        <tr class="text-sm hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-6 py-4">
                                @if($article->image)
                                    <img src="{{ $article->image_url }}" alt="{{ $article->designation }}" class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                                @else
                                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-bold text-blue-600">{{ $article->reference }}</td>
                            <td class="px-6 py-4">
                                <div class="text-gray-900 dark:text-gray-100 font-medium">{{ $article->designation }}</div>
                                <div class="text-xs text-gray-500">
                                    @if($article->type) {{ $article->type }} @endif
                                    @if($article->color) - {{ $article->color }} @endif
                                    @if($article->thickness) - Ep {{ $article->thickness }} @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $article->dimensions ?? '-' }}
                                @if($article->surface_area)
                                    <div class="text-xs">{{ number_format($article->surface_area, 2, ',', '.') }} M²</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-medium">{{ $article->unit }}</span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-gray-100">
                                {{ number_format($article->selling_price, 2, ',', '.') }} MAD
                                @if($article->price_per_unit)
                                    <div class="text-xs text-gray-500 font-normal">{{ number_format($article->price_per_unit, 2, ',', '.') }}/M²</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $stockClass = $article->stock_quantity <= 0 ? 'bg-red-100 text-red-700' : 
                                                 ($article->isLowStock() ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700');
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $stockClass }}">
                                    {{ $article->stock_quantity }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('articles.edit', $article) }}" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('articles.destroy', $article) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                Aucun article trouvé. <a href="{{ route('articles.create') }}" class="text-blue-600 hover:underline">Créer votre premier article</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($articles->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
