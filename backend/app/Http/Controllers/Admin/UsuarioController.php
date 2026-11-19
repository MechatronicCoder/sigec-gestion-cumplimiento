<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('name')->get();

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'rol' => 'required|in:admin,evaluador,consulta',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'rol' => $data['rol'],
            'password' => Hash::make($data['password']),
            'activo' => true,
            'debe_cambiar_password' => true,
        ]);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        return view('admin.usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$usuario->id,
            'rol' => 'required|in:admin,evaluador,consulta',
        ]);

        $usuario->update($data);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Deshabilita o rehabilita un usuario. NUNCA elimina.
     */
    public function toggleActivo(User $usuario)
    {
        if ($usuario->id === auth()->user()?->id) {
            return back()->with('error', 'No puedes deshabilitar tu propia cuenta.');
        }

        $usuario->update(['activo' => ! $usuario->activo]);

        return back()->with('success', 'Estado del usuario actualizado.');
    }

    /**
     * Genera una contraseña temporal aleatoria y obliga a cambiarla en el proximo login.
     */
    public function resetPassword(User $usuario)
    {
        $passwordTemporal = Str::random(10);

        $usuario->update([
            'password' => Hash::make($passwordTemporal),
            'debe_cambiar_password' => true,
        ]);

        return redirect()->route('admin.usuarios.index')
            ->with('success', "Contraseña reiniciada para {$usuario->name}. Contraseña temporal: {$passwordTemporal} (comunícasela de forma segura, no volverá a mostrarse).");
    }
}
