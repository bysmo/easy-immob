<?php

namespace App\Livewire\Notifications;

use App\Domain\Notification\Models\SystemNotification;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function markAsRead(int $notificationId): void
    {
        $notification = SystemNotification::where('id', $notificationId)->firstOrFail();
        $notification->update(['status' => 'read']);
    }

    public function render(): \Illuminate\View\View
    {
        $notifications = SystemNotification::latest()
            ->paginate(20);

        return view('livewire.notifications.index', compact('notifications'));
    }
}
