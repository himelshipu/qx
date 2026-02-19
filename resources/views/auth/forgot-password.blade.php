@extends('frontend.layouts.app')
@section('content')
<div class="max-w-lg mx-auto px-4 sm:px-0">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">Forgot Password</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">No problem. Just let us know your email address and we will send you a password reset link.</p>
    </div>

    @if (session('status'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
            <p class="text-sm text-green-700 dark:text-green-400">{{ session('status') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
            @foreach ($errors->all() as $error)
                <p class="text-sm text-red-700 dark:text-red-400">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf
        
        <div class="space-y-2">
            <label for="email" class="text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
            <input 
                type="email" 
                id="email"
                name="email"
                placeholder="Enter your email" 
                class="w-full h-12 px-4 text-base rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-600 focus:border-pink-400 dark:focus:border-pink-600 focus:ring-2 focus:ring-pink-200 dark:focus:ring-pink-900/30 outline-none transition-all duration-200"
                value="{{ old('email') }}"
                required
                autofocus
            >
        </div>

        <div class="flex flex-col gap-3">
            <button type="submit" class="w-full bg-gradient-to-r from-gray-900 to-gray-800 hover:from-pink-600 hover:to-pink-500 text-white font-semibold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                Send Password Reset Link
            </button>
        </div>
    </form>

    <div class="mt-8 text-sm text-gray-600 dark:text-gray-400">
        <p>
            We'll send an email to the address you provide with instructions to reset your password. 
            The link will expire in 60 minutes for security reasons.
        </p>
    </div>

    <div class="mt-4 flex items-center justify-center">
        <a href="{{ route('login') }}" class="text-pink-600 dark:text-pink-400 hover:text-pink-700 dark:hover:text-pink-300 font-medium hover:underline transition-colors">
            Back to Login
        </a>
    </div>
</div>
@endsection