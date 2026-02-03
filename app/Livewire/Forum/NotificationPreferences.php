<?php

namespace App\Livewire\Forum;

use App\Models\ForumNotificationPreference;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationPreferences extends Component
{
    public $preferences = [];

    public function mount()
    {
        $this->loadPreferences();
    }

    public function loadPreferences()
    {
        $user = Auth::user();
        $types = ['new_reply', 'new_thread', 'quote', 'reaction', 'best_answer', 'pm', 'moderation', 'mention'];
        
        foreach ($types as $type) {
            $preference = ForumNotificationPreference::firstOrCreate(
                ['user_id' => $user->id, 'type' => $type],
                [
                    'email_enabled' => true,
                    'on_site_enabled' => true,
                    'digest_mode' => 'none',
                ]
            );
            
            $this->preferences[$type] = [
                'id' => $preference->id,
                'email_enabled' => $preference->email_enabled,
                'on_site_enabled' => $preference->on_site_enabled,
                'digest_mode' => $preference->digest_mode,
            ];
        }
    }

    public function updatePreference($type, $field, $value)
    {
        $user = Auth::user();
        $preference = ForumNotificationPreference::where('user_id', $user->id)
            ->where('type', $type)
            ->first();
        
        if ($preference) {
            if ($field === 'digest_mode') {
                $preference->digest_mode = $value;
            } else {
                $preference->$field = $value;
            }
            $preference->save();
            
            $this->preferences[$type][$field] = $preference->$field;
            
            session()->flash('message', 'Notification preferences updated successfully.');
        }
    }

    public function render()
    {
        return view('livewire.forum.notification-preferences')->layout('layouts.app');
    }
}
