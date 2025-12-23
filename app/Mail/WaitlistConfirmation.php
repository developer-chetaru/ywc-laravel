<?php

namespace App\Mail;

use App\Models\Waitlist;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WaitlistConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $waitlist;

    public function __construct(Waitlist $waitlist)
    {
        $this->waitlist = $waitlist;
    }

    public function build()
    {
        return $this->subject('Welcome to Yacht Workers Council Waitlist')
                    ->view('emails.waitlist-confirmation')
                    ->with([
                        'waitlist' => $this->waitlist,
                    ]);
    }
}

