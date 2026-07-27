<?php

namespace App\Livewire\Incidents;

use App\Application\Services\ReferenceGenerator;
use App\Domain\Incident\Models\Incident;
use App\Domain\Lease\Models\Lease;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    #[Validate('required|exists:leases,id')]
    public ?int $lease_id = null;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|string')]
    public string $description = '';

    #[Validate('required|in:low,medium,high,urgent')]
    public string $priority = 'medium';

    #[Validate('nullable|file|mimes:mp3,wav,m4a,webm,ogg,aac|max:20480')]
    public $audio = null;

    #[Validate(['photos.*' => 'nullable|image|max:10240'])]
    public array $photos = [];

    #[Validate(['videos.*' => 'nullable|file|mimes:mp4,mov,avi,webm|max:51200'])]
    public array $videos = [];

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isTenant() && $user->tenant) {
            $activeLease = Lease::withoutGlobalScopes()
                ->where('tenant_id', $user->tenant->id)
                ->where('status', 'active')
                ->first();

            if ($activeLease) {
                $this->lease_id = $activeLease->id;
            }
        }
    }

    public function save(ReferenceGenerator $generator): void
    {
        $this->validate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $lease = Lease::withoutGlobalScopes()->with(['property', 'tenant'])->findOrFail($this->lease_id);

        $agencyId = $lease->agency_id ?? $user->agency_id;
        $reference = $generator->generate(Incident::class, $agencyId ?? 1, 'INC');

        // Store Audio
        $audioPath = null;
        if ($this->audio) {
            $audioPath = is_object($this->audio) ? $this->audio->store('incidents/audios', 'public') : (string)$this->audio;
        }

        // Store Photos
        $photoPaths = [];
        foreach ($this->photos as $photo) {
            if (is_object($photo)) {
                $photoPaths[] = $photo->store('incidents/photos', 'public');
            } elseif (is_string($photo)) {
                $photoPaths[] = $photo;
            }
        }

        // Store Videos
        $videoPaths = [];
        foreach ($this->videos as $video) {
            if (is_object($video)) {
                $videoPaths[] = $video->store('incidents/videos', 'public');
            } elseif (is_string($video)) {
                $videoPaths[] = $video;
            }
        }

        $incident = Incident::create([
            'agency_id'   => $agencyId,
            'property_id' => $lease->property_id,
            'lease_id'    => $lease->id,
            'tenant_id'   => $lease->tenant_id,
            'reference'   => $reference,
            'title'       => $this->title,
            'description' => $this->description,
            'priority'    => $this->priority,
            'audio_path'  => $audioPath,
            'photos'      => $photoPaths,
            'videos'      => $videoPaths,
            'status'      => 'reported',
        ]);

        session()->flash('success', "Votre signalement d'incident {$incident->reference} a été transmis avec succès.");

        $this->redirect(route('incidents.show', $incident->id), navigate: false);
    }

    public function render(): \Illuminate\View\View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isTenant() && $user->tenant) {
            $leases = Lease::withoutGlobalScopes()->with('property')
                ->where('tenant_id', $user->tenant->id)
                ->get();
        } else {
            $leases = Lease::with(['property', 'tenant'])
                ->where('agency_id', $user->agency_id)
                ->where('status', 'active')
                ->get();
        }

        return view('livewire.incidents.create', compact('leases'));
    }
}
