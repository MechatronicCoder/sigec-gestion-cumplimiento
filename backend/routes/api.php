<?php

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

    return response()->json(['user' => \App\Models\User::with('roles:id,name')->find(Auth::id())]);
});

Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->noContent();
});

// Devuelve el usuario autenticado via sesion (Sanctum SPA)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return \App\Models\User::with('roles:id,name')->find($request->user()->id);
});

// Estadisticas para las tarjetas del dashboard (cualquier autenticado)
Route::middleware('auth:sanctum')->get('/dashboard/stats', function () {
    return response()->json([
        'dimensiones' => \App\Models\Dimension::count(),
        'indicadores' => \App\Models\Indicador::count(),
        'items' => \App\Models\Item::count(),
        'evaluaciones' => \App\Models\Evaluacion::count(),
    ]);
});

// Catalogo (Dimensiones/Indicadores/Items): solo administradores
Route::middleware(['auth:sanctum', 'permission:gestionar-catalogo'])->group(function () {
    Route::apiResource('dimensiones', \App\Http\Controllers\Api\DimensionApiController::class)
        ->parameters(['dimensiones' => 'dimension']);

    Route::apiResource('indicadores', \App\Http\Controllers\Api\IndicadorApiController::class)
        ->parameters(['indicadores' => 'indicador']);

    Route::apiResource('items', \App\Http\Controllers\Api\ItemApiController::class)
        ->parameters(['items' => 'item']);
});

// Checklist - lectura (evaluaciones, estructura, resumen): admin, evaluador y consulta
Route::middleware(['auth:sanctum', 'permission:ver-checklist'])->group(function () {
    Route::get('/evaluaciones', [\App\Http\Controllers\Api\ChecklistApiController::class, 'evaluaciones']);
    Route::get('/evaluaciones/{evaluacion}/checklist', [\App\Http\Controllers\Api\ChecklistApiController::class, 'show']);
    Route::get('/evaluaciones/{evaluacion}/resumen', [\App\Http\Controllers\Api\ChecklistApiController::class, 'resumen']);
});

// Checklist - escritura (crear, guardar, finalizar): solo admin y evaluador
Route::middleware(['auth:sanctum', 'permission:gestionar-checklist'])->group(function () {
    Route::post('/evaluaciones', [\App\Http\Controllers\Api\ChecklistApiController::class, 'crearEvaluacion']);
    Route::post('/evaluaciones/{evaluacion}/checklist', [\App\Http\Controllers\Api\ChecklistApiController::class, 'guardar']);
    Route::patch('/evaluaciones/{evaluacion}/finalizar', [\App\Http\Controllers\Api\ChecklistApiController::class, 'finalizar']);
});

// Usuarios: solo administradores
Route::middleware(['auth:sanctum', 'permission:gestionar-usuarios'])->group(function () {
    Route::get('/roles', [\App\Http\Controllers\Api\UsuarioApiController::class, 'roles']);
    Route::get('/usuarios', [\App\Http\Controllers\Api\UsuarioApiController::class, 'index']);
    Route::post('/usuarios', [\App\Http\Controllers\Api\UsuarioApiController::class, 'store']);
    Route::put('/usuarios/{usuario}', [\App\Http\Controllers\Api\UsuarioApiController::class, 'update']);
    Route::patch('/usuarios/{usuario}/toggle', [\App\Http\Controllers\Api\UsuarioApiController::class, 'toggleActivo']);
    Route::post('/usuarios/{usuario}/reset-password', [\App\Http\Controllers\Api\UsuarioApiController::class, 'resetPassword']);
});

// Auditoria: solo administradores
Route::middleware(['auth:sanctum', 'permission:ver-auditoria'])->group(function () {
    Route::get('/auditoria', [\App\Http\Controllers\Api\AuditoriaApiController::class, 'index']);
    Route::get('/auditoria/filtros', [\App\Http\Controllers\Api\AuditoriaApiController::class, 'filtros']);
    Route::get('/auditoria/exportar-excel', [\App\Http\Controllers\Api\AuditoriaApiController::class, 'exportarExcel']);
    Route::get('/auditoria/exportar-pdf', [\App\Http\Controllers\Api\AuditoriaApiController::class, 'exportarPdf']);
});
