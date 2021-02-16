<?php

namespace App\Jobs;

use App\Mail\DeliveryDaysEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class DeliveryDaysEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $details;
    
    public function __construct($details)
    {
        $this->details  =   $details;
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = new DeliveryDaysEmail($this->details);
        Mail::to($this->details['buyer_owner_email'])->send($email);
    }
}
