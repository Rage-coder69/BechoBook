<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email:rfc,dns|exists:users,email',
            'password' => 'required',
        ];
        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return response()->json(['error' => $validate->errors(), 'status' => 401]);
        }
        $user = User::where('email', trim($request->email))->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response(['message' => 'Invalid Login Credentials!'], 401);
        }

        $token = $user->createToken("appToken")->plainTextToken;

        $response = [
            'message' => 'Login Successful!',
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }
}
