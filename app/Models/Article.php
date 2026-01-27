<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'reference',
        'designation',
        'category',
        'dimensions',
        'thickness',
        'color',
        'type',
        'unit',
        'selling_price',
        'price_per_unit',
        'surface_area',
        'stock_quantity',
        'reserved_quantity',
        'min_stock',
        'is_active',
        'image'
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'price_per_unit' => 'decimal:2',
        'surface_area' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get the full designation with all details
     */
    public function getFullDesignationAttribute(): string
    {
        $parts = [$this->designation];
        
        if ($this->type) {
            $parts[] = $this->type;
        }
        if ($this->color) {
            $parts[] = $this->color;
        }
        if ($this->reference) {
            $parts[] = $this->reference;
        }
        if ($this->dimensions) {
            $parts[] = $this->dimensions;
        }
        if ($this->thickness) {
            $parts[] = 'Ep ' . $this->thickness;
        }
        
        return implode(' - ', $parts);
    }

    /**
     * Check if stock is low
     */
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock;
    }

    /**
     * Add stock from purchase
     */
    public function addStock(int $quantity): void
    {
        $this->increment('stock_quantity', $quantity);
    }

    /**
     * Reduce stock from sale
     */
    public function reduceStock(int $quantity): bool
    {
        if ($this->stock_quantity >= $quantity) {
            $this->decrement('stock_quantity', $quantity);
            return true;
        }
        return false;
    }

    /**
     * Get the image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    /**
     * Get available stock (stock_quantity - reserved_quantity)
     */
    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->stock_quantity - $this->reserved_quantity);
    }
}
