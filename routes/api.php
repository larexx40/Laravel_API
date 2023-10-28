<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserBankAccountController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//register route in api
Route::name('auth')->group(base_path ('routes/authroute.php'));
Route::name('userbank')->group(base_path ('routes/userBankroute.php'));
Route::name('user')->group(base_path ('routes/userRoute.php'));
Route::name('admin')->group(base_path ('routes/adminRoute.php'));
// Route::name('sendgrid')->group(base_path ('routes/sendgridRoute.php'));


// Route::get('users', [UserController::class, 'index']);
// Route::get('user/{id}', [UserController::class, 'show']);
// Route::post('adduser', [UserController::class, 'store']);


// //protected routes
// Route::group(['middleware' => 'auth:api'], function () {
//     // Protected routes go here
// });
