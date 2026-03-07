@extends('backend.layouts.app')

@section('content')
<x-backend.shell.breadcrumb pageTitle="All Creators" />

<div class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]" x-data="creatorManager()">
    
    <!-- Header with Search & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="relative flex-1 max-w-md">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <x-icons.search class="w-4 h-4" />
            </span>
            <input type="text" 
                placeholder="Search creators by name or niche..." 
                class="w-full h-10 pl-9 pr-4 text-sm rounded-lg border border-gray-200 bg-transparent focus:border-gray-300 focus:ring-2 focus:ring-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-gray-600">
        </div>
        
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                <x-icons.filter class="w-4 h-4" />
                <span class="hidden sm:inline">Filter</span>
            </button>
            
            <button @click="addCreator()" 
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
                <x-icons.plus class="w-4 h-4" />
                <span>Add Creator</span>
            </button>
        </div>
    </div>

    <!-- Creators Table -->
    <div class="mt-2">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[900px]">
                
                <!-- Table Header -->
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creator</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Followers</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody>
                    <template x-for="creator in creators" :key="creator.id">
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                            
                            <!-- Creator Name with Avatar -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-700">
                                        <template x-if="creator.avatar">
                                            <img :src="creator.avatar" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!creator.avatar">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </template>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white block" x-text="creator.name"></span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400" x-text="creator.handle"></span>
                                    </div>
                                </div>
                            </td>

                            <!-- Niche -->
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-purple-50 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400" x-text="creator.niche"></span>
                            </td>

                            <!-- Email -->
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-600 dark:text-gray-400" x-text="creator.email"></span>
                            </td>

                            <!-- Followers -->
                            <td class="px-4 py-3">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="creator.followers"></span>
                            </td>

                            <!-- Status Toggle -->
                            <td class="px-4 py-3">
                                <button @click="creator.status = creator.status === 'active' ? 'inactive' : 'active'" 
                                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none"
                                    :class="creator.status === 'active' ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600'">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                          :class="creator.status === 'active' ? 'translate-x-4.5' : 'translate-x-0.5'">
                                    </span>
                                </button>
                            </td>

                            <!-- Action Icons -->
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="viewCreator(creator)" class="p-2 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition">
                                        <x-icons.eye class="w-4 h-4" />
                                    </button>
                                    <button @click="editCreator(creator)" 
                                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                        <x-icons.edit class="w-4 h-4" />
                                    </button>
                                    <button @click="deleteCreator(creator)" 
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
    </div>
</div>

<!-- Creator Modal (Add/Edit) -->
<x-ui.modal x-data="{ open: false }" @open-creator-modal.window="open = true" x-show="open" x-cloak class="max-w-2xl">
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="modalTitle"></h3>
            <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <x-icons.close class="w-5 h-5" />
            </button>
        </div>

        <form class="space-y-4" @submit.prevent="saveCreator">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2 sm:col-span-1">
                    <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                    <input type="text" x-model="form.name" placeholder="e.g. John Doe" class="w-full h-11 px-4 text-sm rounded-lg border border-gray-200 bg-transparent dark:border-gray-700 dark:text-white">
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Email Address</label>
                    <input type="email" x-model="form.email" placeholder="john@example.com" class="w-full h-11 px-4 text-sm rounded-lg border border-gray-200 bg-transparent dark:border-gray-700 dark:text-white">
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Select Category</label>
                    <input type="text" x-model="form.niche" placeholder="e.g. Fashion, Lifestyle" class="w-full h-11 px-4 text-sm rounded-lg border border-gray-200 bg-transparent dark:border-gray-700 dark:text-white">
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Follower Count</label>
                    <input type="text" x-model="form.followers" placeholder="e.g. 50k" class="w-full h-11 px-4 text-sm rounded-lg border border-gray-200 bg-transparent dark:border-gray-700 dark:text-white">
                </div>
                <div class="col-span-2">
                    <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Avatar URL</label>
                    <input type="text" x-model="form.avatar" placeholder="https://..." class="w-full h-11 px-4 text-sm rounded-lg border border-gray-200 bg-transparent dark:border-gray-700 dark:text-white">
                </div>
                <!-- Set Status -->
                <div class="col-span-2">
                    <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select x-model="form.status" 
                        class="w-full h-11 px-4 text-sm rounded-lg border border-gray-200 bg-transparent focus:border-gray-300 focus:ring-2 focus:ring-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="button" @click="open = false" class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 dark:bg-purple-600 dark:hover:bg-purple-700">
                    <span x-text="modalAction"></span> Creator
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
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Delete Creator</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Are you sure you want to delete <span class="font-medium text-gray-700 dark:text-gray-300" x-text="deleteCreatorName"></span>?</p>
        <div class="flex items-center gap-3">
            <button @click="open = false" class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg dark:bg-gray-800 dark:text-gray-300">Cancel</button>
            <button @click="confirmDelete" class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600">Delete</button>
        </div>
    </div>
</x-ui.modal>

<x-ui.modal x-data="{ open: false }" @open-view-modal.window="open = true" x-show="open" x-cloak class="max-w-xl">
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl overflow-hidden shadow-2xl">
        <!-- Modal Header / Cover Mockup -->
        <div class="h-24 bg-gradient-to-r from-purple-500 to-indigo-600"></div>
        
        <div class="px-8 pb-8">
            <div class="relative flex justify-between items-end -mt-12 mb-6">
                <div class="w-24 h-24 rounded-full border-4 border-white dark:border-gray-900 overflow-hidden bg-gray-100 shadow-sm">
                    <template x-if="selectedCreator?.avatar">
                        <img :src="selectedCreator.avatar" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!selectedCreator?.avatar">
                        <div class="w-full h-full flex items-center justify-center bg-gray-200 dark:bg-gray-800">
                             <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2" stroke-linecap="round"></path></svg>
                        </div>
                    </template>
                </div>
            </div>

            <div class="mb-6 text-start">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white" x-text="selectedCreator?.name"></h2>
                <p class="text-indigo-600 dark:text-indigo-400 font-medium" x-text="selectedCreator?.handle"></p>
            </div>

            <div class="grid grid-cols-2 gap-6 text-start border-t border-gray-100 dark:border-gray-800 pt-6">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Category</p>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300" x-text="selectedCreator?.niche"></p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Followers</p>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300" x-text="selectedCreator?.followers"></p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Email</p>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 truncate" x-text="selectedCreator?.email"></p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Status</p>
                    <span :class="selectedCreator?.status === 'active' ? 'text-green-500' : 'text-gray-400'" class="text-sm font-bold uppercase" x-text="selectedCreator?.status"></span>
                </div>
            </div>

            <div class="mt-10 flex gap-3">
                <button @click="open = false; editCreator(selectedCreator)" class="flex-1 py-3 bg-gray-900 dark:bg-purple-600 text-white rounded-xl font-bold text-sm hover:opacity-90 transition">Edit Profile</button>
                <button @click="open = false" class="px-6 py-3 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 rounded-xl font-bold text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition">Close</button>
            </div>
        </div>
    </div>
</x-ui.modal>


<script>
function creatorManager() {
    return {
        creators: [
            { id: 1, name: 'Alice Smith', handle: '@alice_fash', niche: 'Fashion', email: 'alice@example.com', followers: '125k', status: 'active', avatar: 'https://i.pravatar.cc/150?u=alice' },
            { id: 2, name: 'Bob Johnson', handle: '@tech_bob', niche: 'Technology', email: 'bob@tech.com', followers: '45k', status: 'active', avatar: 'https://i.pravatar.cc/150?u=bob' },
            { id: 3, name: 'Charlie Davis', handle: '@chef_charlie', niche: 'Food', email: 'charlie@foodie.com', followers: '890k', status: 'inactive', avatar: 'https://i.pravatar.cc/150?u=charlie' },
            { id: 4, name: 'Diana Prince', handle: '@wonder_fitness', niche: 'Fitness', email: 'diana@fitness.com', followers: '2.1M', status: 'active', avatar: 'https://i.pravatar.cc/150?u=diana' }
        ],
        modalTitle: 'Add New Creator',
        modalAction: 'Add',
        form: { id: null, name: '', niche: '', email: '', followers: '', avatar: '', status: 'active' },
        deleteCreatorName: '',
        deleteCreatorId: null,
        
        viewCreator(creator) {
            this.selectedCreator = { ...creator };
            this.$dispatch('open-view-modal');
        },

        addCreator() {
            this.modalTitle = 'Add New Creator';
            this.modalAction = 'Add';
            this.form = { id: null, name: '', niche: '', email: '', followers: '', avatar: '', status: 'active' };
            this.$dispatch('open-creator-modal');
        },
        editCreator(creator) {
            this.modalTitle = 'Edit Creator';
            this.modalAction = 'Save';
            this.form = { ...creator };
            this.$dispatch('open-creator-modal');
        },
        saveCreator() {
            if (this.form.id) {
                const index = this.creators.findIndex(c => c.id === this.form.id);
                this.creators[index] = { ...this.form };
            } else {
                this.form.id = Date.now();
                this.creators.push({ ...this.form });
            }
            this.$dispatch('open-creator-modal', { open: false }); // Logic to close (usually handled by Alpine event)
            window.location.reload(); // Simple refresh for dummy data persistence demo
        },
        deleteCreator(creator) {
            this.deleteCreatorName = creator.name;
            this.deleteCreatorId = creator.id;
            this.$dispatch('open-delete-modal');
        },
        confirmDelete() {
            this.creators = this.creators.filter(c => c.id !== this.deleteCreatorId);
            // close modal logic
        }
    }
}
</script>
@endsection