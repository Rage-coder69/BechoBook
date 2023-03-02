<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ordered_products extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function orders()
    {
        return $this->belongsTo(Orders::class);
    }

    public function products()
    {
        return $this->belongsTo(Products::class);
    }

}
