<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('invoices.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('invoices.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:invoices',
            'client_id' => 'nullable|exists:clients,id',
            'due_date' => 'nullable|date',
            'items_json' => 'required|json',
            'total_amount' => 'required|numeric|min:0.01',
            'status' => 'required|in:brouillon,envoyee,payee,annulee',
        ]);

        $items = json_decode($request->items_json, true);

        // Validate that there's at least one item
        if (empty($items)) {
            return redirect()->back()->withErrors(['items' => 'Vous devez ajouter au moins un article à la facture.'])->withInput();
        }

        $invoice = \App\Models\Invoice::create([
            'invoice_number' => $validated['invoice_number'],
            'client_id' => $validated['client_id'],
            'due_date' => $validated['due_date'],
            'total_amount' => $validated['total_amount'],
            'status' => $validated['status'],
        ]);

        foreach ($items as $item) {
            \App\Models\InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'article_id' => $item['article_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total_price'],
            ]);
        }

        return redirect()->route('invoices.show', $invoice->id)->with('success', 'Facture créée avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = \App\Models\Invoice::with(['client', 'items.article', 'payments'])->findOrFail($id);
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invoice = \App\Models\Invoice::with(['client', 'items.article'])->findOrFail($id);
        return view('invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);
        $oldStatus = $invoice->status;

        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number,' . $id,
            'client_id' => 'nullable|exists:clients,id',
            'due_date' => 'nullable|date',
            'items_json' => 'required|json',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:brouillon,envoyee,payee,annulee',
        ]);

        $items = json_decode($request->items_json, true);

        try {
            DB::beginTransaction();

            // Handle status change to 'payee' - consume reserved stock
            if ($oldStatus !== 'payee' && $validated['status'] === 'payee') {
                // Get the related quote to find the quote ID
                $quote = \App\Models\Quote::where('invoice_id', $invoice->id)->first();
                
                if ($quote) {
                    // Consume reserved stock for all items
                    foreach ($invoice->items as $item) {
                        $article = \App\Models\Article::find($item->article_id);
                        if ($article) {
                            // Decrease actual stock
                            $article->decrement('stock_quantity', $item->quantity);
                            
                            // Decrease reserved quantity
                            $article->decrement('reserved_quantity', $item->quantity);
                            
                            // Log the consumption
                            \App\Models\StockMovement::record(
                                articleId: $item->article_id,
                                quantity: $item->quantity,
                                movementType: 'out',
                                referenceType: 'invoice',
                                referenceId: $invoice->id,
                                reference: $invoice->invoice_number,
                                notes: "Stock consumed - invoice {$invoice->invoice_number} marked as paid"
                            );
                        }
                    }
                }
            }

            $invoice->update([
                'invoice_number' => $validated['invoice_number'],
                'client_id' => $validated['client_id'],
                'due_date' => $validated['due_date'],
                'total_amount' => $validated['total_amount'],
                'status' => $validated['status'],
            ]);

            // Delete existing items
            $invoice->items()->delete();

            // Create new items
            foreach ($items as $item) {
                \App\Models\InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'article_id' => $item['article_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);
            }

            DB::commit();

            return redirect()->route('invoices.show', $invoice->id)->with('success', 'Facture mise à jour avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
