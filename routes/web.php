<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\SearchController;
use App\Http\Controllers\User\ProductController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// User Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Cart Routes - DENGAN MIDDLEWARE AUTH
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::delete('/cart/remove/{id}', [CartController::class, 'removeFromCart'])->name('cart.remove');
    Route::patch('/cart/update/{id}', [CartController::class, 'updateQuantity'])->name('cart.update');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'logoutGet'])->name('logout.get')->withoutMiddleware('csrf');

// Forgot Password Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Registration Routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Email Verification Routes
Route::get('/email/verify', [VerificationController::class, 'notice'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('/email/resend', [VerificationController::class, 'resend'])->middleware('auth')->name('verification.resend');
Route::post('/email/resend-guest', [VerificationController::class, 'resendGuest'])->name('verification.resend.guest');

// Route untuk kategori produk
Route::get('/category/{category?}', [ProductController::class, 'category'])->name('products.category');

// Route untuk detail produk
Route::get('/product/{slug}', [ProductController::class, 'detail'])->name('products.detail');

// Checkout Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/checkout/buy-now/{id}', [CheckoutController::class, 'buyNow'])->name('checkout.buy-now');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/payment/{id}', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::get('/checkout/success/{id}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::post('/checkout/cancel/{id}', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
    Route::get('/checkout/cancelled/{id}', [CheckoutController::class, 'cancelled'])->name('checkout.cancelled');
    Route::get('/checkout/invoice/{id}', [CheckoutController::class, 'invoice'])->name('checkout.invoice');
    
    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/get-json', [NotificationController::class, 'getNotifications'])->name('notifications.get-json');
    Route::delete('/notifications/destroy-read', [NotificationController::class, 'destroyRead'])->name('notifications.destroy-read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change.password');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');
    Route::get('/profile/orders', [ProfileController::class, 'orders'])->name('profile.orders');
    Route::post('/profile/orders/{id}/confirm', [ProfileController::class, 'confirmOrderCompletion'])->name('profile.orders.confirm');
});

// Midtrans callback route - harus bisa diakses tanpa CSRF
Route::post('/api/checkout/callback', [CheckoutController::class, 'callback'])
    ->name('checkout.callback')
    ->middleware('web')
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

// Admin Routes
Route::prefix('admin')->group(function () {
    // Admin auth routes
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    
    // Admin protected routes
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/dashboard/export', [AdminController::class, 'exportToExcel'])->name('admin.dashboard.export');
        Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
        Route::post('/profile/update', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
        Route::get('/change-password', [AdminController::class, 'changePassword'])->name('admin.change.password');
        Route::post('/update-password', [AdminController::class, 'updatePassword'])->name('admin.update.password');
        
        // Chat Routes
        Route::get('/chat', [App\Http\Controllers\Admin\ChatController::class, 'index'])->name('admin.chat');
        
        // Product Routes
        Route::get('/products', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('admin.products.index');
        Route::get('/products/create', [App\Http\Controllers\Admin\ProductController::class, 'create'])->name('admin.products.create');
        Route::post('/products', [App\Http\Controllers\Admin\ProductController::class, 'store'])->name('admin.products.store');
        Route::get('/products/{product}/edit', [App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('admin.products.edit');
        Route::put('/products/{product}', [App\Http\Controllers\Admin\ProductController::class, 'update'])->name('admin.products.update');
        Route::delete('/products/{product}', [App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('admin.products.destroy');
        
        // User Routes
        Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('admin.users.show');
        
        // Admin User Routes
        Route::get('/admin-users', [App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('admin.admin-users.index');
        Route::get('/admin-users/{admin}', [App\Http\Controllers\Admin\AdminUserController::class, 'show'])->name('admin.admin-users.show');
        
        // Order Routes
        Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('admin.orders.show');
        Route::patch('/orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('admin.orders.update-status');
        Route::get('/orders/{order}/invoice', [App\Http\Controllers\Admin\OrderController::class, 'invoice'])->name('admin.orders.invoice');
        
        // Admin Create Order Routes
        Route::get('/create-order', [App\Http\Controllers\Admin\AdminOrderController::class, 'create'])->name('admin.orders.create');
        Route::post('/create-order', [App\Http\Controllers\Admin\AdminOrderController::class, 'store'])->name('admin.orders.store');
        Route::get('/orders/payment/{id}', [App\Http\Controllers\Admin\AdminOrderController::class, 'payment'])->name('admin.orders.payment');
        Route::get('/admin-created-orders', [App\Http\Controllers\Admin\AdminOrderController::class, 'adminCreated'])->name('admin.orders.admin-created');
        Route::get('/orders/success/{id}', [App\Http\Controllers\Admin\AdminOrderController::class, 'success'])->name('admin.orders.success');
    });
});