<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DynamicMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $content;
    public $emailTemplate;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $content, $emailTemplate)
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->emailTemplate = $emailTemplate;
    }

    public function build()
    {
        return $this->view($this->emailTemplate);
    }
}
