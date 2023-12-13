<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    function index(Request $request) {
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

    function avilSlots(Request $request) {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required',
            'day' => 'required|date_format:Y-m-d',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 422]);
        }
        try {
            $data = DB::select('usp_app_apiGetDocAppSlots ?, ?, ?, ?',[
                $request->doctor_id,
                $request->day,
                $request->day,
                '1'
            ]);
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // throw $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }
}
