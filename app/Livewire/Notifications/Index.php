<?php

namespace App\Livewire\Notifications;

use App\Domain\Notification\Models\SystemNotification;
use App\Livewire\Traits\WithDataTable;
use Livewire\Component;

class Index extends Component
{
    use WithDataTable;

    public string $typeFilter = '';

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function markAsRead(int $notificationId): void
    {
        $notification = SystemNotification::where('id', $notificationId)->firstOrFail();
        $notification->update(['status' => 'read']);
    }

    public function render(): \Illuminate\View\View
    {
        $query = SystemNotification::query()
            ->when($this->search, fn ($q) => $q->where(function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('message', 'like', '%' . $this->search . '%');
            }))
            ->when($this->typeFilter, fn ($q) => $q->where('type', $this->typeFilter));

        $notifications = $this->applySorting($query, 'created_at', 'desc')->paginate($this->perPage);

        return view('livewire.notifications.index', compact('notifications'));
    }
}
