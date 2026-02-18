@extends('backend.layouts.app')

@section('content')
<x-common.page-breadcrumb pageTitle="Categories" />

<div class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
    
    <!-- Header with Search & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="relative flex-1 max-w-md">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <x-icons.search class="w-4 h-4" />
            </span>
            <input type="text" 
                placeholder="Search categories..." 
                class="w-full h-10 pl-9 pr-4 text-sm rounded-lg border border-gray-200 bg-transparent focus:border-gray-300 focus:ring-2 focus:ring-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-gray-600">
        </div>
        
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                <x-icons.filter class="w-4 h-4" />
                <span class="hidden sm:inline">Filter</span>
            </button>
            
            <button @click="$dispatch('open-category-modal')" 
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
                <x-icons.plus class="w-4 h-4" />
                <span>Add Category</span>
            </button>
        </div>
    </div>

    <!-- Categories Table -->
    <div x-data="categoryManager()" class="mt-2">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[800px]">
                
                <!-- Table Header -->
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody>
                    <template x-for="category in categories" :key="category.id">
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                            
                            <!-- Category Name with Image -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                        <img :src="category.image" :alt="category.name" class="w-6 h-6 object-contain">
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white" x-text="category.name"></span>
                                </div>
                            </td>

                            <!-- Slug -->
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-600 dark:text-gray-400" x-text="category.slug"></span>
                            </td>

                            <!-- Status Toggle -->
                            <td class="px-4 py-3">
                                <button @click="toggleStatus(category.id)" 
                                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none"
                                    :class="category.status === 'Enabled' ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600'">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                          :class="category.status === 'Enabled' ? 'translate-x-5' : 'translate-x-0.5'">
                                    </span>
                                </button>
                            </td>

                            <!-- Action Icons -->
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="viewCategory(category)" 
                                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                        <x-icons.eye class="w-4 h-4" />
                                    </button>
                                    <button @click="editCategory(category)" 
                                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                        <x-icons.edit class="w-4 h-4" />
                                    </button>
                                    <button @click="deleteCategory(category)" 
                                        class="p-2 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                        <x-icons.trash class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Showing <span x-text="categories.length"></span> of <span x-text="categories.length"></span> categories
            </p>
            <div class="flex gap-2">
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 dark:border-gray-700 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 disabled:opacity-50">
                    <x-icons.chevron-left class="w-4 h-4" />
                </button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 dark:border-gray-700 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 disabled:opacity-50">
                    <x-icons.chevron-right class="w-4 h-4" />
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Category Modal (Add/Edit) -->
<x-ui.modal x-data="{ open: false }" @open-category-modal.window="open = true" x-show="open" x-cloak class="max-w-lg">
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl p-6">
        
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="modalTitle"></h3>
            <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <x-icons.close class="w-5 h-5" />
            </button>
        </div>

        <form class="space-y-4">
            <!-- Category Name -->
            <div>
                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Category Name</label>
                <input type="text" 
                    x-model="form.name"
                    placeholder="e.g., Beauty, Fashion, Travel"
                    class="w-full h-11 px-4 text-sm rounded-lg border border-gray-200 bg-transparent focus:border-gray-300 focus:ring-2 focus:ring-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            </div>

            <!-- Category Slug -->
            <div>
                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Category Slug</label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 text-sm text-gray-500 bg-gray-50 border border-r-0 border-gray-200 rounded-l-lg dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400">
                        /
                    </span>
                    <input type="text" 
                        x-model="form.slug"
                        placeholder="beauty"
                        class="flex-1 h-11 px-4 text-sm border border-gray-200 rounded-r-lg bg-transparent focus:border-gray-300 focus:ring-2 focus:ring-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>
            </div>

            <!-- Icon Upload -->
            <div>
                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Category Icon</label>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg border-2 border-dashed border-gray-200 dark:border-gray-700 flex items-center justify-center">
                        <x-icons.image class="w-5 h-5 text-gray-400" />
                    </div>
                    <div class="flex-1">
                        <input type="file" 
                            accept="image/*"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100 dark:file:bg-gray-800 dark:file:text-gray-300">
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center gap-3 pt-4">
                <button type="button" 
                    @click="open = false"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    Cancel
                </button>
                <button type="button"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
                    <span x-text="modalAction"></span> Save
                </button>
            </div>
        </form>
    </div>
</x-ui.modal>

<!-- Delete Confirmation Modal -->
<x-ui.modal x-data="{ open: false }" @open-delete-modal.window="open = true" x-show="open" x-cloak class="max-w-md">
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl p-6 text-center">
        
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/20 flex items-center justify-center">
            <x-icons.trash class="w-8 h-8 text-red-500" />
        </div>

        <h3 class="text-lg font-semibold text-gray-900 dark-white mb-2">Delete Category</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
            Are you sure you want to delete <span class="font-medium text-gray-700 dark:text-gray-300" x-text="deleteCategoryName"></span>? 
            This action cannot be undone.
        </p>

        <div class="flex items-center gap-3">
            <button @click="open = false" 
                class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                Cancel
            </button>
            <button @click="confirmDelete" 
                class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600">
                Delete
            </button>
        </div>
    </div>
</x-ui.modal>
@endsection

<script>
function categoryManager() {
    return {
        categories: [
            { id: 1, image: '/images/brand/brand-01.svg', name: 'Beauty', slug: 'beauty', status: 'Enabled' },
            { id: 2, image: '/images/brand/brand-02.svg', name: 'Travel', slug: 'travel', status: 'Enabled' },
            { id: 3, image: '/images/brand/brand-03.svg', name: 'Fashion', slug: 'fashion', status: 'Enabled' },
            { id: 4, image: '/images/brand/brand-04.svg', name: 'Photography', slug: 'photography', status: 'Enabled' },
            { id: 5, image: '/images/brand/brand-05.svg', name: 'Bloggers', slug: 'bloggers', status: 'Enabled' },
            { id: 6, image: '/images/brand/brand-06.svg', name: 'Sports & Fitness', slug: 'sports-fitness', status: 'Enabled' },
            { id: 7, image: '/images/brand/brand-07.svg', name: 'Pet', slug: 'pet', status: 'Enabled' },
            { id: 8, image: '/images/brand/brand-08.svg', name: 'Gamers', slug: 'gamers', status: 'Disabled' }
        ],
        
        modalTitle: 'Add New Category',
        modalAction: 'Add',
        form: {
            name: '',
            slug: '',
            image: null
        },
        
        deleteCategoryName: '',
        
        toggleStatus(id) {
            const category = this.categories.find(c => c.id === id);
            if (category) {
                category.status = category.status === 'Enabled' ? 'Disabled' : 'Enabled';
            }
        },
        
        viewCategory(category) {
            alert('View category: ' + category.name);
        },
        
        editCategory(category) {
            this.modalTitle = 'Edit Category';
            this.modalAction = 'Save';
            this.form = { ...category };
            this.$dispatch('open-category-modal');
        },
        
        deleteCategory(category) {
            this.deleteCategoryName = category.name;
            this.$dispatch('open-delete-modal');
        },
        
        confirmDelete() {
            alert('Category deleted');
            this.$dispatch('close-delete-modal');
        }
    }
}
</script>