<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::get('productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::get('productos/crear', [ProductoController::class, 'create'])->name('productos.create');
    Route::post('productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::get('productos/sugerir-sku', [ProductoController::class, 'sugerirSku'])->name('productos.sugerir-sku');
    Route::get('productos/{producto}/editar', [ProductoController::class, 'edit'])->name('productos.edit');
    Route::put('productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
    Route::get('proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
    Route::get('proveedores/crear', [ProveedorController::class, 'create'])->name('proveedores.create');
    Route::post('proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
    Route::get('proveedores/{proveedor}/editar', [ProveedorController::class, 'edit'])->name('proveedores.edit');
    Route::put('proveedores/{proveedor}', [ProveedorController::class, 'update'])->name('proveedores.update');
    Route::delete('proveedores/{proveedor}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');
    Route::get('compras', [CompraController::class, 'index'])->name('compras.index');
    Route::get('compras/crear', [CompraController::class, 'create'])->name('compras.create');
    Route::post('compras', [CompraController::class, 'store'])->name('compras.store');
    Route::patch('compras/{compra}/anular', [CompraController::class, 'anular'])->name('compras.anular');
    Route::get('ventas', [VentaController::class, 'index'])->name('ventas.index');
    Route::get('ventas/crear', [VentaController::class, 'create'])->name('ventas.create');
    Route::post('ventas', [VentaController::class, 'store'])->name('ventas.store');
    Route::put('ventas/{venta}', [VentaController::class, 'update'])->name('ventas.update');
    Route::get('clientes/buscar-nit', [ClienteController::class, 'buscarPorNit'])->name('clientes.buscar-nit');
    Route::patch('ventas/{venta}/anular', [VentaController::class, 'anular'])->name('ventas.anular');
    Route::get('compras/ultimo-precio', [CompraController::class, 'ultimoPrecio'])->name('compras.ultimo-precio');

});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::delete('usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
});

require __DIR__.'/settings.php';
