<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UsuarioApiController extends Controller
{
    public function index()
    {
        return User::with('roles:id,name')->orderBy('name')->get();
    }

    public function roles()
    {
        return Role::orderBy('name')->pluck('name');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'rol' => 'required|string|exists:roles,name',
            'activo' => 'boolean',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'activo' => $data['activo'] ?? true,
        ]);

        $user->syncRoles([$data['rol']]);

        return response()->json($user->load('roles:id,name'), 201);
    }

    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($usuario->id)],
            'rol' => 'required|string|exists:roles,name',
            'activo' => 'boolean',
        ]);

        $usuario->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'activo' => $data['activo'] ?? $usuario->activo,
        ]);

        $usuario->syncRoles([$data['rol']]);

        return response()->json($usuario->load('roles:id,name'));
    }

    public function toggleActivo(User $usuario)
    {
        $usuario->update(['activo' => ! $usuario->activo]);

        return response()->json($usuario->load('roles:id,name'));
    }

    public function resetPassword(User $usuario)
    {
        $temporal = 'Sigec'.random_int(1000, 9999).'!';
        $usuario->update(['password' => Hash::make($temporal)]);

        return response()->json(['password_temporal' => $temporal]);
    }
}
