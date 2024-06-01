<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    protected $path;
    public function __construct()
    {
        $this->path = request()->path();
    }
    // Done
    function index(Request $request) {
        $options = '?';
        foreach ($request->all() as $name => $contents) {
            $options = $options . $name . '=' . $contents . '&';
        }
        $route_url = config('app.route_url');
        $client = new Client();
        $request = new Psr7Request('GET', $route_url . $this->path . $options);
        $res = $client->sendAsync($request)->wait();
        return json_decode($res->getBody());
    }
    // Done 
    function getDoctor($doctor_id) {
        $route_url = config('app.route_url');
        $client = new Client();
        $request = new Psr7Request('GET', $route_url . $this->path);
        $res = $client->sendAsync($request)->wait();
        return json_decode($res->getBody());
    }
    // Done
    function avilSlots(Request $request) {
        $token = $request->header('Authorization');
        $route_url = config('app.route_url');
        $client = new Client();
        $options = '?';
        foreach ($request->all() as $name => $contents) {
            $options = $options . $name . '=' . $contents . '&';
        }
        $headers = [
            'Authorization' => $token ,
        ];
        $request = new Psr7Request('POST', $route_url . $this->path . $options, $headers);
        $res = $client->sendAsync($request)->wait();
        return json_decode($res->getBody());
    }
    // Done
    function addRate(Request $request) {
        $token = $request->header('Authorization');
        $route_url = config('app.route_url');
        $client = new Client();
        $options = '?';
        foreach ($request->all() as $name => $contents) {
            $options = $options . $name . '=' . $contents . '&';
        }
        $headers = [
            'Authorization' => $token ,
        ];
        $request = new Psr7Request('GET', $route_url . $this->path . $options, $headers);
        $res = $client->sendAsync($request)->wait();
        return json_decode($res->getBody());
    }
}
