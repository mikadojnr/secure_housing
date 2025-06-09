<?php

namespace App\Livewire;

use App\Models\Property;
use Livewire\Component;
use Livewire\WithPagination;

class PropertySearch extends Component
{
    use WithPagination;

    protected $layout = 'components.layouts.app';

    public $search = '';
    public $city = '';
    public $minPrice = '';
    public $maxPrice = '';
    public $propertyType = '';
    public $bedrooms = '';
    public $verifiedOnly = false;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public $university = '';
    public $maxDistance = '';
    public $userLocation = '';
    public $amenities = [];
    public $availableAmenities = [
        'wifi', 'parking', 'laundry', 'gym', 'pool', 'security', 'furnished', 'utilities_included'
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'city' => ['except' => ''],
        'minPrice' => ['except' => ''],
        'maxPrice' => ['except' => ''],
        'propertyType' => ['except' => ''],
        'bedrooms' => ['except' => ''],
        'verifiedOnly' => ['except' => false],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'university' => ['except' => ''],
        'maxDistance' => ['except' => ''],
        'amenities' => ['except' => []],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCity()
    {
        $this->resetPage();
    }

    public function updatingMinPrice()
    {
        $this->resetPage();
    }

    public function updatingMaxPrice()
    {
        $this->resetPage();
    }

    public function updatingPropertyType()
    {
        $this->resetPage();
    }

    public function updatingBedrooms()
    {
        $this->resetPage();
    }

    public function updatingVerifiedOnly()
    {
        $this->resetPage();
    }

    public function updatingUniversity()
    {
        $this->resetPage();
    }

    public function updatingMaxDistance()
    {
        $this->resetPage();
    }

    public function updatingAmenities()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $query = Property::with(['landlord.profile', 'primaryImage'])
            ->available();

        // Apply search filters
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->city) {
            $query->where('city', 'like', '%' . $this->city . '%');
        }

        if ($this->minPrice) {
            $query->where('rent_amount', '>=', $this->minPrice);
        }

        if ($this->maxPrice) {
            $query->where('rent_amount', '<=', $this->maxPrice);
        }

        if ($this->propertyType) {
            $query->where('property_type', $this->propertyType);
        }

        if ($this->bedrooms) {
            $query->where('bedrooms', '>=', $this->bedrooms);
        }

        if ($this->verifiedOnly) {
            $query->verified();
        }

        if ($this->university) {
            $query->whereHas('landlord.profile', function ($q) {
                $q->where('university', 'like', '%' . $this->university . '%');
            });
        }

        if (!empty($this->amenities)) {
            foreach ($this->amenities as $amenity) {
                $query->whereJsonContains('amenities', $amenity);
            }
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        $properties = $query->paginate(12);

        return view('livewire.property-search', [
            'properties' => $properties,
        ]);
    }
}
