<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'transaction_id',
        'payment_gateway',
        'payment_method',
        'external_id',
        'amount',
        'status',
        'paid_at',
        'expired_at',
        'raw_response'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'raw_response' => 'array'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
