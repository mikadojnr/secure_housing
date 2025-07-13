<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PropertyController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        return redirect()->route('properties.index');
    }

    public function show(Property $property)
    {
        $property->load(['landlord.profile', 'images', 'reviews.reviewer']);
        // Increment view count
        $property->increment('views_count');

        // Get similar properties
        $similarProperties = Property::where('city', $property->city)
            ->where('id', '!=', $property->id)
            ->where('status', 'active')
            ->limit(3)
            ->get();

        // Check if user has favorited this property
        $isFavorited = auth()->check() ? auth()->user()->favorites()->where('property_id', $property->id)->exists() : false;

        return view('properties.show', compact('property', 'similarProperties', 'isFavorited'));
    }

    public function create()
    {
        // Check if user is authenticated and is a landlord
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        if (auth()->user()->profile->user_type !== 'landlord') {
            abort(403, 'Only landlords can create properties.');
        }

        return view('properties.create');
    }



    public function store(Request $request)
    {
        // Check if user is authenticated and is a landlord
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        if (auth()->user()->profile->user_type !== 'landlord') {
            abort(403, 'Only landlords can create properties.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'property_type' => 'required|in:apartment,house,studio,shared_room',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'rent_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'max_occupants' => 'required|integer|min:1',
            'available_from' => 'required|date|after_or_equal:today',
            'available_until' => 'nullable|date|after:available_from',
            'amenities' => 'nullable|array',
            'utilities_included' => 'nullable|array',
            'house_rules' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['landlord_id'] = auth()->id();
        $validated['status'] = 'active';
        $validated['amenities'] = $validated['amenities'] ?? [];
        $validated['utilities_included'] = $validated['utilities_included'] ?? [];
        $validated['house_rules'] = $validated['house_rules'] ?? [];

        $property = Property::create($validated);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('properties', 'public');
                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path' => $path,
                    'alt_text' => $property->title,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('landlord.properties')
            ->with('success', 'Property created successfully!');

    }

    public function edit(Property $property)
    {
        // Check authorization
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        if (auth()->user()->profile->user_type !== 'landlord' || $property->landlord_id !== auth()->id()) {
            abort(403, 'Unauthorized to edit this property.');
        }

        $property->load('images');
        return view('properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property)
    {
        // Check authorization
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        if (auth()->user()->profile->user_type !== 'landlord' || $property->landlord_id !== auth()->id()) {
            abort(403, 'Unauthorized to update this property.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'property_type' => 'required|in:apartment,house,studio,shared_room',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'rent_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'max_occupants' => 'required|integer|min:1',
            'available_from' => 'required|date',
            'available_until' => 'nullable|date|after:available_from',
            'amenities' => 'nullable|array',
            'utilities_included' => 'nullable|array',
            'house_rules' => 'nullable|array',
            'status' => 'required|in:active,inactive,rented',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_images' => 'nullable|array',
        ]);

        $validated['amenities'] = $validated['amenities'] ?? [];
        $validated['utilities_included'] = $validated['utilities_included'] ?? [];
        $validated['house_rules'] = $validated['house_rules'] ?? [];

        $property->update($validated);

        // Remove selected images
        if ($request->has('remove_images')) {
            $imagesToRemove = PropertyImage::whereIn('id', $request->remove_images)
                ->where('property_id', $property->id)
                ->get();
            foreach ($imagesToRemove as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $existingImagesCount = $property->images()->count();
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('properties', 'public');
                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path' => $path,
                    'alt_text' => $property->title,
                    'is_primary' => $existingImagesCount === 0 && $index === 0,
                    'sort_order' => $existingImagesCount + $index,
                ]);
            }
        }

        return redirect()->route('landlord.properties')
            ->with('success', 'Property updated successfully!');
    }

    public function destroy(Property $property)
    {
        // Check authorization
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        if (auth()->user()->profile->user_type !== 'landlord' || $property->landlord_id !== auth()->id()) {
            abort(403, 'Unauthorized to delete this property.');
        }

        // Delete associated images
        foreach ($property->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $property->delete();

        return redirect()->route('landlord.properties')
            ->with('success', 'Property deleted successfully!');
    }

    public function myProperties()
    {
        // Check if user is authenticated and is a landlord
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        if (auth()->user()->profile->user_type !== 'landlord') {
            abort(403, 'Only landlords can view this page.');
        }

        $properties = auth()->user()->properties()
            ->with(['images', 'bookings'])
            ->withCount(['bookings', 'reviews'])
            ->latest()
            ->paginate(10);

        return view('landlord.properties', compact('properties'));
    }

    public function toggleFavorite(Property $property)
    {
        $user = auth()->user();
        if ($user->user_type === 'admin') {
            return response()->json(['error' => 'Admins cannot favorite properties'], 403);
        }

        $favorite = $user->favorites()->where('property_id', $property->id)->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorited = false;
            $message = 'Property removed from favorites';
        } else {
            $user->favorites()->create(['property_id' => $property->id]);
            $isFavorited = true;
            $message = 'Property added to favorites';
        }

        return response()->json([
            'success' => true,
            'is_favorited' => $isFavorited,
            'message' => $message
        ]);
    }
}
