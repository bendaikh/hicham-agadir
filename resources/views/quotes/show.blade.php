@php
    use Illuminate\Support\Facades\Storage;
    $businessName = \App\Models\Setting::getBusinessName();
    $logoPath = \App\Models\Setting::getBusinessLogo();
    $logoUrl = $logoPath ? \Illuminate\Support\Facades\Storage::url($logoPath) : null;
    
    $subtotal = $quote->items->sum('total_price');
    $tax = $subtotal * 0.20;
    $total = $subtotal + $tax;
@endphp

<x-app-layout>
    <div class="max-w-5xl mx-auto">
        <!-- Action Buttons -->
        <div class="flex items-center justify-between mb-6 print:hidden no-print">
            <a href="{{ route('quotes.index') }}" class="inline-flex items-center px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour
            </a>
            <div class="flex items-center gap-3">
                @if($quote->status !== 'rejected' && !$quote->invoice_id)
                    <form action="{{ route('quotes.convert-to-invoice', $quote) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir convertir ce devis en facture?');">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Convertir en facture
                        </button>
                    </form>
                @elseif($quote->invoice_id)
                    <a href="{{ route('invoices.show', $quote->invoice_id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Voir la facture
                    </a>
                @endif
                <a href="{{ route('quotes.edit', $quote) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </a>
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Imprimer
                </button>
            </div>
        </div>

        <!-- Quote Container -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden print:shadow-none print:border-none">
            <!-- Header with Logo and Business Info -->
            <div class="bg-gradient-to-r from-blue-600 to-cyan-500 p-8 text-white print:from-blue-600 print:to-cyan-500">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        @if($logoUrl)
                            <img src="{{ $logoUrl }}" alt="Logo" class="h-20 mb-4 object-contain print:h-16">
                        @endif
                        <h1 class="text-3xl font-bold mb-2">{{ $businessName ?: 'Mon Entreprise' }}</h1>
                        <p class="text-blue-100 text-sm">Devis Professionnel</p>
                    </div>
                    <div class="text-right">
                        <div class="bg-white/20 backdrop-blur-sm rounded-xl px-6 py-4 inline-block">
                            <p class="text-sm text-blue-100 mb-1">Numéro de Devis</p>
                            <p class="text-2xl font-bold">{{ $quote->quote_number }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quote Details -->
            <div class="p-8 print:p-6 quote-content">
                <!-- Quote Info and Client Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 print:gap-4 print:mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Informations du Devis</h3>
                        <div class="space-y-1 text-gray-700">
                            <p><strong>Date:</strong> {{ $quote->created_at->format('d/m/Y') }}</p>
                            <p><strong>Date d'expiration:</strong> {{ $quote->expires_at ? $quote->expires_at->format('d/m/Y') : 'N/A' }}</p>
                            <p><strong>Statut:</strong> 
                                <span class="px-3 py-1 rounded-full text-xs font-bold 
                                    @if($quote->status === 'accepted') bg-green-100 text-green-700
                                    @elseif($quote->status === 'pending') bg-orange-100 text-orange-700
                                    @elseif($quote->status === 'rejected') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    @if($quote->status === 'accepted') Accepté
                                    @elseif($quote->status === 'pending') En attente
                                    @elseif($quote->status === 'rejected') Refusé
                                    @else Expiré
                                    @endif
                                </span>
                            </p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Client</h3>
                        <div class="space-y-1 text-gray-700">
                            @if($quote->client)
                                <p class="font-semibold text-lg">{{ $quote->client->name }}</p>
                                @if($quote->client->email)
                                    <p class="text-sm">{{ $quote->client->email }}</p>
                                @endif
                                @if($quote->client->phone)
                                    <p class="text-sm">{{ $quote->client->phone }}</p>
                                @endif
                                @if($quote->client->address)
                                    <p class="text-sm">{{ $quote->client->address }}</p>
                                @endif
                            @else
                                <p class="font-semibold text-lg">Client comptoir</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Items Table and Totals Container -->
                <div class="quote-totals-wrapper">
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
                                @foreach($quote->items as $item)
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
                                <span class="text-2xl font-bold text-blue-600 print:text-xl">{{ number_format($quote->total_amount, 2, ',', '.') }} MAD</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
                <p class="text-xs text-gray-500 text-center">
                    Ce devis est valable jusqu'au {{ $quote->expires_at ? $quote->expires_at->format('d/m/Y') : 'N/A' }}. Merci de votre confiance.
                </p>
            </div>
        </div>
    </div>

    <style>
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
            
            .quote-content {
                page-break-inside: avoid;
            }
            
            .quote-totals-wrapper {
                page-break-inside: avoid;
                display: block;
            }
            
            .print-break-inside-avoid {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            
            table tbody tr {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            
            @page {
                margin: 1cm;
            }
        }
    </style>
</x-app-layout>
