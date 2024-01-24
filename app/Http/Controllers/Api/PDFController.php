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
            $pdf = PDF::loadView('RedioReport', compact('data'));
            return $pdf->download("RedioReport-$id.pdf");
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }
}
