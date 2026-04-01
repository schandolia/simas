<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ShareDocNotifEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $dataDoc = $this->data['sharedDocs'];
        $username = $this->data['name'];
        return $this->from('admin@starlegal.id')->subject('File Expiration Notification ')->
            view('email.expiredFileNotif')->
            with('username',$username)->
            with('docData', $dataDoc);
    }
}
