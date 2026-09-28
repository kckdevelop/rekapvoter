<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserApiController extends Controller
{
    /**
     * List all users (Admin only).
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $role   = $request->query('role');
        $tpsId  = $request->query('tps_id');

        $users = User::with('tps')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($role, fn($q) => $q->where('role', $role))
            ->when($tpsId, fn($q) => $q->where('tps_id', $tpsId))
            ->orderByRaw("FIELD(role, 'admin', 'saksi')")
            ->orderBy('name')
            ->paginate(30);

        return response()->json([
            'success' => true,
            'data'    => $users,
        ]);
    }

    /**
     * Store new user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'phone'    => 'nullable|string|max:30',
            'role'     => 'required|in:admin,saksi',
            'tps_id'   => 'nullable|required_if:role,saksi|exists:tps,id',
            'password' => 'required|string|min:6',
        ], [
            'name.required'        => 'Nama pengguna wajib diisi.',
            'email.required'       => 'Email/Username wajib diisi.',
            'email.unique'         => 'Email/Username sudah digunakan.',
            'role.required'        => 'Pilih role pengguna.',
            'tps_id.required_if'   => 'Petugas Saksi wajib ditentukan TPS yang dikelolanya.',
            'tps_id.exists'        => 'TPS yang dipilih tidak valid.',
            'password.required'    => 'Password wajib diisi.',
            'password.min'         => 'Password minimal 6 karakter.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => strtolower(trim($request->email)),
            'phone'    => $request->phone,
            'role'     => $request->role,
            'tps_id'   => $request->role === 'saksi' ? $request->tps_id : null,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil dibuat.',
            'data'    => $user->load('tps'),
        ], 201);
    }

    /**
     * Show single user detail.
     */
    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'data'    => $user->load('tps'),
        ]);
    }

    /**
     * Update user.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'email'    => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'    => 'nullable|string|max:30',
            'role'     => 'sometimes|required|in:admin,saksi',
            'tps_id'   => 'nullable|exists:tps,id',
            'password' => 'nullable|string|min:6',
        ]);

        $role = $request->input('role', $user->role);
        $data = $request->only(['name', 'phone']);

        if ($request->has('email')) {
            $data['email'] = strtolower(trim($request->email));
        }

        if ($request->has('role')) {
            $data['role'] = $role;
            $data['tps_id'] = $role === 'saksi' ? $request->input('tps_id', $user->tps_id) : null;
        } elseif ($request->has('tps_id')) {
            $data['tps_id'] = $user->role === 'saksi' ? $request->tps_id : null;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data pengguna berhasil diperbarui.',
            'data'    => $user->fresh('tps'),
        ]);
    }

    /**
     * Destroy user.
     */
    public function destroy(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri.',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil dihapus.',
        ]);
    }
}
