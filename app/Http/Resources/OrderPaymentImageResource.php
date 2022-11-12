<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderPaymentImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'image' => asset('storage/app/public/payment-images/' . $this->image),
            'payment_method' => new PaymentMethodResource($this->payment_method),
            'created_at' => $this->created_at
        ];
    }
}
