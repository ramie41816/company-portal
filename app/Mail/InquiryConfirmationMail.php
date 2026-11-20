<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InquiryConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Inquiry $inquiry;

    public function __construct(Inquiry $inquiry)
    {
        $this->inquiry = $inquiry;
    }

    public function build()
    {
        $referenceId = $this->inquiry->reference_id ?? ('INQ-' . $this->inquiry->id);

        $mail = $this->subject('Inquiry Received [' . $referenceId . '] - ' . config('company.app_portal_name', 'Company Portal'))
            ->view('templates.emails.inquiry-confirmation')
            ->with([
                'inquiry' => $this->inquiry,
                'executedBy' => 'Customer Support Team',
                'generalRemarks' => $this->inquiry->notes,
            ]);

        $bcc = config('company.email');

        if (! empty($bcc)) {
            $recipients = is_array($bcc)
                ? $bcc
                : array_filter(array_map('trim', explode(',', (string) $bcc)));

            if (! empty($recipients)) {
                $mail->bcc($recipients);
            }
        }

        return $mail;
    }
}
