<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\DynamicMail;
use Illuminate\Support\Facades\Mail;

class SendDynamicMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $subject, $content, $emailTemplate, $recipient;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($subject, $content, $emailTemplate, $recipient)
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->emailTemplate = $emailTemplate;
        $this->recipient = $recipient;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = new DynamicMail( $this->subject, $this->content, $this->emailTemplate );
        Mail::to($this->recipient)->send($email);
    }
}
