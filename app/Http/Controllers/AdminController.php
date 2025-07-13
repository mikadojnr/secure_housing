<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Property;
use App\Models\Booking;
use App\Models\Verification;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check.user.type:admin');
    }

    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::where('user_type', 'student')->count(),
            'total_landlords' => User::where('user_type', 'landlord')->count(),
            'total_properties' => Property::count(),
            'active_properties' => Property::where('status', 'active')->count(),
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'pending_verifications' => Verification::where('status', 'pending')->count(),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentProperties = Property::with('landlord')->latest()->take(5)->get();
        $recentBookings = Booking::with(['student', 'property'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentProperties', 'recentBookings'));
    }

    public function users()
    {
        $users = User::with('profile')
            ->withCount(['properties', 'bookings', 'verifications'])
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function properties()
    {
        $properties = Property::with(['landlord', 'images'])
            ->withCount(['bookings', 'reviews'])
            ->latest()
            ->paginate(20);

        return view('admin.properties.index', compact('properties'));
    }

    public function verifications()
    {
        $verifications = Verification::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.verifications.index', compact('verifications'));
    }



    public function approveVerification(Request $request, Verification $verification)
    {
        $verification->update([
            'status' => 'verified',
            'verified_at' => now(),
            'expires_at' => now()->addYears(2),
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.verifications')->with('success', 'Verification approved successfully.');
    }

    public function rejectVerification(Request $request, Verification $verification)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $verification->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.verifications')->with('success', 'Verification rejected successfully.');
    }

    public function bookings()
    {
        $bookings = Booking::with(['student', 'property.landlord'])
            ->latest()
            ->paginate(20);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function reviews()
    {
        $reviews = Review::with(['student', 'property', 'booking'])
            ->latest()
            ->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleUserStatus(Request $request, User $user)
    {
        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->route('admin.users')->with('success', 'User status updated successfully.');
    }
}
