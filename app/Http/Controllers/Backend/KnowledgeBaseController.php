<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBaseArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KnowledgeBaseController extends Controller
{
    public function index()
    {
        $articles = KnowledgeBaseArticle::orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('backend.pages.knowledge-base.index', compact('articles'));
    }

    public function create()
    {
        return view('backend.pages.knowledge-base.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:knowledge_base_articles,slug',
            'badge' => 'nullable|string|max:120',
            'summary' => 'nullable|string|max:500',
            'content' => 'required|string',
            'read_time_minutes' => 'required|integer|min:1|max:60',
            'sort_order' => 'nullable|integer|min:0',
            'published_at' => 'nullable|date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ]);

        $isPublished = $request->boolean('is_published');

        KnowledgeBaseArticle::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?? Str::slug($validated['title']),
            'badge' => $validated['badge'] ?? null,
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'],
            'read_time_minutes' => $validated['read_time_minutes'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'published_at' => $validated['published_at'] ?? ($isPublished ? now() : null),
            'is_featured' => $request->boolean('is_featured'),
            'is_published' => $isPublished,
        ]);

        return redirect()->route('dashboard.knowledge-base.index')
            ->with('success', 'Knowledge base article created successfully.');
    }

    public function edit(KnowledgeBaseArticle $article)
    {
        return view('backend.pages.knowledge-base.edit', compact('article'));
    }

    public function update(Request $request, KnowledgeBaseArticle $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:knowledge_base_articles,slug,' . $article->id,
            'badge' => 'nullable|string|max:120',
            'summary' => 'nullable|string|max:500',
            'content' => 'required|string',
            'read_time_minutes' => 'required|integer|min:1|max:60',
            'sort_order' => 'nullable|integer|min:0',
            'published_at' => 'nullable|date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ]);

        $isPublished = $request->boolean('is_published');

        $article->update([
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?? Str::slug($validated['title']),
            'badge' => $validated['badge'] ?? null,
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'],
            'read_time_minutes' => $validated['read_time_minutes'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'published_at' => $validated['published_at'] ?? ($isPublished && !$article->published_at ? now() : $article->published_at),
            'is_featured' => $request->boolean('is_featured'),
            'is_published' => $isPublished,
        ]);

        return redirect()->route('dashboard.knowledge-base.index')
            ->with('success', 'Knowledge base article updated successfully.');
    }

    public function destroy(KnowledgeBaseArticle $article)
    {
        $article->delete();

        return redirect()->route('dashboard.knowledge-base.index')
            ->with('success', 'Knowledge base article deleted successfully.');
    }

    public function toggleStatus(KnowledgeBaseArticle $article)
    {
        $article->update([
            'is_published' => !$article->is_published,
            'published_at' => !$article->is_published ? ($article->published_at ?? now()) : $article->published_at,
        ]);

        return redirect()->back()
            ->with('success', 'Knowledge base article status updated successfully.');
    }
}
