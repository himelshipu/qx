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

    /**
     * Display the user's profile form.
     */
    public function index(ContentLibraryIndexRequest $request): View
    {
        $user = $request->user();

        return view('frontend.pages.content-library', $this->contentLibraryService->getListingPayload(
            $user,
            $request->search(),
            $request->status(),
            $request->perPage()
        ));
    }
}
