<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function updateAdditional(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'university' => 'nullable|string|max:255',
            'student_id' => 'nullable|string|max:50',
            'bio' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        // Create or update user profile
        if (!$user->profile) {
            $user->profile()->create($validated);
        } else {
            $user->profile->update($validated);
        }

        return back()->with('success', 'Additional information updated successfully!');
    }
}
