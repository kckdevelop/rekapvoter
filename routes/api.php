<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CandidateApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\QuickCountApiController;
use App\Http\Controllers\Api\RealCountApiController;
use App\Http\Controllers\Api\TpsApiController;
use App\Http\Controllers\Api\UserApiController;
use App\Http\Controllers\Api\VoterApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile REST API Routes (Protected by Laravel Sanctum Token)
|--------------------------------------------------------------------------
|
| Base URL  : /api
| Auth      : Laravel Sanctum (Bearer Token)
| Format    : JSON
|
*/

// ─── 🔓 Public Routes ────────────────────────────────────────────────────────

Route::post('/login', [AuthController::class, 'login']);

// ─── 🔒 Protected Routes (Sanctum Token required) ────────────────────────────

Route::middleware('auth:sanctum')->group(function () {

    // ── Auth & Profile ──────────────────────────────────────────────────────
    Route::get('/me',               [AuthController::class, 'me']);
    Route::post('/logout',          [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // ── Dashboard Mobile ────────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardApiController::class, 'index']);

    // ── Quick Count (Fitur Hitung Cepat TPS) ──────────────────────────────────
    Route::get('/quickcount',              [QuickCountApiController::class, 'index']);
    Route::get('/quickcount/summary',      [QuickCountApiController::class, 'summary']);
    Route::get('/quickcount/{tps}',        [QuickCountApiController::class, 'show']);
    Route::post('/quickcount/{tps}',       [QuickCountApiController::class, 'submit']);
    Route::delete('/quickcount/{tps}/reset', [QuickCountApiController::class, 'reset']);
    Route::post('/quickcount/{tps}/reset',   [QuickCountApiController::class, 'reset']);

    // ── Real Count (Fitur Hitung Real C1 TPS) ────────────────────────────────
    Route::get('/realcount',              [RealCountApiController::class, 'index']);
    Route::get('/realcount/summary',      [RealCountApiController::class, 'summary']);
    Route::get('/realcount/{tps}',        [RealCountApiController::class, 'show']);
    Route::post('/realcount/{tps}',       [RealCountApiController::class, 'submit']);
    Route::delete('/realcount/{tps}/reset', [RealCountApiController::class, 'reset']);
    Route::post('/realcount/{tps}/reset',   [RealCountApiController::class, 'reset']);

    // ── TPS ─────────────────────────────────────────────────────────────────
    Route::get('/tps',       [TpsApiController::class, 'index']);
    Route::post('/tps',      [TpsApiController::class, 'store']);

    // ── Candidates (Master Data Calon) ──────────────────────────────────────
    Route::get('/candidates',            [CandidateApiController::class, 'index']);
    Route::get('/candidates/{candidate}', [CandidateApiController::class, 'show']);
    Route::post('/candidates',           [CandidateApiController::class, 'store']);
    Route::put('/candidates/{candidate}', [CandidateApiController::class, 'update']);
    Route::delete('/candidates/{candidate}', [CandidateApiController::class, 'destroy']);

    // ── Voters (Data Pemilih DPT TPS) ────────────────────────────────────────
    Route::get('/voters',                      [VoterApiController::class, 'index']);
    Route::post('/voters',                     [VoterApiController::class, 'store']);
    Route::post('/voters/bulk-supporter',      [VoterApiController::class, 'bulkSupporter']);
    Route::post('/voters/bulk-delete',         [VoterApiController::class, 'bulkDelete']);
    Route::delete('/voters/bulk-delete',       [VoterApiController::class, 'bulkDelete']);
    Route::get('/voters/{voter}',              [VoterApiController::class, 'show']);
    Route::match(['put', 'patch', 'post'], '/voters/{voter}', [VoterApiController::class, 'update']);
    Route::delete('/voters/{voter}',           [VoterApiController::class, 'destroy']);
    Route::post('/voters/{voter}/delete',      [VoterApiController::class, 'destroy']);
    Route::post('/voters/{voter}/toggle',      [VoterApiController::class, 'toggleSupporter']);

    // ── Supporters & Reports ─────────────────────────────────────────────────
    Route::get('/supporters', [VoterApiController::class, 'supporters']);
    Route::get('/laporan',    [VoterApiController::class, 'laporan']);

    // ── User / Akun Saksi TPS Management (Admin only) ────────────────────────
    Route::middleware('admin')->group(function () {
        Route::get('/users',          [UserApiController::class, 'index']);
        Route::post('/users',         [UserApiController::class, 'store']);
        Route::get('/users/{user}',   [UserApiController::class, 'show']);
        Route::put('/users/{user}',   [UserApiController::class, 'update']);
        Route::delete('/users/{user}', [UserApiController::class, 'destroy']);
    });
});
