<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model {
    protected $fillable = ['name', 'category_id', 'price', 'cost_price', 'stock', 'min_stock'];
    public function category() {
        return $this->belongsTo(Category::class);
    }
}
