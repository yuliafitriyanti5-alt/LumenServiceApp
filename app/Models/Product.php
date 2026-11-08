<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'price',
        'stock',
        'category',
        'description',
        'status',
    ];

    // Timestamps aktif secara default di Eloquent,
    // sehingga created_at dan updated_at akan terisi otomatis.
}
