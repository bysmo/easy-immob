<?php

namespace App\Livewire\Admin\Plans;

use App\Domain\Subscription\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Index extends Component
{
    public bool $showModal = false;
    public ?int $editingPlanId = null;

    public string $name = '';
    public string $description = '';
    public int $max_properties = 10;
    public float $price_monthly = 25000;
    public float $price_yearly = 250000;
    public string $features_text = '';
    public bool $is_active = true;
    public bool $is_popular = false;

    public function openCreateModal(): void
    {
        $this->reset(['editingPlanId', 'name', 'description', 'features_text']);
        $this->max_properties = 10;
        $this->price_monthly = 25000;
        $this->price_yearly = 250000;
        $this->is_active = true;
        $this->is_popular = false;
        $this->showModal = true;
    }

    public function openEditModal(int $planId): void
    {
        $plan = SubscriptionPlan::findOrFail($planId);
        $this->editingPlanId = $plan->id;
        $this->name = $plan->name;
        $this->description = $plan->description ?? '';
        $this->max_properties = $plan->max_properties;
        $this->price_monthly = (float) $plan->price_monthly;
        $this->price_yearly = (float) $plan->price_yearly;
        $this->features_text = implode("\n", $plan->features ?? []);
        $this->is_active = $plan->is_active;
        $this->is_popular = $plan->is_popular;
        $this->showModal = true;
    }

    public function savePlan(): void
    {
        $this->validate([
            'name'           => 'required|string|max:255',
            'max_properties' => 'required|integer|min:1',
            'price_monthly'  => 'required|numeric|min:0',
            'price_yearly'   => 'required|numeric|min:0',
        ]);

        $features = array_filter(array_map('trim', explode("\n", $this->features_text)));

        if ($this->editingPlanId) {
            $plan = SubscriptionPlan::findOrFail($this->editingPlanId);
            $plan->update([
                'name'           => $this->name,
                'description'    => $this->description,
                'max_properties' => $this->max_properties,
                'price_monthly'  => $this->price_monthly,
                'price_yearly'   => $this->price_yearly,
                'features'       => array_values($features),
                'is_active'      => $this->is_active,
                'is_popular'     => $this->is_popular,
            ]);
            session()->flash('message', "Le forfait '{$plan->name}' a été mis à jour.");
        } else {
            $plan = SubscriptionPlan::create([
                'name'           => $this->name,
                'slug'           => Str::slug($this->name),
                'description'    => $this->description,
                'max_properties' => $this->max_properties,
                'price_monthly'  => $this->price_monthly,
                'price_yearly'   => $this->price_yearly,
                'features'       => array_values($features),
                'is_active'      => $this->is_active,
                'is_popular'     => $this->is_popular,
            ]);
            session()->flash('message', "Le nouveau forfait '{$plan->name}' a été créé.");
        }

        $this->showModal = false;
    }

    public function render(): \Illuminate\View\View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user || !$user->isSuperAdmin()) {
            abort(403, 'Accès réservé exclusivement au Super Admin SaaS.');
        }

        $plans = SubscriptionPlan::withCount('agencies')->get();
        return view('livewire.admin.plans.index', compact('plans'));
    }
}
