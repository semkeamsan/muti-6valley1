<?php

namespace App\Model;

use App\Builders\PaymentMethodBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\CPU\Helpers;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class PaymentMethod extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                if (strpos(url()->current(), '/api')) {
                    return $query->where('locale', App::getLocale());
                } else {
                    return $query->where('locale', Helpers::default_lang());
                }
            }]);
        });
    }

    public function translations()
    {
        return $this->morphMany('App\Model\Translation', 'translationable');
    }


    public static function query(): PaymentMethodBuilder
    {
        return parent::query(); // TODO: Change the autogenerated stub
    }

    public function newEloquentBuilder($query): PaymentMethodBuilder
    {
        return new PaymentMethodBuilder($query);
    }

}
