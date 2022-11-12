<?php

namespace App\Http\Controllers\api\v2\seller\auth;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Seller;
use App\Model\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use function App\CPU\translate;

class RegisterController extends Controller
{

    public function register(Request $request)
    {
        if ($request['phone']) {
            $phone = validate_phone($request['phone']);
            $request->merge(['phone' => $phone]);
        }
        $this->validate($request, [
            'f_name' => 'required',
            'l_name' => 'required',
            'shop_name' => 'required',
            'shop_address' => 'required',
            'email' => ['required', Rule::unique('sellers', 'email')],
            'phone' => ['required', Rule::unique('sellers', 'phone')],
            'password' => 'required|min:8',
            'bank_name' => 'required',
            'holder_name' => 'required',
            'account_no' => 'required',
        ], [
            'f_name.required' => 'The first name field is required.',
            'l_name.required' => 'The last name field is required.',
        ]);

        $user = null;

        $seller = Seller::query()->where('email', $request->email)->orWhere('phone', $request->phone)->first();

        if ($seller) {
            if (!$seller->is_phone_verified) {
                $temporary_token = Str::random(40);
                $seller->update(['temporary_token' => $temporary_token]);
                return response()->json(['temporary_token' => $temporary_token], 200);
            }
        }

        DB::transaction(function ($r) use ($request, &$user) {
            $seller = new Seller();
            $seller->f_name = $request->f_name;
            $seller->l_name = $request->l_name;
            $seller->phone = $request['phone'];
            $seller->email = $request->email;
            $seller->phone_verify_at = null;

            $is_phone_verified = filter_var($request->is_phone_verified, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($is_phone_verified) {
                $seller->phone_verify_at = now();
            }

            $image = collect(explode('/', $request->image))->last();
            $file = move_image('temp/' . $image, 'seller/', $image);
            $seller->image = $file ?? null;

            $id_card_image = collect(explode('/', $request->id_card_image))->last();
            $file = move_image('temp/' . $id_card_image, 'seller/attachment/', $id_card_image);
            $seller->id_card_image = $file ?? null;

            $seller->password = bcrypt($request->password);
            $seller->status = "approved";  // pending , suspended
            $seller->bank_name = $request->bank_name;
            $seller->holder_name = $request->holder_name;
            $seller->account_no = $request->account_no;
            $seller->save();

            $shop = new Shop();
            $shop->seller_id = $seller->id;
            $shop->name = $request->shop_name;
            $shop->address = $request->shop_address;
            $shop->contact = $request['phone'];

            $logo = collect(explode('/', $request->logo))->last();
            $file = move_image('temp/' . $logo, 'shop/banner/', $logo);
            $shop->image = $file ?? null;

            $banner = collect(explode('/', $request->banner))->last();
            $file = move_image('temp/' . $banner, 'shop/banner/', $banner);
            $shop->banner = $file ?? null;

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
            $user = $seller;
        });


        if (!$user->is_phone_verified) {
            $temporary_token = Str::random(40);
            $user->update(['temporary_token' => $temporary_token]);
            return response()->json(['temporary_token' => $temporary_token], 200);
        }

        $token = Str::random(50);

        auth('seller')->loginUsingId($user->id);

        return response()->json([
            'token' => $token,
            'message' => translate('Shop apply successfully!')
        ]);

    }


    public function check_phone(Request $request)
    {
        if ($request['phone']) {
            $phone = validate_phone($request['phone']);
            $request->merge(['phone' => $phone]);
        }

        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $phone = validate_phone($request->phone);

        $user = Seller::query()
            ->where(['phone' => $phone])
            ->first();

        return response()->json([
            'exist' => isset($user),
            'key' => 'phone',
            'value' => $phone
        ]);

    }


    public function check_email(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user = Seller::query()
            ->where(['email' => $request->email])
            ->first();

        return response()->json([
            'exist' => isset($user),
            'key' => 'email',
            'value' => $request->email
        ]);

    }
}
