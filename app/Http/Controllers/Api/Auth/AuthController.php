<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\Patient;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $path;
    public function __construct()
    {
        $this->path = request()->path();
    }

    function setNewPass(Request $request)
    {
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
        if ($patient) {
            if ($lastOtp) {
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
                } else {
                    return response()->json([
                        'error' => "This OTP Number not valid.",
                        'errorAr' => "رقم التاكديد غير صحيح!",
                        'status' => 422
                    ]);
                }
            } else {
                return response()->json([
                    'error' => "This OTP Number is Expired.",
                    'errorAr' => "رقم التاكديد منتهى!",
                    'status' => 422
                ]);
            }
        } else {
            return response()->json([
                'error' => "This Medical file Not exist in our recoreds.",
                'errorAr' => "رقم رقم الملف غبر صحيح!",
                'status' => 422
            ]);
        }
    }

    function forgetPass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Registration_No' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'errorAr' => $validator->errors(), 'status' => 422]);
        }
        $patient = Patient::where('Registration_No', $request->Registration_No)->first();
        if ($patient) {
            return $this->generateOtp($patient, "Forget Password");
        } else {
            return response()->json([
                'error' => "This Medical file Not exist in our recoreds.",
                'errorAr' => "رقم رقم الملف غبر صحيح!",
                'status' => 422
            ]);
        }
    }
    function changePass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'oldPassword' => 'required',
            'newPassword' => 'required|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'errorAr' => $validator->errors(), 'status' => 422]);
        }
        // check if the old pass = the auth pass
        $Patient = $request->user();
        if ($Patient->patient_password == base64_encode($request->oldPassword)) {
            $Patient->patient_password = base64_encode($request->newPassword);
            $Patient->save();
            return response()->json([
                'message' => "Your password has been Updated Successfully.",
                'messageAr' => "تم التحديث بنجاح.",
                'status' => 200
            ]);
        } else {
            return response()->json([
                'error' => "The Old password is not correct!",
                'errorAr' => "الرقم السرى القديم غير صحيح!",
                'status' => 422
            ]);
        }
    }
    // Done
    public function login(Request $request)
    {
        $route_url = config('app.route_url');
        $client = new Client();
        $multipart = [];
        foreach ($request->all() as $name => $contents) {
            $multipart[] = [
                'name' => $name,
                'contents' => $contents
            ];
        }
    
        $options = [
            'multipart' => $multipart
        ];
        $request = new Psr7Request('POST', $route_url . $this->path);
        $res = $client->sendAsync($request, $options)->wait();
        return json_decode($res->getBody());
    }
    // Done
    public function register(Request $request)
    {
        $route_url = config('app.route_url');
        $client = new Client();
        $multipart = [];
        foreach ($request->all() as $name => $contents) {
            $multipart[] = [
                'name' => $name,
                'contents' => $contents
            ];
        }
    
        $options = [
            'multipart' => $multipart
        ];
        $request = new Psr7Request('POST', $route_url . $this->path);
        $res = $client->sendAsync($request, $options)->wait();
        return json_decode($res->getBody());
    }
    // Done
    public function logout(Request $request)
    {
        $token = $request->header('Authorization');
        $route_url = config('app.route_url');
        $client = new Client();
        $headers = [
            'Authorization' => $token ,
        ];
        $request = new Psr7Request('POST', $route_url . $this->path, $headers);
        $res = $client->sendAsync($request)->wait();
        return json_decode($res->getBody());
    }
}
