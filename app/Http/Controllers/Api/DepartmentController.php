<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    function index()  {
        try {
            $data = DB::select('usp_app_apiGetAllDepartments');
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['error' => 'Database Error !', 'errorAr' => 'خطأ في قاعده البيانات!','status' => 500]);
        }
    }
}
