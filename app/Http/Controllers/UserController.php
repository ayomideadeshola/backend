<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::orderBy('created_at', 'desc')->paginate(10));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6',
            ]);

            $user = User::create([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'created_by' => 'admin',
                'created_by_user_id' => $request->user()->id,
            ]);

            return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'User creation failed', 'error' => $e->getMessage()], 400);
        }
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        try {
            $validated = $request->validate([
                'full_name' => 'sometimes|required|string|max:255',
                'email' => ['sometimes', 'required', 'email', Rule::unique('users')->ignore($user->id)],
                'password' => 'sometimes|nullable|string|min:6',
            ]);

            $updateData = [];
            if (isset($validated['full_name'])) {
                $updateData['full_name'] = $validated['full_name'];
            }
            if (isset($validated['email'])) {
                $updateData['email'] = $validated['email'];
            }
            if (isset($validated['password']) && $validated['password']) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            if (!empty($updateData)) {
                $user->update($updateData);
            }

            return response()->json(['message' => 'User updated successfully', 'user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'User update failed', 'error' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
