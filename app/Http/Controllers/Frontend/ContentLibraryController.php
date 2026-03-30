<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\ContentLibraryIndexRequest;
use App\Services\Frontend\ContentLibraryService;
use Illuminate\View\View;

class ContentLibraryController extends Controller
{
    public function __construct(
        private readonly ContentLibraryService $contentLibraryService
    ) {}

    public function index(ContentLibraryIndexRequest $request): View
    {
        $user = $request->user();
        
        $filters = [
            'search' => $request->search(),
            'status' => $request->status(),
            'platform' => $request->platform(),
            'date_from' => $request->dateFrom(),
            'date_to' => $request->dateTo(),
            'per_page' => $request->perPage(),
        ];
        
        $data = $this->contentLibraryService->getListingPayload($user, $filters);
        
        $data['filters'] = $filters;
        
        return view('frontend.pages.content-library', $data);
    }
}