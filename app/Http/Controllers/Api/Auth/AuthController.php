<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
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
            $patient->tokens()->delete();
            $token = $patient->createToken('auth_token', ['*'], $expirationTime)->plainTextToken;
            return response()->json(['token' => $token, 'status' => 200]);
        }

        // return response()->json(['error' => 'Invalid credentials'], 401);
    }
}
