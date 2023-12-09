<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NationalNo' => 'required|unique:Patient|numeric|digits:10',
            'First_Name' => 'required',
            'Middle_Name' => 'required',
            'Last_Name' => 'required',
            'Mobile' => 'required|unique:Patient|numeric|digits:9',
            'Date_Of_Birth' => 'required|date_format:Y-m-d|before:tomorrow',
            'Gender' => 'required|integer|exists:Gender_Mst,Gender_ID,Active,1',
            'MaritalStatus' => 'required|integer|exists:MaritalStatus_Mst,Status_ID,Deactive,0',
            'Nationality' => 'required|integer|exists:Nationality_Master,NationalityId,Deactive,0', 
            'patient_password' => 'required|min:6|confirmed',
            'PatientType_ID' => 'required|integer|exists:PatientType_mst,PatientType_ID,Deactive,0', 
            'PatientSubType_ID' => 'required|integer|exists:PatientSubType_Mst,PatientSubType_ID,Deactive,0', 
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 422]);
        }
        $Registration_No = Patient::orderBy('Registration_No', 'DESC')->pluck('Registration_No')->toArray();
        $Registration_No =  max($Registration_No)+1;
        $InsertedByUserID = DB::table('User_Mst')->where('UserName', 'mobileApp')->pluck('UserID')->first();
        // return $Registration_No ;
        //Patient Creation
        $patient = Patient::create([
            'NationalNo' => $request->NationalNo,
            'First_Name' => $request->First_Name,
            'Middle_Name' => $request->Middle_Name,
            'Last_Name' => $request->Last_Name,
            'Mobile' => $request->Mobile,
            'Gender'=> $request->Gender,
            'MaritalStatus' => $request->MaritalStatus,
            'Nationality'=> $request->Nationality,
            'patient_password' => base64_encode($request->patient_password),
            'Registration_No' => $Registration_No,
            'PatientType_ID' => $request->PatientType_ID,
            'PatientSubType_ID' => $request->PatientSubType_ID,
            //defultValue
            'InsertedByUserID' => $InsertedByUserID,
            'InsertedMacName' => 'flutterApp',
            'InsertedMacID' => '10',
            'InsertedIPAddress' => '192.168.1.1',
        ]);
        if($patient->save()){
            $expirationTime = now()->addDays(7);
            $patient = Patient::where('Registration_No', $Registration_No)->first();
            $token = $patient->createToken('auth_token', ['*'], $expirationTime)->plainTextToken;
            return response()->json(['token' => $token, 'patient' => $patient, 'status' => 200]);    
        }else{
            return response()->json(['errors' => 'DB Error', 'status' => 500]);    
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['userId', 'password']);
        $credentials['password'] = base64_encode($credentials['password']);

        $result = DB::select('EXEC usp_app_apiLoginRegNo ?, ?', [
            $credentials['userId'],
            $credentials['password'],
        ]);
        if (isset($result[0]->Id) && in_array($result[0]->Id, [-1, -2, -3])) {
            // No details available or OTP count exceeded
            return response()->json(['errors' => $result[0]->Msg, 'status' => 401]);
        } elseif (isset($result[0]->Registration_No) && $result[0]->Registration_No == $credentials['userId']) {

            $expirationTime = now()->addDays(7);
            $patient = Patient::where('Registration_No', $credentials['userId'])->first();
            // $patient->tokens()->delete();
            $token = $patient->createToken('auth_token', ['*'], $expirationTime)->plainTextToken;
            return response()->json(['token' => $token, 'status' => 200]);
        }

        // return response()->json(['error' => 'Invalid credentials'], 401);
    }
}
