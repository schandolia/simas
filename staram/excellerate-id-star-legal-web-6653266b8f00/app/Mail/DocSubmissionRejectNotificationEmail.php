<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DocSubmissionRejectNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;
    private $username;
    private $data;
    private $notes;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($username, $data, $notes)
    {
        $this->username = $username;
        $this->data = $data;
        $this->notes = $notes;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $username = $this->username;
        $dataDoc = $this->data;
        $notes = $this->notes;
        return $this->from('admin@starlegal.id')->subject('F2 form has been rejected')->
            view('email.requestSubmissionReject')->
            with('username',$username)->
            with('docData', $dataDoc)->
            with('legalNotes', $notes);
    }
}
