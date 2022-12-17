<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Models\CustomModel;
use App\Models\User;

class CustomController extends Controller
{
    public function changeValues(Request $request)
    {
        $customModel = customModel::find(1);

        if ($customModel) {

            if ($request->is_enable) {
                CustomModel::where('id', 1)->update(array('is_enable' => $request->is_enable));
            }

            if ($request->title) {
                CustomModel::where('id', 1)->update(array('title' => $request->title));
            }

            if ($request->description) {
                CustomModel::where('id', 1)->update(array('description' => $request->description));
            }
            $image_name = "";
            if ($request->image) {
                //deleting the old image
                $imagePath = parse_url($customModel->image);
                File::delete(public_path($imagePath['path']));
                //saving the new updated image
                $image_name = time() . $request->file('image')->getClientOriginalName();
                //removing space from image name
                $image_name_without_space = str_replace(' ', '-', $image_name);
                $request->file('image')->move('storage/custom_images', $image_name_without_space);
                //update image path on the custom model
                CustomModel::where('id', 1)->update(array('image' => asset('storage/custom_images/' . $image_name_without_space)));
            }

            $customModel = customModel::find(1);

            $isEnable = false;

            if ($customModel->is_enable === "true") {
                $isEnable = true;
            } else {
                $isEnable = false;
            }

            $user = User::all();

            return response()->json([
                'message' => 'Values updated successfully',
                'success' => true,
                'is_enable' => $isEnable,
                'title' => $customModel->title,
                'description' => $customModel->description,
                'image' => $customModel->image,
                'users' => $user
            ]);
        }
    }
}
