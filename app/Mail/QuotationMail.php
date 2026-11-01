<?php

namespace App\Mail;

use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuotationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $quotation;
    public $signatureDataUri;

    public function __construct(Quotation $quotation, $signatureDataUri = null)
    {
        $this->quotation = $quotation;
        $this->signatureDataUri = $signatureDataUri;
    }

    public function build()
    {
        $pdf = Pdf::loadView('pdf.quotation-template', [
            'quotation' => $this->quotation,
            'signatureDataUri' => $this->signatureDataUri,
        ]);

        $clientIdentifier = $this->quotation->client_company ?? $this->quotation->quoted_to;

        $subject = sprintf(
            'Quotation #%s - %s | %s',
            $this->quotation->quotation_number,
            $clientIdentifier,
            config('company.name')
        );

        return $this->subject($subject)
            ->view('emails.quotation')
            ->attachData($pdf->output(), "Quotation-{$this->quotation->quotation_number}.pdf", [
                'mime' => 'application/pdf',
            ]);
    }
}
