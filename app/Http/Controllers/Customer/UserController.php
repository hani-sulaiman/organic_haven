<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
class UserController extends Controller
{
    /**
     * Update the authenticated user's profile information.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'birthdate' => 'sometimes|required|date|before:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update the user's information
        $user->update($request->only(['name', 'age', 'birthdate']));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'birthdate' => $user->birthdate,
                // Add other fields as necessary
            ],
        ], 200);
    }
    public function profile()
    {
        $user = Auth::user();
        return response()->json([
            'success' => true,
            'message' => 'Profile fetched successfully.',
            'user' => $user,
        ], 200);
    }
}
