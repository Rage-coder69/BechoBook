<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $guarded = [];
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderedProduct()
    {
        return $this->hasOne(Products::class, 'id', 'product_id');
    }

}
