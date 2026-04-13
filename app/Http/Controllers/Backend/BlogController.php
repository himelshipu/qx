<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:blogs.index')->only(['index']);
        $this->middleware('permission:blogs.create')->only(['create', 'store']);
        $this->middleware('permission:blogs.show')->only(['show']);
        $this->middleware('permission:blogs.edit')->only(['edit', 'update']);
        $this->middleware('permission:blogs.toggle-status')->only(['toggleStatus']);
        $this->middleware('permission:blogs.destroy')->only(['destroy']);
        $this->middleware('permission:blogs.restore')->only(['restore']);
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q', ''));
        $status = (string) $request->string('status', 'all');

        $postsQuery = BlogPost::with('author')->latest('published_at')->latest('updated_at');

        if ($search !== '') {
            $postsQuery->search($search);
        }

        if ($status === 'published') {
            $postsQuery->published();
        } elseif ($status === 'draft') {
            $postsQuery->where('is_published', false);
        } elseif ($status === 'trashed') {
            $postsQuery->onlyTrashed();
        }

        $posts = $postsQuery->paginate(12)->withQueryString();

        $stats = [
            'total' => BlogPost::count(),
            'published' => BlogPost::where('is_published', true)->count(),
            'draft' => BlogPost::where('is_published', false)->count(),
            'featured' => BlogPost::where('is_featured', true)->count(),
            'trashed' => BlogPost::onlyTrashed()->count(),
        ];

        return view('backend.pages.blog.index', [
            'posts' => $posts,
            'stats' => $stats,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('backend.pages.blog.create', [
            'post' => new BlogPost(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);
        $validated['author_id'] = Auth::id();
        $validated['slug'] = $this->makeSlug($validated['slug'] ?? '', $validated['title']);
        $validated['is_published'] = $request->boolean('is_published');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['published_at'] = $validated['is_published'] ? ($validated['published_at'] ?? now()) : null;

        if ($request->hasFile('featured_image_file')) {
            $validated['featured_image_path'] = $request->file('featured_image_file')->store('blog/posts', 'public');
        }

        $post = BlogPost::create($validated);

        return redirect()
            ->route('dashboard.blogs.show', $post)
            ->with('success', 'Blog post created successfully.');
    }

    public function show(BlogPost $blogPost): View
    {
        return view('backend.pages.blog.show', [
            'post' => $blogPost->load('author'),
        ]);
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('backend.pages.blog.edit', [
            'post' => $blogPost,
        ]);
    }

    public function update(Request $request, BlogPost $blogPost): RedirectResponse
    {
        $validated = $this->validatePayload($request, $blogPost);
        $validated['slug'] = $this->makeSlug($validated['slug'] ?? '', $validated['title'], $blogPost->id);
        $validated['is_published'] = $request->boolean('is_published');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['published_at'] = $validated['is_published']
            ? ($blogPost->published_at ?? now())
            : null;

        if ($request->hasFile('featured_image_file')) {
            $this->deleteFromPublicDisk($blogPost->featured_image_path);
            $validated['featured_image_path'] = $request->file('featured_image_file')->store('blog/posts', 'public');
        }

        $blogPost->update($validated);

        return redirect()
            ->route('dashboard.blogs.show', $blogPost)
            ->with('success', 'Blog post updated successfully.');
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $blogPost->delete();

        return redirect()
            ->route('dashboard.blogs.index')
            ->with('success', 'Blog post moved to trash.');
    }

    public function toggleStatus(BlogPost $blogPost): RedirectResponse
    {
        $blogPost->update([
            'is_published' => ! $blogPost->is_published,
            'published_at' => $blogPost->is_published ? null : ($blogPost->published_at ?? now()),
        ]);

        return redirect()
            ->back()
            ->with('success', $blogPost->is_published ? 'Blog post published.' : 'Blog post moved to draft.');
    }

    public function restore(string $slug): RedirectResponse
    {
        $blogPost = BlogPost::withTrashed()->where('slug', $slug)->firstOrFail();
        $blogPost->restore();

        return redirect()
            ->route('dashboard.blogs.index', ['status' => 'trashed'])
            ->with('success', 'Blog post restored successfully.');
    }

    private function validatePayload(Request $request, ?BlogPost $blogPost = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('blog_posts', 'slug')->ignore($blogPost?->id)->whereNull('deleted_at'),
            ],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'featured_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'is_published' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function makeSlug(?string $slug, string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug(trim((string) $slug) !== '' ? $slug : $title);
        $base = $base !== '' ? $base : 'blog-post';

        $candidate = $base;
        $suffix = 1;

        while (BlogPost::withTrashed()
            ->where('slug', $candidate)
            ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $candidate = $base . '-' . $suffix;
            $suffix++;
        }

        return $candidate;
    }

    private function deleteFromPublicDisk(?string $path): void
    {
        if (! $path) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}