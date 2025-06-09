<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Property;
use App\Models\Booking;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('user_type:student')->except(['index', 'show']);
    }

    public function index()
    {
        $reviews = auth()->user()->reviews()
            ->with(['property', 'booking'])
            ->latest()
            ->paginate(10);

        return view('reviews.index', compact('reviews'));
    }

    public function create(Property $property, Booking $booking = null)
    {
        // Verify user can review this property
        if (!$booking) {
            $booking = auth()->user()->bookings()
                ->where('property_id', $property->id)
                ->where('status', 'completed')
                ->whereDoesntHave('review')
                ->first();
        }

        if (!$booking) {
            return redirect()->back()->with('error', 'You can only review properties you have stayed at.');
        }

        return view('reviews.create', compact('property', 'booking'));
    }

    public function store(Request $request, Property $property)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:2000',
            'pros' => 'nullable|array',
            'cons' => 'nullable|array',
            'is_anonymous' => 'boolean',
        ]);

        // Verify booking belongs to user and property
        $booking = auth()->user()->bookings()
            ->where('id', $validated['booking_id'])
            ->where('property_id', $property->id)
            ->where('status', 'completed')
            ->whereDoesntHave('review')
            ->firstOrFail();

        $validated['property_id'] = $property->id;
        $validated['student_id'] = auth()->id();
        $validated['is_verified'] = true; // Auto-verify since it's from a completed booking

        Review::create($validated);

        return redirect()->route('properties.show', $property)
            ->with('success', 'Review submitted successfully!');
    }

    public function show(Review $review)
    {
        $review->load(['property', 'student', 'booking']);
        return view('reviews.show', compact('review'));
    }

    public function edit(Review $review)
    {
        $this->authorize('update', $review);
        return view('reviews.edit', compact('review'));
    }

    public function update(Request $request, Review $review)
    {
        $this->authorize('update', $review);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:2000',
            'pros' => 'nullable|array',
            'cons' => 'nullable|array',
            'is_anonymous' => 'boolean',
        ]);

        $review->update($validated);

        return redirect()->route('reviews.show', $review)
            ->with('success', 'Review updated successfully!');
    }

    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);

        $review->delete();

        return redirect()->route('reviews.index')
            ->with('success', 'Review deleted successfully!');
    }
}
