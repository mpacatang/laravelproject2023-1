<?php

namespace App\Mail;


use App\Models\BusinessSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class CustomerEmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        $verify_url = route('customer.profile.verify_email', ['token' => $this->token]);
        return $this->view('mails.customer_email_verification')->with([
            'verify_url' => $verify_url
        ]);
    }
}
