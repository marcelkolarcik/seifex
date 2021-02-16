<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewCountryRequestEmail extends Mailable /*implements ShouldQueue*/
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $email;
    public $token;
    public $country;
    
    /**
     * Create a new message instance.
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
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      
      
        return $this->subject($this->country.' request')
                    ->view('emails.new_country_email');
        
    }
      
   
}
