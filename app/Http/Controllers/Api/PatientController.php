<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    function insurance(Request $request) {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer',
            'hospital_id' => 'required|integer',
            'month' => 'required',
            'year' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 422]);
        }
        try {
            $data = DB::select('usp_apiGetPatientInsuranceDetails ?, ?, ?, ?', [
                $request->patient_id,
                $request->hospital_id,
                $request->month,
                $request->year,
            ]);
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }

    function radiologyReports(Request $request) {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer',
            'hospital_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 422]);
        }
        try {
            $data = DB::select('usp_apiGetAllRadioDatewise ?, ?', [
                $request->patient_id,
                $request->hospital_id,
            ]);
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }

    function labResults(Request $request) {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 422]);
        }
        try {
            $data = DB::select('usp_apigetLabPatwiseResults ?', [
                $request->patient_id,
            ]);
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }

    function prescriptions(Request $request) {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer',
            'hospital_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 422]);
        }
        try {
            $data = DB::select('usp_apiGetAllPrescriptions ?, ?', [
                $request->patient_id,
                $request->hospital_id,
            ]);
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }

    function bills(Request $request) {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer',
            'hospital_id' => 'required|integer',
            'month' => 'required',
            'year' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 422]);
        }
        try {
            $data = DB::select('usp_apiGetAllInvoices ?, ?, ?, ?', [
                $request->patient_id,
                $request->hospital_id,
                $request->month,
                $request->year,
            ]);
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);

        }
    }
}
