<?php

namespace App\Livewire\Properties;

use App\Domain\Owner\Models\Owner;
use App\Domain\Property\Enums\PropertyStatus;
use App\Domain\Property\Models\Property;
use App\Domain\Property\Models\PropertyType;
use App\Domain\Rent\Models\RentHistory;
use App\Domain\Lease\Models\Lease;
use App\Domain\Notification\Models\SystemNotification;
use App\Domain\Tenant\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public Property $property;

    #[Validate('required|exists:owners,id')]
    public ?int $owner_id = null;

    #[Validate('required|exists:property_types,id')]
    public ?int $property_type_id = null;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('nullable|string')]
    public ?string $description = null;

    #[Validate('required|string|max:255')]
    public string $address = '';

    #[Validate('required|string|max:255')]
    public string $city = '';

    #[Validate('nullable|string|max:255')]
    public ?string $neighborhood = null;

    #[Validate('nullable|numeric|between:-90,90')]
    public ?float $latitude = null;

    #[Validate('nullable|numeric|between:-180,180')]
    public ?float $longitude = null;

    #[Validate('nullable|url|max:500')]
    public ?string $google_maps_url = null;

    #[Validate('nullable|numeric|min:0')]
    public ?float $surface_area = null;

    #[Validate('nullable|integer|min:0')]
    public ?int $bedrooms = null;

    #[Validate('nullable|integer|min:0')]
    public ?int $bathrooms = null;

    #[Validate('required|numeric|min:1000')]
    public float $rent_amount = 0;

    #[Validate('required')]
    public string $status = 'available';

    // Photos & Vidéos (max 10 photos, max 3 vidéos)
    public array $photos = [];
    public array $videos = [];
    public string $new_photo_url = '';
    public string $new_video_url = '';
    public $photo_file;
    public $video_file;

    // Propriétés pour la révision / augmentation du loyer
    public bool $showIncreaseModal = false;
    public float $new_rent_amount = 0;
    public string $increase_reason = '';
    public ?string $effective_date = null;
    public bool $update_active_lease = true;

    public function mount(int $propertyId): void
    {
        $this->property = Property::with(['rentHistories.user'])->where('id', $propertyId)->first() ?? abort(404);
        $this->authorize('update', $this->property);

        $this->owner_id         = $this->property->owner_id;
        $this->property_type_id = $this->property->property_type_id;
        $this->title            = $this->property->title;
        $this->description      = $this->property->description;
        $this->address          = $this->property->address;
        $this->city             = $this->property->city;
        $this->neighborhood     = $this->property->neighborhood;
        $this->latitude         = $this->property->latitude ? (float) $this->property->latitude : null;
        $this->longitude        = $this->property->longitude ? (float) $this->property->longitude : null;
        $this->google_maps_url  = $this->property->google_maps_url;
        $this->surface_area     = $this->property->surface_area ? (float) $this->property->surface_area : null;
        $this->bedrooms         = $this->property->bedrooms;
        $this->bathrooms        = $this->property->bathrooms;
        $this->rent_amount      = (float) $this->property->rent_amount;
        $this->photos           = $this->property->photos ?? [];
        $this->videos           = $this->property->videos ?? [];
        $this->status           = $this->property->status->value;
    }

    public function addPhotoUrl(): void
    {
        if (count($this->photos) >= 10) {
            $this->addError('new_photo_url', 'Vous ne pouvez pas ajouter plus de 10 photos par bien.');
            return;
        }

        if (!empty(trim($this->new_photo_url))) {
            $this->photos[] = trim($this->new_photo_url);
            $this->new_photo_url = '';
            $this->resetErrorBag('new_photo_url');
        }
    }

    public function uploadPhotoFile(): void
    {
        if (count($this->photos) >= 10) {
            $this->addError('photo_file', 'Vous ne pouvez pas ajouter plus de 10 photos par bien.');
            return;
        }

        $this->validate([
            'photo_file' => 'required|image|max:10240', // 10Mo max
        ], [
            'photo_file.image' => 'Le fichier doit être une image valide (JPG, PNG, WEBP...).',
            'photo_file.max'   => 'L\'image ne doit pas dépasser 10 Mo.',
        ]);

        $path = $this->photo_file->store('properties/photos', 'public');
        $this->photos[] = Storage::url($path);
        $this->photo_file = null;
        $this->resetErrorBag('photo_file');
    }

    public function removePhoto(int $index): void
    {
        unset($this->photos[$index]);
        $this->photos = array_values($this->photos);
    }

    public function addVideoUrl(): void
    {
        if (count($this->videos) >= 3) {
            $this->addError('new_video_url', 'Vous ne pouvez pas ajouter plus de 3 vidéos par bien.');
            return;
        }

        if (!empty(trim($this->new_video_url))) {
            $this->videos[] = trim($this->new_video_url);
            $this->new_video_url = '';
            $this->resetErrorBag('new_video_url');
        }
    }

    public function uploadVideoFile(): void
    {
        if (count($this->videos) >= 3) {
            $this->addError('video_file', 'Vous ne pouvez pas ajouter plus de 3 vidéos par bien.');
            return;
        }

        $this->validate([
            'video_file' => 'required|file|mimes:mp4,mov,avi,webm|max:102400', // 100Mo max
        ], [
            'video_file.mimes' => 'La vidéo doit être au format MP4, MOV, AVI ou WEBM.',
            'video_file.max'   => 'La vidéo ne doit pas dépasser 100 Mo.',
        ]);

        $path = $this->video_file->store('properties/videos', 'public');
        $this->videos[] = Storage::url($path);
        $this->video_file = null;
        $this->resetErrorBag('video_file');
    }

    public function removeVideo(int $index): void
    {
        unset($this->videos[$index]);
        $this->videos = array_values($this->videos);
    }

    public function openIncreaseModal(): void
    {
        $this->new_rent_amount   = (float) $this->property->rent_amount;
        $this->increase_reason   = '';
        $this->effective_date    = now()->format('Y-m-d');
        $this->update_active_lease = true;
        $this->showIncreaseModal = true;
    }

    public function closeIncreaseModal(): void
    {
        $this->showIncreaseModal = false;
    }

    public function increaseRent(): void
    {
        $this->authorize('update', $this->property);

        $this->validate([
            'new_rent_amount' => 'required|numeric|gt:0',
            'increase_reason' => 'required|string|min:3',
            'effective_date'  => 'required|date',
        ], [
            'new_rent_amount.required' => 'Le nouveau loyer est requis.',
            'new_rent_amount.gt'       => 'Le nouveau loyer doit être supérieur à 0.',
            'increase_reason.required' => 'Le motif de la révision/augmentation est obligatoire.',
            'increase_reason.min'      => 'Le motif doit comporter au moins 3 caractères.',
        ]);

        $oldRent = (float) $this->property->rent_amount;
        $newRent = (float) $this->new_rent_amount;
        $changeAmount = $newRent - $oldRent;

        $activeLease = Lease::where('property_id', $this->property->id)
            ->where('status', 'active')
            ->first();

        RentHistory::create([
            'agency_id'       => $this->property->agency_id,
            'property_id'     => $this->property->id,
            'lease_id'        => $activeLease?->id,
            'old_rent_amount' => $oldRent,
            'new_rent_amount' => $newRent,
            'change_amount'   => $changeAmount,
            'reason'          => $this->increase_reason,
            'user_id'         => Auth::id(),
            'effective_date'  => $this->effective_date,
        ]);

        $this->property->update([
            'rent_amount' => $newRent,
        ]);
        $this->rent_amount = $newRent;

        if ($this->update_active_lease && $activeLease) {
            $activeLease->update([
                'rent_amount' => $newRent,
            ]);

            if ($activeLease->tenant_id) {
                SystemNotification::create([
                    'agency_id'      => $this->property->agency_id,
                    'recipient_type' => Tenant::class,
                    'recipient_id'   => $activeLease->tenant_id,
                    'type'           => 'rent_adjustment',
                    'channel'        => 'database',
                    'subject'        => "Révision du loyer — Bien {$this->property->title}",
                    'content'        => "Le loyer pour votre bien '{$this->property->title}' a été révisé à " . number_format($newRent, 0, ',', ' ') . " FCFA (Motif : {$this->increase_reason}). Date d'effet : {$this->effective_date}.",
                    'sent_at'        => now(),
                    'status'         => 'unread',
                ]);
            }
        }

        $this->showIncreaseModal = false;
        $this->property->load('rentHistories.user');

        session()->flash('success', "Le loyer du bien a été révisé avec succès (" . number_format($newRent, 0, ',', ' ') . " FCFA). Historique enregistré.");
    }

    public function save(): void
    {
        $this->authorize('update', $this->property);

        if (count($this->photos) > 10) {
            $this->addError('photos', 'Le nombre de photos est limité à 10 maximum.');
            return;
        }

        if (count($this->videos) > 3) {
            $this->addError('videos', 'Le nombre de vidéos est limité à 3 maximum.');
            return;
        }

        $this->validate();

        $this->property->update([
            'owner_id'         => $this->owner_id,
            'property_type_id' => $this->property_type_id,
            'title'            => $this->title,
            'description'      => $this->description,
            'address'          => $this->address,
            'city'             => $this->city,
            'neighborhood'     => $this->neighborhood,
            'latitude'         => $this->latitude,
            'longitude'        => $this->longitude,
            'google_maps_url'  => $this->google_maps_url,
            'surface_area'     => $this->surface_area,
            'bedrooms'         => $this->bedrooms,
            'bathrooms'        => $this->bathrooms,
            'rent_amount'      => $this->rent_amount,
            'photos'           => array_slice(array_values(array_filter($this->photos)), 0, 10),
            'videos'           => array_slice(array_values(array_filter($this->videos)), 0, 3),
            'status'           => $this->status,
        ]);

        session()->flash('success', "Le bien {$this->property->title} a été mis à jour.");

        $this->redirect(route('properties.index'), navigate: false);
    }

    public function render(): \Illuminate\View\View
    {
        $owners        = Owner::where('status', 'active')->orWhere('id', $this->owner_id)->orderBy('last_name')->get();
        $propertyTypes = PropertyType::where('status', 'active')->orWhere('id', $this->property_type_id)->orderBy('name')->get();
        $statusOptions = PropertyStatus::options();

        return view('livewire.properties.edit', compact('owners', 'propertyTypes', 'statusOptions'));
    }
}
