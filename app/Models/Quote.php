<?php

namespace App\Models;

use App\Services\StockService;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = ['client_id', 'quote_number', 'total_amount', 'status', 'expires_at', 'invoice_id'];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'expires_at' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    /**
     * Check if stock is available for this quote
     */
    public function checkStockAvailability()
    {
        $items = $this->items->map(fn ($item) => [
            'article_id' => $item->article_id,
            'quantity' => $item->quantity
        ])->toArray();

        return StockService::checkMultipleAvailability($items);
    }

    /**
     * Get all articles that have low stock
     */
    public function getLowStockItems()
    {
        return $this->items()
            ->with('article')
            ->get()
            ->filter(function ($item) {
                $reserved = StockService::getReservedQuantity($item->article_id);
                return $item->article->stock_quantity - $reserved < $item->quantity;
            });
    }
}
