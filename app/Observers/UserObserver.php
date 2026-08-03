<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Wallet;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0,
        ]);
    }
}
