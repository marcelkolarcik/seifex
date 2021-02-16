<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\InvoiceEmail;
use Illuminate\Support\Facades\Mail;

class InvoiceEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $details;
    public $view;
    /**
     * Create a new job instance.
     *
     * @return void
     */
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
        if( $this->details->action    ==  'invoice_to_be_paid' ||  $this->details->action    ==  'invoice_payment_confirmed')
        {
            $buyer_email = new InvoiceEmail($this->details);
            Mail::to($this->details->buyer_accountant_email)->send($buyer_email);
        }
        elseif( $this->details->action    ==  'invoice_paid' )
        {
            $seller_email = new InvoiceEmail($this->details);
            Mail::to($this->details->seller_accountant_email)->send($seller_email);
        }
        
        
    }
}
