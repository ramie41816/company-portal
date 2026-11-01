<?php

namespace App\Mail;

use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CertificateOfCompletionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $quotation;
    public $generalRemarks;
    public $executedBy;
    public $processedItems;
    public $signatureDataUri;

    public function __construct(Quotation $quotation, array $processedItems, ?string $generalRemarks, ?string $executedBy, ?string $signatureDataUri)
    {
        $this->quotation = $quotation;
        $this->processedItems = $processedItems;
        $this->generalRemarks = $generalRemarks;
        $this->executedBy = $executedBy;
        $this->signatureDataUri = $signatureDataUri;
    }

    public function build()
    {
        $pdf = Pdf::loadView('templates.pdf.coc-template', [
            'quotation' => $this->quotation,
            'processedItems' => $this->processedItems,
            'generalRemarks' => $this->generalRemarks,
            'executedBy' => $this->executedBy,
            'signatureDataUri' => $this->signatureDataUri,
        ]);

        $pdf->getDomPDF()->set_option('isPhpEnabled', true);
        $pdfOutput = $pdf->output();

        $clientIdentifier = $this->quotation->client_company ?? $this->quotation->quoted_to;

        $subject = sprintf(
            'Certificate of Completion #%s - %s | %s',
            $this->quotation->quotation_number,
            $clientIdentifier,
            config('company.name')
        );

        return $this->subject($subject)
            ->view('templates.emails.certificate-of-completion')
            ->attachData($pdfOutput, 'Certificate-of-Completion-' . $this->quotation->quotation_number . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
