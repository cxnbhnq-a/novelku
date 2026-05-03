<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        // Kita kirim raw text aja biar cepet dan ringan
        return $this->subject('Kode OTP Login NovelKu')
                    ->html("<h2>Kode OTP Anda: <b>{$this->otp}</b></h2><p>Kode ini hanya berlaku selama 5 menit. Jangan berikan kepada siapapun!</p>");
    }
}