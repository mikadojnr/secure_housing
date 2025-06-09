<?php

namespace App\Livewire;

use App\Models\Property;
use App\Models\PropertyImage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PropertyForm extends Component
{
    use WithFileUploads;

    public $property;
    public $title = '';
    public $description = '';
    public $propertyType = 'apartment';
    public $rentAmount = '';
    public $depositAmount = '';
    public $address = '';
    public $city = '';
    public $state = '';
    public $postalCode = '';
    public $country = 'USA';
    public $bedrooms = 1;
    public $bathrooms = 1;
    public $maxOccupants = 1;
    public $availableFrom = '';
    public $availableUntil = '';
    public $amenities = [];
    public $utilitiesIncluded = [];
    public $houseRules = [];
    public $images = [];
    public $existingImages = [];
    public $status = 'draft';

    public $amenityOptions = [
        'wifi' => 'WiFi',
        'laundry' => 'Laundry',
        'parking' => 'Parking',
        'gym' => 'Gym',
        'pool' => 'Swimming Pool',
        'ac' => 'Air Conditioning',
        'heating' => 'Heating',
        'dishwasher' => 'Dishwasher',
        'microwave' => 'Microwave',
        'furnished' => 'Furnished',
        'pet_friendly' => 'Pet Friendly',
        'balcony' => 'Balcony',
        'garden' => 'Garden',
        'security' => '24/7 Security',
    ];

    public $utilityOptions = [
        'electricity' => 'Electricity',
        'water' => 'Water',
        'gas' => 'Gas',
        'internet' => 'Internet',
        'cable' => 'Cable TV',
        'trash' => 'Trash Collection',
        'maintenance' => 'Maintenance',
    ];

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string|min:50',
        'propertyType' => 'required|in:apartment,house,room,studio,shared',
        'rentAmount' => 'required|numeric|min:0',
        'depositAmount' => 'required|numeric|min:0',
        'address' => 'required|string',
        'city' => 'required|string',
        'state' => 'required|string',
        'postalCode' => 'required|string',
        'country' => 'required|string',
        'bedrooms' => 'required|integer|min:0',
        'bathrooms' => 'required|integer|min:1',
        'maxOccupants' => 'required|integer|min:1',
        'availableFrom' => 'required|date|after_or_equal:today',
        'availableUntil' => 'nullable|date|after:availableFrom',
        'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
    ];

    public function mount($property = null)
    {
        if ($property) {
            $this->property = $property;
            $this->fill([
                'title' => $property->title,
                'description' => $property->description,
                'propertyType' => $property->property_type,
                'rentAmount' => $property->rent_amount,
                'depositAmount' => $property->deposit_amount,
                'address' => $property->address,
                'city' => $property->city,
                'state' => $property->state,
                'postalCode' => $property->postal_code,
                'country' => $property->country,
                'bedrooms' => $property->bedrooms,
                'bathrooms' => $property->bathrooms,
                'maxOccupants' => $property->max_occupants,
                'availableFrom' => $property->available_from->format('Y-m-d'),
                'availableUntil' => $property->available_until?->format('Y-m-d'),
                'amenities' => $property->amenities ?? [],
                'utilitiesIncluded' => $property->utilities_included ?? [],
                'houseRules' => $property->house_rules ?? [],
                'status' => $property->status,
            ]);
            $this->existingImages = $property->images;
        } else {
            $this->availableFrom = now()->addDays(30)->format('Y-m-d');
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'property_type' => $this->propertyType,
            'rent_amount' => $this->rentAmount,
            'deposit_amount' => $this->depositAmount,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postalCode,
            'country' => $this->country,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'max_occupants' => $this->maxOccupants,
            'available_from' => $this->availableFrom,
            'available_until' => $this->availableUntil,
            'amenities' => $this->amenities,
            'utilities_included' => $this->utilitiesIncluded,
            'house_rules' => $this->houseRules,
            'status' => $this->status,
        ];

        if ($this->property) {
            $this->property->update($data);
            $property = $this->property;
        } else {
            $property = Property::create([
                ...$data,
                'landlord_id' => Auth::id(),
            ]);
        }

        // Handle image uploads
        if ($this->images) {
            $currentImageCount = $property->images()->count();

            foreach ($this->images as $index => $image) {
                $path = $image->store('property-images', 'public');

                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path' => $path,
                    'sort_order' => $currentImageCount + $index,
                    'is_primary' => $currentImageCount === 0 && $index === 0,
                ]);
            }
        }

        session()->flash('success', $this->property ? 'Property updated successfully!' : 'Property created successfully!');

        return redirect()->route('landlord.properties');
    }

    public function removeExistingImage($imageId)
    {
        $image = PropertyImage::find($imageId);
        if ($image && $image->property_id === $this->property->id) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
            $this->existingImages = $this->property->fresh()->images;
        }
    }

    public function render()
    {
        return view('livewire.property-form');
    }
}
