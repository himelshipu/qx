<!--After login menu -->
<div class="mx-auto py-6 flex flex-col sm:px-0 px-4 sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
    <div class="flex items-center gap-2">
        <img src="/images/logo/logo-dark.png" alt="Logo" class="h-11 dark:block hidden">
        <img src="/images/logo/logo.png" alt="Logo" class="h-11 dark:hidden block">
    </div>

    <div>
        <nav class="flex flex-wrap items-center justify-center gap-4 md:gap-6 lg:gap-8 text-sm lg:text-[14px] font-medium text-gray-600 dark:text-gray-300">
            <a href="#" class="hover:text-black dark:hover:text-white py-2 transition-colors border-b-3 border-b-purple-300 ">Home</a>
            <a href="/content-library/" class="hover:text-black dark:hover:text-white py-2 transition-colors">Library</a>
            <a href="#" class="hover:text-black dark:hover:text-white py-2 transition-colors">Search</a>
            <a href="#" class="hover:text-black dark:hover:text-white py-2 transition-colors">Track</a>
            <a href="#" class="hover:text-black dark:hover:text-white py-2 transition-colors">Help Center</a>
        </nav>
    </div>

    <div class="flex items-center gap-5">
        
        <div class="relative cursor-pointer hover:opacity-70 transition-opacity">
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
                
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                
                <div class="w-10 h-10 rounded-full bg-[#FFE4C4] flex items-center justify-center text-base font-bold text-black uppercase tracking-tighter">
                    SM
                </div>
            </button>

            <!-- Dropdown Content -->
            
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