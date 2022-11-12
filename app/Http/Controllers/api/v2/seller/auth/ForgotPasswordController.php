<?php

namespace App\Http\Controllers\api\v2\seller\auth;

use App\CPU\Helpers;
use App\CPU\SMS_module;
use App\Http\Controllers\Controller;
use App\Model\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Requests\SellerResetPasswordRequest;

use function App\CPU\translate;

class ForgotPasswordController extends Controller
{
    public function reset_password_request(Request $request)
    {
        if ($request->identity) {
            $request->merge([
                'identity' => validate_phone($request['identity'])
            ]);
        }
        $validator = Validator::make($request->all(), [
            'identity' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }


        $verification_by = Helpers::get_business_settings('forgot_password_verification');


        DB::table('password_resets')->where('user_type', 'seller')->where('identity', 'like', "%{$request['identity']}%")->delete();

        if ($verification_by == 'phone') {
            $seller = Seller::where('phone', $request['identity'])->firstOrFail();
            if (isset($seller)) {

                $token = Str::random(120);

                DB::table('password_resets')->insert([
                    'identity' => $seller['phone'],
                    'token' => $token,
                    'user_type' => 'seller',
                    'created_at' => now(),
                ]);

                return response()->json([
                    'message' => 'reset password request success',
                    'token' => $token
                ], 200);
            }
        }
        return response()->json(['errors' => [
            ['code' => 'not-found', 'message' => 'user not found!']
        ]], 404);
    }

    public function reset_password_submit(SellerResetPasswordRequest $request)
    {

        $data = DB::table('password_resets')
            ->where('identity', $request->identity)
            ->where('token', $request->token)->first();

        if (isset($data)) {

            $seller = Seller::where('phone', $request['identity'])->firstOrFail();

            $seller->update([
                'password' => bcrypt(str_replace(' ', '', $request['password'])),
                'temporary_token' => null,
            ]);

            if (!$seller->is_phone_verified) {
                $seller->update(['phone_verify_at' => now()]);
            }

            DB::table('password_resets')
                ->where('identity', $request->identity)
                ->where('token', $request->token)
                ->delete();

            return response()->json(['message' => 'Password changed successfully.'], 200);
        }
        return response()->json(['errors' => [
            ['code' => 'invalid', 'message' => 'Invalid token.']
        ]], 400);
    }
}
