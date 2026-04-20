@extends('backend.layouts.app')

@section('title', 'Create Blog Post')

@section('content')
    <x-backend.shell.breadcrumb pageTitle="Create Blog Post" />

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-col gap-4 border-b border-gray-200 p-5 lg:flex-row lg:items-center lg:justify-between dark:border-gray-800">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">New Blog Post</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Write a polished article for your platform blog.</p>
            </div>
            <a href="{{ route('dashboard.blogs.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                Back to Posts
            </a>
        </div>

        <form action="{{ route('dashboard.blogs.store') }}" method="POST" enctype="multipart/form-data" novalidate class="p-5">
            @csrf

            @include('backend.pages.blog.form', [
                'post' => $post,
                'action' => route('dashboard.blogs.store'),
                'method' => 'POST',
                'submitButtonText' => 'Create Post',
                'isPublished' => false,
                'isFeatured' => false,
                'featuredImageUrl' => null,
                'nextSortOrder' => $nextSortOrder ?? 0
            ])
        </form>
    </div>
@endsection
