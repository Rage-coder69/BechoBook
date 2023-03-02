<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json([ 'categories' => $categories,
            'success' => true,
            'message' => 'Categories fetched successfully'
            ], 200);
    }
}
