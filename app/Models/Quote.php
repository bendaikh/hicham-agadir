<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = ['client_id', 'quote_number', 'total_amount', 'status', 'expires_at'];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'expires_at' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }
}
