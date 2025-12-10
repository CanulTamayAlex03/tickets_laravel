<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SupportPersonalController;
use App\Http\Controllers\Admin\PermissionManagerController;
use App\Http\Controllers\MisSolicitudesController;
use App\Http\Controllers\TicketLiberacionController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    $buildings = App\Models\Building::orderBy('description')->get();
    $departments = App\Models\Department::orderBy('description')->get();
    $employees = App\Models\Employee::orderBy('full_name')->get();

    return view('home', compact('buildings', 'departments', 'employees'));
})->name('home');

Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');

// Ruta para "Mis Solicitudes" - DEBE IR PRIMERO
Route::get('/mis-solicitudes', [MisSolicitudesController::class, 'index'])
    ->name('mis_solicitudes');

Route::get('/mis-solicitudes/{id}/detalles', [TicketLiberacionController::class, 'show'])->name('mis_solicitudes.detalles');
Route::post('/mis-solicitudes/{id}/confirmar-liberacion', [TicketLiberacionController::class, 'liberar'])->name('mis_solicitudes.confirmar_liberacion');

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ================== RUTAS ADMINISTRATIVAS GENERALES ==================
Route::prefix('admin')->middleware(['auth', 'permission:acceso administrador'])->group(function () {

    // Gestión de solicitudes
    Route::get('/admin_solicitudes', [TicketController::class, 'index'])
        ->name('admin.admin_solicitudes')
        ->middleware('permission:ver tickets');

    Route::get('/solicitudes/{id}/edit', [TicketController::class, 'edit'])
        ->name('admin.solicitudes.edit')
        ->middleware('permission:editar tickets');

    Route::put('/solicitudes/{id}', [TicketController::class, 'update'])
        ->name('admin.solicitudes.update')
        ->middleware('permission:editar tickets');

    Route::get('/solicitudes/servicios-por-indicador/{indicatorId}', [TicketController::class, 'getServicesByIndicator'])
        ->name('admin.solicitudes.servicios-por-indicador');
        
    Route::post('/solicitudes/{id}/agregar-seguimiento', [TicketController::class, 'agregarSeguimiento'])
    ->name('admin.solicitudes.agregar-seguimiento')
    ->middleware('permission:editar tickets');

    // Ruta para verificar nuevas solicitudes
    Route::get('/admin/tickets/check-new', [TicketController::class, 'checkNewTickets'])
    ->name('admin.tickets.check_new')
    ->middleware('auth');

    Route::get('/admin/new-tickets-count', [TicketController::class, 'getNewTicketsCount'])
    ->name('admin.new_tickets_count')
    ->middleware(['auth', 'can:ver tickets']);

    // Gestión de usuarios
    Route::get('/usuarios', [UserController::class, 'index'])
        ->name('admin.usuarios')->middleware('permission:ver usuarios');

    Route::post('/usuarios', [UserController::class, 'store'])
        ->name('admin.usuarios.store')->middleware('permission:crear usuarios');

    Route::get('/usuarios/{user}/edit', [UserController::class, 'edit'])
        ->name('admin.usuarios.edit')->middleware('permission:editar usuarios');

    Route::put('/usuarios/{user}', [UserController::class, 'update'])
        ->name('admin.usuarios.update')->middleware('permission:editar usuarios');

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
    Route::get('/reportes', [ReportController::class, 'index'])
    ->name('admin.reportes')
    ->middleware('permission:ver reportes');

    Route::post('/reportes/exportar', [ReportController::class, 'export'])
        ->name('admin.reportes.export')
        ->middleware('permission:ver reportes');


    // ================== RUTAS EXCLUSIVAS PARA SUPERADMIN ==================
    Route::prefix('gestion-permisos')->middleware(['role:superadmin'])->group(function () {
        Route::get('/', [PermissionManagerController::class, 'index'])->name('admin.permisos.manager');
        Route::put('/rol/{role}/permisos', [PermissionManagerController::class, 'updateRolePermissions'])->name('admin.permisos.update-role');
        Route::put('/usuario/{user}/roles', [PermissionManagerController::class, 'updateUserRoles'])->name('admin.permisos.update-user');
        Route::post('/crear-permiso', [PermissionManagerController::class, 'createPermission'])->name('admin.permisos.create-permission');
        Route::post('/crear-rol', [PermissionManagerController::class, 'createRole'])->name('admin.permisos.create-role');
        Route::delete('/permiso/{permission}', [PermissionManagerController::class, 'deletePermission'])->name('admin.permisos.delete-permission');
        Route::delete('/rol/{role}', [PermissionManagerController::class, 'deleteRole'])->name('admin.permisos.delete-role');
        Route::get('/rol/{role}/edit-ajax', [PermissionManagerController::class, 'editAjax'])->name('admin.permisos.edit-ajax');
    });
});
