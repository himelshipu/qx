@extends('layouts.general.app')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-950 px-4 sm:px-6 lg:px-8 py-20">
    <main class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">
                Campaigns
            </h1>
            
            <a href="/post-campaign" class="flex items-center gap-2 bg-[#1A1A1A] hover:bg-purple-400 text-white px-6 py-4 rounded-xl font-bold text-sm transition shadow-lg active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                </svg>
                Post a Campaign
            </a>
        </div>

        <!-- Tab Navigation -->
        <div class="flex gap-10 border-b border-gray-100 dark:border-gray-800 mb-10">
            <div class="relative pb-4">
                <button class="text-lg font-bold text-gray-900 dark:text-white transition-all">
                    Drafts
                </button>
                <!-- Active Underline -->
                <div class="absolute bottom-0 left-0 w-full h-1 bg-black dark:bg-white rounded-t-full"></div>
            </div>
        </div>

        <!-- Campaign Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            
            <!-- Campaign Card Component -->
            <div class="group cursor-pointer max-w-sm">
                <div class="relative aspect-square rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-200 dark:from-gray-800 dark:to-gray-900 flex items-center justify-center overflow-hidden transition-all shadow-sm group-hover:shadow-md">
                    
                    <div class="transform group-hover:scale-110 transition-transform duration-300 ease-in-out">
                        <svg class="w-28 h-28 text-[#71717a] opacity-50" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    
                    <div class="absolute bottom-0 left-0 w-full p-5 bg-gradient-to-t from-black/40 to-transparent">
                        <p class="text-sm font-medium text-gray-400 leading-tight">In-progress</p>
                        <p class="text-sm text-gray-700 font-medium">Instagram Campaign</p>
                    </div>
                </div>
            </div>

        </div>

    </main>
</div>
@endsection