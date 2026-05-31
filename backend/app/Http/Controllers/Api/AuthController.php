<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::query()->where('email', $request->email)->with('profile')->first();

        // Validasi user dan password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah.'
            ], 401);
        }

        // Cek apakah akun aktif (dari tabel profiles)
        if ($user->profile && !$user->profile->is_active) {
            return response()->json([
                'message' => 'Akun dinonaktifkan. Silakan hubungi Owner.'
            ], 403);
        }

        // Generate Token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->profile->name ?? 'No Name',
                'role' => $user->profile->role ?? 'guest',
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ]
        ]);
    }

    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan saat ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }

    public function me(Request $request)
    {
        // Ambil data user yang sedang login beserta profilenya
        $user = $request->user()->load('profile');

        return response()->json([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->profile->name ?? 'No Name',
                'role' => $user->profile->role ?? 'guest',
            ]
        ]);
    }
}
