<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Http\Request;

class PDFController extends Controller
{
    protected $path;
    public function __construct()
    {
        $this->path = request()->path();
    }
    // Done
    public function RedioPDF($id, Request $request) 
    {
        $token = $request->header('Authorization');
        $route_url = config('app.route_url');
        $client = new Client();
        $headers = [
            'Authorization' => $token ,
        ];
        $request = new Psr7Request('GET', $route_url . $this->path, $headers);
        $res = $client->sendAsync($request)->wait();
        $pdf = $res->getBody();
        return response($pdf, 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="RedioReport.pdf"');
    }
    // Done
    public function prescriptionsPDF($id, Request $request)
    {
        $token = $request->header('Authorization');
        $route_url = config('app.route_url');
        $client = new Client();
        $headers = [
            'Authorization' => $token ,
        ];
        $request = new Psr7Request('GET', $route_url . $this->path, $headers);
        $res = $client->sendAsync($request)->wait();
        $pdf = $res->getBody();
        return response($pdf, 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="prescription.pdf"');
    }
    // Done
    public function medicalPDF($id, Request $request)
    {
        $token = $request->header('Authorization');
        $route_url = config('app.route_url');
        $client = new Client();
        $headers = [
            'Authorization' => $token ,
        ];
        $request = new Psr7Request('GET', $route_url . $this->path, $headers);
        $res = $client->sendAsync($request)->wait();
        $pdf = $res->getBody();
        return response($pdf, 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="medicalReport.pdf"');
    }
    // Done
    public function labPDF($id, Request $request)
    {
        $token = $request->header('Authorization');
        $route_url = config('app.route_url');
        $client = new Client();
        $headers = [
            'Authorization' => $token ,
        ];
        $request = new Psr7Request('GET', $route_url . $this->path, $headers);
        $res = $client->sendAsync($request)->wait();
        $pdf = $res->getBody();
        return response($pdf, 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="labReport.pdf"');
    }
    // Done
    public function labGroupPDF($id, Request $request)
    {
        $token = $request->header('Authorization');
        $route_url = config('app.route_url');
        $client = new Client();
        $headers = [
            'Authorization' => $token ,
        ];
        $request = new Psr7Request('GET', $route_url . $this->path, $headers);
        $res = $client->sendAsync($request)->wait();
        $pdf = $res->getBody();
        return response($pdf, 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="labGroupReport.pdf"');
    }
    // Done
    public function billPDF(Request $request)
    {
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
        $pdf = $res->getBody();
        return response($pdf, 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="billPDF.pdf"');
    }
    // Done
    public function billDatePDF(Request $request)
    {
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
        $pdf = $res->getBody();
        return response($pdf, 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="billDatePdf.pdf"');
    }

}
