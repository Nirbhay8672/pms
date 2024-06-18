<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Plugin;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PluginController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('plugin/Index',[
            'plugin_details' => Plugin::first() ?? null,
            'page_name' => 'Plugin Setting',
        ]);
    }

    public function setDefaultPlugin(Request $request): JsonResponse
    {
        $request->validate([
            'zip_file' => 'required|file|mimes:zip',
        ]);
        
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

    public function updatePlugin(Request $request) : JsonResponse
    {
        $request->validate([
            'zip_file' => 'required|file|mimes:zip',
        ]);

        $client = new Client();

        try {
            $member = Member::find($request->id);

            $file = $request->file('zip_file');
            $url = $member->website_link.''.'wp-json/update-plugin/v1/submit';

            $response = $client->post($url, [
                'multipart' => [
                    [
                        'name' => 'zip_file',
                        'contents' => file_get_contents($file),
                        'filename' => 'file.zip',
                    ],
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);

            if (isset($responseBody['success']) && $responseBody['success'] === true) {

                $member->fill([
                    'plugin_version' => $responseBody['plugin_version'],
                    'plugin_is_active' => $responseBody['plugin_status'],
                ])->save();

                return $this->successResponse(message: "Plugin files update successfully.", data : [
                    'status' => 200,
                ]);
            } else {
                return $this->errorResponse(message: "Somthing went wrong.", status : 404);
            }
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $body = $response->getBody();   
            $data = json_decode($body, true);

            return $this->errorResponse(message: $data['message'], status : 404);
        }
    }

    public function bulkUpdatePlugin(Request $request) : JsonResponse
    {
        $default_plugin_details = Plugin::get()->first();

        $client = new Client();

        if($default_plugin_details) {

            $exist_zip_file = public_path($default_plugin_details->file_path);

            foreach ($request->selected_members as $member_id) {

                try {
                    $member = Member::find($member_id);

                    $url = $member->website_link.''.'wp-json/update-plugin/v1/submit';

                    $response = $client->post($url, [
                        'multipart' => [
                            [
                                'name' => 'zip_file',
                                'contents' => file_get_contents($exist_zip_file),
                                'filename' => 'file.zip',
                            ],
                        ],
                    ]);

                    $responseBody = json_decode($response->getBody(), true);

                    if (isset($responseBody['success']) && $responseBody['success'] === true) {
        
                        $member->fill([
                            'plugin_version' => $responseBody['plugin_version'],
                            'plugin_is_active' => $responseBody['plugin_status'],
                        ])->save();
                    }
                } catch (ClientException $e) {
                    
                }
            }
            return $this->successResponse(message: "Plugin files update successfully.");

        } else {
            return $this->errorResponse(message: "Default file dose not exist." , status : 404);
        }
    }

    public function bulkDeletePlugin(Request $request) : JsonResponse
    {
        $client = new Client();

        foreach ($request->selected_members as $member_id) {

            try {
                $member = Member::find($member_id); 

                $url = $member->website_link.''.'wp-json/delete/data';
                $response = $client->get($url);

                $responseBody = json_decode($response->getBody(), true);

                if (isset($responseBody['success']) && $responseBody['success'] === true) {
    
                    $member->fill([
                        'plugin_version' => null,
                        'plugin_is_active' => null,
                    ])->save();
                }
            } catch (ClientException $e) {
                
            }
        }
        return $this->successResponse(message: "Plugin delete from all site successfully.");
    }

    public function activeOrDeactive(Request $request) : JsonResponse
    {
        $client = new Client();

        $member = Member::find($request->member_id);

        $url = $member->website_link.''.'wp-json/update-plugin-status/submit';

        $response = $client->post($url,[
            'json' => [
                'status' => $request->status
            ],
        ]);

        $body = $response->getBody();
        $data = json_decode($body, true);

        if($data) {
            $member->fill([
                'plugin_version' => $data['plugin_version'],
                'plugin_is_active' => $data['plugin_status'],
            ])->save();

            $msg = $request->status == 1 ? 'Plugin active successfully.' : 'Plugin Deactivate successfully.';
            return $this->successResponse(message: $msg);
        } else {
            return $this->errorResponse(message: "Somthing went wrong please try again." , status : 404);
        }
    }

    public function delete(Member $member) : JsonResponse
    {
        $client = new Client();

        $url = $member->website_link.''.'wp-json/delete/data';
        $response = $client->get($url);
        $body = $response->getBody();
        $data = json_decode($body, true);

        if($data) {
            $member->fill([
                'plugin_version' => null,
                'plugin_is_active' => null,
            ])->save();
            return $this->successResponse(message: "Plugin delete successfully.");
        } else {
            return $this->errorResponse(message: "Somthing went wrong please try again." , status : 404);
        }
    }
}