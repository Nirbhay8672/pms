<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberFormRequest;
use App\Http\Requests\WebsiteExistFormRequest;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class MemberController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('member/Index', [
            'mini_sidebar' => true, 
            'page_name' => 'Members',
        ]);
    }

    public function datatable(Request $request): JsonResponse
    {
        try {
            $search = $request->search;
            $perPage = $request->per_page ?? 10;
            $page = $request->page ?? 1;

            $query = Member::query();

            if ($search) {
                $query->orWhere('username', 'like', '%' . $search . '%');
                $query->orWhere('email', 'like', '%' . $search . '%');
                $query->orWhere('phone_number', 'like', '%' . $search . '%');
                $query->orWhere('website_name', 'like', '%' . $search . '%');
            }

            $total = $query->count(); 
            $offset = ($page - 1) * $perPage;

            $members = $query->offset($offset)
                ->limit($perPage)
                ->get();

            $total_pages = ceil($total / $perPage);

            $startIndex = ($page - 1) * $perPage;
            $endIndex = min($startIndex + $perPage, $total);

            return $this->successResponse(message: "Members details fetch.", data: [
                'members' => $members,
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

    private function generateRandomKey($length = 32) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';    
        $charLength = strlen($characters);
        $key = '';
    
        for ($i = 0; $i < $length; $i++) {
            $randomIndex = mt_rand(0, $charLength - 1);
            $key .= $characters[$randomIndex];
        }
        return $key;
    }

    public function createOrUpdate(MemberFormRequest $request, Member $member): JsonResponse
    {
        try {
            DB::beginTransaction();

            $fields = $request->fields();

            if($request->id == '' || $request->id == null) {
                $fields['licence_key'] = $this->generateRandomKey();
            }

            $member->fill($fields)->save();

            DB::commit();

            return $this->successResponse(message: "{$member->username} member has been {$request->action()} successfully.");
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse(message: $exception->getMessage());
        }
    }

    public function updateStatus(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $member = Member::find($request->member_id);

            $member->fill([
                'user_status' => $request->user_status == 1 ? 0 : 1,
            ])->save();

            DB::commit();

            $status = $request->user_status == 1 ? 'deactivate' : 'activate';

            return $this->successResponse(message: "{$member->username} member has been {$status} successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }

    public function checkWebsiteIsExist(WebsiteExistFormRequest $request): JsonResponse
    {
        $website_link = $request->website;

        if (substr($request->website, -1) !== '/') {
            $website_link = $request->website."/";
        }

        try {
            $member = Member::where('website_link', $website_link)
                ->where('licence_key', $request->licence_key)
                ->where('email', $request->email)
                ->select([
                    'id',
                    'username',
                    'email',
                    'user_status',
                    'phone_number',
                    'website_name',
                    'website_link',
                    'wp_username',
                    'otech_username',
                    'otech_password',
                    'licence_key',
                    'plugin_version',
                    'send_update',
                ])
                ->first();

            $data = [];
            $msg = '';

            if($member) {
                if($member->user_status == 1) {
                   $data['details'] = $member;
                   $data['is_exist'] = true;
                   $msg = 'Website details fetch successfully.';
                } else {
                    $data['is_user_active'] = false;  
                    $data['is_exist'] = true;
                    $msg = 'Website details fetched but user is deactivate.';
                }
            } else {
                $data['details'] = null;
                $data['is_exist'] = false;
                $msg = 'Website dose not exist.';
            }

            return $this->successResponse(message: $msg, data: $data);

        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse(message: $exception->getMessage());
        }
    }
}