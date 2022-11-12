<?php

namespace App\Http\Controllers\api\v1\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ForgotPassword extends Controller
{
    public function reset_password_request(ForgotPasswordRequest $request)
    {

        DB::table('password_resets')->where('user_type', 'customer')->where('identity', 'like', "%{$request['identity']}%")->delete();

        $customer = User::query()->where('phone', $request['identity'])->first();
        if (isset($customer)) {
            $token = Str::random(120);
            DB::table('password_resets')->insert([
                'identity' => $customer['phone'],
                'token' => $token,
                'created_at' => now(),
            ]);
            return response()->json([
                'message' => 'reset password request success',
                'token' => $token
            ], 200);
        }

        return response()->json(['errors' => [
            ['code' => 'not-found', 'message' => 'user not found!']
        ]], 404);
    }

    public function reset_password_submit(ResetPasswordRequest $request)
    {

        $data = DB::table('password_resets')
            ->where('identity', $request->phone)
            ->where(['token' => $request['token']])->first();

        if (isset($data)) {
            DB::table('users')->where('phone', $data->identity)
                ->update([
                    'password' => bcrypt(str_replace(' ', '', $request['password'])),
                    'temporary_token' => null
                ]);

            DB::table('password_resets')
                ->where('identity', $request->phone)
                ->where(['token' => $request['token']])->delete();

            $user = User::query()->where('phone', $request->phone)->first();

            if ($user && $user->is_phone_verified == 0) {
                $user->update([
                    'is_phone_verified' => 1,
                    'temporary_token' => null
                ]);
            }

            return response()->json(['message' => 'Password changed successfully.'], 200);
        }
        return response()->json(['errors' => [
            ['code' => 'invalid', 'message' => 'Invalid token.']
        ]], 400);
    }
}
