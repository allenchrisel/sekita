<?php

use App\Enums\UserRole;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReviewDisputeController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\VerificationDocumentController as AdminVerificationDocumentController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Provider\DashboardController as ProviderDashboardController;
use App\Http\Controllers\Provider\PortfolioController;
use App\Http\Controllers\Provider\ProfileController as ProviderProfileController;
use App\Http\Controllers\Provider\ReviewManagementController;
use App\Http\Controllers\Provider\VerificationDocumentController as ProviderVerificationDocumentController;
use App\Http\Controllers\ProviderPublicController;
use App\Http\Controllers\ReviewController;
use App\Http\Middleware\UpdateUserLastOnline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/locations/{provinceCode}/regencies', [LocationController::class, 'regencies'])
    ->where('provinceCode', '[0-9.]+')->name('locations.regencies');
Route::get('/locations/{regencyCode}/districts', [LocationController::class, 'districts'])
    ->where('regencyCode', '[0-9.]+')->name('locations.districts');
Route::get('/providers/{providerProfile}', [ProviderPublicController::class, 'show'])
    ->name('providers.show');
Route::post('/providers/{providerProfile}/reviews', [ReviewController::class, 'store'])
    ->middleware(['auth', 'role:CLIENT'])
    ->name('reviews.store');

Route::get('/dashboard', function (Request $request) {
    return match ($request->user()->role) {
        UserRole::ADMIN => redirect()->route('admin.dashboard'),
        UserRole::PROVIDER => redirect()->route('provider.dashboard'),
        default => redirect()->route('client.dashboard'),
    };
})->middleware('auth')->name('dashboard');

Route::middleware(['auth', 'role:CLIENT'])->group(function (): void {
    Route::get('/client/dashboard', ClientDashboardController::class)->name('client.dashboard');
    Route::patch('/client/reviews/{review}', [ReviewController::class, 'update'])->name('client.reviews.update');
    Route::delete('/client/reviews/{review}', [ReviewController::class, 'destroy'])->name('client.reviews.destroy');
});

Route::prefix('provider')->name('provider.')->middleware(['auth', 'role:PROVIDER', UpdateUserLastOnline::class])->group(function (): void {
    Route::get('/dashboard', ProviderDashboardController::class)->name('dashboard');
    Route::get('/profile', [ProviderProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProviderProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/portfolio', [PortfolioController::class, 'store'])->name('profile.portfolio.store');
    Route::delete('/profile/portfolio/{portfolioGallery}', [PortfolioController::class, 'destroy'])->name('profile.portfolio.destroy');
    Route::get('/documents', [ProviderVerificationDocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents', [ProviderVerificationDocumentController::class, 'store'])->name('documents.store');
    Route::get('/reviews', [ReviewManagementController::class, 'index'])->name('reviews.index');
    Route::put('/reviews/{review}/reply', [ReviewManagementController::class, 'reply'])->name('reviews.reply');
    Route::post('/reviews/{review}/dispute', [ReviewManagementController::class, 'dispute'])->name('reviews.dispute');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:ADMIN'])->group(function (): void {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::get('/documents', [AdminVerificationDocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/{verificationDocument}/download', [AdminVerificationDocumentController::class, 'download'])->name('documents.download');
    Route::patch('/documents/{verificationDocument}', [AdminVerificationDocumentController::class, 'update'])->name('documents.update');
    Route::get('/disputes', [ReviewDisputeController::class, 'index'])->name('disputes.index');
    Route::patch('/disputes/{reviewDispute}', [ReviewDisputeController::class, 'update'])->name('disputes.update');
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
