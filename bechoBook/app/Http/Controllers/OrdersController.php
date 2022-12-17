<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrdersController extends Controller
{
    public function index()
    {
        $orders = Orders::with('users')->get();
        return response()->json([
            'orders' => $orders
        ], 200);
    }

    public function store(Request $request)
    {
        $rules = [
            'user_id' => 'required',
            'user_name' => 'required',
            'street_address' => 'required',
            'district' => 'required',
            'state' => 'required',
            'pincode' => 'required',
            'phone_number' => 'required',
            'order_status' => 'required',
        ];
        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 401);
        }
        $order = Orders::create($request->all());
        return response()->json([
            'order' => $order
        ], 200);
    }

    public function getUserOrders(Request $request) {
        $user_id = $request->user_id;
        $orders = Orders::with('users')->where('user_id', $user_id)->get();
        return response()->json([
            'orders' => $orders
        ], 200);
    }

    public function changeStatus(Request $request)
    {
        $order = Orders::findOrFail($request->id);
        $order->update(['order_status' => $request->order_status]);
        return response()->json([
            'message' => 'Order status updated successfully!'
        ], 200);
    }

}
