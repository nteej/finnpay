<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CustomerPaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FreelancerProfileController;
use App\Http\Controllers\PaymentReferenceController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\ReleaseController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', function () {
    if (auth()->check()) return redirect('/dashboard');
    $usdRate = \App\Models\ExchangeRate::getRate('USD');
    $eurRate = \App\Models\ExchangeRate::getRate('EUR');
    $updatedAt = \App\Models\ExchangeRate::where('is_active', true)
        ->latest('updated_at')->value('updated_at');
    $featuredFreelancers = \App\Models\FreelancerProfile::public()
        ->with('user')
        ->latest()
        ->take(3)
        ->get();
    return view('landing', compact('usdRate', 'eurRate', 'updatedAt', 'featuredFreelancers'));
})->name('home');

// Public customer payment page
Route::get('/pay/{reference}', [CustomerPaymentController::class, 'show'])->name('customer.pay');
Route::post('/pay/{reference}', [CustomerPaymentController::class, 'pay'])->name('customer.pay.submit');

// Public freelancer directory
Route::get('/freelancers', [PublicProfileController::class, 'index'])->name('freelancers.index');
Route::get('/freelancers/{slug}', [PublicProfileController::class, 'show'])->name('freelancers.show');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Authenticated (any user) — logout + pending verification page
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/verification/pending', function () {
        if (auth()->user()->is_verified) {
            return redirect('/dashboard');
        }
        return view('auth.pending-verification');
    })->name('verification.pending');
});

// Authenticated + verified freelancer routes
Route::middleware(['auth', \App\Http\Middleware\EnsureUserIsVerified::class])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('references', PaymentReferenceController::class)
        ->only(['index', 'create', 'store', 'show', 'destroy']);

    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    Route::get('/releases', [ReleaseController::class, 'index'])->name('releases.index');
    Route::get('/releases/{release}', [ReleaseController::class, 'show'])->name('releases.show');
    Route::post('/releases/process', [ReleaseController::class, 'process'])->name('releases.process');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
    Route::post('/packages/{package}/select', [PackageController::class, 'select'])->name('packages.select');

    // Freelancer public profile management
    Route::get('/freelancer/profile', [FreelancerProfileController::class, 'edit'])->name('freelancer.profile.edit');
    Route::patch('/freelancer/profile', [FreelancerProfileController::class, 'update'])->name('freelancer.profile.update');
    Route::post('/freelancer/profile/work', [FreelancerProfileController::class, 'storeWork'])->name('freelancer.profile.work.store');
    Route::delete('/freelancer/profile/work/{entry}', [FreelancerProfileController::class, 'destroyWork'])->name('freelancer.profile.work.destroy');

    Route::post('/bank-accounts', [BankAccountController::class, 'store'])->name('bank-accounts.store');
    Route::delete('/bank-accounts/{bankAccount}', [BankAccountController::class, 'destroy'])->name('bank-accounts.destroy');
    Route::patch('/bank-accounts/{bankAccount}/default', [BankAccountController::class, 'setDefault'])->name('bank-accounts.setDefault');
});
