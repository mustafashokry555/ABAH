<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\DB;

class PDFController extends Controller
{
    public function RedioPDF($id)
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
            $pdf = PDF::loadView('RedioReport', compact('data'));
            return $pdf->download("RedioReport-$id.pdf");
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }

    public function prescriptionsPDF($id)
    {
        try {
            $data = DB::select('usp_app_GetPrescriptionsByID ?', [
                $id,
            ]);
            $pdf = PDF::loadView('prescription', compact('data'));
            return $pdf->download("prescription-$id.pdf");
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }

    public function medicalPDF($id)
    {
        try {
            $data = DB::select('usp_app_apiPatientMedicalReport ?', [
                $id,
            ]);
            $data = $data[0];
            $pdf = PDF::loadView('medicalReport', compact('data'));
            return $pdf->download("medicalReport-$id.pdf");
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }

    public function labPDF($id, Request $request)
    {
        try {
            $data = DB::select('usp_app_apigetLabPatwiseResults ?', [
                $request->user()->PatientId,
            ]);
            $data = collect($data)->filter(function ($item) use ($id) {
                return $item->LabNo == $id;
            })->values()->first();
            $pdf = PDF::loadView('labReport', compact('data'));
            return $pdf->download("labReport-$id.pdf");
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }

    public function billPDF(Request $request)
    {
        // return $request->billNo;
        try {
            $data = DB::select('usp_app_GetBillByNo ?', [
                $request->billNo,
            ]);
            $data = $data[0];
            $pdf = PDF::loadView('billPdf', compact('data'));
            return $pdf->download("billPDF-$request->billNo.pdf");
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }
}
