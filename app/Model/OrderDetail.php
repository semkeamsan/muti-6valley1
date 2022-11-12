<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class OrderDetail extends Model
{
    use HasRelationships;

    protected $casts = [
        'product_id' => 'integer',
        'order_id' => 'integer',
        'price' => 'float',
        'discount' => 'float',
        'qty' => 'integer',
        'tax' => 'float',
        'shipping_method_id' => 'integer',
        'seller_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'refund_request' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class)->where('status', 1);
    }

    public function active_product()
    {
        return $this->belongsTo(Product::class)->where('status', 1);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function address()
    {
        return $this->belongsTo(ShippingAddress::class, 'shipping_address');
    }

    public function payment_images()
    {
        return $this->hasManyDeepFromRelations($this->order(), (new order())->payment_images());
    }
}
