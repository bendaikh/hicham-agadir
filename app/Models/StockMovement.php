<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'article_id',
        'movement_type',
        'quantity',
        'unit_cost',
        'reference',
        'reference_type',
        'reference_id',
        'notes'
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
    ];

    /**
     * Get the article this movement belongs to
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Record a stock movement
     */
    public static function record(
        $articleId,
        $quantity,
        $movementType = 'out',
        $referenceType = null,
        $referenceId = null,
        $reference = null,
        $notes = null,
        $unitCost = null
    ) {
        return static::create([
            'article_id' => $articleId,
            'quantity' => $quantity,
            'movement_type' => $movementType,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'reference' => $reference,
            'notes' => $notes,
            'unit_cost' => $unitCost,
        ]);
    }
}
