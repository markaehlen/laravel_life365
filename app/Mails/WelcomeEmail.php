<?php

namespace App\Mails;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\EmailTemplate;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var User
     */

    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $emailTemplate = EmailTemplate::whereSlug('welcome-notification')->first(['subject', 'content']);

        $subject = $emailTemplate->subject;
        $content = $emailTemplate->content;

        $subject = str_replace('%APP_NAME%', config('app.name'), $subject);
        $content = str_replace('%USER_NAME%', $this->user->first_name, $content);
        $content = str_replace('%APP_LINK%', route('dashboard'), $content);
        $content = str_replace('%APP_NAME%', config('app.name'), $content);

        $this->subject($subject)
            ->from(config('setting.from_email'), config('setting.email_from_name'))
            ->replyTo(config('setting.from_email'))
            ->view('emails.layout')
            ->with(['template' => $content]);
    }
}
