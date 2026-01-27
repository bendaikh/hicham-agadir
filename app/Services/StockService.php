<?php

namespace App\Services;

use App\Models\Article;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Check if there's enough stock for an article
     */
    public static function checkAvailability($articleId, $quantity)
    {
        $article = Article::find($articleId);
        
        if (!$article) {
            return ['available' => false, 'message' => 'Article not found'];
        }

        $reservedQuantity = static::getReservedQuantity($articleId);
        $availableStock = $article->stock_quantity - $reservedQuantity;

        if ($availableStock < $quantity) {
            return [
                'available' => false,
                'message' => "Stock insuffisant. Disponible: {$availableStock}, Demandé: {$quantity}",
                'available_quantity' => $availableStock
            ];
        }

        return ['available' => true];
    }

    /**
     * Check if multiple items are available
     */
    public static function checkMultipleAvailability($items)
    {
        foreach ($items as $item) {
            $check = static::checkAvailability($item['article_id'], $item['quantity']);
            if (!$check['available']) {
                return $check;
            }
        }

        return ['available' => true];
    }

    /**
     * Get total reserved quantity for an article
     */
    public static function getReservedQuantity($articleId)
    {
        return StockMovement::where('article_id', $articleId)
            ->where('movement_type', 'reserved')
            ->sum('quantity');
    }

    /**
     * Reserve stock when quote is accepted
     */
    public static function reserveStock($quoteId, $quoteItems, $quoteNumber)
    {
        return DB::transaction(function () use ($quoteId, $quoteItems, $quoteNumber) {
            foreach ($quoteItems as $item) {
                $article = Article::find($item->article_id);
                
                // Increase reserved quantity
                $article->increment('reserved_quantity', $item->quantity);

                // Record the reservation
                StockMovement::record(
                    articleId: $item->article_id,
                    quantity: $item->quantity,
                    movementType: 'reserved',
                    referenceType: 'quote',
                    referenceId: $quoteId,
                    reference: $quoteNumber,
                    notes: "Stock reserved for accepted quote {$quoteNumber}"
                );
            }

            return true;
        });
    }

    /**
     * Release reserved stock when quote is rejected or expires
     */
    public static function releaseReservedStock($quoteId, $quoteItems, $quoteNumber)
    {
        return DB::transaction(function () use ($quoteId, $quoteItems, $quoteNumber) {
            foreach ($quoteItems as $item) {
                $article = Article::find($item->article_id);
                
                // Decrease reserved quantity
                $article->decrement('reserved_quantity', $item->quantity);

                // Record the release
                StockMovement::record(
                    articleId: $item->article_id,
                    quantity: -$item->quantity, // Negative to reverse the reservation
                    movementType: 'released',
                    referenceType: 'quote',
                    referenceId: $quoteId,
                    reference: $quoteNumber,
                    notes: "Stock released from quote {$quoteNumber}"
                );
            }

            return true;
        });
    }

    /**
     * Consume reserved stock when invoice is created (from quote conversion)
     */
    public static function consumeReservedStock($quoteId, $invoiceId, $quoteNumber, $invoiceNumber)
    {
        return DB::transaction(function () use ($quoteId, $invoiceId, $quoteNumber, $invoiceNumber) {
            // Get all reserved movements for this quote
            $reservations = StockMovement::where('reference_type', 'quote')
                ->where('reference_id', $quoteId)
                ->where('movement_type', 'reserved')
                ->get();

            foreach ($reservations as $reservation) {
                $article = Article::find($reservation->article_id);
                
                // Decrease actual stock
                $article->decrement('stock_quantity', $reservation->quantity);
                
                // Decrease reserved quantity
                $article->decrement('reserved_quantity', $reservation->quantity);

                // Record the consumption
                StockMovement::record(
                    articleId: $reservation->article_id,
                    quantity: $reservation->quantity,
                    movementType: 'out',
                    referenceType: 'invoice',
                    referenceId: $invoiceId,
                    reference: $invoiceNumber,
                    notes: "Stock consumed from quote {$quoteNumber} converted to invoice {$invoiceNumber}"
                );
            }

            return true;
        });
    }

    /**
     * Direct stock decrease (for invoices created directly, not from quotes)
     */
    public static function decreaseStock($articleId, $quantity, $referenceType, $referenceId, $reference, $notes = null)
    {
        return DB::transaction(function () use ($articleId, $quantity, $referenceType, $referenceId, $reference, $notes) {
            $article = Article::find($articleId);
            
            if (!$article) {
                throw new \Exception('Article not found');
            }

            if ($article->stock_quantity < $quantity) {
                throw new \Exception("Insufficient stock for article {$article->reference}");
            }

            // Decrease stock
            $article->decrement('stock_quantity', $quantity);

            // Record the movement
            StockMovement::record(
                articleId: $articleId,
                quantity: $quantity,
                movementType: 'out',
                referenceType: $referenceType,
                referenceId: $referenceId,
                reference: $reference,
                notes: $notes
            );

            return true;
        });
    }

    /**
     * Increase stock (restocking)
     */
    public static function increaseStock($articleId, $quantity, $unitCost = null, $notes = null)
    {
        return DB::transaction(function () use ($articleId, $quantity, $unitCost, $notes) {
            $article = Article::find($articleId);
            
            if (!$article) {
                throw new \Exception('Article not found');
            }

            // Increase stock
            $article->increment('stock_quantity', $quantity);

            // Record the movement
            StockMovement::record(
                articleId: $articleId,
                quantity: $quantity,
                movementType: 'in',
                notes: $notes,
                unitCost: $unitCost
            );

            return true;
        });
    }

    /**
     * Get stock movement history for an article
     */
    public static function getMovementHistory($articleId, $limit = 50)
    {
        return StockMovement::where('article_id', $articleId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get stock alert status for an article
     */
    public static function getStockAlerts()
    {
        return Article::where('is_active', true)
            ->whereRaw('stock_quantity <= min_stock')
            ->get()
            ->map(function ($article) {
                return [
                    'article' => $article,
                    'status' => $article->stock_quantity <= 0 ? 'out_of_stock' : 'low_stock',
                    'message' => $article->stock_quantity <= 0 
                        ? "Article {$article->reference} is out of stock"
                        : "Article {$article->reference} stock is low ({$article->stock_quantity})"
                ];
            });
    }
}
