<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnecdoteController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\UserController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'index']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum', 'role:visiteur,userConnecter,admin'])->get('/anecdotes', [AnecdoteController::class, 'index']);

Route::middleware(['auth:sanctum', 'role:userConnecter,admin'])->post('/anecdotes', [AnecdoteController::class, 'store']);

Route::middleware(['auth:sanctum', 'role:admin'])->delete('/anecdotes/{id}', [AnecdoteController::class, 'destroy']);

Route::middleware(['auth:sanctum', 'role:admin'])->get('/users', [UserController::class, 'index']);

Route::middleware(['auth:sanctum', 'role:userConnecter,admin'])->post('/anecdotes/{id}/vote', [VoteController::class, 'store']);