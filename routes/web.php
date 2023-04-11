<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\StripeProductController;
use App\Http\Controllers\Auth\GoogleAuthController;


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


//GOOGLE OAUTH
Route::get('/login/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/login/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

//HOME
Route::get('/', function () {
    return view('welcome');
});

//DASHBOARD
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//PROFILE ROUTES
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//USERS, ROLES, PERMISSIONS and PRODUCTS ROUTES
Route::group(['prefix' => 'admin', 'middleware' => ['permission:manage_all']], function () {
    //get '/', return index.blade.php
    Route::get('/', function () {
        return view('admin.index');
    })->name('admin.index');


    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::resource('users', UserController::class);
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/visibility', [ProductController::class, 'setVisibility'])->name('products.setVisibility');
});

//STRIPE ROUTES
Route::middleware(['auth'])->group(function () {
    Route::post('/create-checkout-session', [StripeController::class, 'createCheckoutSession']);
    Route::get('/payment-success', [StripeController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment-cancel', [StripeController::class, 'paymentCancel'])->name('payment.cancel');
    Route::get('/get-payment-info-from-stripe', [StripeController::class, 'getPaymentInfoFromStripe']);
    Route::post('/update-role-to-registered', [StripeController::class, 'updateRoleToRegistered']);
    Route::get('/sync-stripe-products', [StripeProductController::class, 'sync']);
});
Route::post('/stripe-webhook', [StripeController::class, 'handleStripeWebhook']);






//GET PAYMENT INTENT INFO FROM DB
Route::middleware(['auth'])->group(function () {
    Route::get('get-user-payment-info-from-db/{user_id}', [StripeController::class, 'getUserPaymentInfoFromDb']);
});


//CONTACT
Route::get('/contact', function () {
    return view('contact');
})->name('contact');
Route::post('/contact/submit', [ContactController::class, 'submit'])->name('contact.submit');


require __DIR__.'/auth.php';
