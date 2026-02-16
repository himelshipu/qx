@extends('web.layouts.app')
@section('content')
<div class="max-w-lg mx-auto px-4 sm:px-0">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">Verify your email</h1>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">We sent an email to <span class="font-semibold text-gray-900 dark:text-white" x-text="email"></span></p>
            <a href="#" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-200 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M17.3726 8.17276C20.0986 9.39695 22 12.1611 22 15.375V18.75C22 19.9926 21.0051 21 19.7778 21H13.1111C10.2084 21 7.73898 19.1217 6.82379 16.5M17.3726 8.17276C16.6711 5.20566 14.0344 3 10.8889 3H9.77778C5.48223 3 2 6.52576 2 10.875V14.25C2 15.4926 2.99492 16.5 4.22222 16.5H6.82379M17.3726 8.17276C17.4922 8.67875 17.5556 9.20688 17.5556 9.75C17.5556 13.4779 14.5708 16.5 10.8889 16.5H6.82379M10 10C11.1046 10 12 9.10457 12 8C12 6.89543 11.1046 6 10 6C8.89543 6 8 6.89543 8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M11 13C11 13.5523 10.5523 14 10 14C9.44772 14 9 13.5523 9 13C9 12.4477 9.44772 12 10 12C10.5523 12 11 12.4477 11 13Z" fill="currentColor"/>
                </svg>
                Open Gmail
            </a>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                <span class="font-medium">Tip:</span> Check your <span class="font-semibold text-blue-600 dark:text-blue-400">spam</span> folder for an email from 
                <span class="font-mono text-xs bg-blue-100 dark:bg-blue-900/40 px-2 py-1 rounded-md">info@gmail.com</span>
            </p>
        </div>
        
        <div class="space-y-2">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Verification Code</label>
            <input 
                type="text" 
                placeholder="Enter 6-digit code" 
                class="w-full h-12 px-4 text-lg text-center tracking-widest font-mono rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-pink-400 dark:focus:border-pink-600 focus:ring-2 focus:ring-pink-200 dark:focus:ring-pink-900/30 outline-none transition-all duration-200"
                maxlength="6"
            >
        </div>
        
        <div class="flex flex-col gap-3">
            <button class="w-full bg-gradient-to-r from-gray-900 to-gray-800 hover:from-pink-600 hover:to-pink-500 text-white font-semibold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                Continue
            </button>
            
            <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                Didn't receive the code? 
                <button class="text-pink-600 dark:text-pink-400 hover:text-pink-700 dark:hover:text-pink-300 font-medium hover:underline transition-colors">
                    Resend
                </button>
            </p>
        </div>
    </div>




    <div class="my-2 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>


    <!-- STEP 3: OBJECTIVE (Survey) -->
    <div x-show="step === 'objective'" x-cloak class="text-center my-2">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-10">What are you here to do?</h1>
        
        <div class="space-y-4 mb-10">
            <!-- Option 1 -->
            <label class="relative flex items-center p-5 border rounded-2xl cursor-pointer transition-all hover:border-pink-400"
                    :class="objective === 'one-time' ? 'border-pink-400 ring-1 ring-pink-400' : 'border-gray-100 dark:border-gray-800'">
                <input type="radio" x-model="objective" value="one-time" class="sr-only">
                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center mr-4" :class="objective === 'one-time' ? 'border-pink-400' : 'border-gray-200'">
                    <div class="w-3 h-3 rounded-full bg-pink-400" x-show="objective === 'one-time'"></div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xl">👥</span>
                    <span class="text-lg font-medium text-gray-800 dark:text-white">Find influencers for a one-time campaign</span>
                </div>
            </label>

            <!-- Option 2 -->
            <label class="relative flex items-center p-5 border rounded-2xl cursor-pointer transition-all hover:border-pink-400"
                    :class="objective === 'ongoing' ? 'border-pink-400 ring-1 ring-pink-400' : 'border-gray-100 dark:border-gray-800'">
                <input type="radio" x-model="objective" value="ongoing" class="sr-only">
                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center mr-4" :class="objective === 'ongoing' ? 'border-pink-400' : 'border-gray-200'">
                    <div class="w-3 h-3 rounded-full bg-pink-400" x-show="objective === 'ongoing'"></div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xl">🛒</span>
                    <span class="text-lg font-medium text-gray-800 dark:text-white">Get ongoing influencer content</span>
                </div>
            </label>

            <!-- Option 3 -->
            <label class="relative flex items-center p-5 border rounded-2xl cursor-pointer transition-all hover:border-pink-400"
                    :class="objective === 'exploring' ? 'border-pink-400 ring-1 ring-pink-400' : 'border-gray-100 dark:border-gray-800'">
                <input type="radio" x-model="objective" value="exploring" class="sr-only">
                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center mr-4" :class="objective === 'exploring' ? 'border-pink-400' : 'border-gray-200'">
                    <div class="w-3 h-3 rounded-full bg-pink-400" x-show="objective === 'exploring'"></div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xl">📱</span>
                    <span class="text-lg font-medium text-gray-800 dark:text-white">I'm not sure yet, just exploring</span>
                </div>
            </label>
        </div>

        <button class="w-full py-4 rounded-xl font-bold transition-all"
                :class="objective ? 'bg-[#1A1A1A] text-white hover:bg-black' : 'bg-gray-400 text-white cursor-not-allowed'">
            Continue
        </button>
    </div>


</div>
@endsection
