<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'icon', 'sort_order', 'is_active'];

    // Relasi One-to-Many ke Product
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
