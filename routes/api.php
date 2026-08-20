<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CandidateApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\RealCountApiController;
use App\Http\Controllers\Api\TpsApiController;
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
    Route::get('/me',      [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // ── Dashboard Mobile ────────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardApiController::class, 'index']);

    // ── TPS ─────────────────────────────────────────────────────────────────
    Route::get('/tps',       [TpsApiController::class, 'index']);
    Route::post('/tps',      [TpsApiController::class, 'store']);

    // ── Candidates (Master Data Calon) ──────────────────────────────────────
    Route::get('/candidates',            [CandidateApiController::class, 'index']);
    Route::get('/candidates/{candidate}', [CandidateApiController::class, 'show']);
    Route::post('/candidates',           [CandidateApiController::class, 'store']);
    Route::put('/candidates/{candidate}', [CandidateApiController::class, 'update']);
    Route::delete('/candidates/{candidate}', [CandidateApiController::class, 'destroy']);

    // ── Real Count ──────────────────────────────────────────────────────────
    Route::get('/realcount',          [RealCountApiController::class, 'index']);
    Route::get('/realcount/summary',  [RealCountApiController::class, 'summary']);
    Route::get('/realcount/{tps}',    [RealCountApiController::class, 'show']);
    Route::post('/realcount/{tps}',   [RealCountApiController::class, 'submit']);

    // ── Voters ──────────────────────────────────────────────────────────────
    Route::get('/voters',                      [VoterApiController::class, 'index']);
    Route::post('/voters',                     [VoterApiController::class, 'store']);
    Route::post('/voters/{voter}/toggle',      [VoterApiController::class, 'toggleSupporter']);
    Route::post('/voters/bulk-supporter',      [VoterApiController::class, 'bulkSupporter']);

    // ── Supporters & Reports ─────────────────────────────────────────────────
    Route::get('/supporters', [VoterApiController::class, 'supporters']);
    Route::get('/laporan',    [VoterApiController::class, 'laporan']);
});
