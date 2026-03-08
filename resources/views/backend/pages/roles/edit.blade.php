@extends('backend.layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Role</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update role details and permissions</p>
            </div>
            <a href="{{ route('dashboard.roles.index') }}" 
               class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Roles
            </a>
        </div>
    </div>

    <form action="{{ route('dashboard.roles.update', $role->id) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Role Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name', $role->name) }}"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                       placeholder="Enter role name"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status
                </label>
                <div class="flex items-center">
                    <input type="checkbox" 
                           name="is_active" 
                           id="is_active" 
                           value="1"
                           {{ old('is_active', $role->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="is_active" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                        Active
                    </label>
                </div>
            </div>

            <!-- Description -->
            <div class="md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Description
                </label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="3"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Enter role description">{{ old('description', $role->description) }}</textarea>
            </div>
        </div>

        <!-- Permissions -->
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Permissions</h3>
            
            @if($permissions->isNotEmpty())
            <div class="space-y-6">
                @foreach($permissions as $module => $modulePermissions)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    @php
                        $modulePermissionIds = $modulePermissions->pluck('id')->toArray();
                        $rolePermissionIds = $role->permissions->pluck('id')->toArray();
                        $checkedCount = count(array_intersect($modulePermissionIds, $rolePermissionIds));
                        $allChecked = $checkedCount === count($modulePermissionIds);
                    @endphp
                    
                    <div class="flex items-center gap-2 mb-3">
                        <input type="checkbox" 
                               id="module_{{ $loop->index }}"
                               class="module-checkbox w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                               data-module="{{ $loop->index }}"
                               {{ $allChecked ? 'checked' : '' }}>
                        <label for="module_{{ $loop->index }}" class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $module }}
                        </label>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            ({{ $checkedCount }}/{{ $modulePermissions->count() }} selected)
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 pl-6">
                        @foreach($modulePermissions as $permission)
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="permissions[]" 
                                   id="permission_{{ $permission->id }}"
                                   value="{{ $permission->id }}"
                                   {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}
                                   class="permission-checkbox w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                   data-module="{{ $loop->parent->index }}">
                            <label for="permission_{{ $permission->id }}" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                                {{ $permission->name }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-500 dark:text-gray-400">No permissions available.</p>
            @endif
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('dashboard.roles.index') }}" 
               class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition-colors">
                Cancel
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                Update Role
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.querySelectorAll('.module-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const moduleIndex = this.dataset.module;
        document.querySelectorAll(`.permission-checkbox[data-module="${moduleIndex}"]`).forEach(permission => {
            permission.checked = this.checked;
        });
    });
});

// Update module checkbox when individual permissions change
document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const moduleIndex = this.dataset.module;
        const modulePermissions = document.querySelectorAll(`.permission-checkbox[data-module="${moduleIndex}"]`);
        const moduleCheckbox = document.querySelector(`.module-checkbox[data-module="${moduleIndex}"]`);
        
        const allChecked = Array.from(modulePermissions).every(p => p.checked);
        const someChecked = Array.from(modulePermissions).some(p => p.checked);
        
        moduleCheckbox.checked = allChecked;
        moduleCheckbox.indeterminate = someChecked && !allChecked;
    });
});
</script>
@endpush
@endsection
