<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RejectedDocumentRevNotivEmail extends Mailable
{
    use Queueable, SerializesModels;
    private $username;
    private $data;
    private $comment;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($username, $data, $comment)
    {
        $this->username = $username;
        $this->data = $data;
        $this->comment = $comment;
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
        
        return $this->from('admin@starlegal.id')->subject('Request Revision Rejected Notification')->
            view('email.rejectedDocsRevNotif')->
            with('username',$username)->
            with('docData', $dataDoc)->
            with('comment', $this->comment);
    }
}
