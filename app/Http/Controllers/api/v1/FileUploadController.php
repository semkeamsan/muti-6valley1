<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    public function upload_temp_image(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:5120'],  // 5MB
        ]);
        $image = $request->file('image');
        $response = upload_temp($image);
        return response()->json($response);
    }
}
