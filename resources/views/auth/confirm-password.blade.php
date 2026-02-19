@extends('frontend.layouts.app')
@section('content')
<div class="max-w-lg mx-auto px-4 sm:px-0">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">Confirm Password</h1>
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                <span class="font-medium">Security Check:</span> This is a secure area of the application. Please confirm your password before continuing.
            </p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
            @foreach ($errors->all() as $error)
                <p class="text-sm text-red-700 dark:text-red-400">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
        @csrf

        <!-- Password -->
        <div class="space-y-2">
            <label for="password" class="text-sm font-medium text-gray-700 dark:text-gray-300">Your Password</label>
            <input 
                type="password" 
                id="password"
                name="password"
                placeholder="Enter your password to continue"
                class="w-full h-12 px-4 text-base rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-pink-400 dark:focus:border-pink-600 focus:ring-2 focus:ring-pink-200 dark:focus:ring-pink-900/30 outline-none transition-all duration-200"
                required
                autocomplete="current-password"
                autofocus
            >
        </div>

        <div class="flex flex-col gap-3">
            <button type="submit" class="w-full bg-gradient-to-r from-gray-900 to-gray-800 hover:from-pink-600 hover:to-pink-500 text-white font-semibold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                Confirm Password
            </button>
        </div>
    </form>

    <div class="mt-8 text-sm text-gray-600 dark:text-gray-400">
        <p>
            You're accessing a sensitive area that requires additional verification. 
            This helps us keep your account secure from unauthorized access.
        </p>
    </div>

    <div class="mt-4 flex items-center justify-center gap-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-pink-600 dark:text-pink-400 hover:text-pink-700 dark:hover:text-pink-300 font-medium hover:underline transition-colors">
                Log Out
            </button>
        </form>
        
        <span class="text-gray-300 dark:text-gray-700">|</span>
        
        <a href="{{ route('dashboard') }}" class="text-pink-600 dark:text-pink-400 hover:text-pink-700 dark:hover:text-pink-300 font-medium hover:underline transition-colors">
            Back to Dashboard
        </a>
    </div>
</div>
@endsection