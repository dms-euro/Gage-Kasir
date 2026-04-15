<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UssersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('username', 'like', '%' . $request->search . '%')
                    ->orWhere('nama', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->paginate(15);

        return view('pages.pengguna', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'username' => 'required',
            'nama'     => 'required',
            'level'    => 'required',
            'password' => 'required',
        ]);

        $validate['password'] = Hash::make($validate['password']);

        User::create($validate);

        return redirect()->route('user.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validate = $request->validate([
            'username' => 'required|unique:users,username,' . $id,
            'nama'     => 'required',
            'level'    => 'required',
            'password' => 'nullable',
        ]);

        if ($request->filled('password')) {
            $validate['password'] = Hash::make($validate['password']);
        } else {
            unset($validate['password']);
        }

        $user->update($validate);

        return redirect()->route('user.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id == auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('user.index')->with('success', 'Pengguna berhasil dihapus.');
    }
    public function me()
    {
        return view('pages.me');
    }

    /**
     * Update data diri
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        $user = auth()->user();
        $user->update([
            'nama' => $request->nama,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:4|confirmed',
        ]);

        $user = auth()->user();

        // Cek password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
