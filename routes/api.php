<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\SettingController;
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
/*
Response Status
    200 - OK
    401 - Unauthorized
    404 - Not Found
    422 - Validation Errors
    500 - DataBase Errors
*/

// Auth Routs
// Route::get('/PDF', [DepartmentController::class, 'PD']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/setNewPass', [AuthController::class, 'setNewPass']);
Route::post('/forgetPass', [AuthController::class, 'forgetPass']);

// departments Routs
Route::group(['prefix' => 'departments'], function () {
    Route::get('/all', [DepartmentController::class,'index']);
});

// doctors Routs
Route::group(['prefix' => 'doctors'], function () {
    Route::get('/doctor/{doctor_id}', [DoctorController::class,'getDoctor']);
    Route::get('/all', [DoctorController::class,'index']);
    Route::post('/avilSlots', [DoctorController::class,'avilSlots']);
});

// patients Routs
Route::group(['prefix' => 'patient'], function () {
    Route::post('/makeAppointment', [PatientController::class,'makeAppointment']);
    Route::post('/cancelAppointment', [PatientController::class,'cancelAppointment']);
});

//Basic Data
Route::get('/basicData', [SettingController::class,'index']);

//Garded Routs
Route::middleware('auth:sanctum')->group(function () {
    // patients Routs
    Route::group(['prefix' => 'patient'], function () {
        Route::post('/info', function (Request $request) {
            return response()->json(['data' => $request->user(), 'status' => 200]);
        });
        Route::get('/insurance', [PatientController::class,'insurance']);
        Route::get('/radio/reports', [PatientController::class,'radiologyReports']);
        Route::get('/lab/results', [PatientController::class,'labResults']);
        Route::get('/prescriptions', [PatientController::class,'prescriptions']);
        Route::get('/bills', [PatientController::class,'bills']);
        Route::get('/visit_history', [PatientController::class,'visit_history']);
        Route::get('/myRate', [PatientController::class,'myRate']);
        Route::get('/appointments', [PatientController::class,'appointment']);

    });

    // Auth
    Route::post('/changePass', [AuthController::class,'changePass']);
    Route::post('/logout', [AuthController::class,'logout']);
    

    // doctors Routs
    Route::group(['prefix' => 'doctors'], function () {
        Route::get('/rate', [DoctorController::class,'addRate']);
    });
});