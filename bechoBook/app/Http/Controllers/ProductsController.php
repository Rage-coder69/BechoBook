<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    public function index()
    {
        $products = Products::where('is_active', "yes")->get();
        return response()->json([
            'products' => $products,
            'success' => true,
            'message' => 'Products fetched successfully'
        ], 200);
    }

    public function store(Request $request)
    {
        $rules = [
            'product_image' => 'required',
            'product_name' => 'required',
            'product_description' => 'required',
            'product_amount' => 'required',
        ];
        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors(),
                'success' => false,
                'message' => 'Validation error'
                ], 401);
        }
        $product = new Products();

        $image_name = time() . $request->file('product_image')->getClientOriginalName();
        $request->file('product_image')->move('storage/products', $image_name);

        $product->product_image = 'storage/products/' . $image_name;
        $product->product_name = $request->product_name;
        $product->product_description = $request->product_description;
        $product->product_amount = $request->product_amount;
        $product->save();

        return response()->json([
            'product' => $product,
            'success' => true,
            'message' => 'Product created successfully'
        ], 200);
    }

    public function destroy(Request $request)
    {
        $rules = [
            'id' => 'required',
        ];
        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors(),
                'success' => false,
                'message' => 'Validation error'
                ], 401);
        }
        $product = Products::findOrFail($request->id);
        $product->is_active = 'no';
        $product->save();
        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully!'
        ], 200);
    }

}
