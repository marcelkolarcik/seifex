<?php

namespace App\Broadcasting;

use App\Buyer;
use Illuminate\Http\Request;

class BuyerNotificationChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
       $this->request   =   $request;
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\User  $user
     * @return array|bool
     */
    public function join(Buyer $buyer)
    {
        // here you can decide if buyer can listen to notifications or not
        if($buyer) return true;
      
    }
}
