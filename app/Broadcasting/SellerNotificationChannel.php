<?php

namespace App\Broadcasting;

use App\Seller;
use Illuminate\Http\Request;

class SellerNotificationChannel
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
    public function join(Seller $seller)
    {
      // here you can decide if seller can listen to notifications or not
        if($seller) return true;
       
    }
}
