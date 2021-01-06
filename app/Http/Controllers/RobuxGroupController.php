<?php

namespace App\Http\Controllers;

use App\Models\RobuxGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class RobuxGroupController extends Controller
{
    public function index(): JsonResponse
    {
        $robuxGroups = RobuxGroup::withTotalWithdrawn()->orderByDesc('id')->paginate(10);

        $robuxGroupsArr = $robuxGroups->append('formatted_total_withdrawn', 'formatted_disabled_at');

        $robuxGroupsArr->map(static function (RobuxGroup $robuxGroup) {
            $robuxGroup->cookie = Str::limit($robuxGroup->cookie, 200);
        });

        $pagination = $robuxGroups->toArray();
        unset($pagination['data']);

        return response()->json([
            'groups' => $robuxGroupsArr,
            'pagination' => $pagination,
        ]);
    }
}
