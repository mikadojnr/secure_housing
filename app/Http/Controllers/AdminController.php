<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Property;
use App\Models\Booking;
use App\Models\Verification;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

    public function verifications(Request $request)
    {
        $query = Verification::with('user');

        if ($request->filled('type')) {
            $query->where('verification_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $verifications = $query->latest()->paginate(20);

        // ✅ Decode JSON to array for each verification
        foreach ($verifications as $verification) {
            if (is_string($verification->verification_data)) {
                $verification->verification_data = json_decode($verification->verification_data, true) ?? [];

            } elseif (!is_array($verification->verification_data)) {
                $verification->verification_data = [];
            }

            // dd($verification->verification_data);
        }

        $pendingCount = Verification::where('status', 'pending')->count();
        $verifiedCount = Verification::where('status', 'verified')->count();

        return view('admin.verifications.index', compact('verifications', 'pendingCount', 'verifiedCount'));
    }


    public function showVerification(Verification $verification)
    {
        $verification->load('user');
        $verificationData = is_array($verification->verification_data) ? $verification->verification_data : [];
        return response()->json([
            'html' => view('admin.verifications.partials.show', compact('verification', 'verificationData'))->render(),
            'verification' => $verification->toArray()
        ]);
    }

    public function updateVerification(Request $request, Verification $verification)
    {
        $validated = $request->validate([
            'status' => 'required|in:verified,rejected',
            'admin_notes' => 'nullable|string|max:1000',
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        try {
            $updateData = [
                'status' => $validated['status'],
                'admin_notes' => $validated['admin_notes'],
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ];

            if ($validated['status'] === 'verified') {
                $updateData['verified_at'] = now();
                $updateData['expires_at'] = $verification->verification_type === 'identity' ? now()->addYears(2) : now()->addYears(1);
                $updateData['rejection_reason'] = null;
            } elseif ($validated['status'] === 'rejected') {
                $updateData['rejection_reason'] = $validated['rejection_reason'];
                $updateData['verified_at'] = null;
                $updateData['expires_at'] = null;
            }

            $verification->update($updateData);

            if ($validated['status'] === 'verified') {
                $verification->user->updateTrustScore();
            }

            return response()->json([
                'success' => true,
                'message' => 'Verification ' . $validated['status'] . ' successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Admin verification update failed', [
                'verification_id' => $verification->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update verification status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approveVerification(Verification $verification)
    {
        try {
            $verification->update([
                'status' => 'verified',
                'verified_at' => now(),
                'expires_at' => $verification->verification_type === 'identity' ? now()->addYears(2) : now()->addYears(1),
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'rejection_reason' => null,
                'admin_notes' => 'Approved by admin via quick action.',
            ]);

            $verification->user->updateTrustScore();

            return redirect()->route('admin.verifications')->with('success', 'Verification approved successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to approve verification', [
                'verification_id' => $verification->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('admin.verifications')->with('error', 'Failed to approve verification: ' . $e->getMessage());
        }
    }

    public function rejectVerification(Request $request, Verification $verification)
    {
        try {
            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:1000',
                'admin_notes' => 'nullable|string|max:1000',
            ]);

            $verification->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
                'admin_notes' => $validated['admin_notes'],
                'verified_at' => null,
                'expires_at' => null,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            return redirect()->route('admin.verifications')->with('success', 'Verification rejected successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to reject verification', [
                'verification_id' => $verification->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('admin.verifications')->with('error', 'Failed to reject verification: ' . $e->getMessage());
        }
    }

    public function downloadVerificationDocument(Verification $verification, $documentType)
    {
        $verificationData = is_array($verification->verification_data) ? $verification->verification_data : [];
        $documentPaths = [
            'identity_document_front' => $verificationData['documents']['identity_document_front_path'] ?? null,
            'identity_document_back' => $verificationData['documents']['identity_document_back_path'] ?? null,
            'selfie' => $verificationData['documents']['selfie_path'] ?? null,
            'enrollment_document' => $verificationData['documents']['enrollment_document_path'] ?? null,
            'student_id_card' => $verificationData['documents']['student_id_card_path'] ?? null,
        ];

        if (!isset($documentPaths[$documentType]) || !$documentPaths[$documentType]) {
            abort(404, 'Document not found.');
        }

        $filePath = $documentPaths[$documentType];

        if (!str_starts_with($filePath, 'verifications/')) {
            abort(403, 'Invalid file path.');
        }

        if (!Storage::disk('private')->exists($filePath)) {
            abort(404, 'Document not found.');
        }

        $mimeType = Storage::disk('private')->mimeType($filePath);
        $fileName = basename($filePath);

        return Storage::disk('private')->download($filePath, $fileName, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
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
