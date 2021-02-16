<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\OrderEmail;
use Illuminate\Support\Facades\Mail;

class OrderEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $details;
    public $timeout =   120;
   
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details)
    {
       $this->details   =   $details;
       
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      
        
        if($this->details['action'] ==  'order_placed')
        {
            $seller_email = new OrderEmail($this->details);
            Mail::to($this->details['seller_email'])->send($seller_email);
            $buyer_email = new OrderEmail($this->details);
            Mail::to($this->details['buyer_email'])->send($buyer_email);
        }
        elseif($this->details['action'] ==  'order_dispatched')
        {
            $buyer_email = new OrderEmail($this->details);
            Mail::to($this->details['buyer_email'])->send($buyer_email);
        }
        elseif($this->details['action'] ==  'order_delivered')
        {
            $buyer_email = new OrderEmail($this->details);
            Mail::to($this->details['buyer_email'])->send($buyer_email);
        }
        elseif($this->details['action'] ==  'order_delivery_confirmed')
        {
            $seller_email = new OrderEmail($this->details);
            Mail::to($this->details['seller_email'])->send($seller_email);
        }
        
    }
}
