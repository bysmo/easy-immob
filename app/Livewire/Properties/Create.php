<?php

namespace App\Livewire\Properties;

use App\Application\Services\ReferenceGenerator;
use App\Domain\Agency\Models\Agency;
use App\Domain\Owner\Models\Owner;
use App\Domain\Property\Enums\PropertyStatus;
use App\Domain\Property\Models\Property;
use App\Domain\Property\Models\PropertyType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

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
    public float $rent_amount = 150000;

    #[Validate('boolean')]
    public bool $is_subject_to_irf = false;

    #[Validate('required|in:percentage,fixed')]
    public string $agency_fee_type = 'percentage';

    #[Validate('nullable|numeric|min:0')]
    public ?float $agency_fee_value = null;

    #[Validate('required')]
    public string $status = 'available';

    // Photos & Vidéos (max 10 photos, max 3 vidéos)
    public array $photos = [];
    public array $videos = [];
    public string $new_photo_url = '';
    public string $new_video_url = '';
    public $photo_file;
    public $video_file;

    public bool $hasReachedLimit = false;

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user && $user->agency) {
            $this->hasReachedLimit = $user->agency->hasReachedPropertyLimit();
        }
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

    public function save(ReferenceGenerator $generator): void
    {
        $this->authorize('create', Property::class);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->agency && $user->agency->hasReachedPropertyLimit()) {
            $this->addError('quota', 'Votre agence a atteint la limite de biens autorisés par votre forfait d\'abonnement actuel. Veuillez surclasser votre offre dans l\'espace Mon Abonnement.');
            return;
        }

        if (count($this->photos) > 10) {
            $this->addError('photos', 'Le nombre de photos est limité à 10 maximum.');
            return;
        }

        if (count($this->videos) > 3) {
            $this->addError('videos', 'Le nombre de vidéos est limité à 3 maximum.');
            return;
        }

        $this->validate();

        $reference = $generator->generate(Property::class, $user->agency_id, 'BIE');

        $property = Property::create([
            'reference'        => $reference,
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
            'rent_amount'       => $this->rent_amount,
            'is_subject_to_irf' => $this->is_subject_to_irf,
            'agency_fee_type'   => $this->agency_fee_type,
            'agency_fee_value'  => $this->agency_fee_value,
            'photos'            => array_slice(array_values(array_filter($this->photos)), 0, 10),
            'videos'           => array_slice(array_values(array_filter($this->videos)), 0, 3),
            'status'           => $this->status,
        ]);

        session()->flash('success', "Le bien {$property->title} a été créé avec la référence {$property->reference}.");

        $this->redirect(route('properties.index'), navigate: false);
    }

    public function render(): \Illuminate\View\View
    {
        $owners        = Owner::where('status', 'active')->orderBy('last_name')->get();
        $propertyTypes = PropertyType::where('status', 'active')->orderBy('name')->get();
        $statusOptions = PropertyStatus::options();

        return view('livewire.properties.create', compact('owners', 'propertyTypes', 'statusOptions'));
    }
}
