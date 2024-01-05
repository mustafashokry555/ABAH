<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\Patient;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'NationalNo' => 'required',
            'Registration_No' => 'required',
            'Mobile' => 'required', 
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 422]);
        }
        $patient = Patient::where('Registration_No', $request->Registration_No)->first();
        if($patient){
            if ($patient->Mobile == $request->Mobile) {
                if ($patient->Deactive == '0') {
                    if ($patient->patient_password == 'sdc1' || $patient->patient_password == NULL) {
                        // create new pass and sent it to the mobile 
                        $newPassword = $patient->PassportNo;
                        $patient->patient_password = base64_encode($newPassword);
                        $patient->save();
                        
                        return $this->generateOtp($patient, "First Register");
                        
                    }else{
                        return response()->json([
                            'errors' => "This Account is already registered.",
                            'status' => 422
                        ]);
                    }
                }else{
                    return response()->json([
                        'errors' => "This Account is Deactive, Please contact the hospital to activate your account.",
                        'status' => 422
                    ]);
                }
            }else{
                return response()->json([
                    'errors' => "This Mobile Number not valid for this medical file.",
                    'status' => 422
                ]);
            }
        }else{
            return response()->json([
                'errors' => "This Medical file Not exist in our recoreds.",
                'status' => 422
            ]);
        }
    }


    public function generateOtp(Patient $patient, $reason)
    {
        $otp = 999999;
        // $otp = rand(100000, 999999);
        // if Patient count >= 4 don't create new one 
        if ($patient->OTP_Request_Count >=4 ){
            $lastOtp = Otp::where('patient_id', $patient->PatientId)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastOtp && now()->diffInHours($lastOtp->created_at) > 24) {
                $patient->OTP_Request_Count =  1 ;
            }else{
                response()->json([
                    'errors' => "You have reached maximum OTP request limit. Try again after 24H!",
                    'status' => 422
                ]);
            }
            
        }
        else{
            $patient->OTP_Request_Count = $patient->OTP_Request_Count + 1 ;
        }
        
        // Send SMS with the generated otp to patient mobile number
        // $res = $this->sendSms($patient->MobileNumber ,$otp );
        $res = true;
        if($res){
            $patient->OTP = $otp;
            $patient->save();
                Otp::create([
                    'patient_id' => $patient->PatientId,
                    'reason' => $reason,
                    'otp' => $otp,
                ]);
            $firstDigits = substr($patient->Mobile, 0, 1);
            $lastDigits = substr($patient->Mobile, -3);
            return response()->json([
                "message" => "We Send you a password on your phone: +966$firstDigits"."xxxxx$lastDigits .",
                "status" => 200
            ]);   
        }else{
            response()->json([
                'errors' => "Send SMS Error!",
                'status' => 422
            ]);
        }

        return response()->json(['message' => 'OTP generated successfully']);
    }

    function sendSms($phone, $otp){
        // send the sms massage throw api
        $text = "Your One Time Password (OTP) is : $otp \n\r".
        "This code will expire in 5 minutes.\n\r".
        "Do Not shere it with Others.\n
        \r\n".
        "Regards,\n".
        "ABAH";
        $url = "http://46.151.210.31:8080/websmpp/websms";
        $params = [
            'user' => 'Alibinali',
            'pass' => 'Waleed@23',
            'mno' => $phone,
            'type' => 4,
            'text' => $text,
            'sid' => 'ABAH',
            'respformat' => 'json',
        ];
        $client = new Client();
        $response = $client->request('GET', $url, ['query' => $params]);
        $responseCode = $response->getStatusCode();
        $responseBody = $response->getBody()->getContents();
        
    }

    function setNewPass(Request $request){
        $validator = Validator::make($request->all(), [
            'Registration_No' => 'required',
            'otp' => 'required|numeric|digits:6',
            'password' => 'required|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 422]);
        }
        // check if the old pass = the auth pass
        $patient = Patient::where('Registration_No', $request->Registration_No)->first();
        $expiryTime = Carbon::now()->subMinutes(5);
        $lastOtp = Otp::where('patient_id', $patient->PatientId)
            ->where('created_at', '>', $expiryTime)
            ->orderBy('created_at', 'desc')
            ->first();
        if($patient){
            if($lastOtp){
                if ($patient->OTP == $request->otp && $request->otp == $lastOtp->otp) {
                    $patient->patient_password = base64_encode($request->password);
                    $patient->OTP_Request_Count = 0;
                    $patient->OTP = NULL;
                    $patient->save();
                    return response()->json(['massege' => "Your password has been Updated Successfully!", 'status' => 422]);
                }else{
                    return response()->json([
                        'errors' => "This OTP Number not valid.",
                        'status' => 422
                    ]);
                }
            }else{
                return response()->json([
                    'errors' => "This OTP Number is Expired.",
                    'status' => 422
                ]);
            }
        }else{
            return response()->json([
                'errors' => "This Medical file Not exist in our recoreds.",
                'status' => 422
            ]);
        }
    }

    function forgetPass(Request $request) {
        $validator = Validator::make($request->all(), [
            'Registration_No' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 422]);
        }
        $patient = Patient::where('Registration_No', $request->Registration_No)->first();
        if($patient){
            return $this->generateOtp($patient, "Forget Password");
        }else{
            return response()->json([
                'errors' => "This Medical file Not exist in our recoreds.",
                'status' => 422
            ]);
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

    function changePass(Request $request){
        $validator = Validator::make($request->all(), [
            'oldPassword' => 'required',
            'newPassword' => 'required|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 422]);
        }
        // check if the old pass = the auth pass
        $Patient = $request->user();
        if($Patient->patient_password == base64_encode($request->oldPassword)){
            $Patient->patient_password = base64_encode($request->newPassword);
            $Patient->save();
            return response()->json(['massege' => "Your password has been Updated Successfully!", 'status' => 422]);
        }else{
            return response()->json(['errors' => "The Old password is not correct!", 'status' => 422]);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        // Revoke all tokens for the authenticated user
        $user->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
