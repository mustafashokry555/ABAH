<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    public function index(){
        $gender = DB::table('Gender_Mst')
        ->select('Gender_ID', 'Description')->where('Active', '1')->get();
        
        $maritalStatus = DB::table('MaritalStatus_Mst')
        ->select('Status_ID', 'Status_Code', 'Status_Name')
        ->where('Deactive', '0')->get();

        $nationality = DB::table('Nationality_Master')
        ->select('NationalityId', 'NationalityCode', 'NationalityDesc')
        ->where('Deactive', '0')->get();
        
        $patientType = DB::table('PatientType_mst')
        ->select('PatientType_ID', 'PatientType', 'PatientTypeCode')
        ->where('Deactive', '0')->get();

        $patientSubType_Mst = DB::table('PatientSubType_Mst')
        ->select('PatientSubType_ID', 'PatientSubType_Code', 'PatientSubType_Desc')
        ->where('Deactive', '0')->get();


        $data =[
            'gender' => $gender,
            'maritalStatus' => $maritalStatus,
            'nationality' => $nationality,
            'patientType' => $patientType,
            'patientSubType_Mst' => $patientSubType_Mst,
        ];
        return $data;
    }
}
