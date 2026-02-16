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
            <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                Didn't receive the code? 
                <form method="POST" action="{{ route('verification.send') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-pink-600 dark:text-pink-400 hover:text-pink-700 dark:hover:text-pink-300 font-medium hover:underline transition-colors">
                        Resend
                    </button>
                </form>
            </div>
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

    <div class="mt-4 flex items-center justify-center gap-4">

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>


   








    <div 
    x-data="{ 
        stepIndex: 1,
        steps: ['objective','budget','business-type','people','influencer-type'],
        objective: '',
        budget: '',
        businessType: '',
        companySize: '',
        influencerTypes: [],
        get step() { return this.steps[this.stepIndex - 1] },
        next() { if(this.stepIndex < this.steps.length) this.stepIndex++ },
        prev() { if(this.stepIndex > 1) this.stepIndex-- },
        progress() { return (this.stepIndex / this.steps.length) * 100 }
    }"
    class="max-w-2xl mx-auto my-10"
>

    <!-- Top Navigation -->
    <div class="flex items-center justify-between mb-6">
        <button 
            @click="prev()" 
            x-show="stepIndex > 1"
            class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:underline">
            ← Back
        </button>

        <div class="text-sm text-gray-500 dark:text-gray-400">
            Step <span x-text="stepIndex"></span> of <span x-text="steps.length"></span>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="w-full bg-gray-200 dark:bg-gray-800 rounded-full h-2 mb-10">
        <div 
            class="bg-pink-400 h-2 rounded-full transition-all duration-300"
            :style="'width: ' + progress() + '%'"
        ></div>
    </div>

    <!-- STEP 1: OBJECTIVE -->
    <div x-show="step === 'objective'" x-cloak class="text-center">
        <h1 class="text-3xl font-bold mb-10">What are you here to do?</h1>

        <div class="space-y-4 mb-10">
            <label class="flex items-center p-5 border rounded-2xl cursor-pointer"
                :class="objective === 'one-time' ? 'border-pink-400 ring-1 ring-pink-400' : 'border-gray-200'">
                <input type="radio" x-model="objective" value="one-time" class="sr-only">
                <span class="text-lg font-medium">Find influencers for a one-time campaign</span>
            </label>

            <label class="flex items-center p-5 border rounded-2xl cursor-pointer"
                :class="objective === 'ongoing' ? 'border-pink-400 ring-1 ring-pink-400' : 'border-gray-200'">
                <input type="radio" x-model="objective" value="ongoing" class="sr-only">
                <span class="text-lg font-medium">Get ongoing influencer content</span>
            </label>

            <label class="flex items-center p-5 border rounded-2xl cursor-pointer"
                :class="objective === 'exploring' ? 'border-pink-400 ring-1 ring-pink-400' : 'border-gray-200'">
                <input type="radio" x-model="objective" value="exploring" class="sr-only">
                <span class="text-lg font-medium">I'm not sure yet, just exploring</span>
            </label>
        </div>

        <div class="space-y-3">
            <button @click="next()" 
                class="w-full py-4 rounded-xl font-bold"
                :class="objective ? 'bg-black text-white' : 'bg-gray-400 text-white cursor-not-allowed'"
                :disabled="!objective">
                Continue
            </button>
            <button @click="next()" class="text-sm text-gray-500 hover:underline">Skip</button>
        </div>
    </div>

    <!-- STEP 2: BUDGET -->
    <div x-show="step === 'budget'" x-cloak class="text-center">
        <h1 class="text-3xl font-bold mb-10">What's your approximate budget?</h1>

        <div class="space-y-4 mb-10">
            <template x-for="item in ['under-1000','1000-5000','5000-10000','10000-25000','25000-50000','50000+']">
                <label class="flex items-center p-5 border rounded-2xl cursor-pointer"
                    :class="budget === item ? 'border-pink-400 ring-1 ring-pink-400' : 'border-gray-200'">
                    <input type="radio" x-model="budget" :value="item" class="sr-only">
                    <span class="text-lg font-medium" x-text="item"></span>
                </label>
            </template>
        </div>

        <div class="space-y-3">
            <button @click="next()" 
                class="w-full py-4 rounded-xl font-bold"
                :class="budget ? 'bg-black text-white' : 'bg-gray-400 text-white cursor-not-allowed'"
                :disabled="!budget">
                Continue
            </button>
            <button @click="next()" class="text-sm text-gray-500 hover:underline">Skip</button>
        </div>
    </div>

    <!-- STEP 3: BUSINESS TYPE -->
    <div x-show="step === 'business-type'" x-cloak class="text-center">
        <h1 class="text-3xl font-bold mb-10">What type of business are you?</h1>

        <div class="space-y-4 mb-10">
            <template x-for="item in ['agency','ecommerce','website','local','other']">
                <label class="flex items-center p-5 border rounded-2xl cursor-pointer"
                    :class="businessType === item ? 'border-pink-400 ring-1 ring-pink-400' : 'border-gray-200'">
                    <input type="radio" x-model="businessType" :value="item" class="sr-only">
                    <span class="text-lg font-medium capitalize" x-text="item"></span>
                </label>
            </template>
        </div>

        <div class="space-y-3">
            <button @click="next()" 
                class="w-full py-4 rounded-xl font-bold"
                :class="businessType ? 'bg-black text-white' : 'bg-gray-400 text-white cursor-not-allowed'"
                :disabled="!businessType">
                Continue
            </button>
            <button @click="next()" class="text-sm text-gray-500 hover:underline">Skip</button>
        </div>
    </div>

    <!-- STEP 4: PEOPLE -->
    <div x-show="step === 'people'" x-cloak class="text-center">
        <h1 class="text-3xl font-bold mb-10">How many people work at your company?</h1>

        <div class="space-y-4 mb-10">
            <template x-for="item in ['just-me','2-10','11-50','51-200','201-500','500+']">
                <label class="flex items-center p-5 border rounded-2xl cursor-pointer"
                    :class="companySize === item ? 'border-pink-400 ring-1 ring-pink-400' : 'border-gray-200'">
                    <input type="radio" x-model="companySize" :value="item" class="sr-only">
                    <span class="text-lg font-medium" x-text="item"></span>
                </label>
            </template>
        </div>

        <div class="space-y-3">
            <button @click="next()" 
                class="w-full py-4 rounded-xl font-bold"
                :class="companySize ? 'bg-black text-white' : 'bg-gray-400 text-white cursor-not-allowed'"
                :disabled="!companySize">
                Continue
            </button>
            <button @click="next()" class="text-sm text-gray-500 hover:underline">Skip</button>
        </div>
    </div>

    <!-- STEP 5: INFLUENCER TYPE -->
    <div x-show="step === 'influencer-type'" x-cloak class="text-center">
        <h1 class="text-3xl font-bold mb-10">What type of influencers are you looking for?</h1>

        <div class="grid grid-cols-2 gap-4 mb-10">
            <template x-for="item in ['beauty','fashion','travel','health','food','tech','gaming','lifestyle']">
                <label class="flex items-center justify-center p-5 border rounded-2xl cursor-pointer"
                    :class="influencerTypes.includes(item) ? 'border-pink-400 ring-1 ring-pink-400' : 'border-gray-200'">
                    <input type="checkbox" x-model="influencerTypes" :value="item" class="sr-only">
                    <span class="text-lg font-medium capitalize" x-text="item"></span>
                </label>
            </template>
        </div>

        <button 
            @click="console.log({objective,budget,businessType,companySize,influencerTypes})"
            class="w-full py-4 rounded-xl font-bold bg-black text-white hover:bg-gray-900">
            Save
        </button>
    </div>

</div>













</div>
@endsection
