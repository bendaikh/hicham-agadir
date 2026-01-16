<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Article;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'items.article'])->latest()->paginate(15);
        return view('purchases.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $articles = Article::where('is_active', true)->orderBy('designation')->get();
        return view('purchases.create', compact('suppliers', 'articles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'description' => 'nullable|string',
            'total_amount' => 'required|numeric|min:0',
            'items_json' => 'required|string',
        ]);

        $items = json_decode($request->items_json, true);
        
        if (empty($items)) {
            return back()->withErrors(['items' => 'Veuillez ajouter au moins un article.'])->withInput();
        }

        DB::transaction(function () use ($validated, $items) {
            // Create the purchase
            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'purchase_date' => $validated['purchase_date'],
                'description' => $validated['description'],
                'total_amount' => $validated['total_amount'],
                'status' => 'completed',
            ]);

            // Create purchase items and update stock
            foreach ($items as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'article_id' => $item['article_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);

                // Update article stock
                $article = Article::find($item['article_id']);
                if ($article) {
                    $article->addStock($item['quantity']);
                }
            }
        });

        return redirect()->route('purchases.index')
            ->with('success', 'Achat enregistré avec succès. Le stock a été mis à jour.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.article']);
        return view('purchases.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.article']);
        $suppliers = Supplier::orderBy('name')->get();
        $articles = Article::where('is_active', true)->orderBy('designation')->get();
        return view('purchases.edit', compact('purchase', 'suppliers', 'articles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $purchase->update($validated);

        return redirect()->route('purchases.index')
            ->with('success', 'Achat mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        // Optionally reverse stock if purchase was completed
        if ($purchase->status === 'completed') {
            foreach ($purchase->items as $item) {
                $item->article->decrement('stock_quantity', $item->quantity);
            }
        }

        $purchase->delete();

        return redirect()->route('purchases.index')
            ->with('success', 'Achat supprimé avec succès.');
    }
}
