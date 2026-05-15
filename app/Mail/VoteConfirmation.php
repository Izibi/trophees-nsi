<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VoteConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $confirmationLink;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($confirmationLink)
    {
        $this->confirmationLink = $confirmationLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Trophées NSI 2026 - Confirmation de votre vote pour le Prix du Public';
        return $this->view('mails.vote_confirmation')
                    ->text('mails.vote_confirmation_plain')
                    ->subject($subject)
                    ->with([
                        'confirmation_link' => $this->confirmationLink,
                    ]);
    }
}
