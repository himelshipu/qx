@extends('layouts.admin.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Categories" />

    <x-common.component-card title="Categories">

        {{-- Header Button --}}
        <x-slot name="action">
            <button type="button"
                @click="$dispatch('open-profile-info-modal')"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium border border-gray-300 rounded-lg hover:bg-gray-100 transition dark:hover:bg-transparent add-new-btn">
                
                <svg xmlns="http://www.w3.org/2000/svg"
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none">
                    <path d="M12 6V18M18 12L6 12"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"/>
                </svg>

                <span>Add New</span>
            </button>
        </x-slot>

        <x-tables.basic-tables.categories-table />

    </x-common.component-card>


    <!-- Add Category Modal -->
    <x-ui.modal 
        x-data="{ open: false }"
        @open-profile-info-modal.window="open = true"
        x-show="open"
        x-cloak
        class="max-w-[700px]"
    >
        <div
            class="no-scrollbar relative w-full max-w-[700px] overflow-y-auto rounded-3xl bg-white p-4 dark:bg-gray-900 lg:p-11">

            <div class="px-2 pr-14">
                <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
                    Add New Category
                </h4>
            </div>

            <form class="flex flex-col">

                <div class="custom-scrollbar h-[458px] overflow-y-auto p-2">

                    <div class="mb-4">
                        <label for="categoryName"
                            class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">
                            Category Name
                        </label>

                        <input type="text"
                            id="categoryName"
                            name="categoryName"
                            placeholder="Enter category name"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                    </div>

                    <x-form.form-elements.dropzone />

                </div>

                <div class="flex items-center gap-3 px-2 mt-6 lg:justify-end">

                    <button 
                        @click="open = false"
                        type="button"
                        class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
                        Close
                    </button>

                    <button 
                        type="button"
                        class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                        Save Changes
                    </button>

                </div>

            </form>
        </div>
    </x-ui.modal>

@endsection
