<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberFormRequest;
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
                $query->where('username', 'like', '%' . $search . '%');
                $query->orWhere('email', 'like', '%' . $search . '%');
                $query->where('phone_number', 'like', '%' . $search . '%');
                $query->where('website_name', 'like', '%' . $search . '%');
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

    public function delete(Member $member): JsonResponse
    {
        try {
            DB::beginTransaction();

            $member->delete();

            DB::commit();

            return $this->successResponse(message: "{$member->username} member has been deleted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }

    public function deleteMembers(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            foreach ($request->selected_members as $member_id) {
                $member = Member::find(intval($member_id));
                $member->delete();
            }

            DB::commit();

            return $this->successResponse(message: "{$member->username} member has been deleted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }

    public function checkWebsiteIsExist(Request $request): JsonResponse
    {
        try {
            $member = Member::where('website_link', $request->website)
                ->select([
                    'id',
                    'username',
                    'email',
                    'user_status',
                    'phone_number',
                    'website_name',
                    'website_link',
                    'wp_username',
                    'wp_password',
                    'otech_username',
                    'otech_password',
                    'licence_key',
                    'plugin_version',
                    'send_update',
                ])
                ->where('licence_key', $request->licence_key)
                ->first();

            return $this->successResponse(message: "Response from server.", data: [
                'is_exist' => $member ? true : false,
                'details' => $member ?? null,
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponse(message: $exception->getMessage());
        }
    }
}