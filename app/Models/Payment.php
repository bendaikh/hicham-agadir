<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['payable_id', 'payable_type', 'amount', 'payment_method', 'transaction_id', 'payment_date'];

    public function payable()
    {
        return $this->morphTo();
    }
}
