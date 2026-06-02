<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class VoidLog extends Model {
    protected $fillable = ['transaction_id', 'reason', 'void_by'];
    public function transaction() {
        return $this->belongsTo(Transaction::class);
    }
}
