<?php

use App\Http\Controllers\Admin\AuditoriaController;
use App\Http\Controllers\Admin\DimensionController;
use App\Http\Controllers\Admin\IndicadorController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;

// use App\Models\Evaluacion;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Catalogo (Dimensiones/Indicadores/Items): solo administradores
Route::middleware(['auth', 'permission:gestionar-catalogo'])->prefix('admin')->name('admin.')->group(function () {});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::middleware('permission:gestionar-catalogo')->group(function () {
        Route::resource('dimensiones', DimensionController::class)->parameters(['dimensiones' => 'dimension']);
        Route::resource('indicadores', IndicadorController::class)->parameters(['indicadores' => 'indicador']);
        Route::resource('items', ItemController::class)->parameters(['items' => 'item']);
        Route::patch('dimensiones/{dimension}/toggle', [DimensionController::class, 'toggleActivo'])->name('dimensiones.toggle');
        Route::patch('indicadores/{indicador}/toggle', [IndicadorController::class, 'toggleActivo'])->name('indicadores.toggle');
        Route::patch('items/{item}/toggle', [ItemController::class, 'toggleActivo'])->name('items.toggle');
    });

    Route::middleware('permission:gestionar-usuarios')->group(function () {
        Route::resource('usuarios', UsuarioController::class)->except(['destroy', 'show']);
        Route::patch('usuarios/{usuario}/toggle', [UsuarioController::class, 'toggleActivo'])->name('usuarios.toggle');
        Route::post('usuarios/{usuario}/reset-password', [UsuarioController::class, 'resetPassword'])->name('usuarios.reset-password');
    });

    Route::middleware('permission:ver-auditoria')->group(function () {
        Route::resource('auditoria', AuditoriaController::class)->only(['index']);
    });
});

Route::middleware(['auth', 'permission:gestionar-checklist'])->group(function () {
    Route::get('/checklist', [ChecklistController::class, 'index'])->name('checklist.index');
    Route::post('/checklist/guardar', [ChecklistController::class, 'guardarTodo'])->name('checklist.guardar');
    Route::post('/checklist/nueva', [ChecklistController::class, 'nuevaEvaluacion'])->name('checklist.evaluacion.nueva');
    Route::patch('/checklist/{evaluacion}/finalizar', [ChecklistController::class, 'finalizarEvaluacion'])->name('checklist.evaluacion.finalizar');
});

Route::middleware(['auth', 'permission:ver-reportes'])->prefix('reportes')->name('reportes.')->group(function () {
    Route::get('/', [ReporteController::class, 'resumen'])->name('resumen');
    Route::get('/historico', [ReporteController::class, 'historico'])->name('historico');
    Route::get('/{evaluacion}/exportar-excel', [ReporteController::class, 'exportExcel'])->name('exportar.excel');
    Route::get('/{evaluacion}/exportar-pdf', [ReporteController::class, 'exportPdf'])->name('exportar.pdf');
    Route::get('/{evaluacion}', [ReporteController::class, 'show'])->name('show');
});

Route::middleware(['auth', 'permission:ver-reportes'])->group(function () {
    Route::get('/dashboard-general', function () {
        return 'Acceso concedido: cualquier rol autenticado puede ver esto.';
    });
});

require __DIR__.'/auth.php';
