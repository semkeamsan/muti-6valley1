<?php

namespace App\Http\Controllers\Customer\Auth;

use App\CPU\Helpers;
use App\CPU\SMS_module;
use App\Http\Controllers\Controller;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function App\CPU\translate;

class ForgotPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:customer', ['except' => ['logout']]);
    }


    public function reset_password()
    {
        return view('customer-view.auth.recover-password');
    }

    public function reset_password_request(Request $request)
    {
        $verification_by = Helpers::get_business_settings('forgot_password_verification');
        if ($verification_by == 'phone') {
            $request->validate([
                'phone.*' => 'required',
            ], [
                'phone.*.required' => translate('Phone is required'),
            ]);
            $phone_number = validate_phone($request['phone']['full']);
            DB::table('password_resets')->where('identity', '=', $phone_number)->delete();
            session()->put('forgot_password_identity', $phone_number);
            $customer = User::where('phone', '=', $phone_number)->first();
            if (isset($customer)) {
                $token = rand(100000, 999999);
                DB::table('password_resets')->insert([
                    'identity' => $phone_number,
                    'token' => $token,
                    'created_at' => now(),
                ]);
                Toastr::success(translate('Check your phone. Password reset otp sent.'));
                return redirect()->route('customer.auth.otp-verification');
            }
        }
        Toastr::error(translate('No such user found!'));
        return back();
    }

    public function otp_verification()
    {
        return view('customer-view.auth.verify-otp');
    }

    public function otp_verification_submit(Request $request)
    {
        $identity = session('forgot_password_identity');
        $data = DB::table('password_resets')
            ->where('identity', '=', $identity)
            ->first();
        if (!isset($data->token)) {
            Toastr::error(translate('invalid_otp'));
            return back();
        }
        return redirect()->route('customer.auth.reset-password', ['token' => $data->token]);
    }

    public function reset_password_index(Request $request)
    {
        $identity = session('forgot_password_identity');
        $data = DB::table('password_resets')
            ->where('identity', '=', $identity)
            ->first();

        if ($data) {
            return view('customer-view.auth.reset-password', ['token' => $data->token]);
        }
        Toastr::error(translate('Invalid credentials'));
        return back();
    }

    public function reset_password_submit(Request $request)
    {
        $identity = session('forgot_password_identity');

        $validator = Validator::make($request->all(), [
            'password' => 'required|same:confirm_password',
        ]);

        if ($validator->fails()) {
            Toastr::error(translate('password_mismatch'));
            return view('customer-view.auth.recover-password');
        }

        $user = User::query()->where('phone', '=', $identity)->first();
        if ($user) {
            $user->update(['password' => bcrypt(str_replace(' ', '', $request['password']))]);
            Toastr::success(translate('Password reset successfully.'));
            DB::table('password_resets')->where('identity', '=', $identity)->delete();
            session()->forget('forgot_password_identity');
            return redirect()->route('customer.auth.login');
        }
        Toastr::error(translate('Invalid data.'));
        return back();

    }

}
