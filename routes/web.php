<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

// ── Frontend Public Routes ──────────────────────────────────────────────────
Route::get('/', [FrontendController::class, 'home'])->name('frontend.home');
Route::get('/gallery', [FrontendController::class, 'gallery'])->name('frontend.gallery');
Route::get('/events', [FrontendController::class, 'events'])->name('frontend.events');
Route::get('/members', [FrontendController::class, 'members'])->name('frontend.members');
Route::get('/family-history', [FrontendController::class, 'familyHistory'])->name('frontend.family-history');
Route::get('/contact', [FrontendController::class, 'contact'])->name('frontend.contact');
Route::post('/contact', [FrontendController::class, 'sendContact'])->name('frontend.contact.send');

Route::redirect('/login', '/admin')->name('login');

Route::get('/admin', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin', [AdminAuthController::class, 'login'])->name('admin.login.submit');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminAuthController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/members', [AdminAuthController::class, 'buyToken'])->name('admin.members');
    Route::post('/members/items', [AdminAuthController::class, 'storeMember'])->name('admin.members.store');
    Route::put('/members/items/{member}', [AdminAuthController::class, 'updateMember'])->name('admin.members.update');
    Route::delete('/members/items/{member}', [AdminAuthController::class, 'deleteMember'])->name('admin.members.delete');
    Route::get('/gallery', [AdminAuthController::class, 'icoDistribution'])->name('admin.gallery');
    Route::post('/gallery/items', [AdminAuthController::class, 'storeGalleryPhoto'])->name('admin.gallery.store');
    Route::delete('/gallery/items/{photo}', [AdminAuthController::class, 'deleteGalleryPhoto'])->name('admin.gallery.delete');
    Route::get('/event-news', [AdminAuthController::class, 'transactions'])->name('admin.event-news');
    Route::post('/event-news/items', [AdminAuthController::class, 'storeEventNews'])->name('admin.event-news.store');
    Route::put('/event-news/items/{item}', [AdminAuthController::class, 'updateEventNews'])->name('admin.event-news.update');
    Route::delete('/event-news/items/{item}', [AdminAuthController::class, 'deleteEventNews'])->name('admin.event-news.delete');
    Route::get('/landing-slides', [AdminAuthController::class, 'landingSlides'])->name('admin.landing-slides');
    Route::post('/landing-slides', [AdminAuthController::class, 'storeLandingSlide'])->name('admin.landing-slides.store');
    Route::put('/landing-slides/{slide}', [AdminAuthController::class, 'updateLandingSlide'])->name('admin.landing-slides.update');
    Route::delete('/landing-slides/{slide}', [AdminAuthController::class, 'deleteLandingSlide'])->name('admin.landing-slides.delete');
    Route::get('/family-history', [AdminAuthController::class, 'familyHistory'])->name('admin.family-history');
    Route::post('/family-history', [AdminAuthController::class, 'updateFamilyHistory'])->name('admin.family-history.update');
    Route::get('/contact-messages', [AdminAuthController::class, 'contactMessages'])->name('admin.contact-messages');
    Route::get('/custom-messages', [AdminAuthController::class, 'customMessages'])->name('admin.custom-messages');
    Route::post('/custom-messages', [AdminAuthController::class, 'sendCustomMessage'])->name('admin.custom-messages.send');
    Route::get('/profile', [AdminAuthController::class, 'profile'])->name('admin.profile');
    Route::post('/profile/personal-data', [AdminAuthController::class, 'updateProfile'])->name('admin.profile.update');
    Route::post('/profile/password', [AdminAuthController::class, 'updatePassword'])->name('admin.profile.password.update');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});
        Route::delete('/landing-slides/{slide}', [AdminAuthController::class, 'deleteLandingSlide'])->name('admin.landing-slides.delete');
