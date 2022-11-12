<?php

namespace App\Http\Controllers\Seller\Auth;

use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\PhoneOrEmailVerification;
use App\Model\Seller;
use App\Model\Shop;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\CPU\Helpers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function App\CPU\translate;

class RegisterController extends Controller
{
    public function create()
    {
        $business_mode = Helpers::get_business_settings('business_mode');
        $seller_registration = Helpers::get_business_settings('seller_registration');
        if ((isset($business_mode) && $business_mode == 'single') || (isset($seller_registration) && $seller_registration == 0)) {
            Toastr::warning(translate('access_denied!!'));
            return redirect('/');
        }
        return view('seller-views.auth.register');
    }

    public function store(Request $request)
    {
        if ($request->phone['full']) {
            $request->merge(['phone_number' => validate_phone($request->phone['full'])]);
        }

        $this->validate($request, [
            'email' => ['required', Rule::unique('sellers', 'email')],
            'phone_number' => ['required', Rule::unique('sellers', 'phone')],
            'shop_address' => 'required',
            'f_name' => 'required',
            'l_name' => 'required',
            'shop_name' => 'required',
            'password' => 'required|min:8',
        ]);

        $user = null;


        DB::transaction(function ($r) use ($request, &$user) {
            $seller = new Seller();
            $seller->f_name = $request->f_name;
            $seller->l_name = $request->l_name;
            $seller->phone = $request->phone_number;
            $seller->email = $request->email;

            $seller->phone_verify_at = null;

            $is_phone_verified = filter_var($request->is_phone_verified, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($is_phone_verified) {
                $seller->phone_verify_at = now();
            }

            $seller->image = ImageManager::upload('seller/', 'png', $request->file('image'));
            $seller->id_card_image = ImageManager::upload('seller/attachment/', 'png', $request->file('image'));
            $seller->password = bcrypt($request->password);
            $seller->status = $request->status == 'approved' ? 'approved' : "pending";
            $seller->save();

            $user = $seller;

            $shop = new Shop();
            $shop->seller_id = $seller->id;
            $shop->name = $request->shop_name;
            $shop->address = $request->shop_address;
            $shop->contact = $request->phone_number;
            $shop->image = ImageManager::upload('shop/', 'png', $request->file('logo'));
            $shop->banner = ImageManager::upload('shop/banner/', 'png', $request->file('banner'));
            $shop->save();

            DB::table('seller_wallets')->insert([
                'seller_id' => $seller['id'],
                'withdrawn' => 0,
                'commission_given' => 0,
                'total_earning' => 0,
                'pending_withdraw' => 0,
                'delivery_charge_earned' => 0,
                'collected_cash' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        });

        if (!$user->is_phone_verified) {
            return redirect(route('seller.auth.check', [$user->id]));
        }

        $seller_index_url = $request->seller_index_url ?? null;

        if ($seller_index_url) {
            Toastr::success(translate('Seller created successfully'));
            return redirect()->to($seller_index_url);
        }

        if ($user->status == 'approved' && $user->is_phone_verified) {
            if (auth('seller')->check()) {
                auth('seller')->logout();
            }
            auth('seller')->loginUsingId($user->id);
            Toastr::info('Welcome to your dashboard!');
            return redirect()->route('seller.dashboard.index');
        }
        Toastr::success('Shop apply successfully!');
        return redirect()->route('seller.auth.login');

    }


    public static function check($id)
    {
        $user = Seller::query()->findOrFail($id);
        // check if already verified
        if ($user->is_phone_verified) {
            return redirect()->route('seller.auth.login');
        }
        $token = rand(100000, 999999);

        DB::table('phone_or_email_verifications')->insert([
            'phone_or_email' => $user->phone,
            'token' => $token,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return view('seller-views.auth.verify', compact('user'));
    }

    public static function verify(Request $request)
    {
        Validator::make($request->all(), [
            'token' => 'required',
        ]);

        $user = Seller::query()->findOrFail($request->id);
        $verify = PhoneOrEmailVerification::query()
            ->where(['phone_or_email' => $user->phone])->first();

        if ($verify) {
            try {
                $user->phone_verify_at = now();
                $user->save();
                PhoneOrEmailVerification::query()->where(['phone_or_email' => $user->phone])->delete();
            } catch (\Exception $exception) {
                Toastr::info('Try again');
            }

            if (auth()->check('seller')) {
                auth('seller')->logout();
            }

            if ($user->is_phone_verified && $user->status == 'approved') {
                auth('seller')->loginUsingId($user->id);
                Toastr::info('Welcome to your dashboard!');
                return redirect()->route('seller.dashboard.index');
            }

        }
        return redirect(route('seller.auth.login'));
    }
}
