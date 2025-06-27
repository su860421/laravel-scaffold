<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// User API Routes - Only includes basic CRUD operations
Route::apiResource('users', UserController::class);

// This will automatically generate the following routes:
// GET    /api/users          - index (display all users)
// POST   /api/users          - store (create new user)
// GET    /api/users/{id}     - show (display specific user)
// PUT    /api/users/{id}     - update (update user)
// DELETE /api/users/{id}     - destroy (delete user)

// If authentication is required, you can use the following:
// Route::middleware(['auth:api'])->group(function () {
//     Route::apiResource('users', UserController::class);
// });

Route::fallback(function ($request) {
    return response()->json([
        'message' => 'Page Not Found. If you are calling a resource, make sure to include the HTTP method. Example: GET, POST, PUT, DELETE.'
    ], 404);
});
