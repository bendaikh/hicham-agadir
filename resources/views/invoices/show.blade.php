@php
    $businessName = \App\Models\Setting::getBusinessName();
    $logoPath = \App\Models\Setting::getBusinessLogo();
    $logoUrl = $logoPath ? \Illuminate\Support\Facades\Storage::url($logoPath) : null;
    
    $subtotal = $invoice->items->sum('total_price');
    $tax = $subtotal * 0.20;
    $total = $subtotal + $tax;
@endphp

<x-app-layout>
    <div class="max-w-5xl mx-auto">
        <!-- Action Buttons -->
        <div class="flex items-center justify-between mb-6 print:hidden no-print">
            <a href="{{ route('invoices.index') }}" class="inline-flex items-center px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour
            </a>
            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Imprimer
                </button>
            </div>
        </div>

        <!-- Invoice Container -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden print:shadow-none print:border-none">
            <!-- Header with Logo and Business Info -->
            <div class="bg-gradient-to-r from-blue-600 to-cyan-500 p-8 text-white print:from-blue-600 print:to-cyan-500">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="Logo" class="h-20 mb-4 object-contain print:h-16">
                        @endif
                        <h1 class="text-3xl font-bold mb-2">{{ $businessName ?: 'Mon Entreprise' }}</h1>
                        <p class="text-blue-100 text-sm">Facture Professionnelle</p>
                    </div>
                    <div class="text-right">
                        <div class="bg-white/20 backdrop-blur-sm rounded-xl px-6 py-4 inline-block">
                            <p class="text-sm text-blue-100 mb-1">Numéro de Facture</p>
                            <p class="text-2xl font-bold">{{ $invoice->invoice_number }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Details -->
            <div class="p-8 print:p-6 invoice-content">
                <!-- Invoice Info and Client Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 print:gap-4 print:mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Informations de Facturation</h3>
                        <div class="space-y-1 text-gray-700">
                            <p><strong>Date:</strong> {{ $invoice->created_at->format('d/m/Y') }}</p>
                            <p><strong>Date d'échéance:</strong> {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : 'N/A' }}</p>
                            <p><strong>Statut:</strong> 
                                <span class="px-3 py-1 rounded-full text-xs font-bold 
                                    @if($invoice->status === 'payee') bg-green-100 text-green-700
                                    @elseif($invoice->status === 'envoyee') bg-blue-100 text-blue-700
                                    @elseif($invoice->status === 'brouillon') bg-gray-100 text-gray-700
                                    @elseif($invoice->status === 'annulee') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    @if($invoice->status === 'payee') Payée
                                    @elseif($invoice->status === 'envoyee') Envoyée
                                    @elseif($invoice->status === 'brouillon') Brouillon
                                    @elseif($invoice->status === 'annulee') Annulée
                                    @else {{ $invoice->status }}
                                    @endif
                                </span>
                            </p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Client</h3>
                        <div class="space-y-1 text-gray-700">
                            @if($invoice->client)
                                <p class="font-semibold text-lg">{{ $invoice->client->name }}</p>
                                @if($invoice->client->email)
                                    <p class="text-sm">{{ $invoice->client->email }}</p>
                                @endif
                                @if($invoice->client->phone)
                                    <p class="text-sm">{{ $invoice->client->phone }}</p>
                                @endif
                                @if($invoice->client->address)
                                    <p class="text-sm">{{ $invoice->client->address }}</p>
                                @endif
                            @else
                                <p class="font-semibold text-lg">Client comptoir</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Items Table and Totals Container (keep together) -->
                <div class="invoice-totals-wrapper">
                    <!-- Items Table -->
                    <div class="mb-8 print:mb-4">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b-2 border-gray-200">
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase print:px-2 print:py-2">Article</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase print:px-2 print:py-2">Quantité</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase print:px-2 print:py-2">Prix unitaire</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase print:px-2 print:py-2">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($invoice->items as $item)
                                    <tr class="hover:bg-gray-50 print-break-inside-avoid">
                                        <td class="px-4 py-4 print:px-2 print:py-2">
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $item->article->designation }}</p>
                                                @if($item->article->reference)
                                                    <p class="text-sm text-gray-500">Ref: {{ $item->article->reference }}</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center text-gray-700 print:px-2 print:py-2">
                                            {{ $item->quantity }} {{ $item->article->unit }}
                                        </td>
                                        <td class="px-4 py-4 text-right text-gray-700 print:px-2 print:py-2">
                                            {{ number_format($item->unit_price, 2, ',', '.') }} MAD
                                        </td>
                                        <td class="px-4 py-4 text-right font-semibold text-gray-900 print:px-2 print:py-2">
                                            {{ number_format($item->total_price, 2, ',', '.') }} MAD
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Payment Methods -->
                    @if($invoice->payments->count() > 0)
                        <div class="mb-6 p-4 bg-blue-50 rounded-xl border border-blue-100 print:mb-3 print:p-3 print-break-inside-avoid">
                            <h4 class="font-semibold text-gray-900 mb-3 print:mb-2">Méthodes de Paiement</h4>
                            <div class="space-y-2">
                                @foreach($invoice->payments as $payment)
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-700">
                                            @php
                                                $methodLabels = [
                                                    'cash' => 'Espèces',
                                                    'card' => 'Carte bancaire',
                                                    'cheque' => 'Chèque',
                                                    'bank_transfer' => 'Virement',
                                                    'mobile_payment' => 'Paiement mobile'
                                                ];
                                            @endphp
                                            {{ $methodLabels[$payment->payment_method] ?? $payment->payment_method }}
                                        </span>
                                        <span class="font-semibold text-gray-900">
                                            {{ number_format($payment->amount, 2, ',', '.') }} MAD
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Totals -->
                    <div class="flex justify-end print-break-inside-avoid">
                        <div class="w-full md:w-80 space-y-3 print:space-y-2">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Sous-total:</span>
                                <span>{{ number_format($subtotal, 2, ',', '.') }} MAD</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>TVA (20%):</span>
                                <span>{{ number_format($tax, 2, ',', '.') }} MAD</span>
                            </div>
                            <div class="flex justify-between items-center pt-3 border-t-2 border-gray-200 print:pt-2">
                                <span class="text-lg font-bold text-gray-900 print:text-base">Total:</span>
                                <span class="text-2xl font-bold text-blue-600 print:text-xl">{{ number_format($invoice->total_amount, 2, ',', '.') }} MAD</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
                <p class="text-xs text-gray-500 text-center">
                    Merci de votre confiance. Cette facture est générée automatiquement.
                </p>
            </div>
        </div>
    </div>

    <style>
        /* Hide action buttons when printing */
        @media print {
            .print\:hidden,
            .no-print {
                display: none !important;
                visibility: hidden !important;
            }
            
            body * {
                visibility: hidden;
            }
            .max-w-5xl, .max-w-5xl * {
                visibility: visible;
            }
            .max-w-5xl {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .bg-gradient-to-r {
                background: linear-gradient(to right, #2563eb, #06b6d4) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            /* Prevent page breaks in critical sections */
            .invoice-content {
                page-break-inside: avoid;
            }
            
            .invoice-totals-wrapper {
                page-break-inside: avoid;
                display: block;
            }
            
            .print-break-inside-avoid {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            
            /* Keep table rows together */
            table tbody tr {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            
            /* Ensure totals stay with at least last row */
            table tbody tr:last-of-type {
                page-break-after: avoid;
                break-after: avoid;
            }
            
            /* Adjust spacing for print */
            .invoice-content > * {
                page-break-inside: avoid;
            }
            
            /* Reduce margins */
            @page {
                margin: 1cm;
            }
        }
    </style>
</x-app-layout>
