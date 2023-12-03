<?php

use App\Http\Controllers\Api\DepartmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/login', function () {
    return response()->json(['message' => 'Unauthorized.', 'status' => 401]);
})->name('login');

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/departments', [DepartmentController::class,'index']);
