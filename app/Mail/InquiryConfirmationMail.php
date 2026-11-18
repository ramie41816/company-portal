<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InquiryConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $inquiry;

    public function __construct(Inquiry $inquiry)
    {
        $this->inquiry = $inquiry;
    }

    public function build()
    {
        // Map inquiry data to fit the template variables structure
        $schedule = (object) [
            'schedule_number' => 'INQ-' . $this->inquiry->id,
            'scheduled_to' => $this->inquiry->name,
            'reference_project' => $this->inquiry->service_type,
        ];

        return $this->subject('Inquiry Received - ' . config('company.app_portal_name', 'Company Portal'))
            ->view('templates.emails.inquiry-confirmation')
            ->with([
                'schedule' => $schedule,
                'executedBy' => 'Customer Support Team',
                'generalRemarks' => $this->inquiry->notes,
            ]);
    }
}
