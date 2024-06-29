<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $path;
    public function __construct()
    {
        $this->path = request()->path();
    }
    // Done
    function userData (Request $request) {
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
    // Done
    function setNewPass(Request $request)
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
    function forgetPass(Request $request)
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
    function changePass(Request $request)
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
