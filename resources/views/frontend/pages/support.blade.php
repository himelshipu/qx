@extends('frontend.layouts.app')

@section('content')
	<div class="min-h-screen transition-colors duration-200" x-data="{
    step: 'main',
    activeQuestion: null,
    selectedCategory: null,
    formData: {
        subject: '',
        category: '',
        description: ''
    },
    loading: false,
    message: '',

    // Helper to go back
    goBack() {
        if (this.step === 'form') this.step = 'categories';
        else if (this.step === 'categories') this.step = 'main';
    },

    submitForm() {
        this.loading = true;
        this.message = '';

        fetch('{{ route('support-tickets.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(this.formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.message = data.message;
                    this.formData = { subject: '', category: '', description: '' };
                    this.step = 'success';
                    setTimeout(() => {
                        this.step = 'main';
                        this.message = '';
                    }, 5000);
                } else {
                    this.message = data.message || 'Something went wrong. Please try again.';
                }
            })
            .catch(error => {
                this.message = 'An error occurred. Please try again.';
                console.error(error);
            })
            .finally(() => {
                this.loading = false;
            });
    }
}">

		<div class="max-w-6xl mx-auto">

			<div class="mb-16">
				<div class="bg-pink-50 dark:bg-transparent border border-pink-100 dark:border-gray-200 rounded-xl p-6 text-center">
					<p class="text-sm font-medium text-gray-800 dark:text-white">
						Beware of scams, QX will never contact you on Telegram or WeeChat.
					</p>
				</div>
			</div>

			<div x-show="step === 'main'" x-transition:enter="transition ease-out duration-500"
				x-transition:enter-start="opacity-0 transform scale-95" class="space-y-16">
				<div class="text-center">
					<h1 class="text-3xl md:text-4xl font-semibold text-[#222] dark:text-white leading-tight text-center mb-1">What Do
						You Need Help With Today?</h1>
					<p class="mt-4 text-gray-500 dark:text-gray-400  text-base">Choose from our support options below to get the help
						you need</p>
				</div>

				<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
					<!-- Card 1: Knowledge Base -->
					<a href="#"
						class="group relative flex flex-col items-center text-center bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-14 rounded-xl shadow-sm hover:shadow-xl transition-all">
						<div class="w-16 h-16 bg-pink-50 dark:bg-purple-400/10 rounded-full flex items-center justify-center mb-8"><svg
								class="w-7 h-7 text-pink-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path
									d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
									stroke-width="1.5" />
							</svg></div>
						<h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">View Knowledge Base</h3>
						<p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">Browse help articles and tutorials to find
							quick answers</p>
					</a>

					<!-- Card 2: Submit Ticket -->
					<div @click="step = 'categories'"
						class="cursor-pointer group relative flex flex-col items-center text-center bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-14 rounded-xl shadow-sm hover:shadow-xl transition-all">
						<div class="w-16 h-16 bg-blue-50 dark:bg-purple-400/10 rounded-full flex items-center justify-center mb-8"><svg
								class="w-7 h-7 text-blue-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="1.5" />
							</svg></div>
						<h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Submit a Ticket</h3>
						<p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">Contact our support team directly for
							personalized assistance</p>
					</div>
				</div>
			</div>

			<div x-show="step === 'categories'" x-cloak x-transition:enter="transition ease-out duration-400"
				x-transition:enter-start="opacity-0 translate-x-4" class="space-y-12">
				<div class="flex items-center gap-6">
					<button @click="goBack()"
						class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
						<svg class="w-4 h-4 text-gray-800 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path d="M15 19l-7-7 7-7" stroke-width="2.5" />
						</svg>
					</button>
					<h2 class="text-3xl font-semibold text-[#222] dark:text-white leading-tight text-left mb-4">What category does your
						question relate to?</h2>
				</div>

				<div class="space-y-0 divide-y divide-gray-100 dark:divide-gray-800 border-t border-gray-100 dark:border-gray-800">
					<template x-for="cat in ['Orders & Payments', 'General Question', 'Feedback', 'Other']">
						<div @click="selectedCategory = cat; formData.category = cat; step = 'form'"
							class="group flex items-center justify-between py-6 cursor-pointer hover:px-4 transition-all duration-300">
							<span
								class="text-lg font-bold text-gray-800 dark:text-gray-200 group-hover:text-black dark:group-hover:text-purple-400"
								x-text="cat"></span>
							<svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none"
								stroke="currentColor" viewBox="0 0 24 24">
								<path d="M9 5l7 7-7 7" stroke-width="2.5" />
							</svg>
						</div>
					</template>
				</div>
			</div>

			<!-- Ticket Form -->
			<div x-show="step === 'form'" x-cloak x-transition:enter="transition ease-out duration-400"
				x-transition:enter-start="opacity-0 translate-x-4" class="space-y-10">
				<div class="flex items-center gap-6">
					<button @click="goBack()"
						class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
						<svg class="w-4 h-4 text-gray-800 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path d="M15 19l-7-7 7-7" stroke-width="2.5" />
						</svg>
					</button>
					<div>
						<h2 class="text-3xl font-semibold text-[#222] dark:text-white leading-tight text-left mb-1">Submit Your Ticket</h2>
						<p class="text-sm text-gray-400 font-medium mt-1">Category: <span x-text="selectedCategory"
								class="text-gray-600 dark:text-gray-300 font-semibold"></span></p>
					</div>
				</div>

				<form @submit.prevent="submitForm"
					class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl p-8 space-y-6">
					<!-- Message display -->
					<template x-if="message">
						<div class="p-4 rounded-lg"
							:class="{ 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800': step === 'success', 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800': step !== 'success' }">
							<p x-text="message"></p>
						</div>
					</template>

					<!-- Subject -->
					<div>
						<label class="block text-sm font-semibold text-gray-800 dark:text-white mb-2">Subject</label>
						<input type="text" x-model="formData.subject" placeholder="Brief description of your issue..." required
							class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white placeholder-gray-400">
					</div>

					<!-- Description -->
					<div>
						<label class="block text-sm font-semibold text-gray-800 dark:text-white mb-2">Description</label>
						<textarea x-model="formData.description" placeholder="Please provide detailed information about your issue..." required
						 rows="6"
						 class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white placeholder-gray-400"></textarea>
					</div>

					<!-- Submit Button -->
					<div class="flex gap-4">
						<button type="submit" :disabled="loading"
							class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
							<template x-if="!loading">
								<span>Submit Ticket</span>
							</template>
							<template x-if="loading">
								<span>Submitting...</span>
								<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path
										d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
								</svg>
							</template>
						</button>
					</div>
				</form>
			</div>

			<!-- Success Message -->
			<div x-show="step === 'success'" x-cloak x-transition:enter="transition ease-out duration-400"
				x-transition:enter-start="opacity-0 scale-95" class="text-center py-16">
				<div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full mb-6">
					<svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
						viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
				</div>
				<h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Ticket Submitted!</h2>
				<p class="text-gray-600 dark:text-gray-400 mb-4">Thank you for reaching out. Our support team will review your
					ticket shortly.</p>
				<p class="text-sm text-gray-500 dark:text-gray-400">You will be redirected shortly...</p>
			</div>

		</div>
	</div>

	<style>
		[x-cloak] {
			display: none !important;
		}
	</style>
@endsection
