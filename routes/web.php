<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventFileController;
use App\Http\Controllers\EventRecordController;
use App\Http\Controllers\EventTagController;
use App\Http\Controllers\FallbackController;
use App\Http\Controllers\FundController;
use App\Http\Controllers\FundAccountController;
use App\Http\Controllers\FundBudgetController;
use App\Http\Controllers\FundChartController;
use App\Http\Controllers\FundEarningPeriodController;
use App\Http\Controllers\FundMonthlyController;
use App\Http\Controllers\FundRentalController;
use App\Http\Controllers\FundSkinController;
use App\Http\Controllers\FundSkinEarningController;
use App\Http\Controllers\PasswordController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.attempt');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:5,1')
        ->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'changePassword'])
        ->middleware('throttle:5,1')
        ->name('password.change.update');

    Route::get('/passwords', [PasswordController::class, 'index'])->name('passwords.index');
    Route::get('/passwords/create', [PasswordController::class, 'create'])->name('passwords.create');
    Route::post('/passwords', [PasswordController::class, 'store'])->name('passwords.store');
    Route::get('/passwords/{password}/edit', [PasswordController::class, 'edit'])->name('passwords.edit');
    Route::put('/passwords/{password}', [PasswordController::class, 'update'])->name('passwords.update');
    Route::delete('/passwords/{password}', [PasswordController::class, 'destroy'])->name('passwords.destroy');
    Route::post('/passwords/{password}/reveal', [PasswordController::class, 'reveal'])
        ->middleware('throttle:30,1')
        ->name('passwords.reveal');

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

    // 资金记录
    Route::get('/funds', [FundController::class, 'index'])->name('funds.index');
    Route::get('/funds/statistics', [FundController::class, 'statistics'])->name('funds.statistics');
    Route::get('/funds/historical-earnings', [FundController::class, 'historicalEarnings'])->name('funds.historical-earnings');
    Route::get('/funds/historical-earnings/data', [FundEarningPeriodController::class, 'index'])->name('funds.historical-earnings.data');
    Route::post('/funds/historical-earnings/periods', [FundEarningPeriodController::class, 'store'])->name('funds.historical-earnings.periods.store');
    Route::put('/funds/historical-earnings/periods/{period}', [FundEarningPeriodController::class, 'update'])->name('funds.historical-earnings.periods.update');
    Route::delete('/funds/historical-earnings/periods/{period}', [FundEarningPeriodController::class, 'destroy'])->name('funds.historical-earnings.periods.destroy');
    Route::get('/funds/chart-data', [FundChartController::class, 'chartData'])->name('funds.chart-data');
    Route::resource('funds/accounts', FundAccountController::class)->names(['index' => 'funds.accounts.index', 'create' => 'funds.accounts.create', 'store' => 'funds.accounts.store', 'edit' => 'funds.accounts.edit', 'update' => 'funds.accounts.update', 'destroy' => 'funds.accounts.destroy']);
    Route::resource('funds/budgets', FundBudgetController::class)->names(['index' => 'funds.budgets.index', 'create' => 'funds.budgets.create', 'store' => 'funds.budgets.store', 'edit' => 'funds.budgets.edit', 'update' => 'funds.budgets.update', 'destroy' => 'funds.budgets.destroy']);
    Route::resource('funds/monthlies', FundMonthlyController::class)->names(['index' => 'funds.monthlies.index', 'create' => 'funds.monthlies.create', 'store' => 'funds.monthlies.store', 'edit' => 'funds.monthlies.edit', 'update' => 'funds.monthlies.update', 'destroy' => 'funds.monthlies.destroy']);
    Route::resource('funds/skins', FundSkinController::class)->names(['index' => 'funds.skins.index', 'create' => 'funds.skins.create', 'store' => 'funds.skins.store', 'edit' => 'funds.skins.edit', 'update' => 'funds.skins.update', 'destroy' => 'funds.skins.destroy'])->except(['show']);
    Route::resource('funds/skins/{skin}/rentals', FundRentalController::class)->names(['index' => 'funds.rentals.index', 'create' => 'funds.rentals.create', 'store' => 'funds.rentals.store', 'edit' => 'funds.rentals.edit', 'update' => 'funds.rentals.update', 'destroy' => 'funds.rentals.destroy'])->except(['show']);
    Route::resource('funds/skins/{skin}/earnings', FundSkinEarningController::class)->names(['index' => 'funds.skin-earnings.index', 'create' => 'funds.skin-earnings.create', 'store' => 'funds.skin-earnings.store', 'edit' => 'funds.skin-earnings.edit', 'update' => 'funds.skin-earnings.update', 'destroy' => 'funds.skin-earnings.destroy'])->except(['show']);
});

Route::fallback([FallbackController::class, '__invoke']);
