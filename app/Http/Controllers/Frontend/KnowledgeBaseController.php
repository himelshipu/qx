<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBaseArticle;

class KnowledgeBaseController extends Controller
{
    public function index()
    {
        $featured = KnowledgeBaseArticle::published()
            ->where('is_featured', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        $articles = KnowledgeBaseArticle::published()
            ->orderBy('sort_order', 'asc')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('frontend.pages.knowledge-base.index', compact('articles', 'featured'));
    }

    public function show(KnowledgeBaseArticle $article)
    {
        abort_unless($article->is_published, 404);

        $related = KnowledgeBaseArticle::published()
            ->whereKeyNot($article->id)
            ->when($article->badge, fn ($query) => $query->where('badge', $article->badge))
            ->orderBy('sort_order', 'asc')
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();

        return view('frontend.pages.knowledge-base.show', compact('article', 'related'));
    }
}
