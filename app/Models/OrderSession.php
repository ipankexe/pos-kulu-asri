<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderSession extends Model
{
    protected $fillable = [
        'dining_table_id',
        'session_token',
        'customer_name',
        'status'
    ];

    public function diningTable()
    {
        return $this->belongsTo(DiningTable::class, 'dining_table_id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($session) {
            if (empty($session->session_token)) {
                $session->session_token = Str::random(40);
            }
        });
    }
}
