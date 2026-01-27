<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        $invoice = \App\Models\Invoice::findOrFail($id);

        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number,' . $id,
            'client_id' => 'nullable|exists:clients,id',
            'due_date' => 'nullable|date',
            'items_json' => 'required|json',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:brouillon,envoyee,payee,annulee',
        ]);

        $items = json_decode($request->items_json, true);

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

        return redirect()->route('invoices.show', $invoice->id)->with('success', 'Facture mise à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
