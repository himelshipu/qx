@extends('frontend.layouts.app')

@section('title', 'Edit Package')

@section('content')
	<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
		<div class="flex items-center justify-between mb-6">
			<h1 class="text-3xl font-bold text-gray-900 dark:text-white">
				Edit Package
			</h1>
			<a href="{{ route('frontend.packages.index') }}"
				class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:text-gray-500 dark:hover:text-gray-400 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition">
				<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
				</svg>
				Cancel
			</a>
		</div>

		<div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8">
			<form action="{{ route('frontend.packages.update', $package) }}" method="POST">
				@csrf
				@method('PUT')
				@include('frontend.packages._form', ['package' => $package])
				<div class="mt-8 pt-5 border-t border-gray-200 dark:border-gray-700">
					<div class="flex justify-end">
						<button type="submit"
							class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
							Save Changes
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
@endsection
