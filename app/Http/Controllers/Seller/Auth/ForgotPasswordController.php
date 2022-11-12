<?php

namespace App\Http\Controllers\Seller\Auth;

use App\Http\Controllers\Controller;
use App\Model\Seller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\CPU\Helpers;
use App\CPU\SMS_module;
use function App\CPU\translate;

class ForgotPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:seller', ['except' => ['logout']]);
    }

    public function forgot_password()
    {
        return view('seller-views.auth.forgot-password');
    }

    public function reset_password_request(Request $request)
    {
        $request->validate([
            'phone.*' => 'required',
        ], [
            'phone.*.required' => 'phone is required',
        ]);
        $phone_number = validate_phone($request['phone']['full']);
        DB::table('password_resets')->where('identity', '=', $phone_number)->delete();
        session()->put('forgot_password_identity', $phone_number);
        $seller = Seller::where('phone', '=', $phone_number)->first();
        if (isset($seller)) {
            $token = rand(100000, 999999);
            DB::table('password_resets')->insert([
                'identity' => $phone_number,
                'token' => $token,
                'created_at' => now(),
            ]);
            Toastr::success('Check your phone. Password reset otp sent.');
            return redirect()->route('seller.auth.otp-verification');
        }
        Toastr::error('No such user found!');
        return back();
    }

    public function otp_verification()
    {
        return view('seller-views.auth.verify-otp');
    }

    public function otp_verification_submit()
    {
        $identity = session('forgot_password_identity');
        $data = DB::table('password_resets')
            ->where('identity', '=', $identity)
            ->first();
        return redirect()->route('seller.auth.reset-password', ['token' => $data->token]);
    }

    public function reset_password_index(Request $request)
    {
        $data = DB::table('password_resets')->where(['token' => $request['token']])->first();
        if (isset($data)) {
            $token = $request['token'];
            return view('seller-views.auth.reset-password', compact('token'));
        }
        Toastr::error('Invalid URL.');
        return redirect('/seller/auth/login');
    }

    public function reset_password_submit(Request $request)
    {
        $request->validate([
            'password' => 'required|same:confirm_password|min:8',
        ]);

        $data = DB::table('password_resets')->where(['token' => $request['token']])->first();
        if (!isset($data)) {
            Toastr::error('Invalid URL.');
            return redirect('/seller/auth/login');
        }
        $seller = DB::table('sellers')
            ->where(['phone' => $data->identity])
            ->orWhere(['email' => $data->identity])
            ->update([
                'phone_verify_at' => now(),
                'password' => bcrypt(str_replace(' ', '', $request['confirm_password']))
            ]);

        if ($seller) {
            DB::table('password_resets')->where(['identity' => $data->identity])->delete();
            Toastr::success('Password reset successfully.');
            return redirect('/seller/auth/login');
        }
        Toastr::error('Invalid URL.');
        return redirect('/seller/auth/login');
    }
}
