<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
            'amenities' => 'array',
            'utilities_included' => 'array',
            'house_rules' => 'array',
        ]);

        $property = Property::create([
            ...$validated,
            'landlord_id' => auth()->id(),
            'status' => 'draft',
        ]);

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
            'amenities' => 'array',
            'utilities_included' => 'array',
            'house_rules' => 'array',
            'status' => 'in:draft,active,inactive',
        ]);

        $property->update($validated);

        return response()->json([
            'data' => $property,
            'message' => 'Property updated successfully',
        ]);
    }

    public function destroy(Property $property): JsonResponse
    {
        $this->authorize('delete', $property);

        $property->delete();

        return response()->json([
            'message' => 'Property deleted successfully',
        ]);
    }
}

