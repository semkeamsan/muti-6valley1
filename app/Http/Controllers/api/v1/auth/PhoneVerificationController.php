<?php

namespace App\Http\Controllers\api\v1\auth;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use function App\CPU\translate;

class PhoneVerificationController extends Controller
{

    public function verify_phone(Request $request)
    {
        if ($request->phone) {
            $phone = validate_phone($request->phone);
            $request->merge(['phone' => $phone]);
        }
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'temporary_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user = User::query()->where([
            'temporary_token' => $request['temporary_token'],
            'phone' => $request->phone,
        ])->first();

        $user->phone = $request['phone'];
        $user->is_phone_verified = 1;
        $user->save();

        $token = $user->createToken('LaravelAuthApp')->accessToken;
        return response()->json([
            'message' => translate('otp_verified'),
            'token' => $token
        ], 200);

    }
}
