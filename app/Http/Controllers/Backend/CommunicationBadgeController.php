<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Admin\CommunicationBadgeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class CommunicationBadgeController extends Controller
{
    public function __construct(
        private readonly CommunicationBadgeService $service,
    ) {
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'conversations' => 0,
                'support_tickets' => 0,
                'notifications' => 0,
            ]);
        }

        return response()->json($this->service->getSidebarBadgeCounts($user));
    }
}
