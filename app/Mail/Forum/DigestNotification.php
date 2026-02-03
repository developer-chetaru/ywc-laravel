<?php

namespace App\Mail\Forum;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Collection;

class DigestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $groupedNotifications;
    public $mode;

    public function __construct(User $user, Collection $groupedNotifications, string $mode)
    {
        $this->user = $user;
        $this->groupedNotifications = $groupedNotifications;
        $this->mode = $mode;
    }

    public function build()
    {
        $totalCount = $this->groupedNotifications->sum(fn($notifications) => $notifications->count());
        $subject = $this->mode === 'daily' 
            ? "Daily Forum Digest - {$totalCount} new notifications"
            : "Weekly Forum Digest - {$totalCount} new notifications";

        return $this->subject($subject)
            ->view('emails.forum.digest', [
                'user' => $this->user,
                'groupedNotifications' => $this->groupedNotifications,
                'mode' => $this->mode,
                'totalCount' => $totalCount,
            ]);
    }
}
