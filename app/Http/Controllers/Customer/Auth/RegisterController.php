<?php

namespace App\Http\Controllers\Customer\Auth;

use App\CPU\CartManager;
use App\CPU\Helpers;
use App\CPU\SMS_module;
use App\Http\Controllers\Controller;
use App\Model\BusinessSetting;
use App\Model\PhoneOrEmailVerification;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function App\CPU\translate;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:customer', ['except' => ['logout']]);
    }

    public function register()
    {
        session()->put('keep_return_url', url()->previous());
        return view('customer-view.auth.register');
    }

    public function submit(Request $request)
    {
        $phone = null;
        if ($request->phone['full']) {
            $phone = validate_phone($request->phone['full']);
            $request->merge(['phone_number' => $phone]);
        }

        $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'phone_number' => ['required', Rule::unique('users', 'phone')],
            'password' => 'required|min:8|same:con_password'
        ], [
            'f_name.required' => 'First name is required',
            'l_name.required' => 'Last name is required',
        ]);

        $user = User::create([
            'f_name' => $request['f_name'],
            'l_name' => $request['l_name'],
            'email' => $request['email'],
            'phone' => $phone ?? validate_phone($request->phone['full']),
            'is_active' => 1,
            'password' => bcrypt($request['password'])
        ]);

        $phone_verification = Helpers::get_business_settings('phone_verification');
        $email_verification = Helpers::get_business_settings('email_verification');
        if ($phone_verification && !$user->is_phone_verified) {
            return redirect(route('customer.auth.check', [$user->id]));
        }
        if ($email_verification && !$user->is_email_verified) {
            return redirect(route('customer.auth.check', [$user->id]));
        }

        // Toastr::success(translate('registration_success_login_now'));
        // return redirect(route('customer.auth.login'));
        auth('customer')->loginUsingId($user->id);
        Toastr::info('Welcome to ' . Helpers::get_business_settings('company_name') . '!');
        return redirect('/');
    }

    public static function check($id)
    {
        $user = User::findOrFail($id);


        // check if already verified
        if ($user->is_phone_verified == 1 || $user->is_email_verified == 1) {
            return redirect()->route('customer.auth.login');
        }

        $token = rand(100000, 999999);

        DB::table('phone_or_email_verifications')->insert([
            'phone_or_email' => $user->phone,
            'token' => $token,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return view('customer-view.auth.verify', compact('user'));
    }

    public static function verify(Request $request)
    {
        Validator::make($request->all(), [
            'token' => 'required',
        ]);

        $email_status = BusinessSetting::where('type', 'email_verification')->first()->value;
        $phone_status = BusinessSetting::where('type', 'phone_verification')->first()->value;

        $user = User::findOrFail($request->id);
        $verify = PhoneOrEmailVerification::query()
            ->where(['phone_or_email' => $user->phone]);
        if ($email_status == 1) {
            $verify->where(['token' => $request['token']]);
        }
        $verify = $verify->first();

        if ($email_status == 1 || ($email_status == 0 && $phone_status == 0)) {
            if (isset($verify)) {
                try {
                    $user->is_email_verified = 1;
                    $user->save();
                    $verify->delete();
                } catch (\Exception $exception) {
                    Toastr::info(\App\CPU\translate('Try again'));
                }
                Toastr::success(translate('verification_done_successfully'));
            } else {
                Toastr::error(translate('Verification_code_or_OTP mismatched'));
                return redirect()->back();
            }

        } else {
            if (isset($verify)) {
                try {
                    $user->is_phone_verified = 1;
                    $user->save();
                    PhoneOrEmailVerification::query()->where(['phone_or_email' => $user->phone])->delete();
                } catch (\Exception $exception) {
                    Toastr::info(\App\CPU\translate('Try again'));
                }

                Toastr::success(\App\CPU\translate('Verification Successfully Done'));
            } else {
                Toastr::error(\App\CPU\translate('Verification code/ OTP mismatched'));
            }

        }
        auth()->guard('customer')->loginUsingId($user->id);
        CartManager::cart_to_db();
        return redirect()->to('/');
        // return redirect(route('customer.auth.login'));
    }


}
