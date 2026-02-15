<?php

use App\Http\Middleware\EnsureStaffToken;
use App\Livewire\JournalList;
use App\Livewire\LoginForm;
use App\Livewire\StaffCreateForm;
use App\Livewire\StaffList;
use Illuminate\Support\Facades\Route;

Route::get('/login', LoginForm::class)->name('login');

Route::middleware([EnsureStaffToken::class])->group(function () {
    Route::get('/', fn () => redirect()->route('staff.index'))->name('dashboard');
    Route::get('/staff', StaffList::class)->name('staff.index');
    Route::get('/staff/create', StaffCreateForm::class)->name('staff.create');
    Route::get('/journals', JournalList::class)->name('journals.index');
});

Route::post('/logout', function () {
    app(\App\Services\TitoGraphQLService::class)->clearTokens();
    return redirect()->route('login');
})->name('logout')->middleware('web');
