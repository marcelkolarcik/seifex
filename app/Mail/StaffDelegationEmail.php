<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class StaffDelegationEmail extends Mailable /*implements ShouldQueue*/
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $details;
    public $staff_role;
    public $token;
    
    
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details,$staff_role,$token = null)
    {
        $this->details      =   $details;
        $this->staff_role   =   $staff_role;
        $this->token        =   $token;
        
      
    }
    
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      
      if($this->token)
      {
          return $this
              ->subject('Delegation')
              ->view('emails.delegation_email');
      }
      else
      {
          return $this
              ->subject('Un-Delegation')
              ->view('emails.delegation_email');
      }
      
        
    }
      
   
}
