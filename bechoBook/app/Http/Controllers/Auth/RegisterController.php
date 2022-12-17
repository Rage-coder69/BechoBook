<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {

        $rules = [
            'name' => 'required',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'password' => 'required',
            'phone_number' => 'required',
        ];

        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors(), 'status' => 401]);
        }

        $user = User::where('phone_number', $request->phone_number)->get();
        if (count($user) > 0) {
            return response()->json([
                'message' => 'The phone number has already been taken.',
                'success' => false
            ], 200);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->phone_number = $request->phone_number;
        if ($request->profile_picture) {
            $image_name = time() . $request->file('profile_picture')->getClientOriginalName();
            $request->file('profile_picture')->move('storage/user_profile_images', $image_name);
            $user->profile_picture = 'storage/user_profile_images/' . $image_name;
        }
        $user->profile_picture = 'storage/user_profile_images/other.jpg';
        $user->save();
        $response = [
            'message' => 'User created successfully!',
            'user' => $user,
        ];
        return response($response, 201);
    }
}
