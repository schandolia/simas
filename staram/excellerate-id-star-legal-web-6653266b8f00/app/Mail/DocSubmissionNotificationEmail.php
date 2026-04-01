<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DocSubmissionNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;
    private $username;
    private $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($username, $data)
    {
        $this->username = $username;
        $this->data = $data;
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
        return $this->from('admin@starlegal.id')->subject('New Request has been processed')->
            view('email.requestSubmission')->
            with('username',$username)->
            with('docData', $dataDoc);
    }
}
