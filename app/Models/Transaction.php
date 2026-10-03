<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_number',
        'user_id',
        'customer_name',
        'table_number',
        'order_source',
        'order_status',
        'payment_status',
        'total',
        'payment',
        'change',
        'status',
        'payment_method',
        'payment_gateway',
        'payment_reference',
        'external_payment_id',
        'discount'
    ];

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function voidLog()
    {
        return $this->hasOne(VoidLog::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function diningTable()
    {
        return $this->belongsTo(DiningTable::class, 'table_number', 'name');
    }

    public function scopePos($query)
    {
        return $query->where('order_source', 'pos');
    }

    public function scopeQr($query)
    {
        return $query->where('order_source', 'qr');
    }

    public function isQr(): bool
    {
        return $this->order_source === 'qr';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid' || $this->payment_status === 'paid';
    }
}
