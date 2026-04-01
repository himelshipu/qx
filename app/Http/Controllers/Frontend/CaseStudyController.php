<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use Illuminate\View\View;

/**
 * Class CaseStudyController
 *
 * Handles HTTP requests for public case studies display.
 */
final class CaseStudyController extends Controller
{
    /**
     * Display a listing of published case studies.
     *
     * @return View
     */
    public function index(): View
    {
        $caseStudies = CaseStudy::where('is_published', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('frontend.pages.case-studies', [
            'caseStudies' => $caseStudies,
        ]);
    }

    /**
     * Display a single published case study.
     *
     * @param CaseStudy $caseStudy
     * @return View
     */
    public function show(CaseStudy $caseStudy): View
    {
        // Abort if case study is not published
        abort_unless($caseStudy->is_published, 404);

        // Get related case studies (latest published, excluding current)
        $related = CaseStudy::where('is_published', true)
            ->whereKeyNot($caseStudy->id)
            ->orderBy('sort_order', 'asc')
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return view('frontend.pages.case-studies.show', [
            'caseStudy' => $caseStudy,
            'related' => $related,
        ]);
    }
}
