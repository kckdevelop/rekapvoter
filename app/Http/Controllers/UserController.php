<?php

namespace App\Http\Controllers;

use App\Models\Tps;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan daftar user/akun saksi TPS dan admin.
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
            ->paginate(15)
            ->withQueryString();

        $tpsList = Tps::orderBy('nama_tps')->get();
        $totalUsers = User::count();
        $totalSaksi = User::where('role', 'saksi')->count();
        $totalAdmin = User::where('role', 'admin')->orWhereNull('role')->count();

        return view('users.index', compact(
            'users',
            'tpsList',
            'search',
            'role',
            'tpsId',
            'totalUsers',
            'totalSaksi',
            'totalAdmin'
        ));
    }

    /**
     * Form tambah user baru.
     */
    public function create()
    {
        $tpsList = Tps::orderBy('nama_tps')->get();
        return view('users.create', compact('tpsList'));
    }

    /**
     * Simpan user baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'phone'    => 'nullable|string|max:30',
            'role'     => 'required|in:admin,saksi',
            'tps_id'   => 'nullable|required_if:role,saksi|exists:tps,id',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required'        => 'Nama pengguna wajib diisi.',
            'email.required'       => 'Email/Username wajib diisi.',
            'email.unique'         => 'Email/Username sudah digunakan.',
            'role.required'        => 'Pilih role pengguna.',
            'tps_id.required_if'   => 'Petugas Saksi wajib ditentukan TPS yang dikelolanya.',
            'tps_id.exists'        => 'TPS yang dipilih tidak valid.',
            'password.required'    => 'Password wajib diisi.',
            'password.min'         => 'Password minimal 6 karakter.',
            'password.confirmed'   => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => strtolower(trim($request->email)),
            'phone'    => $request->phone,
            'role'     => $request->role,
            'tps_id'   => $request->role === 'saksi' ? $request->tps_id : null,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')
            ->with('success', "Akun {$request->name} berhasil dibuat.");
    }

    /**
     * Form edit user.
     */
    public function edit(User $user)
    {
        $tpsList = Tps::orderBy('nama_tps')->get();
        return view('users.edit', compact('user', 'tpsList'));
    }

    /**
     * Update data user.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'    => 'nullable|string|max:30',
            'role'     => 'required|in:admin,saksi',
            'tps_id'   => 'nullable|required_if:role,saksi|exists:tps,id',
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'name.required'      => 'Nama pengguna wajib diisi.',
            'email.required'     => 'Email/Username wajib diisi.',
            'email.unique'       => 'Email/Username sudah digunakan.',
            'role.required'      => 'Pilih role pengguna.',
            'tps_id.required_if' => 'Petugas Saksi wajib ditentukan TPS yang dikelolanya.',
            'tps_id.exists'      => 'TPS yang dipilih tidak valid.',
            'password.min'       => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $data = [
            'name'   => $request->name,
            'email'  => strtolower(trim($request->email)),
            'phone'  => $request->phone,
            'role'   => $request->role,
            'tps_id' => $request->role === 'saksi' ? $request->tps_id : null,
        ];

        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', "Data akun {$user->name} berhasil diperbarui.");
    }

    /**
     * Hapus user.
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Akun {$name} berhasil dihapus.");
    }
}
