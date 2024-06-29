<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected $path;
    public function __construct()
    {
        $this->path = request()->path();
    }
    // Done
    public function index()
    {
        $route_url = config('app.route_url');
        $client = new Client();
        $request = new Psr7Request('GET', $route_url . $this->path);
        $res = $client->sendAsync($request)->wait();
        return json_decode($res->getBody());
    }
    // Done
    public function makeComplaint(Request $request)
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
    public function showImage($filename)
    {
        $path = storage_path('app/public/patient/profileImg/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }
}
