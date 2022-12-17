<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;

class UserController extends Controller
{

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json(['user' => $user], 200);
    }

    /*public function changeURL() {
        $users = User::all();
        foreach ($users as $user) {
            $userImage = $user->profile_picture;
            $userImage = str_replace("http://bookbecho.lazyguider.in/", "", $userImage);
             $user->profile_picture = $userImage;
            $user->save();
        }
        return response()->json(['message' => 'success'], 200);
    }*/

    public function update(Request $request)
    {
        $user = User::findOrFail($request->id);

        if ($user) {
            if ($request->profile_picture) {
                //deleting the old image
                $imagePath = parse_url($user->profile_picture);
                File::delete(public_path($imagePath['path']));
                //saving the new updated image
                $image_name = time() . $request->file('profile_picture')->getClientOriginalName();
                $request->file('profile_picture')->move('storage/user_profile_images', $image_name);
                //update image path on the user model
                $user->profile_picture = 'storage/user_profile_images/' . $image_name;
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone_number = $request->phone_number;
            $user->save();
            $response = [
                'message' => 'User updated successfully!',
                'user' => $user,
            ];
            return response($response, 201);
        } else {
            return response()->json(['error' => 'User not found!'], 404);
        }
    }

    public function getUserByPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required',
        ]);

        // $user = User::findOrFail($request->phone);
        $user = User::where('phone_number', $request->phone)->get();

        if (count($user) > 0) {

            $token = $user[0]->createToken("appToken")->plainTextToken;

            return response()->json([
                'data' => $user[0],
                'message' => 'User found successfully',
                'success' => true,
                'token' => $token
            ], 200);
        }

        return response()->json([
            'message' => 'User not found',
            'success' => false,
        ], 200);
    }

    public function userBooks(Request $request): \Illuminate\Http\JsonResponse
    {
//        return response()->json(['user' => $user, 'found' => $user[0]->books_count ], 200);
        if($request->filled('page') && !empty($request->filled('page'))){
            $user = User::with('books.category')->where('id', $request->id)->withCount('books')->paginate($request->page);
            if($request->page >= 1 && $request->page <= $user->lastPage()) {
                $user = User::with('books.category')->where('id', $request->id)->withCount('books')->paginate($request->page);
                return response()->json(['user' => $user, 'pages' => $user->lastPage(),'found' => count($user->items())], 200);
            }
            else{
                return response()->json(['error' => 'Page number does not exist!'], 400);
            }
        }else {
            $user = User::with('books.category')->where('id', $request->id)->withCount('books')->get();
            return response()->json(['user' => $user], 200);
        }
    }
}
