<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientFormRequest;
use App\Http\Requests\PackageTypeFormRequest;
use App\Models\Client;
use App\Models\PackageType;
use App\Models\Payment;
use App\Models\Website;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PackageTypeController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('packageType/Index');
    }
    public function datatable(Request $request): JsonResponse
    {
        try {
            $search = $request->search;
            $perPage = $request->per_page ?? 10;
            $page = $request->page ?? 1;

            $query = PackageType::query();

            if ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            }

            $total = $query->count();
            $offset = ($page - 1) * $perPage;

            $package_types = $query->offset($offset)
                ->limit($perPage)
                ->orderBy('id', 'DESC')
                ->get();

            $total_pages = ceil($total / $perPage);

            $startIndex = ($page - 1) * $perPage;
            $endIndex = min($startIndex + $perPage, $total);

            return $this->successResponse(message: "Package types details fetch.", data: [
                'package_types' => $package_types,
                'total' => $total,
                'total_pages' => $total_pages,
                'start_index' => $startIndex + 1,
                'end_index' => $endIndex,
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse(message: $exception->getMessage());
        }
    }

    public function createOrUpdate(PackageTypeFormRequest $request, PackageType $packageType): JsonResponse
    {
        try {
            DB::beginTransaction();

            $packageType->fill([
                'name' => $request->name,
            ])->save();

            DB::commit();

            return $this->successResponse(message: "{$packageType->name} has been {$request->action()} successfully.");
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse(message: $exception->getMessage());
        }
    }

    public function delete(PackageType $package_type): JsonResponse
    {
        try {
            DB::beginTransaction();

            $websites_count = Website::where('client_id', $package_type->id)->get()->count();

            if ($websites_count > 0) {
                return $this->errorResponse(message: "{$package_type->name} package type is in use.", status: 422);
            }

            $package_type->delete();

            DB::commit();

            return $this->successResponse(message: "{$package_type->name} client has been deleted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }

    public function payments(Request $request): JsonResponse
    {
        try {
            $payments = Payment::join('clients','clients.id','payments.client_id')
                ->leftJoin('websites','websites.id','payments.website_id')
                ->select([
                    'payments.*',
                    'clients.name AS client_name',
                    'websites.website_name AS website_name',
                ])->where('payments.client_id', '=', $request->client_id);

            if($request->website_id) {
                $payments->where('payments.website_id', '=', $request->website_id);
            }

            $payments->where('payments.status','Success');

            return $this->successResponse(message: "Clients details fetch.", data: [
                'payments' => $payments->get(),
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse(message: $exception->getMessage());
        }
    }
}