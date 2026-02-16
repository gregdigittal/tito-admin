<?php

use App\Http\Middleware\EnsureStaffToken;
use App\Livewire\JournalList;
use App\Livewire\LoginForm;
use App\Livewire\StaffCreateForm;
use App\Livewire\StaffList;
use Illuminate\Support\Facades\Route;

Route::get('/login', LoginForm::class)->middleware('throttle:5,1')->name('login');

Route::middleware([EnsureStaffToken::class])->group(function () {
    Route::get('/', fn () => redirect()->route('staff.index'))->name('dashboard');
    Route::get('/staff', StaffList::class)->name('staff.index');
    Route::get('/staff/create', StaffCreateForm::class)->name('staff.create');
    Route::get('/journals', JournalList::class)->name('journals.index');
    // TODO: Re-enable when config views are implemented
    // Route::get('/config/topup', fn () => view('config.topup'))->name('config.topup');
    // Route::get('/config/settlement', fn () => view('config.settlement'))->name('config.settlement');
});

Route::post('/logout', function () {
    app(\App\Services\TitoGraphQLService::class)->clearTokens();
    return redirect()->route('login');
})->name('logout')->middleware('web');
