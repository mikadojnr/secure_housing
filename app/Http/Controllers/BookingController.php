<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->user_type === 'student') {
            $bookings = $user->bookings()
                ->with(['property.landlord', 'property.images'])
                ->latest()
                ->paginate(10);
        } elseif ($user->user_type === 'landlord') {
            $bookings = Booking::whereHas('property', function ($query) use ($user) {
                $query->where('landlord_id', $user->id);
            })
            ->with(['student', 'property.images'])
            ->latest()
            ->paginate(10);
        } else {
            // Admin can see all bookings
            $bookings = Booking::with(['student', 'property.landlord', 'property.images'])
                ->latest()
                ->paginate(10);
        }

        return view('bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load(['property.landlord', 'property.images', 'student']);

        return view('bookings.show', compact('booking'));
    }

    public function create(Property $property)
    {
        if (auth()->user()->profile->user_type !== 'student') {
            return redirect()->back()->with('error', 'Only students can book properties.');
        }

        if ($property->status !== 'active') {
            return redirect()->back()->with('error', 'This property is not available for booking.');
        }

        return view('bookings.create', compact('property'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->profile->user_type !== 'student') {
            return response()->json(['error' => 'Only students can book properties.'], 403);
        }

        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'move_in_date' => 'required|date|after_or_equal:today',
            'move_out_date' => 'required|date|after:move_in_date',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $property = Property::findOrFail($validated['property_id']);

        if ($property->status !== 'active') {
            return response()->json(['error' => 'Property is not available for booking.'], 422);
        }

        // Check for conflicting bookings
        $conflictingBooking = Booking::where('property_id', $property->id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('move_in_date', [$validated['move_in_date'], $validated['move_out_date']])
                    ->orWhereBetween('move_out_date', [$validated['move_in_date'], $validated['move_out_date']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('move_in_date', '<=', $validated['move_in_date'])
                          ->where('move_out_date', '>=', $validated['move_out_date']);
                    });
            })
            ->exists();

        if ($conflictingBooking) {
            return response()->json(['error' => 'Property is not available for the selected dates.'], 422);
        }

        $moveInDate = Carbon::parse($validated['move_in_date']);
        $moveOutDate = Carbon::parse($validated['move_out_date']);
        $totalDays = $moveInDate->diffInDays($moveOutDate);
        $monthlyRent = $property->rent_amount;
        $totalAmount = ($monthlyRent / 30) * $totalDays; // Daily rate calculation

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

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json($booking->load(['property', 'student']), 201);
            }

            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Booking request submitted successfully!');

        } catch (\Exception $e) {
            DB::rollback();

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Failed to create booking.'], 500);
            }

            return redirect()->back()->with('error', 'Failed to create booking.');
        }
    }

    public function confirm(Booking $booking)
    {
        $this->authorize('update', $booking);

        if ($booking->status !== 'pending') {
            return response()->json(['error' => 'Booking cannot be confirmed.'], 422);
        }

        DB::beginTransaction();
        try {
            $booking->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            // Update property status if needed
            $booking->property->update(['status' => 'rented']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking confirmed successfully!',
                'booking' => $booking->load(['property', 'student'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to confirm booking.'], 500);
        }
    }

    public function cancel(Booking $booking)
    {
        $this->authorize('cancel', $booking);

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return response()->json(['error' => 'Booking cannot be cancelled.'], 422);
        }

        DB::beginTransaction();
        try {
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            // Make property available again
            $booking->property->update(['status' => 'active']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully!',
                'booking' => $booking->load(['property', 'student'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to cancel booking.'], 500);
        }
    }

    public function complete(Booking $booking)
    {
        $this->authorize('update', $booking);

        if ($booking->status !== 'confirmed') {
            return response()->json(['error' => 'Only confirmed bookings can be completed.'], 422);
        }

        $booking->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking marked as completed!',
            'booking' => $booking->load(['property', 'student'])
        ]);
    }
}
