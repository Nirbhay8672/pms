<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use App\Models\Website;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): Response
    {
        return Inertia::render('Dashboard', [
            'total_websites' => Website::all()->count(),
            'total_clients' => Client::all()->count(),
            'total_users' => User::all()->count(),
        ]);
    }

    // wordpress details

    protected $wpServerUrl = 'http://localhost/wp-pms';
    protected $username = 'admin';
    protected $password = '@LHeIBWJrnr)*YY$Gp';

    public function updatePlugin(Request $request)
    {
        $client = new GuzzleHttpClient();

        try {
            $response = $client->post("$this->wpServerUrl/wp-json/jwt-auth/v1/token", [
                'form_params' => [
                    'username' => $this->username,
                    'password' => $this->password,
                ],
            ]);

            $body = $response->getBody();
            $data = json_decode($body, true);

            if (isset($data['token'])) {

                $file = $request->file('zip_file');

                $response = Http::attach(
                    'zip_file', 
                    file_get_contents($file), 
                    'file.zip'
                )->post("$this->wpServerUrl/wp-json/update-plugin/v1/submit");

                $body = $response->getBody();
                $data = json_decode($body, true);

                if($data['success']) {
                    return $this->successResponse(message: "Plugin files update successfully.");
                } else {
                    return $this->errorResponse(message: "Somthing went wrong please try again.");
                }
            }
            
        } catch (RequestException $e) {
            return $this->errorResponse(message: "Somthing went wrong please try again.");
        }
    }

    public function getPluginDetails()
    {
        $client = new GuzzleHttpClient();

        try {
            $response = $client->post("$this->wpServerUrl/wp-json/jwt-auth/v1/token", [
                'form_params' => [
                    'username' => $this->username,
                    'password' => $this->password,
                ],
            ]);

            $body = $response->getBody();
            $data = json_decode($body, true);

            if (isset($data['token'])) {
                $response = Http::get("$this->wpServerUrl/wp-json/get-plugin-details/data");
                $body = $response->getBody();
                $data = json_decode($body, true);

                if($data) {
                    return $this->successResponse(message: "Plugin details fetch successfully.", data : $data);
                } else {
                    return $this->errorResponse(message: "Somthing went wrong please try again.");
                }
            }
            
        } catch (RequestException $e) {
            return $this->errorResponse(message: "Somthing went wrong please try again.");
        }
    }

    public function activeOrDeactive(Request $request)
    {
        $client = new GuzzleHttpClient();

        try {
            $response = $client->post("$this->wpServerUrl/wp-json/jwt-auth/v1/token", [
                'form_params' => [
                    'username' => $this->username,
                    'password' => $this->password,
                ],
            ]);

            $body = $response->getBody();
            $data = json_decode($body, true);

            if (isset($data['token'])) {

                $response = Http::post("$this->wpServerUrl/wp-json/update-plugin-status/submit",[
                    'status' => $request->status
                ]);

                $body = $response->getBody();
                $data = json_decode($body, true);

                if($data['success']) {
                    $msg = $request->status == 1 ? 'Plugin active successfully.' : 'Plugin Deactivate successfully.';
                    return $this->successResponse(message: $msg);
                } else {
                    return $this->errorResponse(message: "Somthing went wrong please try again.");
                }
            }
            
        } catch (RequestException $e) {
            return $this->errorResponse(message: "Somthing went wrong please try again.");
        }
    }

    public function delete()
    {
        $client = new GuzzleHttpClient();

        try {
            $response = $client->post("$this->wpServerUrl/wp-json/jwt-auth/v1/token", [
                'form_params' => [
                    'username' => $this->username,
                    'password' => $this->password,
                ],
            ]);

            $body = $response->getBody();
            $data = json_decode($body, true);

            if (isset($data['token'])) {
                $response = Http::get("$this->wpServerUrl/wp-json/delete/data");
                $body = $response->getBody();
                $data = json_decode($body, true);

                if($data) {
                    return $this->successResponse(message: "Plugin delete successfully.");
                } else {
                    return $this->errorResponse(message: "Somthing went wrong please try again.");
                }
            }
            
        } catch (RequestException $e) {
            return $this->errorResponse(message: "Somthing went wrong please try again.");
        }
    }
}