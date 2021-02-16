<?php

namespace App\Http\Controllers;

use App\Services\Pusher;
use Illuminate\Http\Request;


class PusherNotificationController extends Controller
{
    public function pusher_notification(Request $request)
    {
        $action = $request->details['action'];
        
        return Pusher::$action( $request );
    }
}
