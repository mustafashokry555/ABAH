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
            'NationalNo' => 'required|unique:Patient',
            'First_Name' => 'required',
            'Middle_Name' => 'required',
            'Last_Name' => 'required',
            'Mobile' => 'required|unique:Patient',
            'Registration_No' => 'required|unique:Patient',
            // 'Date_Of_Birth' => 'required',
            'Gender' => 'required',
            'MaritalStatus' => 'required',
            'patient_password' => 'required|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 422]);
        }

        //Patient Creation
        $patient = Patient::create([
            'NationalNo' => $request->NationalNo,
            'First_Name' => $request->First_Name,
            'Middle_Name' => $request->Middle_Name,
            'Last_Name' => $request->Last_Name,
            'Mobile' => $request->Mobile,
            'Registration_No' => $request->Registration_No,
            'PatientType_ID' => '1',
            'PatientSubType_ID' => '1',
            'InsertedByUserID'=> '1',
            'Gender'=>'1',
            'Nationality'=>'1',
            'patient_password' => base64_encode($request->patient_password),
        ]);
        if($patient->save()){
            $expirationTime = now()->addDays(7);
            $patient = Patient::where('Registration_No', $request->Registration_No)->first();
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