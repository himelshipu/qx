@extends('backend.layouts.app')

@section('title', 'Assign Permissions')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Assign Permissions</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Assign permissions to roles</p>
            </div>
            <a href="{{ route('dashboard.permissions.index') }}" 
               class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Permissions
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 m-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <form action="{{ route('dashboard.permissions.assign.store') }}" method="POST" class="p-6">
        @csrf

        <!-- Select Role -->
        <div class="mb-8">
            <label for="role_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Select Role <span class="text-red-500">*</span>
            </label>
            <select 
                name="role_id" 
                id="role_id" 
                class="w-full md:w-1/3 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-1 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white"
                required
                onchange="loadRolePermissions(this.value)">
                <option value="">-- Select a Role --</option>
                @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
            @error('role_id')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Permissions by Module -->
        @if($permissions->isNotEmpty())
        <div id="permissions-container" class="space-y-6" style="display: none;">
            @foreach($permissions as $module => $modulePermissions)
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <input type="checkbox" 
                           id="module_select_{{ $loop->index }}"
                           class="module-checkbox w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                           data-module="{{ $loop->index }}"
                           onchange="toggleModulePermissions({{ $loop->index }})">
                    <label for="module_select_{{ $loop->index }}" class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $module }}
                    </label>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        ({{ $modulePermissions->count() }} permissions)
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 pl-6">
                    @foreach($modulePermissions as $permission)
                    <div class="flex items-center">
                        <input type="checkbox" 
                               name="permissions[]" 
                               id="perm_{{ $permission->id }}"
                               value="{{ $permission->id }}"
                               class="permission-checkbox w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                               data-module="{{ $loop->parent->index }}"
                               onchange="updateModuleCheckbox({{ $loop->parent->index }})">
                        <label for="perm_{{ $permission->id }}" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ $permission->name }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" 
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Save Permissions
                </button>
            </div>
        </div>
        @else
        <div class="text-center py-8">
            <p class="text-gray-500 dark:text-gray-400">No permissions available. Please create permissions first.</p>
            <a href="{{ route('dashboard.permissions.create') }}" class="mt-2 inline-block text-purple-600 hover:text-purple-700 dark:text-purple-400">
                Create Permissions
            </a>
        </div>
        @endif
    </form>
</div>

@push('scripts')
<script>
function loadRolePermissions(roleId) {
    const container = document.getElementById('permissions-container');
    if (!roleId) {
        container.style.display = 'none';
        return;
    }

    container.style.display = 'block';

    // Reset all checkboxes
    document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
    document.querySelectorAll('.module-checkbox').forEach(cb => cb.checked = false);

    // Fetch current permissions for the selected role
    fetch(`/dashboard/roles/${roleId}/permissions`, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Check the permissions
        data.permissions.forEach(permId => {
            const checkbox = document.getElementById('perm_' + permId);
            if (checkbox) checkbox.checked = true;
        });

        // Update module checkboxes
        document.querySelectorAll('.module-checkbox').forEach((moduleCb, index) => {
            updateModuleCheckbox(index);
        });
    })
    .catch(error => console.error('Error loading permissions:', error));
}

function toggleModulePermissions(moduleIndex) {
    const moduleCheckbox = document.getElementById('module_select_' + moduleIndex);
    document.querySelectorAll(`.permission-checkbox[data-module="${moduleIndex}"]`).forEach(permission => {
        permission.checked = moduleCheckbox.checked;
    });
}

function updateModuleCheckbox(moduleIndex) {
    const moduleCheckbox = document.getElementById('module_select_' + moduleIndex);
    const modulePermissions = document.querySelectorAll(`.permission-checkbox[data-module="${moduleIndex}"]`);
    
    const allChecked = Array.from(modulePermissions).every(p => p.checked);
    const someChecked = Array.from(modulePermissions).some(p => p.checked);
    
    moduleCheckbox.checked = allChecked;
    moduleCheckbox.indeterminate = someChecked && !allChecked;
}
</script>
@endpush
@endsection
