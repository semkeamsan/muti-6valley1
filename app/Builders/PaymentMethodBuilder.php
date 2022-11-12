<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class PaymentMethodBuilder extends Builder
{
    public function active()
    {
        return $this->where('status', 1);
    }

    public function inactive()
    {
        return $this->where('status', 0);
    }
}
