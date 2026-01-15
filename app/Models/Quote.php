<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = ['client_id', 'quote_number', 'total_amount', 'status', 'expires_at'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
