<?php

namespace App\Http\Controllers;

use App\Models\Rewards;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RewardsController extends Controller
{
    public function store(Request $request){
        $rules = [
            'user_id' => 'required',
            'reward_type' => 'required',
            'time' => 'required',
            'reward_amount' => 'required',
            'refer_name' => 'required',
            'reward_description' => 'required',
            'option' => 'required',
        ];
        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 401);
        }
        $reward = Rewards::create($request->all());
        return response()->json(['reward' => $reward], 200);
    }

    public function index(){
        $rewards = Rewards::all();
        return response()->json(['rewards' => $rewards], 200);
    }

    public function getUserReward(Request $request){
        $rules = [
            'user_id' => 'required',
        ];
        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors()], 401);
        }
        $reward = Rewards::with('user')->where('user_id', $request->user_id)->get();
        $totalRewardAmount = Rewards::where('user_id', $request->user_id)->sum('reward_amount');
        return response()->json(['reward' => $reward, 'totalRewardAmount' => $totalRewardAmount], 200);
    }



}
