<?php

namespace App\Livewire\Inquiries;

use App\Domain\Property\Models\PropertyInquiry;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all';

    public function render(): \Illuminate\View\View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = PropertyInquiry::with(['property', 'tenant', 'user', 'latestMessage']);

        if (!$user->isAgencyAdmin() && !$user->isSuperAdmin()) {
            $query->where('user_id', $user->id);
        }

        $query->when($this->search, function ($q) {
            $q->where(function ($sub) {
                $sub->where('subject', 'like', '%' . $this->search . '%')
                    ->orWhereHas('property', fn ($p) => $p->where('title', 'like', '%' . $this->search . '%'))
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%' . $this->search . '%'));
            });
        })
        ->when($this->statusFilter !== 'all', fn ($q) => $q->where('status', $this->statusFilter));

        $inquiries = $query->orderBy('updated_at', 'desc')->paginate(10);

        return view('livewire.inquiries.index', compact('inquiries'));
    }
}
