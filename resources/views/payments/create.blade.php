<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Nouveau Paiement</h1>
                <p class="text-gray-500">Enregistrer un paiement client</p>
            </div>
            <a href="{{ route('payments.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
            <form action="{{ route('payments.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="invoice_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Facture *</label>
                    <select name="invoice_id" id="invoice_id" required class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Sélectionner une facture</option>
                        @foreach (\App\Models\Invoice::with('client')->where('status', '!=', 'paid')->get() as $invoice)
                            <option value="{{ $invoice->id }}">#{{ $invoice->invoice_number }} - {{ $invoice->client->name ?? 'Client' }} ({{ number_format($invoice->total_amount, 2) }} MAD)</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Montant (MAD) *</label>
                        <input type="number" step="0.01" name="amount" id="amount" required class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                    </div>
                    <div>
                        <label for="payment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date du paiement *</label>
                        <input type="date" name="payment_date" id="payment_date" required value="{{ date('Y-m-d') }}" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Méthode de paiement *</label>
                    <select name="payment_method" id="payment_method" required class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="cash">Espèces</option>
                        <option value="card">Carte bancaire</option>
                        <option value="transfer">Virement</option>
                        <option value="check">Chèque</option>
                    </select>
                </div>

                <div>
                    <label for="transaction_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Référence de transaction</label>
                    <input type="text" name="transaction_id" id="transaction_id" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="N° chèque, référence virement...">
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('payments.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold text-sm hover:bg-blue-700 transition">
                        Enregistrer le paiement
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
