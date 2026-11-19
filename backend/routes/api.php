<?php

use App\Http\Controllers\Api\AuditoriaApiController;
use App\Http\Controllers\Api\ChecklistApiController;
use App\Http\Controllers\Api\DimensionApiController;
use App\Http\Controllers\Api\IndicadorApiController;
use App\Http\Controllers\Api\ItemApiController;
use App\Http\Controllers\Api\UsuarioApiController;
use App\Models\Dimension;
use App\Models\Evaluacion;
use App\Models\Indicador;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

// Login dedicado para el frontend React (responde JSON, no redirige).
// No reemplaza el login Blade existente en routes/auth.php.
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if (! Auth::attempt($credentials, $request->boolean('remember'))) {
        throw ValidationException::withMessages([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
    }

    $request->session()->regenerate();

    return response()->json(['user' => User::with('roles:id,name')->find(Auth::id())]);
});

Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->noContent();
});

// Devuelve el usuario autenticado via sesion (Sanctum SPA)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return User::with('roles:id,name')->find($request->user()->id);
});

// Estadisticas para las tarjetas del dashboard (cualquier autenticado)
Route::middleware('auth:sanctum')->get('/dashboard/stats', function () {
    return response()->json([
        'dimensiones' => Dimension::count(),
        'indicadores' => Indicador::count(),
        'items' => Item::count(),
        'evaluaciones' => Evaluacion::count(),
    ]);
});

// Catalogo (Dimensiones/Indicadores/Items): solo administradores
Route::middleware(['auth:sanctum', 'permission:gestionar-catalogo'])->group(function () {
    Route::apiResource('dimensiones', DimensionApiController::class)
        ->parameters(['dimensiones' => 'dimension']);

    Route::apiResource('indicadores', IndicadorApiController::class)
        ->parameters(['indicadores' => 'indicador']);

    Route::apiResource('items', ItemApiController::class)
        ->parameters(['items' => 'item']);
});

// Checklist - lectura (evaluaciones, estructura, resumen): admin, evaluador y consulta
Route::middleware(['auth:sanctum', 'permission:ver-checklist'])->group(function () {
    Route::get('/evaluaciones', [ChecklistApiController::class, 'evaluaciones']);
    Route::get('/evaluaciones/{evaluacion}/checklist', [ChecklistApiController::class, 'show']);
    Route::get('/evaluaciones/{evaluacion}/resumen', [ChecklistApiController::class, 'resumen']);
});

// Checklist - escritura (crear, guardar, finalizar): solo admin y evaluador
Route::middleware(['auth:sanctum', 'permission:gestionar-checklist'])->group(function () {
    Route::post('/evaluaciones', [ChecklistApiController::class, 'crearEvaluacion']);
    Route::post('/evaluaciones/{evaluacion}/checklist', [ChecklistApiController::class, 'guardar']);
    Route::patch('/evaluaciones/{evaluacion}/finalizar', [ChecklistApiController::class, 'finalizar']);
});

// Usuarios: solo administradores
Route::middleware(['auth:sanctum', 'permission:gestionar-usuarios'])->group(function () {
    Route::get('/roles', [UsuarioApiController::class, 'roles']);
    Route::get('/usuarios', [UsuarioApiController::class, 'index']);
    Route::post('/usuarios', [UsuarioApiController::class, 'store']);
    Route::put('/usuarios/{usuario}', [UsuarioApiController::class, 'update']);
    Route::patch('/usuarios/{usuario}/toggle', [UsuarioApiController::class, 'toggleActivo']);
    Route::post('/usuarios/{usuario}/reset-password', [UsuarioApiController::class, 'resetPassword']);
});

// Auditoria: solo administradores
Route::middleware(['auth:sanctum', 'permission:ver-auditoria'])->group(function () {
    Route::get('/auditoria', [AuditoriaApiController::class, 'index']);
    Route::get('/auditoria/filtros', [AuditoriaApiController::class, 'filtros']);
    Route::get('/auditoria/exportar-excel', [AuditoriaApiController::class, 'exportarExcel']);
    Route::get('/auditoria/exportar-pdf', [AuditoriaApiController::class, 'exportarPdf']);
});
