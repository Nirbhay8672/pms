<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentFormRequest;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Website;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function addPayment(PaymentFormRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $client = Client::where('email', $request->client_email)->first() ?? new Client();

            if(!$client->exists) {
                $client->fill([
                    'name' => $request->client_name,
                    'phone_number' => $request->client_phone_number,
                    'email' => $request->client_email,
                    'joining_date' => $request->client_joining_date,
                ])->save();
            }

            $website = Website::where('website_name', $request->website_name)->first() ?? new Website();

            if(!$website->exists) {
                $website->fill([
                    'website_name' => $request->website_name,
                    'website_url' => $request->website_url,
                    'website_logo_path' => '',
                    'google_rank' => $request->google_rank,
                    'client_id' => $client->id,
                    'time' => $request->google_rank,
                    'total_update' => $request->total_update,
                    'is_backup_active' => $request->is_backup_active,
                    'total_site_helth' => $request->total_site_helth,
                    'total_php_issue' => $request->total_php_issue,
                    'wp_admin_url' => $request->wp_admin_url,
                ])->save();

                if ($request->website_logo) {
                    $this->storeFile($request->website_logo, $website);
                }
            }

            Payment::create([
                'client_id' => $client->id,
                'website_id' => $website->id,
                'payment_date' => $request->payment_date,
                'payment_time' => $request->payment_time,
                'amount' => $request->amount,
                'status' => $request->status,
                'last_try' => $request->status == 'Pending' ? now() : null,
                'last_success' => $request->status == 'Success' ? now() : null,
            ]);

            DB::commit();

            return $this->successResponse(message: "Payment has been added successfully.");
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse(message: $exception->getMessage());
        }
    }

    private function storeFile($file, Website $project)
    {
        $fileName = time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = public_path('uploads/websites/' . $project->id);
        $file->move($destinationPath, $fileName);

        $project->fill([
            'website_logo_path' => '/uploads/websites/' . $project->id . '/' . $fileName,
        ])->save();
    }
}