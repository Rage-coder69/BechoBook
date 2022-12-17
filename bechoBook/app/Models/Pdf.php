<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pdf extends Model
{
    use HasFactory;
    protected $table = 'pdfs';

    protected $fillable = ['name', 'pdf_document', 'pdf_image', 'pdf_description', 'category_id'];
}
