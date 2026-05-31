<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['category_id', 'name', 'description', 'price', 'type', 'image', 'is_active'];

    // Relasi balik ke Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
