<?php

namespace App\Observers;

use App\Model\Seller;

class SellerObserver
{

    /**
     * Handle the User "created" event.
     *
     * @param \App\User $seller
     * @return void
     */
    public function created(Seller $seller)
    {
        $seller->name = $seller->f_name . ' ' . $seller->l_name;
        $seller->saveQuietly();
    }


    public function saved(Seller $seller)
    {
        if ($seller->wasChanged('f_name') || $seller->wasChanged('l_name')) {
            $seller->name = $seller->f_name . ' ' . $seller->l_name;
            $seller->saveQuietly();
        }
    }


    public function saving(Seller $seller)
    {
        if ($seller->wasChanged('f_name') || $seller->wasChanged('l_name')) {
            $seller->name = $seller->f_name . ' ' . $seller->l_name;
            $seller->saveQuietly();
        }
    }

    /**
     * Handle the User "updated" event.
     *
     * @param \App\User $seller
     * @return void
     */
    public function updated(Seller $seller)
    {
        if ($seller->wasChanged('f_name') || $seller->wasChanged('l_name')) {
            $seller->name = $seller->f_name . ' ' . $seller->l_name;
            $seller->saveQuietly();
        }
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param \App\User $seller
     * @return void
     */
    public function deleted(Seller $seller)
    {
        //
    }

    /**
     * Handle the User "restored" event.
     *
     * @param \App\User $seller
     * @return void
     */
    public function restored(Seller $seller)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param \App\User $seller
     * @return void
     */
    public function forceDeleted(Seller $seller)
    {
        //
    }
}
