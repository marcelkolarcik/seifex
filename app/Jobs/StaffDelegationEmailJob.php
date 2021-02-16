<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\StaffDelegationEmail;
use Illuminate\Support\Facades\Mail;
class StaffDelegationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $details;
    public $staff_role;
    public $staff_email;
    public $token;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details,$staff_role,$staff_email,$token = null)
    {
        $this->details       =   $details;
        $this->staff_role    =   $staff_role;
        $this->staff_email   =   $staff_email;
        $this->token         =   $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = new StaffDelegationEmail($this->details,$this->staff_role,$this->token);
        
        Mail::to($this->staff_email)->send($email);
    }
}
