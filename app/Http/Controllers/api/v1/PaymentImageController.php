<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderPaymentImageResource;
use App\Model\Order;
use App\Model\PaymentImage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentImageController extends Controller
{

    public function index(Request $request)
    {
        $request->validate([
            'order_id' => ['required', Rule::exists('orders', 'id')],
        ]);

        $payment_images = $this->get_payment_images($request->order_id);

        return OrderPaymentImageResource::collection($payment_images);
    }

    public function store(Request $request)
    {

        $request->validate([
            'order_id' => ['required', Rule::exists('orders', 'id')],
            'image' => ['required'],
            'payment_method' => ['required']
        ]);

        $order = Order::query()->findOrFail($request->order_id);

        $payment_method = $request->payment_method;

        $payment_method['image'] = collect(explode('/', $payment_method['image']))->last();

        $image = collect(explode('/', $request['image']))->last();

        $file = move_image('temp/' . $image, 'payment-images/', $image);

        $order->payment_images()->create([
            'image' => $file,
            'payment_method' => $payment_method,
            'payment_method_id' => $payment_method['id'] ?? null,
        ]);

        $payment_images = $this->get_payment_images($request->order_id);

        return OrderPaymentImageResource::collection($payment_images);

    }

    private function get_payment_images($order_id)
    {
        return PaymentImage::query()
            ->with(['table_payment_method' => function ($query) {
                return $query->withTrashed();
            }])
            ->where('order_id', $order_id)
            ->get();
    }
}
