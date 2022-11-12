<?php

namespace App\Observers;

use App\User;

class UserObserver
{

    /**
     * Handle the User "created" event.
     *
     * @param \App\User $user
     * @return void
     */
    public function created(User $user)
    {
        $user->name = $user->f_name . ' ' . $user->l_name;
        $user->saveQuietly();
    }


    public function saved(User $user)
    {
        if ($user->wasChanged('f_name') || $user->wasChanged('l_name')) {
            $user->name = $user->f_name . ' ' . $user->l_name;
            $user->saveQuietly();
        }
    }


    public function saving(User $user)
    {
        if ($user->wasChanged('f_name') || $user->wasChanged('l_name')) {
            $user->name = $user->f_name . ' ' . $user->l_name;
            $user->saveQuietly();
        }
    }

    /**
     * Handle the User "updated" event.
     *
     * @param \App\User $user
     * @return void
     */
    public function updated(User $user)
    {
        if ($user->wasChanged('f_name') || $user->wasChanged('l_name')) {
            $user->name = $user->f_name . ' ' . $user->l_name;
            $user->saveQuietly();
        }
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param \App\User $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the User "restored" event.
     *
     * @param \App\User $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param \App\User $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
