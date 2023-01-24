<?php

namespace App\Http\Controllers;

use App\Models\Ordered_products;
use App\Models\Orders;
use App\Models\Products;
use App\Models\Rewards;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrdersController extends Controller
{
    public function index()
    {
        $orders = Orders::with('user', 'orderedProduct')->get();
        return response()->json([
            'message' => 'Orders fetched successfully',
            'orders' => $orders,
            'success' => true
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
            'product_id' => 'required',
            'order_status' => 'required',
        ];
        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 401);
        }

        // check if the user has sufficient reward points to order the product
        $userRewardData = User::findOrFail($request->user_id);
        $product = Products::findOrFail($request->product_id);
        if ($userRewardData->total_rewards >= $product->product_amount) {
            // subtract the reward points from the user's reward points
            User::where('id', '=', $request->user_id)->update([
                'total_rewards' => $userRewardData->total_rewards - $product->product_amount
            ]);
        } else {
            return response()->json(['message' => 'Insufficient reward points',
                'success' => false], 401);
        }

        $order = Orders::create([
            'user_id' => $request->user_id,
            'user_name' => $request->user_name,
            'product_id' => $request->product_id,
            'street_address' => $request->street_address,
            'district' => $request->district,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'phone_number' => $request->phone_number,
            'order_status' => $request->order_status,
        ]);

        if ($order) {
            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order,
                'success' => true
            ], 200);
        } else {
            return response()->json([
                'message' => 'Order creation failed',
                'success' => false
            ], 401);
        }
    }

    public function getUserOrders(Request $request) {
        $rules = [
            'user_id' => 'required',
        ];
        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors(),
                'success' => false], 401);
        }
        $orders = Orders::with('user', 'orderedProduct')->where('user_id', $request->user_id)->get();
        return response()->json([
            'orders' => $orders,
            'success' => true
        ], 200);
    }

    public function changeStatus(Request $request)
    {
        $rules = [
            'id' => 'required',
            'order_status' => 'required',
        ];
        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 401);
        }
        $order = Orders::findOrFail($request->id);
        $order->update(['order_status' => $request->order_status]);
        return response()->json([
            'message' => 'Order status updated successfully!',
            'order' => $order,
            'success' => true
        ], 200);
    }

}
