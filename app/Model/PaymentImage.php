<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PaymentImage extends Model
{
    protected $guarded = ['id'];

    protected $table = 'payment_images';

    protected $casts = [
        'payment_method' => 'object'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function table_payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
