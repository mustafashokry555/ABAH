<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{
    function index()  {
        try {
            $data = DB::select('usp_apiGetAllDoctors');
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }

    function department(Request $request) {
        try {
            $data = DB::select('usp_apiGetAllDoctors');
            if(isset($request->search) && isset($request->dep_id)){
                $dep_id = $request->dep_id;
                $search = strtolower(trim($request->search));

                $data = collect($data)->filter(function ($item) use ($dep_id, $search) {
                    return $item->DepartmentId == $dep_id  && 
                    (stripos(strtolower($item->DoctorName), $search) !== false || stripos(strtolower($item->DoctorNameAr), $search) !== false);
                })->values()->all();

                return response()->json(['data' => $data, 'status' => 200]);

            }elseif (isset($request->dep_id)) {
                $dep_id = $request->dep_id;
                $data = collect($data)->filter(function ($item) use ($dep_id) {
                    return $item->DepartmentId == $dep_id;
                })->values()->all();

            }elseif (isset($request->search)) {
                $search = strtolower(trim($request->search));
                $data = collect($data)->filter(function ($item) use ($search) {
                    return stripos(strtolower($item->DoctorName), $search) !== false
                    || stripos(strtolower($item->DoctorNameAr), $search) !== false;
                })->values()->all();
            }


            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }
}
