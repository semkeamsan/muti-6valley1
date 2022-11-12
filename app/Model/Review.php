<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Review extends Model
{
    protected $casts = [
        'product_id'  => 'integer',
        'customer_id' => 'integer',
        'rating'      => 'integer',
        'status'      => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public function scopeActive($query)
    {
        $query->where('status',1);
    }
    public function user()
    {
        return $this->hasOne('App\User', 'id', 'customer_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }


    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('active', function (Builder $builder) {
            if(str_contains(url()->current(), url('/').'/admin') || str_contains(url()->current(), url('/').'/seller') || str_contains(url()->current(), url('/').'/api/v2'))
            {
                $builder;
            }else{
                $builder->where('status',1);
            }
            
        });
    }
}
