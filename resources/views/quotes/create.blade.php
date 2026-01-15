<x-app-layout>
    <div class="max-w-2xl mx-auto">
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

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
            <form action="{{ route('quotes.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="client_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Client *</label>
                    <select name="client_id" id="client_id" required class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sélectionner un client</option>
                        @foreach (\App\Models\Client::all() as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="quote_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">N° Devis *</label>
                        <input type="text" name="quote_number" id="quote_number" required value="DEV-{{ str_pad(\App\Models\Quote::count() + 1, 4, '0', STR_PAD_LEFT) }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="expires_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date d'expiration</label>
                        <input type="date" name="expires_at" id="expires_at" value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label for="total_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Montant total (MAD) *</label>
                    <input type="number" step="0.01" name="total_amount" id="total_amount" required class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('quotes.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition">
                        Créer le devis
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
