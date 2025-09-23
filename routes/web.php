<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SupportPersonalController;
use App\Http\Controllers\UserTypeController;
use App\Http\Controllers\Admin\PermissionManagerController;


Route::get('/', function () {
    $buildings = App\Models\Building::orderBy('description')->get();
    $departments = App\Models\Department::orderBy('description')->get();
    $employees = App\Models\Employee::orderBy('full_name')->get();

    return view('home', compact('buildings', 'departments', 'employees'));
})->name('home');

Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');

Route::get('/solicitudes', function () {
    return view('solicitudes.solicitudes');
})->name('solicitudes.solicitudes');

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas administrativas protegidas
Route::prefix('admin')->middleware(['auth', 'permission:acceso administrador'])->group(function () {


    // Gestión de solicitudes
    Route::get('/admin_solicitudes', [TicketController::class, 'index'])
    ->name('admin.admin_solicitudes')
    ->middleware('permission:ver tickets');

    // Gestión de usuarios
    Route::get('/usuarios', [UserController::class, 'index'])
        ->name('admin.usuarios')->middleware('permission:ver usuarios');

    Route::post('/usuarios', [UserController::class, 'store'])
        ->name('admin.usuarios.store')->middleware('permission:crear usuarios');

    Route::get('/usuarios/{user}/edit', [UserController::class, 'edit'])
        ->name('admin.usuarios.edit')->middleware('permission:editar usuarios');

    Route::put('/usuarios/{user}', [UserController::class, 'update'])
        ->name('admin.usuarios.update')->middleware('permission:editar usuarios');

    // Gestión de tipos de usuario
    Route::prefix('usuarios_tipo')->group(function () {
        Route::get('/', [UserTypeController::class, 'index'])
            ->name('admin.usuarios_tipo')->middleware('permission:ver tipos usuario');

        Route::post('/store', [UserTypeController::class, 'store'])
            ->name('admin.usuarios_tipo.store')->middleware('permission:crear tipos usuario');

        Route::get('/edit/{id}', [UserTypeController::class, 'edit'])
            ->name('admin.usuarios_tipo.edit')->middleware('permission:editar tipos usuario');

        Route::put('/update/{id}', [UserTypeController::class, 'update'])
            ->name('admin.usuarios_tipo.update')->middleware('permission:editar tipos usuario');
    });

    // Personal de soporte
    Route::prefix('soporte')->group(function () {
        Route::get('/', [SupportPersonalController::class, 'index'])
            ->name('admin.soporte')->middleware('permission:ver personal soporte');

        Route::post('/', [SupportPersonalController::class, 'store'])
            ->name('admin.soporte.store')->middleware('permission:crear personal soporte');

        Route::get('/edit/{id}', [SupportPersonalController::class, 'edit'])
            ->name('admin.soporte.edit')->middleware('permission:editar personal soporte');

        Route::put('/{id}', [SupportPersonalController::class, 'update'])
            ->name('admin.soporte.update')->middleware('permission:editar personal soporte');
    });

    // Reportes
    Route::get('/reportes', function () {
        return view('administrador.admin.reportes');
    })->name('admin.reportes')->middleware('permission:ver reportes');
});

// ================== RUTAS EXCLUSIVAS PARA SUPERADMIN ==================
Route::prefix('admin')->middleware(['auth', 'role:superadmin'])->group(function () {

    // Gestión de permisos (solo superadmin)
    Route::prefix('gestion-permisos')->group(function () {
        Route::get('/', [PermissionManagerController::class, 'index'])->name('admin.permisos.manager');
        Route::put('/rol/{role}/permisos', [PermissionManagerController::class, 'updateRolePermissions'])->name('admin.permisos.update-role');
        Route::put('/usuario/{user}/roles', [PermissionManagerController::class, 'updateUserRoles'])->name('admin.permisos.update-user');
        Route::post('/crear-permiso', [PermissionManagerController::class, 'createPermission'])->name('admin.permisos.create-permission');
        Route::post('/crear-rol', [PermissionManagerController::class, 'createRole'])->name('admin.permisos.create-role');
        Route::delete('/permiso/{permission}', [PermissionManagerController::class, 'deletePermission'])->name('admin.permisos.delete-permission');
        Route::delete('/rol/{role}', [PermissionManagerController::class, 'deleteRole'])->name('admin.permisos.delete-role');
    });
});