<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Transaction extends Model {
    protected $fillable = ['transaction_number', 'user_id', 'customer_name', 'table_number', 'total', 'payment', 'change', 'status', 'payment_method', 'discount'];
    public function details() {
        return $this->hasMany(TransactionDetail::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function voidLog() {
        return $this->hasOne(VoidLog::class);
    }
}
