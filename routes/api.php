<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Admin\CategoryController as CategoryAdminController;
use App\Http\Controllers\Admin\ProductController as ProductAdminController;
use App\Http\Controllers\Admin\OrderController as OrderAdminController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\OrderController as OrderCustomerController;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\RecommendationController;
use App\Http\Controllers\RecommendationSysController;
use App\Http\Controllers\Customer\WishlistController;
use App\Http\Controllers\Customer\UserController;

// Public routes
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/verify-email', action: [RegisterController::class, 'verifyEmail']);
Route::post('/password-reset/request', [PasswordResetController::class, 'sendOtp']);
Route::post('/password-reset/reset', [PasswordResetController::class, 'resetPassword']);
Route::get('/search', [SearchController::class, 'search']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/category/{id}', [ProductController::class, 'productsByCategory']);
Route::get('/categories', [ProductController::class, 'get_categories']);

Route::prefix('recommend')->group(function () {
    Route::get('/general', [RecommendationSysController::class, 'general']);
    Route::get('/similar/{product_id}', [RecommendationSysController::class, 'similar']);
});
Route::post('/stripe/webhook', [PaymentController::class, 'handleWebhook']);
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);

    Route::post('/resend-otp', [EmailVerificationController::class, 'resendOtp']);

    Route::middleware(['verified'])->group(function () {
        Route::prefix('admin')->middleware(['role:admin'])->group(function () {
            Route::get('/statistics', [StatisticsController::class, 'index'])->name('admin.statistics.index');

            // Category Routes
            Route::get('categories', [CategoryAdminController::class,'index']);
            Route::post('categories', [CategoryAdminController::class,'store']);
            Route::post('categories/{id}', [CategoryAdminController::class,'update']);
            Route::delete('categories/{id}', [CategoryAdminController::class,'destroy']);
            // Product Routes
            Route::get('products', [ProductAdminController::class,'index']);
            Route::post('products', [ProductAdminController::class,'store']);
            Route::post('products/{id}', [ProductAdminController::class,'update']);
            Route::delete('products/{id}', [ProductAdminController::class,'destroy']);

            Route::get('/orders', [OrderAdminController::class, 'index'])->name('admin.orders.index');
            Route::get('/orders/{id}', [OrderAdminController::class, 'show'])->name('admin.orders.show');
            Route::put('/orders/{id}', [OrderAdminController::class, 'update'])->name('admin.orders.update');
            Route::delete('/orders/{id}', [OrderAdminController::class, 'destroy'])->name('admin.orders.destroy');
            
            Route::post('/train-model', [RecommendationController::class, 'train'])->name('admin.train-model');
            Route::post('/train-model-async', [RecommendationController::class, 'trainAsync'])->name('admin.train-model-async');
        });

        Route::prefix('customer')->middleware(['role:customer'])->group(function () {

            Route::post('/update-order-status', [PaymentController::class, 'updateOrderStatus']);
            Route::get('/cart', [CartController::class, 'index']); // View Cart
            Route::post('/cart', [CartController::class, 'store']); // Add to Cart
            Route::put('/cart/{id}', [CartController::class, 'update']); // Update Cart Item
            Route::delete('/cart/{id}', [CartController::class, 'destroy']); // Remove Cart Item
            Route::delete('/cart', [CartController::class, 'clearCart']); // Clear Cart

            // Order Routes
            Route::post('/orders', [OrderCustomerController::class, 'store']); // Create Order
            Route::get('/orders', [OrderCustomerController::class, 'index']); // List Orders
            Route::get('/orders/{id}', [OrderCustomerController::class, 'show']); // View Specific Order
            Route::get('/pending-orders', [OrderCustomerController::class, 'pendingOrder']); // View Pending Order

            Route::post('/create-payment-intent', [PaymentController::class, 'createPaymentIntent']);

            Route::post('/wishlist/add/{product_id}', [WishlistController::class, 'addToWishlist'])->name('wishlist.add');
            Route::delete('/wishlist/remove/{product_id}', [WishlistController::class, 'removeFromWishlist'])->name('wishlist.remove');
            Route::get('/wishlist', [WishlistController::class, 'getWishlist'])->name('wishlist.get');

            Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
            Route::put('/profile/update', [UserController::class, 'updateProfile'])->name('user.update');
            // User Profile Routes
        });
    });
});
//stripe listen --forward-to localhost:8000/api/stripe/webhook
//stripe trigger payment_intent.succeeded