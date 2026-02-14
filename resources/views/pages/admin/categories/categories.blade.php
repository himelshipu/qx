@extends('layouts.admin.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Categories" />

    <x-common.component-card title="Create Categories">

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

                <div class="custom-scrollbar h-fit-content overflow-y-auto p-2">

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

                    <div class="mb-4">
                        <label for="categoryName"
                            class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-400">
                            Category Slug
                        </label>

                        <input type="text"
                            id="categorySlug"
                            name="categorySlug"
                            placeholder="Enter category slug"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Upload Icon
                        </label>
                        <input type="file"
                            class="focus:border-ring-brand-300 shadow-theme-xs focus:file:ring-brand-300 h-11 w-full overflow-hidden rounded-lg border border-gray-300 bg-transparent text-sm text-gray-500 transition-colors file:mr-5 file:border-collapse file:cursor-pointer file:rounded-l-lg file:border-0 file:border-r file:border-solid file:border-gray-200 file:bg-gray-50 file:py-3 file:pr-3 file:pl-3.5 file:text-sm file:text-gray-700 placeholder:text-gray-400 hover:file:bg-gray-100 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:text-white/90 dark:file:border-gray-800 dark:file:bg-white/[0.03] dark:file:text-gray-400 dark:placeholder:text-gray-400" />
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400 mt-4">
                            Select Input
                        </label>
                        <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                            <select
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                                :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                                <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                    Select Active Option
                                </option>
                                <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                    Enable
                                </option>
                                <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400">
                                    Disable
                                </option>
                            </select>
                            <span
                                class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                    </div>
                    


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
                        class="flex w-full justify-center rounded-lg bg-[#1A1A1A] hover:bg-[#ff84a3] px-4 py-2.5 text-sm font-medium text-white sm:w-auto">
                        Save Changes
                    </button>

                </div>

            </form>
        </div>
    </x-ui.modal>

@endsection
