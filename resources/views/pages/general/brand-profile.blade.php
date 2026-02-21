@extends('layouts.general.app')

@section('content')

<div class="min-h-screen bg-white dark:bg-gray-950 flex flex-col gap-4 px-4 sm:px-6 lg:px-8 py-20">


    <div class="bg-[#1A1A1A] text-white p-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">

            <div class="max-w-2xl">
                <h1 class="text-2xl font-bold mb-2">Complete Your Profile</h1>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Your profile is the first thing creators view to learn about your brand. 
                    Having a complete, detailed profile helps creators decide if you're a fit to collaborate with.
                </p>
            </div>

            <a href="/brand-edit-profile/"
                class="bg-white text-black px-6 py-2.5 rounded-xl font-bold text-sm 
                        hover:bg-gray-100 transition shadow-md whitespace-nowrap">
                Complete Profile
            </a>
        </div>
    </div>

    <div class="px-4 flex flex-col gap-6">

        <!-- ================= PROFILE SECTION ================= -->
        <section class="py-14 max-w-svw mx-auto">

            <!-- Edit Button -->
            <div class="flex justify-end mb-6">
                <a href="/brand-edit-profile/" class="flex items-center gap-2 text-sm font-semibold text-gray-600 dark:text-gray-50 hover:text-black transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    Edit
                </a>
            </div>

            <!-- Cover -->
            <div class="relative mb-20">
                <div class="w-full h-80 md:h-[320px] bg-gray-200 rounded-3xl"></div>

                <!-- Avatar -->
                <div class="absolute -bottom-14 left-1/2 -translate-x-1/2">
                    <div class="w-32 h-32 rounded-full bg-[#FFE4C4]
                                border-[6px] border-white shadow-md
                                flex items-center justify-center
                                text-4xl font-bold text-black">
                        S
                    </div>
                </div>
            </div>

            <!-- Name + Description -->
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Script Notion</h2>

                <p class="text-sm text-gray-500 dark:text-white max-w-2xl mx-auto leading-relaxed">
                    A quality description, logo and adding your social channels results in 
                    <span class="font-semibold text-gray-800 dark:text-gray-100">3x more influencer collaborations</span> on QX.
                    <a href="#" class="font-semibold text-black dark:text-gray-100 underline hover:opacity-70 transition">
                        Complete your profile now.
                    </a>
                </p>
            </div>

        </section>


        <!-- ================= CAMPAIGNS SECTION ================= -->
        <section class="border-t py-4 border-gray-100">

            <h3 class="text-xl font-medium text-[#222] dark:text-white mb-4">Campaigns</h3>

            <div class="max-w-xl relative group overflow-hidden rounded-2xl 
                        bg-gray-200 aspect-video flex items-center justify-center
                        hover:shadow-xl transition duration-300 cursor-pointer">

                <svg class="w-20 h-20 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>

                <div class="absolute bottom-4 left-4">
                    <span class="bg-black/70 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1.5 rounded-lg">
                        In-progress
                    </span>
                </div>
            </div>

        </section>


        <!-- ================= REVIEWS SECTION ================= -->
        <section class="border-t py-4 border-gray-100">

            <h3 class="text-xl font-medium text-[#222] dark:text-white mb-4">Reviews</h3>

            <p class="text-sm text-gray-500 italic">
                You have no reviews yet.
            </p>

        </section>

    </div>
</div>

@endsection