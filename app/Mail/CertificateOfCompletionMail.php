<?php

namespace App\Mail;

use App\Models\Schedule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CertificateOfCompletionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $schedule;
    public $generalRemarks;
    public $executedBy;
    public $processedItems;
    public $signatureDataUri;

    public function __construct(Schedule $schedule, array $processedItems, ?string $generalRemarks, ?string $executedBy, ?string $signatureDataUri)
    {
        $this->schedule = $schedule;
        $this->processedItems = $processedItems;
        $this->generalRemarks = $generalRemarks;
        $this->executedBy = $executedBy;
        $this->signatureDataUri = $signatureDataUri;
    }

    public function build()
    {
        $pdf = Pdf::loadView('templates.pdf.coc-template', [
            'schedule' => $this->schedule,
            'processedItems' => $this->processedItems,
            'generalRemarks' => $this->generalRemarks,
            'executedBy' => $this->executedBy,
            'signatureDataUri' => $this->signatureDataUri,
        ]);

        $pdf->getDomPDF()->set_option('isPhpEnabled', true);
        $pdfOutput = $pdf->output();

        $clientIdentifier = $this->schedule->client_company ?? $this->schedule->scheduled_to;

        $subject = sprintf(
            'Certificate of Completion #%s - %s | %s',
            $this->schedule->schedule_number,
            $clientIdentifier,
            config('company.name')
        );

        return $this->subject($subject)
            ->view('templates.emails.certificate-of-completion')
            ->attachData($pdfOutput, 'Certificate-of-Completion-' . $this->schedule->schedule_number . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
