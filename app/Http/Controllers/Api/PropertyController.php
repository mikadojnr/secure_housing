<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyImage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Property::with(['landlord', 'primaryImage', 'reviews'])
            ->available();

        // Apply filters
        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->has('min_price')) {
            $query->where('rent_amount', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('rent_amount', '<=', $request->max_price);
        }

        if ($request->has('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        if ($request->has('verified_only') && $request->verified_only) {
            $query->verified();
        }

        if ($request->has('bedrooms')) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        // Location-based search
        if ($request->has(['lat', 'lng', 'radius'])) {
            $lat = $request->lat;
            $lng = $request->lng;
            $radius = $request->radius; // in km

            $query->whereRaw(
                "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?",
                [$lat, $lng, $lat, $radius]
            );
        }

        $properties = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $properties->items(),
            'meta' => [
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
                'per_page' => $properties->perPage(),
                'total' => $properties->total(),
            ]
        ]);
    }

    public function show(Property $property): JsonResponse
    {
        $property->load(['landlord.profile', 'images', 'reviews.reviewer', 'amenities']);

        // Increment view count
        $property->increment('views_count');

        return response()->json([
            'data' => $property,
            'verification_status' => $property->landlord->getVerificationLevel(),
            'average_rating' => $property->average_rating,
            'total_reviews' => $property->reviews->count(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        // Check if user is authenticated and is a landlord
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        if (auth()->user()->profile->user_type !== 'landlord') {
            return response()->json(['error' => 'Only landlords can create properties'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'property_type' => 'required|in:apartment,house,room,studio,shared',
            'rent_amount' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'postal_code' => 'required|string',
            'country' => 'required|string',
            'bedrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:1',
            'max_occupants' => 'required|integer|min:1',
            'available_from' => 'required|date|after_or_equal:today',
            'available_until' => 'nullable|date|after:available_from',
            'amenities' => 'nullable|array',
            'utilities_included' => 'nullable|array',
            'house_rules' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['landlord_id'] = auth()->id();
        $validated['status'] = 'draft';
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

        return response()->json([
            'data' => $property,
            'message' => 'Property created successfully',
        ], 201);
    }

    public function update(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'string',
            'property_type' => 'in:apartment,house,room,studio,shared',
            'rent_amount' => 'numeric|min:0',
            'deposit_amount' => 'numeric|min:0',
            'address' => 'string',
            'city' => 'string',
            'state' => 'string',
            'postal_code' => 'string',
            'country' => 'string',
            'bedrooms' => 'integer|min:0',
            'bathrooms' => 'integer|min:1',
            'max_occupants' => 'integer|min:1',
            'available_from' => 'date|after_or_equal:today',
            'available_until' => 'nullable|date|after:available_from',
            'amenities' => 'array',
            'utilities_included' => 'array',
            'house_rules' => 'array',
            'status' => 'in:draft,active,inactive',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_images' => 'nullable|array',
        ]);

        $validated['amenities'] = $validated['amenities'] ?? $property->amenities;
        $validated['utilities_included'] = $validated['utilities_included'] ?? $property->utilities_included;
        $validated['house_rules'] = $validated['house_rules'] ?? $property->house_rules;

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

        return response()->json([
            'data' => $property,
            'message' => 'Property updated successfully',
        ]);
    }

    public function destroy(Property $property): JsonResponse
    {
        $this->authorize('delete', $property);

        // Delete associated images
        foreach ($property->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $property->delete();

        return response()->json([
            'message' => 'Property deleted successfully',
        ]);
    }
}
