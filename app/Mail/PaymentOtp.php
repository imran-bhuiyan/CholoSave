<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentOtp extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $amount;
    public $groupName;

    public function __construct($otp, $amount, $groupName)
    {
        $this->otp = $otp;
        $this->amount = $amount;
        $this->groupName = $groupName;
    }

    public function build()
    {
        return $this->subject('Payment OTP - CholoSave')
                    ->view('emails.payment-otp')
                    ->with([
                        'otp' => $this->otp,
                        'amount' => $this->amount,
                        'groupName' => $this->groupName
                    ]);
    }
} 