<div x-data="{
    categories: [
        { id: 1, image: '/images/brand/brand-01.svg', name: 'Beauty', status: 'Enabled' },
        { id: 2, image: '/images/brand/brand-02.svg', name: 'Travel', status: 'Enabled' },
        { id: 3, image: '/images/brand/brand-03.svg', name: 'Fashion', status: 'Enabled' },
        { id: 4, image: '/images/brand/brand-04.svg', name: 'Photography', status: 'Enabled' },
        { id: 5, image: '/images/brand/brand-05.svg', name: 'Bloggers', status: 'Enabled' },
        { id: 6, image: '/images/brand/brand-06.svg', name: 'Sports & Fitness', status: 'Enabled' },
        { id: 7, image: '/images/brand/brand-07.svg', name: 'Pet', status: 'Enabled' },
        { id: 8, image: '/images/brand/brand-08.svg', name: 'Gamers', status: 'Disabled' }
    ],

    getStatusClass(status) {
        return status === 'Enabled'
            ? 'bg-green-100 text-green-700'
            : 'bg-orange-100 text-orange-600';
    },

    modalOpen: false,
    modalType: '', // edit | delete
    selectedCategory: null,

    openEdit(category) {
        this.modalType = 'edit';
        this.selectedCategory = category;
        this.modalOpen = true;
    },

    openDelete(category) {
        this.modalType = 'delete';
        this.selectedCategory = category;
        this.modalOpen = true;
    }
}">
    
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="max-w-full overflow-x-auto custom-scrollbar">
            
            <table class="w-full min-w-[600px]">
                
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-5 py-3 text-left sm:px-6">Image</th>
                        <th class="px-5 py-3 text-left sm:px-6">Status</th>
                        <th class="px-5 py-3 text-left sm:px-6">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <template x-for="category in categories" :key="category.id">
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            
                            <!-- Image -->
                            <td class="px-5 py-4 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 overflow-hidden rounded-full">
                                        <img :src="category.image" :alt="category.name">
                                    </div>
                                    <span x-text="category.name"></span>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-4 sm:px-6">
                                <span class="inline-block px-3 py-1 rounded-full text-sm font-medium"
                                      :class="getStatusClass(category.status)"
                                      x-text="category.status">
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-4 sm:px-6">
                                <div class="flex gap-3">

                                    <!-- Edit -->
                                    <button @click="openEdit(category)"
                                        class="flex gap-2 items-center justify-center w-32 h-12 rounded-lg border border-gray-300 hover:bg-gray-100 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"> <path fill-rule="evenodd" clip-rule="evenodd" d="M2.75 12C2.75 6.89137 6.89137 2.75 12 2.75C17.0825 2.75 21.2077 6.84923 21.2497 11.9218C21.2531 12.336 21.5917 12.669 22.0059 12.6655C22.4201 12.6621 22.7531 12.3236 22.7496 11.9094C22.7009 6.01395 17.9068 1.25 12 1.25C6.06294 1.25 1.25 6.06294 1.25 12C1.25 17.9068 6.01395 22.7009 11.9094 22.7496C12.3236 22.7531 12.6621 22.4201 12.6655 22.0059C12.669 21.5917 12.336 21.2531 11.9218 21.2497C6.84923 21.2077 2.75 17.0825 2.75 12ZM12 7.75C9.65279 7.75 7.75 9.65279 7.75 12C7.75 13.7835 8.84882 15.3122 10.4092 15.9425C10.7933 16.0976 10.9789 16.5347 10.8237 16.9188C10.6686 17.3029 10.2315 17.4884 9.84739 17.3333C7.7395 16.4818 6.25 14.4157 6.25 12C6.25 8.82436 8.82436 6.25 12 6.25C14.4157 6.25 16.4818 7.7395 17.3333 9.84739C17.4884 10.2315 17.3029 10.6686 16.9188 10.8237C16.5347 10.9789 16.0976 10.7933 15.9425 10.4092C15.3122 8.84882 13.7835 7.75 12 7.75ZM14.6699 21.2614L12.0573 13.4235C11.7758 12.5792 12.5792 11.7758 13.4235 12.0573L21.2614 14.6699C22.2462 14.9982 22.2462 16.3911 21.2614 16.7193L18.3672 17.6841C18.0447 17.7916 17.7916 18.0447 17.6841 18.3672L16.7193 21.2614C16.3911 22.2462 14.9982 22.2462 14.6699 21.2614Z" fill="currentColor"/> </svg>
                                        Edit
                                    </button>

                                    <!-- Delete -->
                                    <button @click="openDelete(category)"
                                        class="flex gap-2 items-center justify-center w-32 h-12 rounded-lg border border-gray-300 hover:bg-red-50 hover:text-red-600 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M21.1303 9.8531C22.2899 11.0732 22.2899 12.9268 21.1303 14.1469C19.1745 16.2047 15.8155 19 12 19C8.18448 19 4.82549 16.2047 2.86971 14.1469C1.7101 12.9268 1.7101 11.0732 2.86971 9.8531C4.82549 7.79533 8.18448 5 12 5C15.8155 5 19.1745 7.79533 21.1303 9.8531Z" stroke="currentColor" stroke-width="1.5"/>
                                        <path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z" stroke="currentColor" stroke-width="1.5"/>
                                        </svg>
                                        Disabled 
                                    </button>

                                </div>
                            </td>

                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 🔥 MODAL -->
    <div x-show="modalOpen"
         x-transition
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
         x-cloak>

        <div class="bg-white dark:bg-gray-900 rounded-2xl w-full max-w-md p-6">

            <!-- Title -->
            <h3 class="text-xl font-semibold mb-4"
                x-text="modalType === 'edit' ? 'Edit Category' : 'Delete Category'">
            </h3>

            <!-- Edit UI -->
            <template x-if="modalType === 'edit'">
                <div>
                    <label class="block mb-2 text-sm">Category Name</label>
                    <input type="text"
                           x-model="selectedCategory.name"
                           class="w-full border rounded-lg px-3 py-2 mb-4">

                    <div class="flex justify-end gap-3">
                        <button @click="modalOpen = false"
                            class="px-4 py-2 border rounded-lg">
                            Cancel
                        </button>

                        <button class="px-4 py-2 bg-brand-500 text-white rounded-lg">
                            Save Changes
                        </button>
                    </div>
                </div>
            </template>

            <!-- Delete UI -->
            <template x-if="modalType === 'delete'">
                <div>
                    <p class="mb-6">
                        Are you sure you want to delete 
                        <strong x-text="selectedCategory.name"></strong>?
                    </p>

                    <div class="flex justify-end gap-3">
                        <button @click="modalOpen = false"
                            class="px-4 py-2 border rounded-lg">
                            Cancel
                        </button>

                        <button class="px-4 py-2 bg-red-500 text-white rounded-lg">
                            Yes, Disable
                        </button>
                    </div>
                </div>
            </template>

        </div>
    </div>

</div>
