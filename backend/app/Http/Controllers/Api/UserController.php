<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('profile')->latest()->get();
        return UserResource::collection($users);
    }

    public function store(UserRequest $request)
    {
        // Mulai database transaction
        DB::beginTransaction();
        try {
            // 1. Buat User
            $user = User::create([
                'id' => Str::uuid(),
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // 2. Assign Role Spatie
            $user->assignRole($request->role);

            // 3. Buat Profile
            $user->profile()->create([
                'name' => $request->name,
                'phone' => $request->phone,
                'role' => $request->role,
                'is_active' => $request->is_active ?? true,
            ]);

            DB::commit();
            return new UserResource($user->load('profile'));

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal membuat user: ' . $e->getMessage()], 500);
        }
    }

    public function show(User $user)
    {
        return new UserResource($user->load('profile'));
    }

    public function update(UserRequest $request, User $user)
    {
        DB::beginTransaction();
        try {
            // 1. Update User (email dan password jika diisi)
            $userData = ['email' => $request->email];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $user->update($userData);

            // 2. Sync Role Spatie (jika role berubah)
            $user->syncRoles([$request->role]);

            // 3. Update Profile
            $user->profile()->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'role' => $request->role,
                'is_active' => $request->has('is_active') ? $request->is_active : $user->profile->is_active,
            ]);

            DB::commit();
            return new UserResource($user->load('profile'));

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal mengupdate user: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(User $user)
    {
        // Mencegah admin menghapus dirinya sendiri
        if (auth()->id() === $user->id) {
            return response()->json(['message' => 'Anda tidak dapat menghapus akun Anda sendiri'], 400);
        }

        // Relasi profile otomatis terhapus karena kita pakai cascadeOnDelete di migration
        $user->delete();
        return response()->json(['message' => 'User berhasil dihapus']);
    }
}
