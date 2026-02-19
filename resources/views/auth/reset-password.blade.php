@extends('frontend.layouts.app')
@section('content')
<div class="max-w-lg mx-auto px-4 sm:px-0">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">Reset Password</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">Please choose a new password for your account.</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
            @foreach ($errors->all() as $error)
                <p class="text-sm text-red-700 dark:text-red-400">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
            <input 
                type="email" 
                id="email"
                name="email"
                class="w-full h-12 px-4 text-base rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-pink-400 dark:focus:border-pink-600 focus:ring-2 focus:ring-pink-200 dark:focus:ring-pink-900/30 outline-none transition-all duration-200"
                value="{{ old('email', $request->email) }}"
                required
                autofocus
                autocomplete="username"
                readonly
            >
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <label for="password" class="text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
            <input 
                type="password" 
                id="password"
                name="password"
                placeholder="Enter new password"
                class="w-full h-12 px-4 text-base rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-pink-400 dark:focus:border-pink-600 focus:ring-2 focus:ring-pink-200 dark:focus:ring-pink-900/30 outline-none transition-all duration-200"
                required
                autocomplete="new-password"
            >
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Password must be at least 8 characters long</p>
        </div>

        <!-- Confirm Password -->
        <div class="space-y-2">
            <label for="password_confirmation" class="text-sm font-medium text-gray-700 dark:text-gray-300">Confirm New Password</label>
            <input 
                type="password" 
                id="password_confirmation"
                name="password_confirmation"
                placeholder="Confirm your new password"
                class="w-full h-12 px-4 text-base rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-pink-400 dark:focus:border-pink-600 focus:ring-2 focus:ring-pink-200 dark:focus:ring-pink-900/30 outline-none transition-all duration-200"
                required
                autocomplete="new-password"
            >
        </div>

        <div class="flex flex-col gap-3">
            <button type="submit" class="w-full bg-gradient-to-r from-gray-900 to-gray-800 hover:from-pink-600 hover:to-pink-500 text-white font-semibold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                Reset Password
            </button>
        </div>
    </form>

    <div class="mt-8 text-sm text-gray-600 dark:text-gray-400">
        <p class="mb-2">Password requirements:</p>
        <ul class="list-disc list-inside space-y-1 text-xs">
            <li>Minimum 8 characters long</li>
            <li>Include at least one uppercase letter</li>
            <li>Include at least one number</li>
            <li>Include at least one special character</li>
        </ul>
    </div>

    <div class="mt-4 flex items-center justify-center">
        <a href="{{ route('login') }}" class="text-pink-600 dark:text-pink-400 hover:text-pink-700 dark:hover:text-pink-300 font-medium hover:underline transition-colors">
            Back to Login
        </a>
    </div>
</div>
@endsection