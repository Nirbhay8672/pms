<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Plugin;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PluginController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('plugin/Index',[
            'plugin_details' => Plugin::first() ?? null,
        ]);
    }

    public function setDefaultPlugin(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            if($request->id > 0) {
                $plugin_exist = Plugin::find($request->id);
                $plugin_exist->delete();
                Storage::disk('public')->delete($plugin_exist->file_path);
            }

            $zip_file = $request->zip_file;
            $file_name = $zip_file->getClientOriginalName();
    
            Storage::disk('public')->putFileAs("uploads/plugin", $zip_file, $file_name);

            Plugin::create([
                'file_name' => $file_name,
                'file_path' => '/uploads/plugin/' . $file_name,
                'file_extension' => $zip_file->getClientOriginalExtension(),
                'file_size' => $zip_file->getSize(),
            ]);

            DB::commit();

            return $this->successResponse(message: "Default plugin file set successfully.", data: [
                'plugin_details' => Plugin::get()->first(),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }

    public function updatePlugin(Request $request)
    {
        $client = new Client();

        try {

            $member = Member::find($request->id);

            $response = $client->post("$member->website_link/wp-json/jwt-auth/v1/token", [
                'form_params' => [
                    'username' => $member->wp_username,
                    'password' => $member->wp_password,
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
                )->post("$member->website_link/wp-json/update-plugin/v1/submit");

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

    public function bulkUpdatePlugin(Request $request)
    {
        try {

            $client = new Client();

            $default_plugin_details = Plugin::get()->first();

            if($default_plugin_details) {

                $exist_zip_file = public_path($default_plugin_details->file_path);

                $fileContent = file_get_contents($exist_zip_file);

                foreach ($request->selected_members as $member_id) {
                    
                    $member = Member::find($member_id);
    
                    $response = $client->post("$member->website_link/wp-json/jwt-auth/v1/token", [
                        'form_params' => [
                            'username' => $member->wp_username,
                            'password' => $member->wp_password,
                        ],
                    ]);
    
                    $body = $response->getBody();
                    $data = json_decode($body, true);
    
                    if (isset($data['token'])) {
    
                        $response = Http::attach(
                            'zip_file', 
                            $fileContent, 
                            'file.zip'
                        )->post("$member->website_link/wp-json/update-plugin/v1/submit");
    
                        $body = $response->getBody();
                        $data = json_decode($body, true);
                    }
                }

                return $this->successResponse(message: "Plugin files update successfully.");

            } else {
                return $this->errorResponse(message: "Default file dose not exist.");
            }

        } catch (RequestException $e) {
            return $this->errorResponse(message: "Somthing went wrong please try again.");
        }
    }
}