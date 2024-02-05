<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use DateTime;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    function editProfile(Request $request) {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'Mobile' => 'required|numeric|digits:9',
            'img' => 'nullable|image|max:2048',
            'deleteImg' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'errorAr' => $validator->errors(), 'status' => 422]);
        }
        // Find the patient
        $patient = $request->user();
        // Update phhone
        $patient->Mobile = $request->Mobile;

        if($request->hasFile('img')){
            // Delete existing image if it exists
            if ($patient->ImageURL != NULL && Storage::exists($patient->ImageURL)) {
                Storage::delete($patient->ImageURL);
            }
            $image = $request->file('img');
            $imageName = "$patient->PatientId-$patient->Registration_No."
                    . $image->getClientOriginalExtension();
            $image->move('storage/patient/profileImg', $imageName);
            $patient->ImageURL = "public/patient/profileImg/$imageName";
        }elseif ($request->has('deleteImg') && $request->deleteImg) {
            if ($patient->ImageURL != NULL && Storage::exists($patient->ImageURL)) {
                Storage::delete($patient->ImageURL);
            }
            $patient->ImageURL = NULL;
        }
        // Save changes
        $patient->save();
        return response()->json(['message' => 'Patient data updated successfully', 'status' => 200]);
        return response()->json([
            'message' => "Patient data updated successfully.",
            'messageAr' => "تم التحديث بنجاح.",
            'status' => 200
            ]);
    }

    function insurance(Request $request)
    {
        try {
            $company = DB::select('usp_app_apiPatientInsuranceDetails ?', [
                $request->user()->PatientId,
            ]);
            $approval = DB::select('usp_app_apiPatientApprovalDtls ?', [
                $request->user()->PatientId,
            ]);
            $data = [
                "Company" => $company,
                "Approval" => $approval
            ];
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['error' => 'Database Error !', 'errorAr' => 'خطأ في قاعده البيانات!','status' => 500]);
        }
    }

    function radiologyReports(Request $request)
    {
        try {
            $data = DB::select('usp_app_apiGetAllRadioDatewise ?, ?', [
                $request->user()->PatientId,
                $request->user()->Hospital_ID,
            ]);
            foreach ($data as $item) {
                $item->resultPDF = url('/')."/api/RedioPDF/$item->OrdDtlID";
            }
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['error' => 'Database Error !', 'errorAr' => 'خطأ في قاعده البيانات!','status' => 500]);
        }
    }

    function labResults(Request $request)
    {
        try {
            $data = DB::select('usp_app_apigetLabPatwiseResults ?', [
                $request->user()->PatientId,
            ]);
            foreach ($data as $item) {
                $item->labPDF = url('/')."/api/labPDF/$item->LabNo";
            }
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['error' => 'Database Error !', 'errorAr' => 'خطأ في قاعده البيانات!','status' => 500]);
        }
    }

    function appointment(Request $request)
    {
        try {
            $PatientId = $request->user()->PatientId;
            $appointments = DB::table("Ds_PatientAppoinmentTemperary")
            ->join('Employee_Mst', 'Ds_PatientAppoinmentTemperary.DoctorID', '=', 'Employee_Mst.EmpID')
            ->join('Department_Mst', 'Employee_Mst.Department_ID', '=', 'Department_Mst.Department_ID')
            ->where('Ds_PatientAppoinmentTemperary.PatientID', $PatientId,)
            ->select(
                'Ds_PatientAppoinmentTemperary.*',
                DB::raw("'Dr. ' + Employee_Mst.FirstName + ' ' + Employee_Mst.MiddleName + ' ' + Employee_Mst.LastName AS DoctorName"),
                DB::raw("N'د. ' + Employee_Mst.R_FirstName + ' ' + Employee_Mst.R_MiddleName + ' ' + Employee_Mst.R_LastName AS DoctorNameAr"),
                DB::raw("Department_Mst.Department_Name AS DoctorSpeciality"),
                DB::raw("Department_Mst.Department_Name_Arabic AS DoctorSpecialityAr"),
                )
            ->get();
            return response()->json(['data' => $appointments, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['error' => 'Database Error !', 'errorAr' => 'خطأ في قاعده البيانات!','status' => 500]);
        }
    }

    function prescriptions(Request $request)
    {
        try {
            $data = DB::select('usp_app_apiGetAllPrescriptions ?, ?', [
                $request->user()->PatientId,
                $request->user()->Hospital_ID,
            ]);
            foreach ($data as $item) {
                $item->prescriptionPDF = url('/')."/api/prescriptionsPDF/$item->PreID";
            }
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['error' => 'Database Error !', 'errorAr' => 'خطأ في قاعده البيانات!','status' => 500]);
        }
    }

    function bills(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'month' => 'required',
        //     'year' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['error' => $validator->errors(), 'errorAr' => $validator->errors(), 'status' => 422]);
        // }
        try {
            $data = DB::select('usp_app_BollByID ?, ?', [
                $request->user()->Registration_No,
                $request->user()->Hospital_ID,
            ]);
            foreach ($data as $item) {
                $queryParameters = http_build_query(['billNo' => $item->BillNo]);
                $item->billPDF = url("/api/billPDF?$queryParameters");
            }
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['error' => 'Database Error !', 'errorAr' => 'خطأ في قاعده البيانات!','status' => 500]);
        }
    }

    function visit_history(Request $request)
    {

        try {
            $data = DB::select('usp_app_api_GetAllVisitList ?, ?', [
                $request->user()->PatientId,
                $request->user()->Hospital_ID,
                // NULL,
            ]);
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['error' => 'Database Error !', 'errorAr' => 'خطأ في قاعده البيانات!','status' => 500]);
        }
    }

    function myRate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:Employee_Mst,EmpID,Deactive,0',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'errorAr' => $validator->errors(), 'status' => 422]);
        }
        try {
            $PatientId = $request->user()->PatientId;
            $rate = DB::table("app_rate_doctors")
                ->where([
                    'patient_id' => $PatientId,
                    'doctor_id' => $request->doctor_id
                ])->first();
            return response()->json(['data' => $rate, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['error' => 'Database Error !', 'errorAr' => 'خطأ في قاعده البيانات!','status' => 500]);
        }
    }

    function addTheAppoint(Request $request)
    {
        // get from & to dateTime
        list($fromTime, $toTime) = explode(" To ", $request->SlotsTime);
        $date = new DateTime($request->DATE);
        $from = $date->format('Y-m-d') . ' ' . date('H:i:s', strtotime($fromTime));
        $to = $date->format('Y-m-d') . ' ' . date('H:i:s', strtotime($toTime));
        $AppointmentCount = DB::table('Ds_PatientAppoinmentTemperary')
            ->where([
                'FromTime' => $from,
                'ToTime' => $to,
                'DoctorID' => $request->doctor_id,
                'ApntStatus' => 'CONFIRMED'
            ])
            // ->whereNull('Cancelled')
            ->count();
        if ($AppointmentCount > 0) {
            return response()->json(['error' => 'This Slot is already Booked!',
                    'errorAr' => 'هذا الموعد محجوز!','status' => 422]);
        }

        // generate new AppointmentCode
        $lastAppointmentCode = DB::table('Ds_PatientAppoinmentTemperary')
            ->orderBy('ID', 'DESC')->get(['ID', 'AppointmentCode'])->first();
        $newCode = substr(strrchr($lastAppointmentCode->AppointmentCode, "/"), 1) + 1;
        $newAppointmentCode = 'WA/' . $date->format('Ym') . '/' . $newCode;

        $row = [
            "ID" => $lastAppointmentCode->ID + 1,
            "FromTime" => $from,
            "ToTime" => $to,
            "AppointmentCode" => $newAppointmentCode,
            'DoctorID' => $request->doctor_id,
            'Duration' => 0,
            'Loc_Id' => 1,
            'app_date' => $date->format('Y-m-d') . " 00:00:00.000",
        ];

        $user = Auth::guard('api')->user();
        if ($user) {
            // case the patient is Auth Ds_PatientAppoinmentTemperary
            // merge user data in row array 
            $row = array_merge($row, [
                "PatientID" => $user->PatientId,
                "Firstname" => $user->First_Name,
                "Middlename" => $user->Middle_Name,
                "LastName" => $user->Last_Name,
                "Age" => $user->Age,
                "Gender" => $user->Gender,
                "StreetAddress" => $user->Contract_Address,
                "City" => $user->Contact_City,
                "State" => $user->Contact_State,
                "Mobile" => $user->Mobile,
                'Country' => $user->ContactCountry,
            ]);
        } else {
            //validate important inputs
            $validator = Validator::make($request->all(), [
                'Firstname' => 'required|string',
                'Middlename' => 'string|nullable',
                'LastName' => 'string|nullable',
                'Age' => 'required|integer',
                // 'Gender' => 'required|date',
                // 'StreetAddress' => 'required|date',
                // 'City' => 'required|date',
                // 'State' => 'required|date',
                'Mobile' => 'required|numeric|digits:9',
                // 'Country' => 'required|date',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors(), 'errorAr' => $validator->errors(), 'status' => 422]);
            }
            // case the patient is Gest
            $row = array_merge($row, [
                "PatientID" => -1,
                "Firstname" => $request->Firstname,
                "Middlename" => $request->Middlename,
                "LastName" => $request->LastName,
                "Age" => $request->Age,
                "Gender" => $request->Gender,
                "StreetAddress" => $request->Contract_Address,
                "City" => $request->Contact_City,
                "State" => $request->Contact_State,
                "Mobile" => $request->Mobile,
                'Country' => $request->ContactCountry,
            ]);
        }
        try {
            $newAppointment = DB::table('Ds_PatientAppoinmentTemperary')
                ->insert([$row]);
            return '1';
        } catch (\Throwable $th) {
            // throw $th;
            return response()->json(['error' => 'Database Error !', 'errorAr' => 'خطأ في قاعده البيانات!','status' => 500]);
        }
    }

    function makeAppointment(Request $request)
    {
        //validate important inputs
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:Employee_Mst,EmpID,Deactive,0',
            'DATE' => 'required|date',
            'SlotsTime' => [
                'required',
                'regex:/^(0[1-9]|1[0-2]):[0-5][0-9] (AM|PM) To (0[1-9]|1[0-2]):[0-5][0-9] (AM|PM)$/'
            ],
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'errorAr' => $validator->errors(), 'status' => 422]);
        }

        $avilSlots = DB::select('usp_app_apiGetDocAppSlots ?, ?, ?, ?', [
            $request->doctor_id,
            $request->DATE,
            $request->DATE,
            '1'
        ]);
        if (!empty($avilSlots)) {
            $SlotTime = $request->SlotsTime;
            $avilSlot = collect($avilSlots)->filter(function ($item) use ($SlotTime) {
                return $item->SlotsTime == $SlotTime;
            })->values()->first();
            if (!empty($avilSlot)) {
                if (empty(trim($avilSlot->sub)) && $avilSlot->PatientId == 0) {
                    // continue the booking
                    $bookResult = $this->addTheAppoint($request);
                    if($bookResult == '1'){
                        $userphone = Auth::guard('api')->user()->Mobile;
                        if ($userphone) {
                            $smsRes = $this->sendSms($userphone, $request);
                        }else{
                            $smsRes = $this->sendSms($request->Mobile, $request);
                        }
                        if($smsRes){
                            return response()->json([
                                'message' => "Your Appointment has been Updated Successfully And SMS massage sent Successfully.",
                                'messageAr' => "تم الحجز بنجاح, وارسال رساله تاكيد.",
                                'status' => 200
                                ]);
                        }else{
                            return response()->json([
                                'message' => "Your Appointment has been Updated Successfully But SMS massage faild!",
                                'messageAr' => "تم الحجز بنجاح.",
                                'status' => 200
                                ]);
                        }
                    }else{
                        return $bookResult;
                    }
                }else{
                    // show message that slot is not available
                    return response()->json(['error' => 'This Slot is already Booked!',
                    'errorAr' => 'هذا الموعد محجوز!','status' => 422]);
                }
            } else {
                return response()->json(['error' => 'No Available Slot Found!', 'errorAr' => 'لايوجد معاد متاح!','status' => 422]);
            }
        } else {
            return response()->json(['error' => 'Doctor is not available!', 'errorAr' => 'هذا الدكتور غير متاح!','status' => 422]);
        }
    }

    function sendSms($phone, $request){
        $doctor = DB::table("Employee_Mst")
            ->where('Employee_Mst.EmpID', $request->doctor_id)
            ->select(
                DB::raw("'Dr. ' + Employee_Mst.FirstName + ' ' + Employee_Mst.MiddleName + ' ' + Employee_Mst.LastName AS DoctorName"),
                DB::raw("N'د. ' + Employee_Mst.R_FirstName + ' ' + Employee_Mst.R_MiddleName + ' ' + Employee_Mst.R_LastName AS DoctorNameAr"),
                )
            ->first();
        $text = "Your Appointment has been Updated Successfully with:  \n\r".
        "$doctor->DoctorName  \n\r".
        "$doctor->DoctorNameAr \n\r".
        "on:  \n\r".
        "$request->DATE  at: $request->SlotsTime \n\r".
        "And you will receve a confirmation SMS from us.\n
        \r\n".
        "Regards,\n".
        "ABAH";
        $url = "http://46.151.210.31:8080/websmpp/websms";
        $params = [
            'user' => 'Alibinali',
            'pass' => 'Waleed@23',
            'sid' => 'ABAH',
            'mno' => "966$phone",
            'type' => 1,
            'text' => $text,
            'respformat' => 'json',
        ];
        $client = new Client();
        $response = $client->request('GET', $url, ['query' => $params]);
        $responseCode = $response->getStatusCode();
        $responseBody = json_decode($response->getBody());

        if ($responseCode == 200 && isset($responseBody->Response) && is_numeric($responseBody->Response[0]))
        {return true;} else {return false;}
    }

    function cancelAppointment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'TempId' => 'required|integer',
            'PatientId' => 'required|integer',
            'AppCode' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'errorAr' => $validator->errors(), 'status' => 422]);
        }
        try {
            $data = DB::select('usp_app_api_DeleteAppointment ?, ?, ?, ?', [
                $request->TempId,
                $request->PatientId,
                $request->AppCode,
                1
            ]);
            if (!empty($data)) {
                $data = $data[0];
                if($data->Id == 1){
                    return response()->json([
                        'message' => "Appointment is CANCELED Successfully.",
                        'messageAr' => "تم الالغاء بنجاح.",
                        'status' => 200
                        ]);
                }else{
                    return response()->json(['error' => $data->Msg, 'errorAr' => $data->Msg,'status' => 500]);
                }
            } else {
                return response()->json(['error' => 'Database Error !', 'errorAr' => 'خطأ في قاعده البيانات!','status' => 500]);
            }
        } catch (\Throwable $th) {
            throw $th;
            return response()->json(['error' => 'Database Error !', 'errorAr' => 'خطأ في قاعده البيانات!','status' => 500]);
        }
    }

    function medicalRport(Request $request) {
        
        try {
            $data = DB::select('usp_app_apiPatientMedicalReportById ?', [
                $request->user()->PatientId
            ]);
            foreach ($data as $item) {
                $item->medicalPDF = url('/')."/api/medicalPDF/$item->Visit_ID";
            }
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // throw $th;
            return response()->json(['error' => 'Database Error !', 'errorAr' => 'خطأ في قاعده البيانات!','status' => 500]);
        }
    }
}
