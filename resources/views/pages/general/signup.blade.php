@extends('layouts.general.app')

@section('content')
<div class="relative z-1 bg-white p-6 sm:p-0 dark:bg-gray-900" 
     x-data="{ step: 'signup', role: 'creator', email: 'placeholder@gmail.com', objective: '' }">
    
    
     <div class="px-12 w-full max-w-md pt-8">
        <a href="/"
            class="inline-flex items-center text-sm text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <svg class="stroke-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path d="M12.7083 5L7.5 10.2083L12.7083 15.4167" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Back to dashboard
        </a>
    </div>
    
    <div class="relative flex min-h-screen w-full flex-col items-center justify-start pt-12 dark:bg-gray-900">

        <!-- PROGRESS BAR -->
        <div class="w-full max-w-lg mb-12" x-show="step !== 'signup'" x-cloak>
            <div class="h-2 w-full bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                <div class="h-full bg-pink-300 transition-all duration-500" 
                     :style="step === 'verify' ? 'width: 50%' : 'width: 100%'"></div>
            </div>
        </div>

        <div class="w-full max-w-xl px-6">
            
            <!-- STEP 1: SIGNUP HEADINGS -->
            <div x-show="step === 'signup'">
                <div class="mb-8 text-center sm:text-left">
                    <h1 class="text-3xl text-center font-bold text-gray-900 dark:text-white mb-2">Create Your Account</h1>
                    <p class="text-sm text-gray-500 text-center dark:text-gray-400">Choose your account type below</p>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-5 mb-8">
                    <button type="button" @click="role = 'brand'" :class="role === 'brand' ? 'bg-gray-200 dark:bg-white/10' : 'bg-gray-100 dark:bg-white/5'" class="inline-flex items-center justify-center gap-3 rounded-lg px-6 py-3 text-sm font-medium text-gray-700 transition-all dark:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <ellipse cx="10" cy="17.5" rx="7" ry="3.5" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                    <circle cx="10" cy="7" r="4" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.0448 10.2496C14.7228 10.7485 14.3288 11.1965 13.8774 11.5791C14.2319 11.6901 14.609 11.75 15.0001 11.75C17.0712 11.75 18.7501 10.0711 18.7501 7.99999C18.7501 6.04422 17.2529 4.43814 15.3421 4.26538C15.6083 4.78435 15.8011 5.34717 15.9068 5.94015C16.6979 6.28887 17.2501 7.07994 17.2501 7.99999C17.2501 9.2277 16.2668 10.2257 15.0448 10.2496Z" fill="currentColor"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M18.9997 17.5563C18.9896 18.1705 18.8148 18.7606 18.5009 19.3108C18.8693 19.2095 19.2144 19.092 19.5312 18.96C20.1284 18.7112 20.6606 18.3959 21.055 18.0074C21.452 17.6162 21.7501 17.1064 21.7501 16.5C21.7501 15.8935 21.452 15.3837 21.055 14.9925C20.6606 14.604 20.1284 14.2887 19.5312 14.0399C18.5086 13.6138 17.1907 13.3394 15.7495 13.2683C16.7517 13.7774 17.5702 14.4169 18.1351 15.1443C18.4329 15.2274 18.7072 15.3215 18.9543 15.4245C19.443 15.6281 19.7894 15.8514 20.0023 16.0611C20.2125 16.2682 20.2501 16.416 20.2501 16.5C20.2501 16.5839 20.2125 16.7317 20.0023 16.9388C19.7961 17.1419 19.4645 17.3579 18.9997 17.5563Z" fill="currentColor"/>
                    </svg>
                        Join as Brand
                    </button>

                    <button type="button" @click="role = 'creator'" :class="role === 'creator' ? 'bg-gray-200 dark:bg-white/10' : 'bg-gray-100 dark:bg-white/5'" class="inline-flex items-center justify-center gap-3 rounded-lg px-6 py-3 text-sm font-medium text-gray-700 transition-all dark:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M15.25 7C15.25 8.79493 13.7949 10.25 12 10.25V11.75C14.6234 11.75 16.75 9.62335 16.75 7H15.25ZM12 10.25C10.2051 10.25 8.75 8.79493 8.75 7H7.25C7.25 9.62335 9.37665 11.75 12 11.75V10.25ZM8.75 7C8.75 5.20507 10.2051 3.75 12 3.75V2.25C9.37665 2.25 7.25 4.37665 7.25 7H8.75ZM12 3.75C13.7949 3.75 15.25 5.20507 15.25 7H16.75C16.75 4.37665 14.6234 2.25 12 2.25V3.75ZM18.25 17.5C18.25 18.0294 17.8014 18.7105 16.6143 19.3041C15.4722 19.8751 13.8418 20.25 12 20.25V21.75C14.0242 21.75 15.8938 21.3414 17.2852 20.6457C18.6316 19.9725 19.75 18.9036 19.75 17.5H18.25ZM12 20.25C10.1582 20.25 8.52782 19.8751 7.38566 19.3041C6.19864 18.7105 5.75 18.0294 5.75 17.5H4.25C4.25 18.9036 5.36836 19.9725 6.71484 20.6457C8.10618 21.3414 9.97582 21.75 12 21.75V20.25ZM5.75 17.5C5.75 16.9706 6.19864 16.2895 7.38566 15.6959C8.52782 15.1249 10.1582 14.75 12 14.75V13.25C9.97582 13.25 8.10618 13.6586 6.71484 14.3543C5.36836 15.0275 4.25 16.0964 4.25 17.5H5.75ZM12 14.75C13.8418 14.75 15.4722 15.1249 16.6143 15.6959C17.8014 16.2895 18.25 16.9706 18.25 17.5H19.75C19.75 16.0964 18.6316 15.0275 17.2852 14.3543C15.8938 13.6586 14.0242 13.25 12 13.25V14.75Z" fill="currentColor"/>
                        </svg>
                        Join as Creator
                    </button>
                </div>

                <div x-show="role === 'brand'" x-cloak><x-general.auth.brand-signup-form /></div>
                <div x-show="role === 'creator'" x-cloak><x-general.auth.creator-signup-form /></div>

                <p class="mt-6 text-sm text-center sm:text-left text-gray-700 dark:text-gray-400">Already have an account? <a href="/signin" class="text-pink-400 font-bold">Sign In</a></p>
            </div>

            <!-- STEP 2: VERIFICATION -->
            <div x-show="step === 'verify'" x-cloak>
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">Verify your email</h1>
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">We sent an email to <span class="font-medium text-gray-800 dark:text-white" x-text="email"></span></p>
                        <a href="#" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-white/5 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M17.3726 8.17276C20.0986 9.39695 22 12.1611 22 15.375V18.75C22 19.9926 21.0051 21 19.7778 21H13.1111C10.2084 21 7.73898 19.1217 6.82379 16.5M17.3726 8.17276C16.6711 5.20566 14.0344 3 10.8889 3H9.77778C5.48223 3 2 6.52576 2 10.875V14.25C2 15.4926 2.99492 16.5 4.22222 16.5H6.82379M17.3726 8.17276C17.4922 8.67875 17.5556 9.20688 17.5556 9.75C17.5556 13.4779 14.5708 16.5 10.8889 16.5H6.82379M10 10C11.1046 10 12 9.10457 12 8C12 6.89543 11.1046 6 10 6C8.89543 6 8 6.89543 8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M11 13C11 13.5523 10.5523 14 10 14C9.44772 14 9 13.5523 9 13C9 12.4477 9.44772 12 10 12C10.5523 12 11 12.4477 11 13Z" fill="currentColor"/>
                        </svg>
                        Open Gmail
                        </a>
                    </div>
                </div>

                <div class="space-y-6">
                    <input type="text" placeholder="6-Digit Code" class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-14 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-lg text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                    <div class="bg-blue-50/40 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-900/20 rounded-xl p-5">
                        <p class="text-[13px] text-gray-500 leading-relaxed dark:text-gray-400">Tip: Check your <span class="font-bold">spam</span> folder for an email from info@gmail.com.</p>
                    </div>
                    <button @click="step = 'objective'" class="w-full bg-[#1A1A1A] hover:bg-[#ff84a3] text-white font-bold py-4 rounded-xl shadow-lg">Continue</button>
                </div>
            </div>

            <!-- STEP 3: OBJECTIVE (Survey) -->
            <div x-show="step === 'objective'" x-cloak class="text-center">
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
    </div>
</div>
@endsection