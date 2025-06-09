<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->profile->user_type === 'student') {
            $bookings = $user->studentBookings()
                ->with(['property.landlord', 'property.images'])
                ->latest()
                ->paginate(10);
        } else {
            $bookings = Booking::whereHas('property', function ($query) use ($user) {
                $query->where('landlord_id', $user->id);
            })
            ->with(['student', 'property.images'])
            ->latest()
            ->paginate(10);
        }

        return response()->json($bookings);
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load(['property.landlord', 'property.images', 'student']);

        return response()->json($booking);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'move_in_date' => 'required|date|after_or_equal:today',
            'move_out_date' => 'required|date|after:move_in_date',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $property = Property::findOrFail($validated['property_id']);

        if ($property->status !== 'active') {
            return response()->json([
                'error' => 'Property is not available for booking.'
            ], 422);
        }

        if (auth()->user()->profile->user_type !== 'student') {
            return response()->json([
                'error' => 'Only students can book properties.'
            ], 403);
        }

        $moveInDate = \Carbon\Carbon::parse($validated['move_in_date']);
        $moveOutDate = \Carbon\Carbon::parse($validated['move_out_date']);
        $leaseDurationMonths = $moveInDate->diffInMonths($moveOutDate);

        $totalAmount = $property->rent_amount * $leaseDurationMonths;

        DB::beginTransaction();
        try {
            $booking = Booking::create([
                'property_id' => $property->id,
                'student_id' => auth()->id(),
                'move_in_date' => $validated['move_in_date'],
                'move_out_date' => $validated['move_out_date'],
                'total_amount' => $totalAmount,
                'deposit_amount' => $property->deposit_amount,
                'status' => 'pending',
                'payment_status' => 'pending',
                'special_requests' => $validated['special_requests'],
            ]);

            $property->update(['status' => 'pending']);

            DB::commit();

            return response()->json($booking->load(['property', 'student']), 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Failed to create booking.'
            ], 500);
        }
    }

    public function confirm(Booking $booking)
    {
        $this->authorize('update', $booking);

        if ($booking->status !== 'pending') {
            return response()->json([
                'error' => 'Booking cannot be confirmed.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $booking->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            $booking->property->update(['status' => 'rented']);

            DB::commit();

            return response()->json($booking->load(['property', 'student']));

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Failed to confirm booking.'
            ], 500);
        }
    }

    public function cancel(Booking $booking)
    {
        $this->authorize('cancel', $booking);

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return response()->json([
                'error' => 'Booking cannot be cancelled.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            $booking->property->update(['status' => 'active']);

            DB::commit();

            return response()->json($booking->load(['property', 'student']));

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Failed to cancel booking.'
            ], 500);
        }
    }
}
