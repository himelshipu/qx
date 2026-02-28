@extends('frontend.layouts.app')
@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-semibold text-[#222] dark:text-white leading-tight text-center mb-4">Forgot Password</h1>
        <p class="text-sm text-gray-800 text-center dark:text-gray-400">No problem. Just let us know your email address and we will send you a password reset link.</p>
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
            <label for="email" class="mb-1.5 block text-sm font-medium text-gray-800 dark:text-gray-400">Email Address</label>
            <input 
                type="email" 
                id="email"
                name="email"
                placeholder="Enter your email" 
                class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                value="{{ old('email') }}"
                required
                autofocus
            >
        </div>

        <div class="flex flex-col gap-3">
            <button type="submit" class="bg-[#1A1A1A] hover:bg-purple-400 flex w-full items-center justify-center rounded-xl px-4 py-4 text-sm font-bold text-white transition active:scale-[0.98]">
                Send Password Reset Link
            </button>
        </div>
    </form>

    <div class="mt-8 text-sm text-gray-800 text-center dark:text-gray-400">
        <p>
            We'll send an email to the address you provide with instructions to reset your password. 
            The link will expire in 60 minutes for security reasons.
        </p>
    </div>

    <div class="mt-4 flex items-center justify-center">
        <a href="{{ route('login') }}" class="text-purple-400 font-bold">
            Back to Login
        </a>
    </div>
</div>
@endsection