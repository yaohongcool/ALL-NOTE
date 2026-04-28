<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventFileController;
use App\Http\Controllers\EventRecordController;
use App\Http\Controllers\EventTagController;
use App\Http\Controllers\PasswordController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.change.update');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/passwords', [PasswordController::class, 'index'])->name('passwords.index');
    Route::get('/passwords/create', [PasswordController::class, 'create'])->name('passwords.create');
    Route::post('/passwords', [PasswordController::class, 'store'])->name('passwords.store');
    Route::get('/passwords/{password}/edit', [PasswordController::class, 'edit'])->name('passwords.edit');
    Route::put('/passwords/{password}', [PasswordController::class, 'update'])->name('passwords.update');
    Route::delete('/passwords/{password}', [PasswordController::class, 'destroy'])->name('passwords.destroy');
    Route::post('/passwords/{password}/reveal', [PasswordController::class, 'reveal'])->name('passwords.reveal');

    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/create', [AssetController::class, 'create'])->name('assets.create');
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
    Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
    Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
    Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');

    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
    Route::put('/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::delete('/event-tags/{eventTag}', [EventTagController::class, 'destroy'])->name('event-tags.destroy');
    Route::get('/events/{event}/records/create', [EventRecordController::class, 'create'])->name('event-records.create');
    Route::post('/events/{event}/records', [EventRecordController::class, 'store'])->name('event-records.store');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::get('/event-records/{eventRecord}/edit', [EventRecordController::class, 'edit'])->name('event-records.edit');
    Route::put('/event-records/{eventRecord}', [EventRecordController::class, 'update'])->name('event-records.update');
    Route::delete('/event-records/{eventRecord}', [EventRecordController::class, 'destroy'])->name('event-records.destroy');
    Route::get('/event-files/{eventFile}', [EventFileController::class, 'show'])->name('event-files.show');
    Route::get('/event-files/{eventFile}/download', [EventFileController::class, 'download'])->name('event-files.download');
    Route::delete('/event-files/{eventFile}', [EventFileController::class, 'destroy'])->name('event-files.destroy');
});

Route::fallback(function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
});
