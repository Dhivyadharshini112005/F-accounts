<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AdvanceController;


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return view('auth.login');
})->name('login');


Route::post('/login', function (Request $request) {

    if (Auth::attempt([
        'username' => $request->username,
        'password' => $request->password
    ])) {

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    return back()
        ->withErrors([
            'username' => 'Invalid username or password.',
        ])
        ->withInput();

})->name('login.submit');


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::post('/logout', function () {

    Auth::logout();

    request()->session()->invalidate();

    request()->session()->regenerateToken();

    return redirect()->route('login');

})->name('logout');


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {


    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [
        DashboardController::class,
        'index'
    ])->name('dashboard');


    Route::get('/accounts-dashboard', [
        DashboardController::class,
        'index'
    ])->name('accounts.dashboard');


    /*
    |--------------------------------------------------------------------------
    | DRIVERS
    |--------------------------------------------------------------------------
    */

    Route::get('/drivers', function () {

        return redirect()->route('dashboard');

    })->name('drivers.index');


    /*
    |--------------------------------------------------------------------------
    | INCOME
    |--------------------------------------------------------------------------
    |
    | Income:
    | - View
    | - Add
    | - Edit
    | - Update
    | - Delete
    |
    | No income voucher.
    |
    */

    // Income List
    Route::get('/income', [
        IncomeController::class,
        'index'
    ])->name('income.index');


    // Add Income Page
    Route::get('/income/create', [
        IncomeController::class,
        'create'
    ])->name('income.create');


    // Store Income
    Route::post('/income', [
        IncomeController::class,
        'store'
    ])->name('income.store');


    // Edit Income
    Route::get('/income/{income}/edit', [
        IncomeController::class,
        'edit'
    ])->name('income.edit');


    // Update Income
    Route::put('/income/{income}', [
        IncomeController::class,
        'update'
    ])->name('income.update');


    // Delete Income
    Route::delete('/income/{income}', [
        IncomeController::class,
        'destroy'
    ])->name('income.destroy');


    /*
    |--------------------------------------------------------------------------
    | EXPENSES
    |--------------------------------------------------------------------------
    |
    | Explicit routes are used instead of Route::resource()
    | because ExpenseController does not have show().
    |
    */


    // Expense List
    Route::get('/expenses', [
        ExpenseController::class,
        'index'
    ])->name('expenses.index');


    // Add Expense Page
    Route::get('/expenses/create', [
        ExpenseController::class,
        'create'
    ])->name('expenses.create');


    // Store Expense
    Route::post('/expenses', [
        ExpenseController::class,
        'store'
    ])->name('expenses.store');


    // Edit Expense
    Route::get('/expenses/{expense}/edit', [
        ExpenseController::class,
        'edit'
    ])->name('expenses.edit');


    // Update Expense
    Route::put('/expenses/{expense}', [
        ExpenseController::class,
        'update'
    ])->name('expenses.update');


    // Delete Expense
    Route::delete('/expenses/{expense}', [
        ExpenseController::class,
        'destroy'
    ])->name('expenses.destroy');


    // Expense Voucher
    Route::get('/expenses/{expense}/voucher', [
        ExpenseController::class,
        'voucher'
    ])->name('expenses.voucher');


    /*
    |--------------------------------------------------------------------------
    | EMPLOYEE SALARY ADVANCES
    |--------------------------------------------------------------------------
    |
    | Owner:
    | View + Voucher + Add + Edit + Delete
    |
    | Manager:
    | View + Voucher + Add
    |
    */


    // Employee Advances List
    Route::get('/expenses/advances', [
        AdvanceController::class,
        'index'
    ])->name('expenses.advances.index');


    // Add Salary Advance Page
    Route::get('/expenses/advances/create', [
        AdvanceController::class,
        'create'
    ])->name('expenses.advances.create');


    // Store Salary Advance
    Route::post('/expenses/advances', [
        AdvanceController::class,
        'store'
    ])->name('expenses.advances.store');


    // Salary Advance Voucher
    Route::get('/expenses/advances/{advance}/voucher', [
        AdvanceController::class,
        'voucher'
    ])->name('expenses.advances.voucher');


    // Edit Salary Advance
    Route::get('/expenses/advances/{advance}/edit', [
        AdvanceController::class,
        'edit'
    ])->name('expenses.advances.edit');


    // Update Salary Advance
    Route::put('/expenses/advances/{advance}', [
        AdvanceController::class,
        'update'
    ])->name('expenses.advances.update');


    // Delete Salary Advance
    Route::delete('/expenses/advances/{advance}', [
        AdvanceController::class,
        'destroy'
    ])->name('expenses.advances.destroy');


    /*
    |--------------------------------------------------------------------------
    | REPORTS
    |--------------------------------------------------------------------------
    */

    Route::get('/reports', [
        ReportController::class,
        'index'
    ])->name('reports.index');


    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    Route::get('/search', [
        SearchController::class,
        'index'
    ])->name('search.index');

});