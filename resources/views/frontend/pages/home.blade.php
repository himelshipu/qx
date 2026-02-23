@extends('frontend.layouts.app')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-900 transition-colors duration-200">
   <main class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col gap-20">
    
        @include('frontend.partials.hero')
        @include('frontend.partials.featured')
        @include('frontend.partials.cases')
        @include('frontend.partials.search')
        @include('frontend.partials.campaign')
        @include('frontend.partials.faq')
        @include('frontend.partials.cta')
        
   

    </main>
</div>

<!-- Modal Overlay -->
<div id="wishlist-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <!-- Backdrop shadow -->
    <div id="modal-backdrop" class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

    <!-- Modal Content Card -->
    <div class="relative w-auto lg:w-[500px] max-w-md bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-2xl overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="modal-container">
        
        <!-- Header -->
        <div class="relative p-6 text-center border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add to List</h3>
            <button id="close-modal" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4">
            <!-- Create New List Option -->
            <button class="w-full flex items-center gap-4 p-4 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors group">
                <div class="w-14 h-14 bg-black dark:bg-white flex items-center justify-center rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white dark:text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <span class="text-lg font-bold text-gray-900 dark:text-white">Create new list</span>
            </button>

            <!-- Existing Lists (Mockup) -->
            <div id="existing-lists" class="space-y-2">
                <button class="list-item w-full flex items-center gap-4 p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="w-14 h-14 bg-gray-200 dark:bg-gray-700 rounded-xl flex items-center justify-center overflow-hidden">
                        <!-- Creator Profile Picture from the clicked card will go here -->
                        <img src="" class="list-preview-img hidden w-full h-full object-cover">
                        <svg class="placeholder-icon w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="text-left">
                        <p class="font-bold text-gray-900 dark:text-white text-lg">Brain Capita</p>
                        <p class="text-sm text-gray-500">0 influencers</p>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

<script>

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('wishlist-modal');
    const modalContainer = document.getElementById('modal-container');
    const closeBtn = document.getElementById('close-modal');
    const backdrop = document.getElementById('modal-backdrop');
    const wishlistBtns = document.querySelectorAll('.wishlist-btn');
    const listItems = document.querySelectorAll('.list-item');

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modalContainer.classList.remove('scale-95', 'opacity-0');
            modalContainer.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal() {
        modalContainer.classList.remove('scale-100', 'opacity-100');
        modalContainer.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    wishlistBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            openModal();
        });
    });

    closeBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);

    // Close on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
});
</script>
@endsection