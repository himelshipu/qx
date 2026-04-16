<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\KnowledgeBase\StoreKnowledgeBaseArticleRequest;
use App\Http\Requests\Backend\KnowledgeBase\UpdateKnowledgeBaseArticleRequest;
use App\Models\KnowledgeBaseArticle;
use App\Services\Admin\KnowledgeBaseArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KnowledgeBaseController extends Controller
{
    public function __construct(
        private readonly KnowledgeBaseArticleService $service
    ) {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');
        $featured = (string) $request->query('featured', 'all');

        $articles = $this->service->paginateForDashboard($search, $status, $featured);

        $articles->appends([
            'q' => $search,
            'status' => $status,
            'featured' => $featured,
        ]);

        return view('backend.pages.knowledge-base.index', [
            'articles' => $articles,
            'stats' => $this->service->stats(),
            'search' => $search,
            'status' => $status,
            'featured' => $featured,
        ]);
    }

    public function table(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');
        $featured = (string) $request->query('featured', 'all');

        $articles = $this->service->paginateForDashboard($search, $status, $featured);
        $articles->appends([
            'q' => $search,
            'status' => $status,
            'featured' => $featured,
        ]);

        return view('backend.pages.knowledge-base._results', [
            'articles' => $articles,
        ]);
    }

    public function create(): View
    {
        return view('backend.pages.knowledge-base.create', [
            'article' => new KnowledgeBaseArticle(),
            'nextSortOrder' => $this->service->nextSortOrder(),
        ]);
    }

    public function store(StoreKnowledgeBaseArticleRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $isPublished = $request->boolean('is_published');
        $isFeatured = $request->boolean('is_featured');

        $this->service->create($validated, $isPublished, $isFeatured);

        return redirect()->route('dashboard.knowledge-base.index')
            ->with('success', 'Knowledge base article created successfully.');
    }

    public function edit(KnowledgeBaseArticle $article): View
    {
        return view('backend.pages.knowledge-base.edit', compact('article'));
    }

    public function update(UpdateKnowledgeBaseArticleRequest $request, KnowledgeBaseArticle $article): RedirectResponse
    {
        $validated = $request->validated();

        $isPublished = $request->boolean('is_published');
        $isFeatured = $request->boolean('is_featured');

        $this->service->update($article, $validated, $isPublished, $isFeatured);

        return redirect()->route('dashboard.knowledge-base.index')
            ->with('success', 'Knowledge base article updated successfully.');
    }

    public function destroy(KnowledgeBaseArticle $article): RedirectResponse
    {
        $this->service->delete($article);

        return redirect()->route('dashboard.knowledge-base.index')
            ->with('success', 'Knowledge base article deleted successfully.');
    }

    public function toggleStatus(Request $request, KnowledgeBaseArticle $article): JsonResponse|RedirectResponse
    {
        $article = $this->service->toggleStatus($article);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'id' => $article->id,
                'is_published' => (bool) $article->is_published,
                'published_at' => optional($article->published_at)?->format('M d, Y'),
            ]);
        }

        return redirect()->back()
            ->with('success', 'Knowledge base article status updated successfully.');
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ordered_ids' => ['required', 'array', 'min:1'],
            'ordered_ids.*' => ['required', 'integer', 'exists:knowledge_base_articles,id'],
        ]);

        $this->service->reorder($validated['ordered_ids']);

        return response()->json([
            'ok' => true,
            'message' => 'Knowledge base article order updated successfully.',
        ]);
    }
}
