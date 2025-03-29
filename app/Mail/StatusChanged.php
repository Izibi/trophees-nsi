<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($project, $msg = null)
    {
        $this->project = $project;
        $this->msg = $msg !== null ? '' . $msg : '';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $statusNames = [
            'finalized' => 'enregistré',
            'validated' => 'validé',
            'incomplete' => 'incomplet, modifications nécessaires'
        ];
        $msgbr = str_replace("\n", '<br>', $this->msg);
        $subject = 'Trophées NSI 2025 - Projet ' . $statusNames[$this->project->status];
        return $this->view('mails.status_change.'.$this->project->status)
                    ->text('mails.status_change.'.$this->project->status.'_plain')
                    ->subject($subject)
                    ->with([
                        'id' => $this->project->id,
                        'name' => $this->project->name,
                        'msg' => $this->msg,
                        'msgbr' => $msgbr
                    ]);
    }
}
