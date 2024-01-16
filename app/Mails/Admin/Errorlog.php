<?php

namespace App\Mails\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Errorlog extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $error;
    public $filepath;

    public function __construct($error, $filepath = null)
    {
        $this->error = $error;
        $this->filepath = $filepath;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->subject('Error Message!')
        //     ->view('emails.error');

        $email = $this->view('emails.error'); // Replace 'view.name' with your actual view

        if ($this->filepath) {
            $email->attach($this->filepath, [
                'as' => 'filename.json', // You can customize the file name here
                'mime' => 'application/json',
            ]);
        }

        return $email;
    }
}
