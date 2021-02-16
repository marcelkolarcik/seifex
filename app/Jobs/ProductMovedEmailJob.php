<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\ProductMovedEmail;
use Illuminate\Support\Facades\Mail;

class ProductMovedEmailJob implements ShouldQueue
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
       
        $this->details                      = $details;
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
       
            $email = new ProductMovedEmail($this->details);
            Mail::to($this->details['seller_email'])->send($email);
        
        
    }
    
    public function failed(Exception $exception)
    {
        // Send user notification of failure, etc...
       dd($exception);
    }
}
