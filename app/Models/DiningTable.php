<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DiningTable extends Model
{
    protected $fillable = ['name', 'status', 'qr_token', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function orderSessions()
    {
        return $this->hasMany(OrderSession::class, 'dining_table_id');
    }

    public function activeSession()
    {
        return $this->hasOne(OrderSession::class, 'dining_table_id')->where('status', 'active')->latestOfMany();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'table_number', 'name');
    }

    public function getQrUrlAttribute()
    {
        $baseUrl = config('app.url');
        if (empty($baseUrl) || $baseUrl === 'http://localhost') {
            $baseUrl = url('/');
        }
        return rtrim($baseUrl, '/') . '/order/table/' . $this->qr_token;
    }

    /**
     * Pastikan token QR terisi jika kosong
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            if (empty($table->qr_token)) {
                $table->qr_token = Str::random(32);
            }
            if (!isset($table->is_active)) {
                $table->is_active = true;
            }
        });
    }
}
