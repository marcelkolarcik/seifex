<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\PriceListEmail;
use Illuminate\Support\Facades\Mail;

class PriceListEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $details;
    public $timeout = 120;
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
        $email = new PriceListEmail($this->details);
        Mail::to($this->details['buyer_owner_email'])->send($email);
    }
}
