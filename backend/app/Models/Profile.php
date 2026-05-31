<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = ['user_id', 'name', 'phone', 'role', 'is_active'];

    // Relasi balik ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
