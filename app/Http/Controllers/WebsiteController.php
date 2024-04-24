<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectFormRequest;
use App\Models\Website;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class WebsiteController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('website/Index');
    }

    public function datatable(Request $request): JsonResponse
    {
        try {
            $search = $request->search;
            $perPage = $request->per_page ?? 10;
            $page = $request->page ?? 1;

            $query = Website::query();

            $query->join('clients', 'clients.id', 'websites.client_id');

            $query->select([
                'websites.*',
                'clients.name AS client_name',
            ]);

            if ($search) {
                $query->where('websites.website_name', 'like', '%' . $search . '%');
            }

            $total = $query->count();
            $offset = ($page - 1) * $perPage;

            $websites = $query->offset($offset)
                ->limit($perPage)
                ->orderBy('websites.id', 'DESC')
                ->get();

            $total_pages = ceil($total / $perPage);

            $startIndex = ($page - 1) * $perPage;
            $endIndex = min($startIndex + $perPage, $total);

            return $this->successResponse(message: "Websites details fetch.", data: [
                'websites' => $websites,
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

    public function addWebsite(ProjectFormRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $project = new Website();

            $project->fill([
                'website_name' => $request->website_name,
                'website_url' => $request->website_url,
                'website_logo_path' => '',
                'google_rank' => $request->google_rank,
                'client_id' => 1,
                'time' => $request->google_rank,
                'total_update' => $request->total_update,
                'is_backup_active' => $request->is_backup_active,
                'total_site_helth' => $request->total_site_helth,
                'total_php_issue' => $request->total_php_issue,
                'wp_admin_url' => $request->wp_admin_url,
            ])->save();

            if ($request->project_logo) {
                $this->storeFile($request->project_logo, $project);
            }

            DB::commit();

            return $this->successResponse(message: "Website has been added successfully.");
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