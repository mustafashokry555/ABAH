<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{
    function index(Request $request) {
        try {
            $data = DB::select('usp_app_apiGetAllDoctors');
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

    function getDoctor($doctor_id) {
        $validator = Validator::make(['doctor_id' => $doctor_id], [
            'doctor_id' => 'required|exists:Employee_Mst,EmpID,Deactive,0',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 422]);
        }
        $data = DB::table('Employee_Mst')
        ->leftJoin('app_doctor_details', 'Employee_Mst.EmpID', '=', 'app_doctor_details.doctor_id')
        ->leftJoin('Department_Mst', 'Employee_Mst.Department_ID', '=', 'Department_Mst.Department_ID')
        ->where('EmpID', '=', $doctor_id)
        ->select(
            'Employee_Mst.EmpID',
            DB::raw("N'Dr. ' + FirstName + ' ' + MiddleName + ' ' + LastName AS DoctorName"),
            DB::raw("N'د. ' + R_FirstName + ' ' + R_MiddleName + ' ' + R_LastName AS DoctorNameAr"),
            DB::raw("FORMAT(Employee_Mst.BirthDate, 'yyyy-MM-dd') as BirthDate"),
            'Employee_Mst.Gender',
            DB::raw('Department_Mst.Department_ID AS DepartmentId'),
            DB::raw('Department_Mst.Department_Name AS speciality'),
            DB::raw('Department_Mst.Department_Name_Arabic AS specialityAr'),
            'app_doctor_details.*',
            DB::raw("(select CAST(AVG(CAST(rate AS DECIMAL(10, 2))) AS FLOAT) from app_rate_doctors where doctor_id = $doctor_id group by doctor_id) as rate"),
        )
        ->first();
        return response()->json(['data' => $data ,'status' => 200]);

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

    function addRate(Request $request) {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:Employee_Mst,EmpID,Deactive,0',
            'rate' => 'required|numeric|between:0,5',
            'comment' => 'string|nullable',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 422]);
        }
        $dateTime = Carbon::now();
        try{
            $PatientId = $request->user()->PatientId;
            $rate = DB::table("app_rate_doctors")
            ->where([
                'patient_id' => $PatientId,
                'doctor_id' => $request->doctor_id
            ])->first();
            if(empty($rate)){
                // inset new rate
                $rate = DB::table("app_rate_doctors")->insert([
                    "patient_id" => $PatientId,
                    "doctor_id" => $request->doctor_id,
                    "rate" => $request->rate,
                    "comment" => $request->comment??"",
                    "created_at" => $dateTime,
                    "updated_at" => $dateTime
                    ]);
                return response()->json(['message' => "Your Rating has been submitted successfully." ,'status' => 200]);
            }else{
                // update the rate
                $rate = DB::table("app_rate_doctors")
                ->where("id", $rate->id)
                ->update([
                    "rate" => $request->rate,
                    "comment" => $request->comment??"",
                    "updated_at" => $dateTime
                ]);
                return response()->json(['message' => "Your Rating has been Updated successfully." ,'status' => 200]);
            }
        } catch (\Throwable $th) {
            return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }

    }
}
