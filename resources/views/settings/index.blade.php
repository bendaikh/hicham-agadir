<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Paramètres</h1>
            <p class="text-gray-500">Gérez les paramètres de l'application</p>
        </div>

        <!-- Application Settings Section -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Paramètres de l'Application</h2>
            <p class="text-sm text-gray-500">Configurez les options générales de l'application</p>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-700 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <!-- Article Categories -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Catégories d'Articles</h3>
                    <p class="text-sm text-gray-500">Gérez les catégories disponibles pour les articles</p>
                </div>
            </div>

            <form action="{{ route('settings.update-categories') }}" method="POST" id="categoriesForm">
                @csrf
                <div class="space-y-3 mb-4" id="categoriesContainer">
                    @foreach ($categories as $index => $category)
                        <div class="flex items-center gap-3 category-item">
                            <input type="text" name="categories[]" value="{{ $category }}" required
                                   class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Nom de la catégorie">
                            <button type="button" onclick="removeCategory(this)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" onclick="addCategory()" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Ajouter une catégorie
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition">
                        Enregistrer les catégories
                    </button>
                </div>
            </form>
        </div>

        <!-- Article Types -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Types d'Articles</h3>
                    <p class="text-sm text-gray-500">Gérez les types disponibles pour les articles</p>
                </div>
            </div>

            <form action="{{ route('settings.update-types') }}" method="POST" id="typesForm">
                @csrf
                <div class="space-y-3 mb-4" id="typesContainer">
                    @foreach ($types as $index => $type)
                        <div class="flex items-center gap-3 type-item">
                            <input type="text" name="types[]" value="{{ $type }}" required
                                   class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Nom du type">
                            <button type="button" onclick="removeType(this)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" onclick="addType()" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Ajouter un type
                    </button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition">
                        Enregistrer les types
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function addCategory() {
            const container = document.getElementById('categoriesContainer');
            const newItem = document.createElement('div');
            newItem.className = 'flex items-center gap-3 category-item';
            newItem.innerHTML = `
                <input type="text" name="categories[]" value="" required
                       class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Nom de la catégorie">
                <button type="button" onclick="removeCategory(this)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            `;
            container.appendChild(newItem);
        }

        function removeCategory(button) {
            const container = document.getElementById('categoriesContainer');
            if (container.children.length > 1) {
                button.closest('.category-item').remove();
            } else {
                alert('Vous devez avoir au moins une catégorie.');
            }
        }

        function addType() {
            const container = document.getElementById('typesContainer');
            const newItem = document.createElement('div');
            newItem.className = 'flex items-center gap-3 type-item';
            newItem.innerHTML = `
                <input type="text" name="types[]" value="" required
                       class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Nom du type">
                <button type="button" onclick="removeType(this)" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            `;
            container.appendChild(newItem);
        }

        function removeType(button) {
            const container = document.getElementById('typesContainer');
            if (container.children.length > 1) {
                button.closest('.type-item').remove();
            } else {
                alert('Vous devez avoir au moins un type.');
            }
        }
    </script>
</x-app-layout>
