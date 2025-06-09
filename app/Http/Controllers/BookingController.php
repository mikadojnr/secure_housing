<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Property;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\BookingCreated;
use App\Notifications\BookingConfirmed;
use App\Notifications\BookingCancelled;
use App\Notifications\BookingCompleted;

class BookingController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

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
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check if user can view this booking
        $user = auth()->user();
        if ($user->user_type === 'student' && $booking->student_id !== $user->id) {
            abort(403, 'You can only view your own bookings.');
        } elseif ($user->user_type === 'landlord' && $booking->property->landlord_id !== $user->id) {
            abort(403, 'You can only view bookings for your properties.');
        }

        $booking->load(['property.landlord', 'property.images', 'student']);

        return view('bookings.show', compact('booking'));
    }

    public function create(Property $property)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->user_type !== 'student') {
            return redirect()->back()->with('error', 'Only students can book properties.');
        }

        if ($property->status !== 'active') {
            return redirect()->back()->with('error', 'This property is not available for booking.');
        }

        return view('bookings.create', compact('property'));
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->user_type !== 'student') {
            return redirect()->back()->with('error', 'Only students can book properties.');
        }

        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'move_in_date' => 'required|date|after_or_equal:today',
            'move_out_date' => 'required|date|after:move_in_date',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $property = Property::findOrFail($validated['property_id']);

        if ($property->status !== 'active') {
            return redirect()->back()->with('error', 'Property is not available for booking.');
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
            return redirect()->back()->with('error', 'Property is not available for the selected dates.');
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
                'special_requests' => $validated['special_requests'] ?? null,
            ]);

            // Notify landlord about new booking
            $property->landlord->notify(new BookingCreated($booking));

            DB::commit();

            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Booking request submitted successfully! The landlord will review your request.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to create booking: ' . $e->getMessage());
        }
    }

    public function confirm(Request $request, Booking $booking)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check if user can confirm this booking (landlord only)
        $user = auth()->user();
        if ($user->user_type !== 'landlord' || $booking->property->landlord_id !== $user->id) {
            abort(403, 'You can only confirm bookings for your properties.');
        }

        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Booking cannot be confirmed.');
        }

        DB::beginTransaction();
        try {
            $booking->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            // Notify student about booking confirmation
            $booking->student->notify(new BookingConfirmed($booking));

            DB::commit();

            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Booking confirmed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to confirm booking: ' . $e->getMessage());
        }
    }

    public function cancel(Request $request, Booking $booking)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check if user can cancel this booking
        $user = auth()->user();
        if ($user->user_type === 'student' && $booking->student_id !== $user->id) {
            abort(403, 'You can only cancel your own bookings.');
        } elseif ($user->user_type === 'landlord' && $booking->property->landlord_id !== $user->id) {
            abort(403, 'You can only cancel bookings for your properties.');
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return redirect()->back()->with('error', 'Booking cannot be cancelled.');
        }

        $reason = $request->input('reason');

        DB::beginTransaction();
        try {
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            // Make property available again if it was rented
            if ($booking->property->status === 'rented') {
                $booking->property->update(['status' => 'active']);
            }

            // Notify the other party about cancellation
            if (auth()->id() === $booking->student_id) {
                $booking->property->landlord->notify(new BookingCancelled($booking));
            } else {
                $booking->student->notify(new BookingCancelled($booking));
            }

            DB::commit();

            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Booking cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to cancel booking: ' . $e->getMessage());
        }
    }

    public function complete(Request $request, Booking $booking)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check if user can complete this booking (landlord only)
        $user = auth()->user();
        if ($user->user_type !== 'landlord' || $booking->property->landlord_id !== $user->id) {
            abort(403, 'You can only complete bookings for your properties.');
        }

        if ($booking->status !== 'confirmed') {
            return redirect()->back()->with('error', 'Only confirmed bookings can be completed.');
        }

        DB::beginTransaction();
        try {
            $booking->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Make property available again
            $booking->property->update(['status' => 'active']);

            // Notify student about booking completion
            $booking->student->notify(new BookingCompleted($booking));

            DB::commit();

            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Booking marked as completed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to complete booking: ' . $e->getMessage());
        }
    }

    public function processPayment(Request $request, Booking $booking)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check if user can pay for this booking (student only)
        $user = auth()->user();
        if ($user->user_type !== 'student' || $booking->student_id !== $user->id) {
            abort(403, 'You can only pay for your own bookings.');
        }

        if ($booking->status !== 'confirmed' || $booking->payment_status !== 'pending') {
            return redirect()->back()->with('error', 'Payment can only be processed for confirmed bookings with pending payment.');
        }

        // Validate payment details
        $validated = $request->validate([
            'payment_method' => 'required|string',
            'card_number' => 'required_if:payment_method,credit_card|string',
            'expiry_month' => 'required_if:payment_method,credit_card|string',
            'expiry_year' => 'required_if:payment_method,credit_card|string',
            'cvv' => 'required_if:payment_method,credit_card|string',
        ]);

        // In a real application, this would integrate with a payment gateway like Stripe
        // For now, we'll simulate a successful payment

        DB::beginTransaction();
        try {
            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'amount' => $booking->total_due,
                'payment_method' => $validated['payment_method'],
                'status' => 'completed',
                'transaction_id' => 'DEMO-' . uniqid(),
            ]);

            // Update booking payment status
            $booking->update([
                'payment_status' => 'paid',
                'escrow_transaction_id' => $payment->transaction_id,
            ]);

            DB::commit();

            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Payment processed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to process payment: ' . $e->getMessage());
        }
    }
}
