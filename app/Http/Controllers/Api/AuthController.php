<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login User & Generate Sanctum Token untuk Mobile App.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'       => 'required|email',
            'password'    => 'required|string',
            'device_name' => 'nullable|string',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $email = strtolower(trim($request->email));
        $user  = User::where('email', $email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
            ], 401);
        }

        $deviceName = $request->device_name ?? 'Mobile App';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'token' => $token,
                'user'  => [
                    'id'     => $user->id,
                    'name'   => $user->name,
                    'email'  => $user->email,
                    'phone'  => $user->phone,
                    'role'   => $user->role ?? 'admin',
                    'tps_id' => $user->tps_id,
                    'tps'    => $user->tps ? [
                        'id'       => $user->tps->id,
                        'nama_tps' => $user->tps->nama_tps,
                    ] : null,
                ],
            ],
        ], 200);
    }

    /**
     * Get Authenticated User Info.
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('tps');

        return response()->json([
            'success' => true,
            'data'    => [
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'phone'  => $user->phone,
                'role'   => $user->role ?? 'admin',
                'tps_id' => $user->tps_id,
                'tps'    => $user->tps ? [
                    'id'       => $user->tps->id,
                    'nama_tps' => $user->tps->nama_tps,
                ] : null,
            ],
        ]);
    }

    /**
     * Logout User (Revoke Current Sanctum Token).
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil. Token telah dihapus.',
        ]);
    }

    /**
     * Change Authenticated User Password via API.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password'          => 'required|string',
            'new_password'              => 'required|string|min:6',
            'new_password_confirmation' => 'required|string|same:new_password',
        ], [
            'current_password.required'          => 'Password lama wajib diisi.',
            'new_password.required'              => 'Password baru wajib diisi.',
            'new_password.min'                   => 'Password baru minimal 6 karakter.',
            'new_password_confirmation.required' => 'Konfirmasi password baru wajib diisi.',
            'new_password_confirmation.same'     => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diperbarui.',
        ]);
    }
}
