<?php

namespace App\Http\Controllers\api\v1\auth;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyTokenRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use function App\CPU\translate;

class PassportAuthController extends Controller
{
    public function register(Request $request)
    {
        if ($request->phone) {
            $phone = validate_phone($request->phone);
            $request->merge(['phone' => $phone]);
        }

        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'l_name' => 'required',
            'phone' => ['required', Rule::unique('users', 'phone')],
            'password' => 'required|min:8',
        ], [
            'f_name.required' => 'The first name field is required.',
            'l_name.required' => 'The last name field is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $temporary_token = Str::random(40);
        $user = User::create([
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => 1,
            'password' => bcrypt($request->password),
            'temporary_token' => $temporary_token,
        ]);

        $phone_verification = Helpers::get_business_settings('phone_verification');
        $email_verification = Helpers::get_business_settings('email_verification');
        if ($phone_verification && !$user->is_phone_verified) {
            return response()->json(['temporary_token' => $temporary_token], 200);
        }
        if ($email_verification && !$user->is_email_verified) {
            return response()->json(['temporary_token' => $temporary_token], 200);
        }

        $token = $user->createToken('LaravelAuthApp')->accessToken;
        return response()->json(['token' => $token], 200);
    }

    public function login(Request $request)
    {
        if ($request->email) {
            $phone = validate_phone($request->email);
            $request->merge(['email' => $phone]);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user_id = $request['email'];

        $email = $request->get('email');
        if (preg_match('/(0|\+?\d{2})(\d{7,8})/', $email, $matches)) {
            $credentials = ['phone' => $email, 'password' => $request->get('password')];
        } elseif (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $credentials = ['email' => $email, 'password' => $request->get('password')];
        } else {
            $credentials = ['username' => $email, 'password' => $request->get('password')];
        }

        $user = User::query()->where('phone', $user_id)->orWhere('email', $user_id)->first();

        if ($user && !$user->is_active) {
            return response()->json([
                'errors' => ['code' => 'auth-001', 'message' => translate('Your account is not active')]
            ], 401);
        }

        if (isset($user) && $user->is_active && auth()->attempt($credentials)) {
            $user->temporary_token = Str::random(40);
            $user->save();

            $phone_verification = Helpers::get_business_settings('phone_verification');
            $email_verification = Helpers::get_business_settings('email_verification');
            if ($phone_verification && !$user->is_phone_verified) {
                return response()->json(['temporary_token' => $user->temporary_token], 200);
            }
            if ($email_verification && !$user->is_email_verified) {
                return response()->json(['temporary_token' => $user->temporary_token], 200);
            }

            $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;
            return response()->json(['token' => $token], 200);
        }

        return response()->json([
            'errors' => [['code' => 'auth-001', 'message' => translate('Customer_not_found_or_Account_has_been_suspended')]]
        ], 401);

    }


    public function verify_token(VerifyTokenRequest $request)
    {
        $user = User::query()->where(['phone' => $request['phone'], 'temporary_token' => $request->temporary_token])->firstOrFail();
        $user->update(['is_phone_verified' => true]);

        $user->update([
            'is_phone_verified' => 1,
            'temporary_token' => null
        ]);
        auth()->loginUsingId($user->id);
        $token = $user->createToken('LaravelAuthApp')->accessToken;
        return response()->json(['token' => $token], 200);

    }
}
