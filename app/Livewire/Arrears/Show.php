<?php

namespace App\Livewire\Arrears;

use App\Domain\Arrears\Actions\SendReminderAction;
use App\Domain\Arrears\Models\Arrear;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Show extends Component
{
    public Arrear $arrear;

    #[Validate('required|in:email,sms,in_app')]
    public string $channel = 'email';

    #[Validate('nullable|string')]
    public ?string $customMessage = null;

    public function mount(int $arrearId): void
    {
        $this->arrear = Arrear::with(['tenant', 'lease.property.owner', 'rentSchedule', 'reminders'])
            ->where('id', $arrearId)
            ->first() ?? abort(404);

        $this->authorize('view', $this->arrear);
    }

    public function sendReminder(SendReminderAction $action): void
    {
        $this->authorize('manage', $this->arrear);
        $this->validate();

        $action->execute($this->arrear, $this->channel, $this->customMessage);

        session()->flash('success', "Relance enregistrée et transmise par {$this->channel}.");
        $this->customMessage = null;

        $this->arrear->load('reminders');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.arrears.show');
    }
}
