<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role') && $request->get('role') !== 'all') {
            $query->where('role', $request->get('role'));
        }

        // Filter by status
        if ($request->filled('status') && $request->get('status') !== 'all') {
            $query->where('status', $request->get('status'));
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,admin,owner',
            'status' => 'required|in:active,non-active',
        ], [
            'name.required' => 'Nama harus diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role harus dipilih.',
            'role.in' => 'Role tidak valid.',
            'status.required' => 'Status harus dipilih.',
            'status.in' => 'Status tidak valid.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status' => $request->status,
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menambahkan user.')
                ->withInput();
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->loadCount(['bookings']);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // Prevent deletion of users with active bookings
        $activeBookingsCount = $user->bookings()
            ->whereIn('status', ['pending', 'confirmed', 'ongoing'])
            ->count();

        if ($activeBookingsCount > 0) {
            return redirect()->back()
                ->with('error', "User tidak dapat dihapus karena masih memiliki {$activeBookingsCount} booking aktif.");
        }

        try {
            $userName = $user->name;
            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', "User '{$userName}' berhasil dihapus.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus user.');
        }
    }

    /**
     * Toggle user status (active/non-active).
     */
    public function toggleStatus(User $user)
    {
        // Prevent admin from deactivating themselves
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat mengubah status akun sendiri.');
        }

        try {
            $newStatus = $user->status === 'active' ? 'non-active' : 'active';
            $user->update(['status' => $newStatus]);

            $statusText = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan';
            
            return redirect()->back()
                ->with('success', "User '{$user->name}' berhasil {$statusText}.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengubah status user.');
        }
    }
}
