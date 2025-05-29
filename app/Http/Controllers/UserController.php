<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('membership_number', 'like', "%{$search}%");
            });
        }

        // Filter by membership type
        if ($request->has('membership_type') && $request->get('membership_type') != '') {
            $query->where('membership_type', $request->get('membership_type'));
        }

        // Filter by active status
        if ($request->has('is_active') && $request->get('is_active') != '') {
            $query->where('is_active', $request->get('is_active') == '1');
        }

        $users = $query->paginate(15);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'membership_number' => 'required|string|unique:users,membership_number',
            'membership_type' => 'required|in:standard,premium,student,librarian,admin',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Felhasználó sikeresen létrehozva!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['borrowedBooks.author', 'currentlyBorrowedBooks.author']);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'membership_number' => 'required|string|unique:users,membership_number,' . $user->id,
            'membership_type' => 'required|in:standard,premium,student,librarian,admin',
            'is_active' => 'boolean',
        ]);

        // Only validate password if it's being changed
        if ($request->filled('password')) {
            $passwordValidation = $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $validated['password'] = Hash::make($passwordValidation['password']);
        }

        $validated['is_active'] = $request->has('is_active');

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Felhasználó sikeresen frissítve!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Check if user has active borrows
        if ($user->currentlyBorrowedBooks()->exists()) {
            return redirect()->route('users.index')->with('error', 'A felhasználó nem törölhető, mert aktív kölcsönzése van!');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Felhasználó sikeresen törölve!');
    }
}
