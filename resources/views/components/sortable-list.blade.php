@props([
    'items' => [],
    'modelName' => '',
    'reorderRoute' => '',
    'editRoute' => '',
    'title' => 'Manage Items',
    'description' => 'Drag to reorder items',
    'emptyMessage' => 'No items found',
    'emptyActionText' => 'Create an item',
    'emptyActionRoute' => '',
])

<div class="space-y-6">
    @if(count($items) > 0)
        <!-- Modern Header -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 border border-slate-700/50 shadow-2xl">
            <!-- Animated background -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
                <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
            </div>

            <!-- Content -->
            <div class="relative px-8 py-8 md:px-12 md:py-10">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg">
                                <i class="fas fa-arrows-sort text-white text-lg"></i>
                            </div>
                            <h2 class="text-3xl md:text-4xl font-bold text-white tracking-tight">{{ $title }}</h2>
                        </div>
                        <p class="text-slate-400 text-sm md:text-base mt-2 flex items-center gap-2">
                            <i class="fas fa-info-circle text-indigo-400"></i>
                            {{ $description }}
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="inline-flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-700/50 border border-slate-600/50 backdrop-blur-sm">
                            <i class="fas fa-list-ol text-indigo-400"></i>
                            <span class="text-sm font-semibold text-slate-200">{{ count($items) }} {{ $modelName }}{{ count($items) !== 1 ? 's' : '' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Drag & Drop Container -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-700/50 bg-white dark:bg-slate-900 shadow-lg dark:shadow-2xl overflow-hidden">
            <!-- Info Banner -->
            <div class="px-6 py-4 md:px-8 md:py-5 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 border-b border-slate-200 dark:border-slate-700/50 flex items-start gap-4">
                <div class="flex-shrink-0 mt-0.5">
                    <div class="flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100 dark:bg-indigo-900/40">
                        <i class="fas fa-grip-horizontal text-indigo-600 dark:text-indigo-400"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">Drag to Reorder</p>
                    <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">
                        Hold and drag items by the handle (⋮) to rearrange. Changes save automatically.
                    </p>
                </div>
            </div>

            <!-- Sortable List -->
            <div id="sortable-list" class="space-y-2 p-6 md:p-8" data-reorder-route="{{ $reorderRoute }}">
                @foreach($items as $index => $item)
                    <div class="sortable-item group relative rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 transition-all duration-200 hover:border-indigo-300 dark:hover:border-indigo-600 hover:shadow-md dark:hover:shadow-indigo-900/20 hover:bg-white dark:hover:bg-slate-800"
                        data-id="{{ $item->id }}">
                        
                        <div class="flex items-center gap-4 p-5 md:p-6">
                            <!-- Drag Handle -->
                            <div class="sortable-handle flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-slate-200 dark:bg-slate-700/50 text-slate-400 dark:text-slate-500 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/30 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-all duration-150 cursor-grab active:cursor-grabbing active:bg-indigo-200 dark:active:bg-indigo-900/50 select-none">
                                <i class="fas fa-grip-vertical text-sm"></i>
                            </div>

                            <!-- Item Number -->
                            <div class="flex-shrink-0">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-200 dark:bg-slate-700/50 font-semibold text-slate-600 dark:text-slate-400 text-xs">
                                    {{ $index + 1 }}
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-slate-900 dark:text-white truncate">
                                    {{ $item->name ?? $item->title ?? $item->brand_name ?? $item->author_name ?? 'Item' }}
                                </div>
                                @if($item->description || $item->bio || $item->quote)
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 line-clamp-1">
                                        {{ $item->description ?? $item->bio ?? $item->quote ?? '' }}
                                    </p>
                                @endif
                            </div>

                            <!-- Status Badge -->
                            <div class="flex-shrink-0">
                                @if($item->is_active !== null)
                                    @if($item->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border border-green-200 dark:border-green-800/50">
                                            <span class="inline-block h-2 w-2 rounded-full bg-green-500"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-200 text-slate-600 dark:bg-slate-700/50 dark:text-slate-400 border border-slate-300 dark:border-slate-600/50">
                                            <span class="inline-block h-2 w-2 rounded-full bg-slate-400 dark:bg-slate-500"></span>
                                            Inactive
                                        </span>
                                    @endif
                                @elseif($item->is_published !== null)
                                    @if($item->is_published)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border border-green-200 dark:border-green-800/50">
                                            <span class="inline-block h-2 w-2 rounded-full bg-green-500"></span>
                                            Published
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 border border-amber-200 dark:border-amber-800/50">
                                            <span class="inline-block h-2 w-2 rounded-full bg-amber-500"></span>
                                            Draft
                                        </span>
                                    @endif
                                @endif
                            </div>

                            <!-- Edit Button -->
                            @if($editRoute)
                                <a href="{{ route($editRoute, $item->id) }}"
                                    class="flex-shrink-0 inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 dark:text-slate-500 transition-all duration-150 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 hover:text-indigo-600 dark:hover:text-indigo-400 group-hover:opacity-100 opacity-75">
                                    <i class="fas fa-pencil text-sm"></i>
                                </a>
                            @endif
                        </div>

                        <!-- Divider (except last item) -->
                        @if(!$loop->last)
                            <div class="hidden"></div>
                        @endif
                    </div>
                @endforeach

                <!-- Save Indicator -->
                <div id="save-indicator" class="mt-6 p-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/50 text-green-800 dark:text-green-300 text-xs font-medium hidden flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>Changes saved automatically</span>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="rounded-2xl border-2 border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 p-12 md:p-16 text-center">
            <div class="inline-flex items-center justify-center h-16 w-16 rounded-2xl bg-slate-200 dark:bg-slate-800 mb-6">
                <i class="fas fa-inbox text-2xl text-slate-400 dark:text-slate-500"></i>
            </div>
            <p class="text-slate-600 dark:text-slate-400 text-base font-semibold mb-2">{{ $emptyMessage }}</p>
            <p class="text-slate-500 dark:text-slate-500 text-sm mb-6">Nothing to reorder yet. Create your first item to get started.</p>
            @if($emptyActionRoute)
                <a href="{{ route($emptyActionRoute) }}" 
                    class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 px-6 py-3 text-sm font-semibold text-white transition-all duration-150 shadow-lg hover:shadow-xl">
                    <i class="fas fa-plus"></i>
                    {{ $emptyActionText }}
                </a>
            @endif
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sortableList = document.getElementById('sortable-list');
        const saveIndicator = document.getElementById('save-indicator');
        
        if (sortableList && sortableList.querySelectorAll('.sortable-item').length > 0 && typeof Sortable !== 'undefined') {
            const reorderRoute = sortableList.dataset.reorderRoute;
            let saveTimeout;

            Sortable.create(sortableList, {
                animation: 300,
                ghostClass: 'opacity-50 bg-indigo-50 dark:bg-indigo-900/20 scale-98',
                dragClass: 'dragging',
                draggable: '.sortable-item',
                handle: '.sortable-handle',
                forceFallback: false,
                easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
                onEnd: function(evt) {
                    // Update position numbers
                    const items = sortableList.querySelectorAll('.sortable-item');
                    items.forEach((item, index) => {
                        const numberBadge = item.querySelector('div:nth-child(2)');
                        if (numberBadge) {
                            numberBadge.textContent = index + 1;
                        }
                    });

                    // Get new order
                    const order = Array.from(items).map(item => item.dataset.id);

                    // Show saving state
                    if (saveIndicator) {
                        saveIndicator.classList.remove('hidden');
                        saveIndicator.innerHTML = '<i class="fas fa-spinner animate-spin"></i><span>Saving...</span>';
                    }

                    // Send to server
                    if (reorderRoute) {
                        clearTimeout(saveTimeout);
                        
                        fetch(reorderRoute, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                order: order
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (saveIndicator) {
                                    saveIndicator.innerHTML = '<i class="fas fa-check-circle"></i><span>✨ Changes saved successfully</span>';
                                    saveIndicator.classList.remove('hidden');
                                    
                                    saveTimeout = setTimeout(() => {
                                        saveIndicator.classList.add('hidden');
                                    }, 3000);
                                }
                            } else {
                                console.error('❌ Failed to save order:', data.message);
                                if (saveIndicator) {
                                    saveIndicator.classList.add('hidden');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('❌ Error:', error);
                            if (saveIndicator) {
                                saveIndicator.classList.add('hidden');
                            }
                        });
                    }
                }
            });
        }
    });
</script>

