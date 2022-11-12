<?php

namespace App\Http\Controllers\api\v2\seller\auth;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Seller;
use App\Model\SellerWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function App\CPU\translate;
use Illuminate\Validation\Rule;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }


        $user_id = validate_phone($request['email']);
        if (filter_var($user_id, FILTER_VALIDATE_EMAIL)) {
            $medium = 'email';
        } else {
            $count = strlen(preg_replace("/[^\d]/", "", $user_id));
            if ($count >= 9 && $count <= 15) {
                $medium = 'phone';
            } else {
                return response()->json([
                    'errors' => ['code' => 'email', 'message' => 'Invalid email address or phone number']
                ], 403);
            }
        }

        $data = [
            $medium => $user_id,
            'password' => $request->password
        ];

        $seller = Seller::where(['email' => $user_id])->orWhere('phone', $user_id)->first();

        if ($seller) {
            if (!$seller->is_phone_verified) {
                $temporary_token = Str::random(40);
                $seller->update(['temporary_token' => $temporary_token]);
                return response()->json(['temporary_token' => $temporary_token], 200);
            }
        }

        if (isset($seller) && $seller['status'] == 'approved' && auth('seller')->attempt($data)) {
            $token = Str::random(50);
            Seller::where(['id' => auth('seller')->id()])->update(['auth_token' => $token]);
            if (SellerWallet::where('seller_id', $seller['id'])->first() == false) {
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
            }
            return response()->json(['token' => $token], 200);
        } else {
            $errors = [];
            array_push($errors, ['code' => 'auth-001', 'message' => translate('Invalid credential or account no verified yet')]);
            return response()->json([
                'errors' => $errors
            ], 401);
        }
    }

    public function verify_phone_number(Request $request)
    {
        if ($request['phone']) {
            $phone = validate_phone($request['phone']);
            $request->merge(['phone' => $phone]);
        }

        $this->validate($request, [
            'phone' => ['required', Rule::exists('sellers', 'phone')],
            'temporary_token' => ['required', Rule::exists('sellers', 'temporary_token')],
        ]);

        $seller = Seller::query()->where('phone', $request->phone)->first();

        $seller->update(['phone_verify_at' => now(), 'temporary_token' => null]);

        return response()->json([
            'success' => true,
            'message' => translate('Phone number verified successfully')
        ]);

    }
}
