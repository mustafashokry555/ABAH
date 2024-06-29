<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\DB;

class PDFController extends Controller
{
    public function RedioPDF($id, Request $request) 
    {
        try {
            $data = DB::select('usp_app_apiGetRadioID ?', [
                $id,
            ]);
            $data = $data[0];
            $data2 = DB::select('usp_app_apiGetPatientRadioResult ?', [
                $id,
            ]);
            $data->RadiologyResult = $data2[0]->RadiologyResult;
            $patient = $request->user();
            // return View('RedioReport', compact('data', 'patient'));
            $pdf = PDF::loadView('RedioReport', compact('data', 'patient'));
            return $pdf->download("RedioReport-$id.pdf");
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['error' => 'Database Error !', 'errorAr' => 'خطأ في قاعده البيانات!','status' => 500]);
        }
    }

    public function prescriptionsPDF($id, Request $request)
    {
        try {
            $data = DB::select('usp_app_GetPrescriptionsByID ?', [
                $id,
            ]);
            $patient = $request->user();
            $pdf = PDF::loadView('prescription', compact('data', 'patient'));
            return $pdf->download("prescription-$id.pdf");
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['error' => 'Database Error !', 'errorAr' => 'خطأ في قاعده البيانات!','status' => 500]);
        }
    }

    public function medicalPDF($id, Request $request)
    {
        try {
            $data = DB::select('usp_app_apiPatientMedicalReport ?', [
                $id,
            ]);
            $data = $data[0];
            $patient = $request->user();
            $pdf = PDF::loadView('medicalReport', compact('data', 'patient'));
            return $pdf->download("medicalReport-$id.pdf");
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['error' => 'Database Error !', 'errorAr' => 'خطأ في قاعده البيانات!','status' => 500]);
        }
    }

    public function labPDF($id, Request $request)
    {
        try {
            $data = DB::select('usp_app_apigetLabPatwiseResults ?', [
                $request->user()->PatientId,
            ]);
            $data = collect($data)->filter(function ($item) use ($id) {
                return $item->ResultID == $id;
            })->values()->first();
            $patient = $request->user();
            $pdf = PDF::loadView('labReport', compact('data', 'patient'));
            return $pdf->download("labReport-$id.pdf");
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['error' => 'Database Error !', 'errorAr' => 'خطأ في قاعده البيانات!','status' => 500]);
        }
    }

    public function labGroupPDF($id, Request $request)
    {
        try {
            $data = DB::select('usp_app_apigetLabPatwiseResults ?', [
                $request->user()->PatientId,
            ]);
            $data = collect($data)->filter(function ($item) use ($id) {
                return $item->LabNo == $id;
            })->values();
            $patient = $request->user();
            $pdf = PDF::loadView('labGroupReport', compact('data', 'patient'));
            return $pdf->download("labGroupReport-$id.pdf");
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['error' => 'Database Error !', 'errorAr' => 'خطأ في قاعده البيانات!','status' => 500]);
        }
    }

    public function billPDF(Request $request)
    {
        // return $request->billNo;
        try {
            $data = DB::select('usp_app_GetBillByID ?', [
                $request->ID,
            ]);
            $data = $data[0];
            $patient = $request->user();
            $pdf = PDF::loadView('billPdf', compact('data', 'patient'));
            return $pdf->download("billPDF-$request->ID.pdf");
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['error' => 'Database Error !', 'errorAr' => 'خطأ في قاعده البيانات!','status' => 500]);
        }
    }

    public function billDatePDF(Request $request)
    {
        // return $request->billNo;
        try {
            $data = DB::select('usp_app_BillByNo ?', [
                $request->billNo,
            ]);
            $patient = $request->user();
            $pdf = PDF::loadView('billDatePdf', compact('data', 'patient'));
            return $pdf->download("billDatePdf-$request->billNo.pdf");
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['error' => 'Database Error !', 'errorAr' => 'خطأ في قاعده البيانات!','status' => 500]);
        }
    }
}
