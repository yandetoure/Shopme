<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminOrderController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Routes publiques
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/category/{parentSlug}/{slug}', [CategoryController::class, 'subcategory'])->name('category.subcategory');

// Routes d'authentification
Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
});

Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Routes protégées (utilisateur connecté)
Route::middleware(['auth'])->group(function () {
    // Panier
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/cart/checkout', [OrderController::class, 'checkout'])->name('cart.checkout');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');

    // Commandes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

    // Favoris
    Route::get('/favorites', [App\Http\Controllers\FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/add/{product}', [App\Http\Controllers\FavoriteController::class, 'add'])->name('favorites.add');
    Route::delete('/favorites/remove/{product}', [App\Http\Controllers\FavoriteController::class, 'remove'])->name('favorites.remove');
    Route::post('/favorites/toggle/{product}', [App\Http\Controllers\FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // Profil utilisateur (avec tabs pour mobile)
    Route::get('/profile', function () {
        $user = Auth::user();
        $orders = $user->orders()->latest()->take(5)->get();
        $cartCount = $user->cartItems()->count();
        $favoritesCount = $user->favorites()->count();
        return view('profile.index', compact('user', 'orders', 'cartCount', 'favoritesCount'));
    })->name('profile.index');

    // Routes des dashboards
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        // Dashboard vendeur
        Route::get('/vendeur', [DashboardController::class, 'vendeur'])
            ->name('vendeur')
            ->middleware('role:vendeur');

        // Dashboard admin
        Route::get('/admin', [DashboardController::class, 'admin'])
            ->name('admin')
            ->middleware('role:admin');

        // Dashboard super admin
        Route::get('/superadmin', [DashboardController::class, 'superAdmin'])
            ->name('superadmin')
            ->middleware('role:super_admin');
    });

    // Routes admin (gestion)
    Route::middleware(['role:admin|super_admin'])->prefix('admin')->name('admin.')->group(function () {
        // Produits
        Route::resource('products', AdminProductController::class);
        
        // Catégories
        Route::resource('categories', AdminCategoryController::class);
        
        // Commandes
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::put('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::put('orders/{order}/payment-status', [AdminOrderController::class, 'updatePaymentStatus'])->name('orders.updatePaymentStatus');
    });
});
