<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $table = 'books';
    protected $fillable = ['book_title', 'book_description', 'author_name', 'book_edition', 'book_publisher', 'category_id', 'location_longitude', 'location_latitude', 'location', 'book_price', 'book_selling_price', 'book_images', 'user_id', 'is_request'];

    protected $casts = [
        'book_images' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
