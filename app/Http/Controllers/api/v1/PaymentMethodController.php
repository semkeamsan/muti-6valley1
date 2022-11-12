<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentMethodResource;
use App\Model\PaymentMethod;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $payment_methods = PaymentMethod::query()->active()->get();
        return PaymentMethodResource::collection($payment_methods);
    }
}
