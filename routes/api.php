<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:api');

Route::get('/email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::get('/email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

Route::post('/forgot-password', [ForgotController::class, 'forgot']);
Route::post('/reset-password', [ForgotController::class, 'reset']);
Route::get('/v-reset-password', [ForgotController::class, 'redirectVue'])->name('password.reset');

Route::post('/company', [CompanyController::class, 'index']);
Route::post('/find-company', [CompanyController::class, 'findCompanyByName']);
Route::get('/favourite-company', [CompanyController::class, 'favouriteCompany']);
Route::get('/mark-company/{companyId}', [CompanyController::class, 'markCompany']);
Route::get('/unmark-company/{companyId}', [CompanyController::class, 'unmarkCompany']);

