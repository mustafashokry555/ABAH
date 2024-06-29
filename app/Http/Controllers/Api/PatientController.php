<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    protected $path;
    public function __construct()
    {
        $this->path = request()->path();
    }
    // Done
    function editProfile(Request $request) {
        $token = $request->header('Authorization');
        $route_url = config('app.route_url');
        $client = new Client();
        $headers = [
            'Authorization' => $token ,
        ];
        $multipart = [];
        foreach ($request->all() as $name => $contents) {
            if ($request->hasFile($name)) {
                // Handle file upload
                $multipart[] = [
                    'name' => $name,
                    'contents' => fopen($request->file($name)->getPathname(), 'r'),
                    'filename' => $request->file($name)->getClientOriginalName()
                ];
            } else {
                // Handle other form fields
                $multipart[] = [
                    'name' => $name,
                    'contents' => $contents
                ];
            }
        }
    
        $options = [
            'multipart' => $multipart
        ];
        $request = new Psr7Request('POST', $route_url . $this->path, $headers);
        $res = $client->sendAsync($request, $options)->wait();
        return json_decode($res->getBody());
    }
    // Done
    function insurance(Request $request)
    {
        $token = $request->header('Authorization');
        $route_url = config('app.route_url');
        $client = new Client();
        $headers = [
            'Authorization' => $token ,
        ];
        $request = new Psr7Request('GET', $route_url . $this->path, $headers);
        $res = $client->sendAsync($request)->wait();
        return json_decode($res->getBody());
    }
    // Done
    function radiologyReports(Request $request)
    {
        $token = $request->header('Authorization');
        $route_url = config('app.route_url');
        $client = new Client();
        $headers = [
            'Authorization' => $token ,
        ];
        $request = new Psr7Request('GET', $route_url . $this->path, $headers);
        $res = $client->sendAsync($request)->wait();
        return json_decode($res->getBody());
    }
    // Done
    function labResults(Request $request)
    {
        $token = $request->header('Authorization');
        $route_url = config('app.route_url');
        $client = new Client();
        $headers = [
            'Authorization' => $token ,
        ];
        $request = new Psr7Request('GET', $route_url . $this->path, $headers);
        $res = $client->sendAsync($request)->wait();
        return json_decode($res->getBody());
    }
    // Done
    function prescriptions(Request $request)
    {
        $token = $request->header('Authorization');
        $route_url = config('app.route_url');
        $client = new Client();
        $headers = [
            'Authorization' => $token ,
        ];
        $request = new Psr7Request('GET', $route_url . $this->path, $headers);
        $res = $client->sendAsync($request)->wait();
        return json_decode($res->getBody());
    }
    // Done
    function medicalRport(Request $request) {
        $token = $request->header('Authorization');
        $route_url = config('app.route_url');
        $client = new Client();
        $headers = [
            'Authorization' => $token ,
        ];
        $request = new Psr7Request('GET', $route_url . $this->path, $headers);
        $res = $client->sendAsync($request)->wait();
        return json_decode($res->getBody());
    }
    // Done
    function bills(Request $request)
    {
        $token = $request->header('Authorization');
        $route_url = config('app.route_url');
        $client = new Client();
        $headers = [
            'Authorization' => $token ,
        ];
        $request = new Psr7Request('GET', $route_url . $this->path, $headers);
        $res = $client->sendAsync($request)->wait();
        return json_decode($res->getBody());
    }
    // Done
    function visit_history(Request $request)
    {
        $token = $request->header('Authorization');
        $route_url = config('app.route_url');
        $client = new Client();
        $headers = [
            'Authorization' => $token ,
        ];
        $request = new Psr7Request('GET', $route_url . $this->path, $headers);
        $res = $client->sendAsync($request)->wait();
        return json_decode($res->getBody());
    }
    // Done
    function myRate(Request $request)
    {
        return $this->path;
        $token = $request->header('Authorization');
        $route_url = config('app.route_url');
        $client = new Client();
        $headers = [
            'Authorization' => $token ,
        ];
        $options = '?';
        foreach ($request->all() as $name => $contents) {
            $options = $options . $name . '=' . $contents . '&';
        }
        $request = new Psr7Request('GET', $route_url . $this->path . $options, $headers);
        $res = $client->sendAsync($request)->wait();
        return json_decode($res->getBody());
    }
    // Done
    function appointment(Request $request)
    {
        $token = $request->header('Authorization');
        $route_url = config('app.route_url');
        $client = new Client();
        $headers = [
            'Authorization' => $token ,
        ];
        $request = new Psr7Request('GET', $route_url . $this->path, $headers);
        $res = $client->sendAsync($request)->wait();
        return json_decode($res->getBody());
    }
    // Done
    function makeAppointment(Request $request)
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
    function cancelAppointment(Request $request)
    {
        $token = $request->header('Authorization');
        $route_url = config('app.route_url');
        $client = new Client();
        $headers = [
            'Authorization' => $token ,
        ];
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
        $request = new Psr7Request('POST', $route_url . $this->path, $headers);
        $res = $client->sendAsync($request, $options)->wait();
        return json_decode($res->getBody());
    }
}
