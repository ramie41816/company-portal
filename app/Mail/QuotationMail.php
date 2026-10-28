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

    public $tempSignaturePath;

    /**
     * Create a new message instance.
     */
    public function __construct(Quotation $quotation, $tempSignaturePath = null)
    {
        $this->quotation = $quotation;
        $this->tempSignaturePath = $tempSignaturePath;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Generate the PDF content on the fly, passing the temporary signature path to the view
        $pdf = Pdf::loadView('pdf.quotation', [
            'quotation' => $this->quotation,
            'tempSignaturePath' => $this->tempSignaturePath,
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
