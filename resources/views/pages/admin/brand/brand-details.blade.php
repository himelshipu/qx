@extends('layouts.admin.app')

@section('content')
    <!-- Main Wrapper with Modal State -->
    <div class="flex flex-col gap-6 p-4 md:p-6" x-data="{ 
        activeModal: null,
        objective: 'email'
    }">
        
        <!-- Top Header Area -->
        <div class="flex flex-row justify-between items-center">
            <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Brand Detail - hopeyy</h1>
            <a href="#" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-pink-400 border border-pink-500 rounded-md hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                Login as Brand
            </a>
        </div>

        <!-- Info & Stats Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <!-- Left Card -->
            <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/[0.05] rounded-lg p-6 flex flex-col h-full">
                <div class="flex items-start gap-4 mb-8">
                    <div class="w-20 h-20 bg-[#28303F] rounded-full flex items-center justify-center text-white text-3xl font-bold">HL</div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Hope lusk</h2>
                        <p class="text-sm text-gray-500">EduPeak</p>
                        <a href="#" class="text-sm text-pink-300 hover:underline">https://webdev.com</a>
                    </div>
                </div>
                <div class="space-y-4 border-t pt-6 border-gray-50 dark:border-white/[0.05]">
                    <div class="flex justify-between text-sm"><span class="text-gray-500 font-medium">Mobile Number</span><span class="text-gray-700 dark:text-gray-300">+901234567890</span></div>
                    <div class="flex justify-between text-sm"><span class="text-gray-500 font-medium">Email</span><span class="text-gray-700 dark:text-gray-300">hope.lusk@example.com</span></div>
                    <div class="flex justify-between text-sm"><span class="text-gray-500 font-medium">Joined</span><span class="text-gray-700 dark:text-gray-300">2026-02-16 10:30 AM</span></div>
                </div>
            </div>

            <!-- Stats Column -->
            <div class="xl:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                @php $stats = [ ['l' => 'Deposits', 'v' => '$0.00 USD', 'c' => 'text-green-500', 'b' => 'bg-green-50'], ['l' => 'Transactions', 'v' => '1', 'c' => 'text-orange-500', 'b' => 'bg-orange-50'], ['l' => 'Total Campaign', 'v' => '1', 'c' => 'text-blue-500', 'b' => 'bg-blue-50'], ['l' => 'Running Campaign', 'v' => '0', 'c' => 'text-teal-500', 'b' => 'bg-teal-50'], ['l' => 'Approved Campaign', 'v' => '0', 'c' => 'text-green-600', 'b' => 'bg-green-100'], ['l' => 'Rejected Campaign', 'v' => '0', 'c' => 'text-red-500', 'b' => 'bg-red-50'] ]; @endphp
                @foreach($stats as $s)
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/[0.05] rounded-lg p-5 relative overflow-hidden">
                    <div class="absolute top-2 right-3"><span class="text-[10px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded border border-green-100">View All</span></div>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded flex items-center justify-center {{ $s['b'] }} {{ $s['c'] }}"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2"/></svg></div>
                        <div><h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ $s['v'] }}</h3><p class="text-sm text-gray-500 font-medium">{{ $s['l'] }}</p></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Action Buttons Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
            <button @click="activeModal = 'add'" class="flex items-center justify-center gap-2 px-4 py-3 bg-[#2ecc71] text-white rounded font-medium text-sm transition hover:opacity-90"><span>⊕ Balance</span></button>
            <button @click="activeModal = 'subtract'" class="flex items-center justify-center gap-2 px-4 py-3 bg-[#e74c3c] text-white rounded font-medium text-sm transition hover:opacity-90"><span>⊖ Balance</span></button>
            <button class="flex items-center justify-center gap-2 px-4 py-3 bg-[#4a4ae2] text-white rounded font-medium text-sm transition hover:opacity-90"><span>☷ Logins</span></button>
            <button @click="activeModal = 'notify'" class="flex items-center justify-center gap-2 px-4 py-3 bg-[#7f8c8d] text-white rounded font-medium text-sm transition hover:opacity-90"><span>🔔 Notifications</span></button>
            <button @click="activeModal = 'ban'" class="flex items-center justify-center gap-2 px-4 py-3 bg-[#ff9f43] text-white rounded font-medium text-sm transition hover:opacity-90"><span>🚫 Ban Brand</span></button>
        </div>

        <!-- Main Form Section -->
        <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-white/[0.05] rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-50 dark:border-white/[0.05]"><h3 class="text-lg font-bold text-gray-800 dark:text-white">Information of Hope lusk</h3></div>
            <form class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Full name -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Full Name<span class="text-error-500"> *</span>
                    </label>
                    <input type="text" id="full-name" name="full-name" placeholder="John Doe"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                </div>
                <!-- Brand name -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Brand Name<span class="text-error-500"> *</span>
                    </label>
                    <input type="text" id="brand-name" name="brand-name" placeholder="Brand Name"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                </div>

                <!-- Email -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Email<span class="text-error-500"> *</span>
                    </label>
                    <input type="text" value="" placeholder="hope.lusk@example.com"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                </div>
                <!-- Password -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Update Password<span class="text-error-500"> *</span>
                    </label>
                    <div x-data="{ showPassword: false }" class="relative">
                        <input :type="showPassword ? 'text' : 'password'"
                            placeholder="Enter your password"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-brand-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-11 pl-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        <span @click="showPassword = !showPassword"
                            class="absolute top-1/2 right-4 z-30 -translate-y-1/2 cursor-pointer text-gray-500 dark:text-gray-400">
                            <svg x-show="!showPassword" class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM10.0002 4.04297C6.48191 4.04297 3.49489 6.30917 2.4155 9.4593C2.3615 9.61687 2.3615 9.78794 2.41549 9.94552C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C13.5184 15.3619 16.5055 13.0957 17.5849 9.94555C17.6389 9.78797 17.6389 9.6169 17.5849 9.45932C16.5055 6.30919 13.5184 4.04297 10.0002 4.04297ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z" fill="#98A2B3" />
                            </svg>
                            <svg x-show="showPassword" class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M4.63803 3.57709C4.34513 3.2842 3.87026 3.2842 3.57737 3.57709C3.28447 3.86999 3.28447 4.34486 3.57737 4.63775L4.85323 5.91362C3.74609 6.84199 2.89363 8.06395 2.4155 9.45936C2.3615 9.61694 2.3615 9.78801 2.41549 9.94558C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C11.255 15.3619 12.4422 15.0737 13.4994 14.5598L15.3625 16.4229C15.6554 16.7158 16.1302 16.7158 16.4231 16.4229C16.716 16.13 16.716 15.6551 16.4231 15.3622L4.63803 3.57709ZM12.3608 13.4212L10.4475 11.5079C10.3061 11.5423 10.1584 11.5606 10.0064 11.5606H9.99151C8.96527 11.5606 8.13333 10.7286 8.13333 9.70237C8.13333 9.5461 8.15262 9.39434 8.18895 9.24933L5.91885 6.97923C5.03505 7.69015 4.34057 8.62704 3.92328 9.70247C4.86803 12.1373 7.23361 13.8619 10.0002 13.8619C10.8326 13.8619 11.6287 13.7058 12.3608 13.4212ZM16.0771 9.70249C15.7843 10.4569 15.3552 11.1432 14.8199 11.7311L15.8813 12.7925C16.6329 11.9813 17.2187 11.0143 17.5849 9.94561C17.6389 9.78803 17.6389 9.61696 17.5849 9.45938C16.5055 6.30925 13.5184 4.04303 10.0002 4.04303C9.13525 4.04303 8.30244 4.17999 7.52218 4.43338L8.75139 5.66259C9.1556 5.58413 9.57311 5.54303 10.0002 5.54303C12.7667 5.54303 15.1323 7.26768 16.0771 9.70249Z"
                                    fill="#98A2B3" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
                    <div class="flex flex-col gap-2"><label class="text-sm font-bold text-gray-700 dark:text-gray-300">Email Verification</label><div class="w-full bg-[#2ecc71] text-white py-2 text-center rounded font-bold text-sm">Verified</div></div>
                    <div class="flex flex-col gap-2"><label class="text-sm font-bold text-gray-700 dark:text-gray-300">Mobile Verification</label><div class="w-full bg-[#2ecc71] text-white py-2 text-center rounded font-bold text-sm">Verified</div></div>
                    <div class="flex flex-col gap-2"><label class="text-sm font-bold text-gray-700 dark:text-gray-300">KYC</label><div class="w-full bg-gray-300 text-white py-2 text-center rounded font-bold text-sm">Verified</div></div>
                </div>
                <button type="submit" class="bg-[#222] shadow-theme-xs hover:bg-[#ff84a3] flex w-full items-center justify-center rounded-lg px-4 py-3 text-lg font-medium text-white transition">Submit</button>
            </form>
        </div>

        <!-- ========================= MODAL OVERLAYS ========================= -->
        <div x-show="activeModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center px-4" x-transition.opacity>
            
            <div @click="activeModal = null" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

           
            <div class="relative w-full max-w-xl bg-white dark:bg-gray-900 rounded-xl shadow-2xl overflow-hidden" 
                 x-show="activeModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                
                
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white" 
                        x-text="activeModal === 'add' ? 'Add Balance' : (activeModal === 'subtract' ? 'Subtract Balance' : (activeModal === 'ban' ? 'Ban User' : 'Send Notification to hopeyy'))"></h3>
                    <button @click="activeModal = null" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                </div>

                
                <div class="p-6">
                    <!-- ADD / SUBTRACT BALANCE FORM -->
                    <div x-show="activeModal === 'add' || activeModal === 'subtract'">
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Amount <span class="text-red-500">*</span></label>
                            <div class="flex">
                                <input type="number" placeholder="Please provide positive amount" class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-l-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                                <span class="h-12 flex items-center px-4 bg-gray-100 dark:bg-gray-700 border border-l-0 border-gray-200 dark:border-gray-700 rounded-r-lg font-bold text-gray-500">USD</span>
                            </div>
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Remark <span class="text-red-500">*</span></label>
                            <textarea rows="4" placeholder="Remark" class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
                        </div>
                    </div>

                    <!-- BAN USER -->
                    <div x-show="activeModal === 'ban'">
                        <p class="text-gray-600 dark:text-gray-400 mb-4 font-medium">If you ban this brand he/she won't able to access his/her dashboard.</p>
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Reason <span class="text-red-500">*</span></label>
                            <textarea rows="6" class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
                        </div>
                    </div>

                    <!-- SEND NOTIFICATION -->
                    <div x-show="activeModal === 'notify'">
                        <div class="mb-6">
                            <button class="relative flex items-center justify-center gap-3 w-40 h-20 border-2 border-indigo-500 rounded-lg bg-indigo-50/10 transition">
                                <div class="absolute top-0 right-0 w-6 h-6 bg-indigo-500 flex items-center justify-center rounded-bl-lg">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round"/></svg>
                                </div>
                                <div class="text-center">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke-width="2"/></svg>
                                    <span class="text-sm font-bold text-gray-700">Send Via Email</span>
                                </div>
                            </button>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                            <input type="text" placeholder="Subject / Title" class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Message</label>
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                <div class="bg-gray-50 border-b border-gray-200 p-2 flex flex-wrap gap-2">
                                    <span class="px-2 font-serif text-gray-400">B I U</span>
                                    <span class="px-2 border-l text-gray-400">Font Size...</span>
                                </div>
                                <textarea rows="8" class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button Footer -->
                    <button class="bg-[#222] shadow-theme-xs hover:bg-[#ff84a3] flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                        Submit
                    </button>
                </div>
            </div>
        </div>

    </div>
@endsection