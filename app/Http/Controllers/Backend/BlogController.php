<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Blog\StoreBlogPostRequest;
use App\Http\Requests\Backend\Blog\UpdateBlogPostRequest;
use App\Models\BlogPost;
use App\Services\Admin\BlogPostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function __construct(
        private readonly BlogPostService $service
    ) {
        $this->middleware('permission:blogs.index')->only(['index', 'table']);
        $this->middleware('permission:blogs.create')->only(['create', 'store']);
        $this->middleware('permission:blogs.show')->only(['show']);
        $this->middleware('permission:blogs.edit')->only(['edit', 'update']);
        $this->middleware('permission:blogs.toggle-status')->only(['toggleStatus', 'toggleFeatured']);
        $this->middleware('permission:blogs.destroy')->only(['destroy']);
        $this->middleware('permission:blogs.restore')->only(['restore']);
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q', ''));
        $status = (string) $request->string('status', 'all');

        $payload = $this->service->getListingPayload($search, $status);
        $payload['posts']->appends([
            'q' => $search,
            'status' => $status,
        ]);

        return view('backend.pages.blog.index', $payload);
    }

    public function table(Request $request): View
    {
        $search = trim((string) $request->string('q', ''));
        $status = (string) $request->string('status', 'all');

        $posts = $this->service->getListingPayload($search, $status)['posts'];
        $posts->appends([
            'q' => $search,
            'status' => $status,
        ]);

        return view('backend.pages.blog._results', [
            'posts' => $posts,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('backend.pages.blog.create', [
            'post' => new BlogPost,
            'nextSortOrder' => $this->service->getNextSortOrder(),
        ]);
    }

    public function store(StoreBlogPostRequest $request): RedirectResponse
    {
        $post = $this->service->createBlogPost(
            $request->validated(),
            $request->boolean('is_published'),
            $request->boolean('is_featured'),
            $request->file('featured_image_file'),
            (int) auth()->id()
        );

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
            'nextSortOrder' => $this->service->getNextSortOrder(),
        ]);
    }

    public function update(UpdateBlogPostRequest $request, BlogPost $blogPost): RedirectResponse
    {
        $post = $this->service->updateBlogPost(
            $blogPost,
            $request->validated(),
            $request->boolean('is_published'),
            $request->boolean('is_featured'),
            $request->file('featured_image_file')
        );

        return redirect()
            ->route('dashboard.blogs.show', $post)
            ->with('success', 'Blog post updated successfully.');
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $title = $blogPost->title;
        $this->service->deleteBlogPost($blogPost);

        return redirect()
            ->route('dashboard.blogs.index')
            ->with('success', "Blog post '{$title}' moved to trash.");
    }

    public function restore(int $id): RedirectResponse
    {
        $post = $this->service->restoreBlogPost($id);

        return redirect()
            ->route('dashboard.blogs.index', ['status' => 'trashed'])
            ->with('success', "Blog post '{$post->title}' restored successfully.");
    }

    public function toggleStatus(BlogPost $blogPost): JsonResponse|RedirectResponse
    {
        $post = $this->service->toggleStatus($blogPost);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $post->is_published ? 'Blog post published.' : 'Blog post moved to draft.',
                'is_published' => (bool) $post->is_published,
            ]);
        }

        return redirect()
            ->back()
            ->with('success', $post->is_published ? 'Blog post published.' : 'Blog post moved to draft.');
    }

    public function toggleFeatured(BlogPost $blogPost): JsonResponse|RedirectResponse
    {
        $post = $this->service->toggleFeatured($blogPost);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $post->is_featured ? 'Blog post featured.' : 'Blog post unfeatured.',
                'is_featured' => (bool) $post->is_featured,
            ]);
        }

        return redirect()
            ->back()
            ->with('success', $post->is_featured ? 'Blog post featured.' : 'Blog post unfeatured.');
    }
}
