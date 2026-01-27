<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('quotes.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('quotes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Prepare data - convert empty string to null for client_id
        $data = $request->all();
        if (isset($data['client_id']) && $data['client_id'] === '') {
            $data['client_id'] = null;
        }
        
        $validated = validator($data, [
            'client_id' => 'nullable|exists:clients,id',
            'quote_number' => 'required|string|unique:quotes,quote_number',
            'expires_at' => 'nullable|date',
            'status' => 'required|in:pending,accepted,rejected,expired',
            'total_amount' => 'required|numeric|min:0',
            'items_json' => 'required|string',
        ])->validate();

        $items = json_decode($request->items_json, true);
        
        if (empty($items)) {
            return back()->withErrors(['items' => 'Veuillez ajouter au moins un article.'])->withInput();
        }

        try {
            DB::beginTransaction();

            // Create the quote
            $quote = Quote::create([
                'client_id' => $validated['client_id'],
                'quote_number' => $validated['quote_number'],
                'total_amount' => $validated['total_amount'],
                'status' => $validated['status'],
                'expires_at' => $validated['expires_at'] ?? now()->addDays(30),
            ]);

            // Create quote items
            foreach ($items as $item) {
                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'article_id' => $item['article_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);
            }

            // If status is accepted, reserve stock
            if ($validated['status'] === 'accepted') {
                $quoteItems = $quote->fresh()->items;
                StockService::reserveStock($quote->id, $quoteItems, $quote->quote_number);
            }

            DB::commit();

            return redirect()->route('quotes.index')
                ->with('success', 'Devis créé avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la création: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Quote $quote)
    {
        $quote->load(['client', 'items.article']);
        return view('quotes.show', compact('quote'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quote $quote)
    {
        $quote->load(['client', 'items.article']);
        return view('quotes.edit', compact('quote'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quote $quote)
    {
        // Prepare data - convert empty string to null for client_id
        $data = $request->all();
        if (isset($data['client_id']) && $data['client_id'] === '') {
            $data['client_id'] = null;
        }
        
        $validated = validator($data, [
            'client_id' => 'nullable|exists:clients,id',
            'quote_number' => 'required|string|unique:quotes,quote_number,' . $quote->id,
            'expires_at' => 'nullable|date',
            'status' => 'required|in:pending,rejected,expired',
            'total_amount' => 'required|numeric|min:0',
            'items_json' => 'required|string',
        ])->validate();

        $items = json_decode($request->items_json, true);
        
        if (empty($items)) {
            return back()->withErrors(['items' => 'Veuillez ajouter au moins un article.'])->withInput();
        }

        try {
            DB::beginTransaction();

            $oldStatus = $quote->status;
            $newStatus = $validated['status'];

            // Update the quote
            $quote->update([
                'client_id' => $validated['client_id'],
                'quote_number' => $validated['quote_number'],
                'total_amount' => $validated['total_amount'],
                'status' => $newStatus,
                'expires_at' => $validated['expires_at'],
            ]);

            // Delete old items
            $quote->items()->delete();

            // Create new items
            foreach ($items as $item) {
                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'article_id' => $item['article_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);
            }

            DB::commit();

            return redirect()->route('quotes.show', $quote)
                ->with('success', 'Devis mis à jour avec succès!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quote $quote)
    {
        try {
            DB::beginTransaction();

            // If quote was accepted, release reserved stock
            if ($quote->status === 'accepted') {
                $quoteItems = $quote->items;
                StockService::releaseReservedStock($quote->id, $quoteItems, $quote->quote_number);
            }

            $quote->delete();

            DB::commit();

            return redirect()->route('quotes.index')
                ->with('success', 'Devis supprimé avec succès!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la suppression: ' . $e->getMessage()]);
        }
    }

    /**
     * Convert quote to invoice
     */
    public function convertToInvoice(Quote $quote)
    {
        // Can convert quotes with pending, accepted, or expired status
        if (in_array($quote->status, ['rejected'])) {
            return back()->withErrors(['error' => 'Impossible de convertir un devis refusé.']);
        }

        // Prevent converting the same quote twice
        if ($quote->invoice_id) {
            return back()->withErrors(['error' => 'Ce devis a déjà été converti en facture.']);
        }

        try {
            DB::beginTransaction();

            // Check stock availability first
            $quoteItems = $quote->items;
            $stockCheck = $quote->checkStockAvailability();
            if (!$stockCheck['available']) {
                DB::rollBack();
                return back()->withErrors(['error' => $stockCheck['message']]);
            }

            // Reserve stock if not already reserved
            if ($quote->status !== 'accepted') {
                StockService::reserveStock($quote->id, $quoteItems, $quote->quote_number);
            }

            // Generate invoice number
            $invoiceNumber = 'INV-' . str_pad(Invoice::count() + 1, 6, '0', STR_PAD_LEFT);

            // Create invoice
            $invoice = Invoice::create([
                'client_id' => $quote->client_id,
                'invoice_number' => $invoiceNumber,
                'total_amount' => $quote->total_amount,
                'status' => 'envoyee',
                'due_date' => now()->addDays(30),
            ]);

            // Copy items from quote to invoice and consume reserved stock
            $quoteItems = $quote->items;
            foreach ($quoteItems as $quoteItem) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'article_id' => $quoteItem->article_id,
                    'quantity' => $quoteItem->quantity,
                    'unit_price' => $quoteItem->unit_price,
                    'total_price' => $quoteItem->total_price,
                ]);
            }

            // Consume reserved stock (moves from 'reserved' to 'out')
            StockService::consumeReservedStock(
                $quote->id,
                $invoice->id,
                $quote->quote_number,
                $invoiceNumber
            );

            // Update quote to link with invoice
            $quote->update(['invoice_id' => $invoice->id]);

            DB::commit();

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Devis converti en facture avec succès! Stock mis à jour.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la conversion: ' . $e->getMessage()]);
        }
    }
}
