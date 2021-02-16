<?php

namespace App\Jobs;

use App\Mail\NewCountryRequestEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class NewCountryRequestEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $email;
    public $token;
    public $country;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email,$token,$country)
    {
        $this->email        =   $email;
        $this->token        =   $token;
        $this->country      =   $country;
        
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      
        $email = new NewCountryRequestEmail($this->email,$this->token,$this->country );
    
        Mail::to($this->email)->send($email);
    }
}
