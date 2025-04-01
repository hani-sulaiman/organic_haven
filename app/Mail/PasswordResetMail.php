<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;

    /**
     * Create a new message instance.
     *
     * @param string $otp
     */
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Password Reset Request')
                    ->markdown('emails.password_reset')
                    ->with([
                        'otp' => $this->otp,
                    ]);
    }
}
