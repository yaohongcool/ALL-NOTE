<?php

namespace App\Http\Controllers;

use App\Models\EventTag;
use Illuminate\Http\JsonResponse;

class EventTagController extends Controller
{
    public function destroy(EventTag $eventTag): JsonResponse
    {
        abort_unless($eventTag->user_id === auth()->id(), 403);

        $eventTag->delete();

        return response()->json([
            'message' => '事件标签已删除。',
        ]);
    }
}
