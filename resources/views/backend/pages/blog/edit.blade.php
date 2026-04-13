@extends('backend.layouts.app')

@section('title', 'Edit Blog Post')

@section('content')
    <x-backend.shell.breadcrumb pageTitle="Edit Blog Post" />

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-col gap-4 border-b border-gray-200 p-5 lg:flex-row lg:items-center lg:justify-between dark:border-gray-800">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit: {{ $post->title }}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update the post content, metadata, and publication state.</p>
            </div>
            <a href="{{ route('dashboard.blogs.show', $post) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                Back to Post
            </a>
        </div>

        <form action="{{ route('dashboard.blogs.update', $post) }}" method="POST" enctype="multipart/form-data" novalidate class="p-5">
            @csrf
            @method('PUT')

            @include('backend.pages.blog.form', [
                'post' => $post,
                'action' => route('dashboard.blogs.update', $post),
                'method' => 'PUT',
                'submitButtonText' => 'Save Changes'
            ])
        </form>
    </div>
@endsection
