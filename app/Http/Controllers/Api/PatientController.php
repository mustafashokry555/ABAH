<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use DateTime;
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
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 422]);
        }
        // Find the patient
        $patient = $request->user();
        if($request->has('img')){
            // Delete existing image if it exists
            if ($patient->ImageURL && Storage::exists($patient->ImageURL)) {
                Storage::delete("$patient->ImageURL");
            }
            if($request->img != null){
                // Update image
                if ($request->hasFile('img')) {
                    $image = $request->file('img');
                    $imageName = "$patient->PatientId-$patient->Registration_No."
                        . $image->getClientOriginalExtension();
                    $image->storeAs('public/patient/profileImg', $imageName);
                    $patient->ImageURL = "public/patient/profileImg/$imageName";
                }
            }else{
                $patient->ImageURL = NULL;
            }
        }
        // Update phhone
        $patient->Mobile = $request->Mobile;
        // Save changes
        $patient->save();
        return response()->json(['message' => 'Patient data updated successfully', 'status' => 200]);
    }

    function insurance(Request $request)
    {

        if (!isset($request->user()->PatientId)) {
            return response()->json(['errors' => 'The patient id field is required.', 'status' => 422]);
        }
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
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }

    function radiologyReports(Request $request)
    {
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

    function labResults(Request $request)
    {
        try {
            $data = DB::select('usp_app_apigetLabPatwiseResults ?', [
                $request->user()->PatientId,
            ]);
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
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
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }

    function prescriptions(Request $request)
    {
        try {
            $data = DB::select('usp_app_apiGetAllPrescriptions ?, ?', [
                $request->user()->PatientId,
                $request->user()->Hospital_ID,
            ]);
            return response()->json(['data' => $data, 'status' => 200]);
        } catch (\Throwable $th) {
            return $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }

    function bills(Request $request)
    {
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
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }

    function myRate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:Employee_Mst,EmpID,Deactive,0',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 422]);
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
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
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
            return response()->json(['message' => "This time slot is already booked.", 'status' => 406]);
        }
        // return $AppointmentCount;

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
                return response()->json(['errors' => $validator->errors(), 'status' => 422]);
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
            return response()->json(['message' => 'Your Appointment has been Updated Successfully!', 'status' => 200]);
        } catch (\Throwable $th) {
            throw $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
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
            return response()->json(['errors' => $validator->errors(), 'status' => 422]);
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
                    return $this->addTheAppoint($request);
                }else{
                    // show message that slot is not available
                    return response()->json([
                        "message" => "This Slot is already Booked by Someone else",
                        "status" => 406
                    ]);
                }
            } else {
                return response()->json(['errors' => ['No Available Slot Found!'], 'status' =>
                422]);
            }
        } else {
            return response()->json(['errors' => ['Doctor is not available in selected date or time.
            Please select another doctor or check the availability of the doctor.'], 'status' =>
            422]);
        }
    }

    function cancelAppointment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'TempId' => 'required|integer',
            'PatientId' => 'required|integer',
            'AppCode' => 'required',
            'APPDate' => 'required|date_format:Y-m-d',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 422]);
        }
        try {
            $data = DB::statement('EXEC usp_app_api_DeleteAppointment ?, ?, ?, ?, ?', [
                // 96060,
                $request->TempId,
                // 24894,
                $request->PatientId,
                // 'WA/202308/94269',
                $request->AppCode,
                // '2023-08-13',
                $request->APPDate,
                1
            ]);
            if ($data) {
                return response()->json(['message' => 'Your Appointment has been Deleted Successfully!', 'status' => 200]);
            } else {
                return response()->json(['errors' => $data, 'status' => 500]);
            }
        } catch (\Throwable $th) {
            throw $th;
            return response()->json(['errors' => 'Database Error !', 'status' => 500]);
        }
    }
}
