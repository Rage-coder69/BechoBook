<?php

namespace App\Http\Controllers;

use App\Models\Rewards;
use App\Models\User;
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
            return response()->json(['error' => $validate->errors(),
                'success' => false,
                'message' => 'Validation error'
                ], 401);
        }
        $reward = Rewards::create($request->all());
        if ($reward) {
            // update the user's total reward points
            $user = User::findOrFail($request->user_id);
            User::where('id', '=', $request->user_id)->update([
                'total_rewards' => $user->total_rewards + $request->reward_amount
            ]);
        }
        return response()->json(['reward' => $reward,
            'success' => true,
            'message' => 'Reward created successfully'
            ], 200);
    }

    public function index(){
        $rewards = Rewards::all();
        return response()->json(['rewards' => $rewards,
            'success' => true,
            'message' => 'Rewards fetched successfully'
            ], 200);
    }

    public function getUserReward(Request $request){
        $rules = [
            'user_id' => 'required',
        ];
        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors(),
                'success' => false,
                'message' => 'Validation error'
                ], 401);
        }
        $rewards = Rewards::with('user')->where('user_id', $request->user_id)->get();
        $totalRewardAmount = User::where('user_id', $request->user_id)->get('total_rewards');

        return response()->json(['reward' => $rewards, 'total_rewards' => $totalRewardAmount,
            'success' => true,
            'message' => 'Rewards fetched successfully'
            ], 200);
    }



}
