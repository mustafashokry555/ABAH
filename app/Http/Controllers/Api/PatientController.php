<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    function insurance(Request $request) {

        if(!isset($request->user()->PatientId)){
            return response()->json(['errors' => 'The patient id field is required.', 'status' => 422]);
        }
        try {
            $data = DB::select('usp_app_apiPatientInsuranceDetails ?', [
                $request->user()->PatientId,
            ]);
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }

    function radiologyReports(Request $request) {
        try {
            $data = DB::select('usp_apiGetAllRadioDatewise ?, ?', [
                $request->user()->PatientId,
                $request->user()->Hospital_ID,
            ]);
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }

    function labResults(Request $request) {
        try {
            $data = DB::select('usp_apigetLabPatwiseResults ?', [
                $request->user()->PatientId,
            ]);
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }

    function prescriptions(Request $request) {
        try {
            $data = DB::select('usp_apiGetAllPrescriptions ?, ?', [
                $request->user()->PatientId,
                $request->user()->Hospital_ID,
            ]);
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }

    function bills(Request $request) {
        $validator = Validator::make($request->all(), [
            'month' => 'required',
            'year' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 422]);
        }
        try {
            $data = DB::select('usp_apiGetAllInvoices ?, ?, ?, ?', [
                $request->user()->Registration_No,
                $request->user()->Hospital_ID,
                $request->month,
                $request->year,
            ]);
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);

        }
    }

    function visit_history(Request $request) {

        try {
            $data = DB::select('usp_api_GetAllVisitList ?, ?, ?', [
                $request->user()->PatientId,
                $request->user()->Hospital_ID,
                NULL,
            ]);
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);

        }
    }

    function myRate(Request $request) {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:Employee_Mst,EmpID,Deactive,0',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 422]);
        }
        try{
            $PatientId = $request->user()->PatientId;
            $rate = DB::table("app_rate_doctors")
            ->where([
                'patient_id' => $PatientId,
                'doctor_id' => $request->doctor_id
            ])->first();
            return response()->json(['data' => $rate ,'status' => 200]);
        } catch (\Throwable $th) {
            return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }
}
