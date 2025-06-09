<?php

namespace App\Livewire;

use App\Models\Property;
use Livewire\Component;

class FeaturedProperties extends Component
{
    public function render()
    {
        $properties = Property::with(['landlord.profile', 'primaryImage'])
            ->where('status', 'active')
            ->where('is_verified', true)
            ->orderBy('trust_score', 'desc')
            ->limit(6)
            ->get();

        return view('livewire.featured-properties', compact('properties'));
    }
}
