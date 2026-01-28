<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\Purchase;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    /**
     * Global search across all resources
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'clients' => [],
                'invoices' => [],
                'quotes' => [],
                'purchases' => [],
                'articles' => []
            ]);
        }

        // Search Clients (by name or email)
        $clients = Client::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'type' => 'client',
                    'url' => route('clients.show', $client->id),
                    'email' => $client->email,
                    'icon' => 'users'
                ];
            });

        // Search Invoices (by invoice number or client name)
        $invoices = Invoice::where('invoice_number', 'like', "%{$query}%")
            ->orWhereHas('client', function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->with('client')
            ->limit(5)
            ->get()
            ->map(function($invoice) {
                return [
                    'id' => $invoice->id,
                    'name' => 'Facture ' . $invoice->invoice_number,
                    'type' => 'invoice',
                    'url' => route('invoices.show', $invoice->id),
                    'client' => $invoice->client?->name ?? 'N/A',
                    'status' => $invoice->status,
                    'icon' => 'file-text'
                ];
            });

        // Search Quotes (by quote number or client name)
        $quotes = Quote::where('quote_number', 'like', "%{$query}%")
            ->orWhereHas('client', function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->with('client')
            ->limit(5)
            ->get()
            ->map(function($quote) {
                return [
                    'id' => $quote->id,
                    'name' => 'Devis ' . $quote->quote_number,
                    'type' => 'quote',
                    'url' => route('quotes.show', $quote->id),
                    'client' => $quote->client?->name ?? 'N/A',
                    'status' => $quote->status,
                    'icon' => 'file'
                ];
            });

        // Search Purchases (by purchase number or supplier name)
        $purchases = Purchase::where('purchase_number', 'like', "%{$query}%")
            ->orWhereHas('supplier', function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->with('supplier')
            ->limit(5)
            ->get()
            ->map(function($purchase) {
                return [
                    'id' => $purchase->id,
                    'name' => 'Achat ' . $purchase->purchase_number,
                    'type' => 'purchase',
                    'url' => route('purchases.show', $purchase->id),
                    'supplier' => $purchase->supplier?->name ?? 'N/A',
                    'status' => $purchase->status,
                    'icon' => 'shopping-cart'
                ];
            });

        // Search Articles (by designation or reference)
        $articles = Article::where('designation', 'like', "%{$query}%")
            ->orWhere('reference', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function($article) {
                return [
                    'id' => $article->id,
                    'name' => $article->designation ?? $article->reference,
                    'type' => 'article',
                    'url' => route('articles.show', $article->id),
                    'reference' => $article->reference,
                    'price' => $article->selling_price ?? $article->price_per_unit,
                    'icon' => 'package'
                ];
            });

        return response()->json([
            'clients' => $clients,
            'invoices' => $invoices,
            'quotes' => $quotes,
            'purchases' => $purchases,
            'articles' => $articles,
            'total' => $clients->count() + $invoices->count() + $quotes->count() + $purchases->count() + $articles->count()
        ]);
    }
}
