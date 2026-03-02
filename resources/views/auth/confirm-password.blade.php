@extends('frontend.layouts.app')
@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-semibold text-[#222] dark:text-white leading-tight text-center mb-4">Confirm Password</h1>
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-purple-300 rounded-xl p-4">
            <p class="text-sm text-gray-700 dark:text-gray-400">
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
                class="mt-1.5 dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                required
                autocomplete="current-password"
                autofocus
            >
        </div>

        <div class="flex flex-col gap-3">
            <button type="submit" class="bg-[#222] shadow-theme-xs hover:bg-purple-400 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                Confirm Password
            </button>
        </div>
    </form>

    <div class="mt-8 text-sm text-gray-800 text-center dark:text-gray-400">
        <p>
            You're accessing a sensitive area that requires additional verification. 
            This helps us keep your account secure from unauthorized access.
        </p>
    </div>

    <div class="mt-4 flex items-center justify-center gap-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-[#222] shadow-theme-xs hover:bg-purple-400 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                Log Out
            </button>
        </form>
        
        <span class="text-gray-300 dark:text-gray-700">|</span>
        
        <a href="{{ route('dashboard') }}" class="bg-[#222] shadow-theme-xs hover:bg-purple-400 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
            Back to Dashboard
        </a>
    </div>
</div>
@endsection