<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Modifier l'Article</h1>
                <p class="text-gray-500">{{ $article->reference }} - {{ $article->designation }}</p>
            </div>
            <a href="{{ route('articles.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
            <form action="{{ route('articles.update', $article) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Basic Info -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="reference" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Référence *</label>
                        <input type="text" name="reference" id="reference" required value="{{ old('reference', $article->reference) }}" 
                               class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('reference') border-red-500 @enderror" 
                               placeholder="Ex: 100.27000">
                        @error('reference')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catégorie</label>
                        <select name="category" id="category" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Sélectionner une catégorie</option>
                            @foreach (\App\Models\Setting::getArticleCategories() as $cat)
                                <option value="{{ $cat }}" {{ old('category', $article->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="designation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Désignation *</label>
                    <input type="text" name="designation" id="designation" required value="{{ old('designation', $article->designation) }}" 
                           class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                           placeholder="Ex: PMMA COULE ALTUGLAS">
                    @error('designation')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Product Details -->
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                        <select name="type" id="type" class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Sélectionner</option>
                            @foreach (\App\Models\Setting::getArticleTypes() as $type)
                                <option value="{{ $type }}" {{ old('type', $article->type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Couleur</label>
                        <input type="text" name="color" id="color" value="{{ old('color', $article->color) }}" 
                               class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                               placeholder="Ex: BLANC">
                    </div>
                    <div>
                        <label for="thickness" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Épaisseur</label>
                        <input type="text" name="thickness" id="thickness" value="{{ old('thickness', $article->thickness) }}" 
                               class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                               placeholder="Ex: 3MM">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="dimensions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dimensions</label>
                        <input type="text" name="dimensions" id="dimensions" value="{{ old('dimensions', $article->dimensions) }}" 
                               class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                               placeholder="Ex: 3X2M">
                    </div>
                    <div>
                        <label for="surface_area" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Surface (M²)</label>
                        <input type="number" step="0.0001" name="surface_area" id="surface_area" value="{{ old('surface_area', $article->surface_area) }}" 
                               class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                               placeholder="Ex: 6.19">
                    </div>
                </div>

                <!-- Pricing -->
                <div class="border-t border-gray-300 dark:border-gray-600 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Tarification</h3>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unité de vente *</label>
                            <select name="unit" id="unit" required class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="Plaque" {{ old('unit', $article->unit) == 'Plaque' ? 'selected' : '' }}>Plaque</option>
                                <option value="M²" {{ old('unit', $article->unit) == 'M²' ? 'selected' : '' }}>M²</option>
                                <option value="ml" {{ old('unit', $article->unit) == 'ml' ? 'selected' : '' }}>ml (mètre linéaire)</option>
                                <option value="unité" {{ old('unit', $article->unit) == 'unité' ? 'selected' : '' }}>Unité</option>
                            </select>
                        </div>
                        <div>
                            <label for="selling_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prix de vente (MAD) *</label>
                            <input type="number" step="0.01" name="selling_price" id="selling_price" required value="{{ old('selling_price', $article->selling_price) }}" 
                                   class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                   placeholder="0.00">
                            @error('selling_price')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="price_per_unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prix / M² (MAD)</label>
                            <input type="number" step="0.01" name="price_per_unit" id="price_per_unit" value="{{ old('price_per_unit', $article->price_per_unit) }}" 
                                   class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                   placeholder="0.00">
                        </div>
                    </div>
                </div>

                <!-- Stock -->
                <div class="border-t border-gray-300 dark:border-gray-600 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Gestion du Stock</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="stock_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stock actuel *</label>
                            <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $article->stock_quantity) }}" required
                                   class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                   placeholder="0">
                        </div>
                        <div>
                            <label for="min_stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Stock minimum (alerte)</label>
                            <input type="number" name="min_stock" id="min_stock" value="{{ old('min_stock', $article->min_stock) }}" 
                                   class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                   placeholder="0">
                        </div>
                    </div>
                </div>

                <!-- Image Upload -->
                <div class="border-t border-gray-300 dark:border-gray-600 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Image du Produit</h3>
                    
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Image</label>
                        <div class="mt-1 flex items-center gap-4">
                            <div class="flex-1">
                                <input type="file" name="image" id="image" accept="image/*" 
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer @error('image') border-red-500 @enderror"
                                       onchange="previewImage(this)">
                                <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF ou WEBP (max. 2MB). Laisser vide pour conserver l'image actuelle.</p>
                                @error('image')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="w-32 h-32 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center overflow-hidden bg-gray-50" id="imagePreview">
                                @if($article->image)
                                    <img src="{{ $article->image_url }}" alt="Current image" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Status -->
                <div class="border-t border-gray-300 dark:border-gray-600 pt-6">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $article->is_active) ? 'checked' : '' }} 
                               class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Article actif (visible dans le POS)</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-300 dark:border-gray-600">
                    <a href="{{ route('articles.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover">`;
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                @if($article->image)
                    preview.innerHTML = `<img src="{{ $article->image_url }}" alt="Current image" class="w-full h-full object-cover">`;
                @else
                    preview.innerHTML = `
                        <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    `;
                @endif
            }
        }
    </script>
</x-app-layout>
