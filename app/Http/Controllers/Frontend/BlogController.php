<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q', ''));
        $featuredFilter = (string) $request->string('featured', 'all');

        $featuredPosts = BlogPost::query()
            ->where('is_published', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $posts = BlogPost::query()
            ->with('author')
            ->where('is_published', true)
            ->when($search !== '', fn ($query) => $query->search($search))
            ->when($featuredFilter === 'yes', fn ($query) => $query->where('is_featured', true))
            ->when($featuredFilter === 'no', fn ($query) => $query->where('is_featured', false))
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->paginate(12)
            ->withQueryString();

        return view('frontend.pages.blogs.index', [
            'posts' => $posts,
            'featuredPosts' => $featuredPosts,
            'search' => $search,
            'featuredFilter' => $featuredFilter,
        ]);
    }

    public function show(BlogPost $blogPost): View
    {
        abort_unless($blogPost->is_published, 404);

        $related = BlogPost::query()
            ->where('is_published', true)
            ->whereKeyNot($blogPost->id)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();

        return view('frontend.pages.blogs.show', [
            'post' => $blogPost->load('author'),
            'related' => $related,
        ]);
    }
}
