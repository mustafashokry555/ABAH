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

        $result = DB::select('EXEC usp_apiLoginRegNo ?, ?', [
            $credentials['userId'],
            $credentials['password'],
        ]);
        if (!empty($result)) {
            $expirationTime = now()->addDays(7);
            $patient = Patient::where('Registration_No', $credentials['userId'])->first();
            $patient->tokens()->delete();
            $token = $patient->createToken('auth_token', ['*'], $expirationTime)->plainTextToken;
            return response()->json(['token' => $token, 'user' => $result[0], 'user1' => $expirationTime]);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }
}
