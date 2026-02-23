<div class=" max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">

    <div x-data="{ isCartOpen: false }" class="relative ">
        <!-- After login menu -->
        <div class="mx-auto py-6 flex flex-col sm:px-0 px-4 sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
            <div class="flex items-center gap-2">
                <img src="/images/logo/logo-dark.png" alt="Logo" class="h-11 dark:block hidden">
                <img src="/images/logo/logo.png" alt="Logo" class="h-11 dark:hidden block">
            </div>

            <div>
                <nav class="flex flex-wrap items-center justify-center gap-4 md:gap-6 lg:gap-8 text-sm lg:text-[14px] font-medium text-gray-600 dark:text-gray-300">
                    <a href="#" class="hover:text-black dark:hover:text-white py-2 transition-colors border-b-3 border-b-purple-300">Home</a>
                    <a href="/content-library/" class="hover:text-black dark:hover:text-white py-2 transition-colors">Library</a>
                    <a href="#" class="hover:text-black dark:hover:text-white py-2 transition-colors">Search</a>
                    <a href="{{ route('faq') }}" class="hover:text-black dark:hover:text-white py-2 transition-colors">Faq</a>
                    <a href="{{ route('support') }}" class="hover:text-black dark:hover:text-white py-2 transition-colors">Support</a>
                </nav>
            </div>

            <div class="flex items-center gap-5">
                <!-- Shopping Cart Icon -->
                <div @click="isCartOpen = true" class="relative cursor-pointer hover:opacity-70 transition-opacity">
                    <svg class="w-7 h-7 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 flex items-center gap-0.5">
                        <div class="w-1 h-1 bg-black rounded-full"></div>
                        <div class="w-1 h-1 bg-black rounded-full"></div>
                    </div>
                </div>

                <!-- Dropdown Menu Wrapper -->
                <div class="relative group">
                    <button class="flex items-center gap-3 border border-gray-100 rounded-full p-1 pl-4 bg-white hover:shadow-md transition-all duration-300 active:scale-95">
                        <!-- Hamburger Icon Wrapped in Span with Trigger -->
                        <span @click.stop="isCartOpen = true" class="cart-trigger-btn cursor-pointer p-1">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </span>
                        
                        <div class="w-10 h-10 rounded-full bg-[#FFE4C4] flex items-center justify-center text-base font-bold text-black uppercase tracking-tighter">
                            SM
                        </div>
                    </button>

                    <!-- Profile Dropdown Content -->
                    <div class="absolute right-0 top-full mt-3 w-56 bg-white rounded-[20px] shadow-[0_10px_40px_rgba(0,0,0,0.1)] border border-gray-50 invisible opacity-0 scale-95 group-hover:visible group-hover:opacity-100 group-hover:scale-100 transition-all duration-300 origin-top-right z-50 overflow-hidden">
                        <div class="py-2 flex flex-col">
                            <a href="#" class="px-7 py-3.5 text-[15px] font-bold text-gray-800 hover:bg-gray-50 transition-colors">Profile</a>
                            <a href="#" class="px-7 py-3.5 text-[15px] font-bold text-gray-800 hover:bg-gray-50 transition-colors">Offers</a>
                            <div class="border-t border-gray-100 my-1 mx-2"></div>
                            <a href="#" class="px-7 py-3.5 text-[15px] font-medium text-gray-600 hover:bg-gray-50 transition-colors">Account</a>
                            <a href="#" class="px-7 py-3.5 text-[15px] font-medium text-gray-600 hover:bg-gray-50 transition-colors">Log Out</a>
                        </div>
                    </div>
                </div>
            </div>  
        </div>

        <!-- ========================= CART SIDEBAR MODAL ========================= -->
        <div x-show="isCartOpen" 
            x-cloak 
            class="fixed inset-0 z-[100] overflow-hidden" 
            role="dialog" aria-modal="true">
            
            <!-- Backdrop Blur/Darken -->
            <div x-show="isCartOpen" 
                x-transition.opacity 
                @click="isCartOpen = false" 
                class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>

            <div class="fixed inset-y-0 right-0 flex max-w-full">
                <!-- Sidebar Panel Container -->
                <div x-show="isCartOpen" 
                    x-transition:enter="transform transition ease-in-out duration-500" 
                    x-transition:enter-start="translate-x-full" 
                    x-transition:enter-end="translate-x-0" 
                    x-transition:leave="transform transition ease-in-out duration-500" 
                    x-transition:leave-start="translate-x-0" 
                    x-transition:leave-end="translate-x-full" 
                    class="w-screen max-w-5xl flex shadow-2xl">
                    
                    <!-- LEFT PANEL (Estimated Results - Dark) -->
                    <div class="hidden md:flex flex-col w-[38%] bg-black p-12 text-white justify-between">
                        <div>
                            <h2 class="text-3xl font-bold mb-6 tracking-tight">Estimated Results</h2>
                            <p class="text-sm text-gray-400 leading-relaxed mb-16">
                                Not all influencers will accept your order. Here is a projection of your actual outcome based on acceptance rates.
                            </p>

                            <div class="space-y-12">
                                <!-- Progress Bar: Influencers -->
                                <div>
                                    <div class="flex justify-between items-end mb-3">
                                        <span class="text-xl font-bold">0 Influencers</span>
                                    </div>
                                    <div class="h-1.5 w-full bg-gray-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-gray-600 w-0 transition-all duration-1000"></div>
                                    </div>
                                    <div class="text-right mt-3 text-xs font-bold text-gray-500 uppercase tracking-widest">0 Influencers</div>
                                </div>

                                <!-- Progress Bar: Spend -->
                                <div>
                                    <div class="flex justify-between items-end mb-3">
                                        <span class="text-xl font-bold">$0 Spend</span>
                                    </div>
                                    <div class="h-1.5 w-full bg-gray-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-gray-600 w-0 transition-all duration-1000"></div>
                                    </div>
                                    <div class="text-right mt-3 text-xs font-bold text-gray-500 uppercase tracking-widest">$0</div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Protection Info -->
                        <div class="flex items-start gap-4">
                            <div class="pt-1 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </div>
                            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-tight leading-4">
                                Payment Protection <br>
                                <span class="text-gray-600 font-medium normal-case tracking-normal">If an order is declined, funds will be refunded.</span>
                            </p>
                        </div>
                    </div>

                    <!-- RIGHT PANEL (Cart Details - White) -->
                    <div class="flex-1 flex flex-col bg-white p-12 justify-between">
                        <div class="flex items-center justify-between">
                            <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Cart</h2>
                            <button @click="isCartOpen = false" class="p-2 text-gray-300 hover:text-black transition-all hover:rotate-90">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round"/></svg>
                            </button>
                        </div>

                        <!-- Empty State Content -->
                        <div class="flex flex-col items-center justify-center text-center py-20">
                            <div class="relative mb-8">
                                <svg class="w-28 h-28 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Your cart is empty</h3>
                            <p class="text-gray-400 font-medium max-w-[280px] leading-relaxed">
                                Start adding influencers by clicking the button below
                            </p>
                        </div>

                        <!-- Footer Action Button -->
                        <button @click="isCartOpen = false" class="w-full bg-[#1A1A1A] text-white py-5 rounded-2xl font-bold text-sm tracking-widest hover:bg-black transition-all shadow-xl active:scale-95">
                            Discover Influencers
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
