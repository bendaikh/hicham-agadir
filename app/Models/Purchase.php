<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = ['supplier_id', 'total_amount', 'status', 'description', 'purchase_date'];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'purchase_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    /**
     * Calculate and update total from items
     */
    public function calculateTotal(): void
    {
        $this->total_amount = $this->items()->sum('total_price');
        $this->save();
    }

    /**
     * Update stock for all items when purchase is completed
     */
    public function updateStock(): void
    {
        foreach ($this->items as $item) {
            $item->article->addStock($item->quantity);
        }
    }
}
