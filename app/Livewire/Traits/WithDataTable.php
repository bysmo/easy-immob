<?php

namespace App\Livewire\Traits;

use Livewire\WithPagination;

trait WithDataTable
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 15;
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function applySorting($query, string $defaultField = 'created_at', string $defaultDirection = 'desc')
    {
        $field = $this->sortField ?: $defaultField;
        $direction = in_array(strtolower($this->sortDirection), ['asc', 'desc']) ? $this->sortDirection : $defaultDirection;

        return $query->orderBy($field, $direction);
    }
}
