<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;

class DepartmentController extends Controller
{
    protected $path;
    public function __construct()
    {
        $this->path = request()->path();
    }
    //Done
    function index() {
        $route_url = config('app.route_url');

        $client = new Client();
        $request = new Psr7Request('GET', $route_url . $this->path);
        $res = $client->sendAsync($request)->wait();
        return json_decode($res->getBody());
    }
}
