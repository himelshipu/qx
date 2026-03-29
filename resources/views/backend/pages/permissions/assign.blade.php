@extends('backend.layouts.app')

@section('title', 'Assign Permissions')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700" x-data="permissionManager()">
    <!-- Header -->
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Assign Permissions</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Grant or revoke module access for roles</p>
            </div>
            <a href="{{ route('dashboard.permissions.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Permissions
            </a>
        </div>
    </div>

    <!-- Sessions -->
    @if(session('success'))
    <div class="p-4 m-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <form action="{{ route('dashboard.permissions.assign.store') }}" method="POST">
        @csrf
        <input type="hidden" name="role_id" :value="roleId">

        <!-- Table Wrapper -->
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-4">Modules</th>
                        <th class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <select x-model="roleId" @change="loadRolePermissions()"
                                    class="w-48 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-1 focus:ring-purple-500 dark:bg-gray-900 dark:text-white" required>
                                    <option value="">Select Role *</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" :disabled="!roleId" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition-colors">
                                    Apply
                                </button>
                            </div>
                        </th>
                        @php $standardActions = ['View', 'Create', 'Edit', 'Delete', 'Show', 'Status']; @endphp
                        @foreach($standardActions as $action)
                            <th class="px-4 py-4 text-center">{{ $action }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($permissions->keys() as $moduleName)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white capitalize">
                            {{ $moduleName }}
                        </td>
                        <td class="px-4 py-4"></td>
                        @foreach($standardActions as $action)
                            @php $permissionString = strtolower($moduleName) . '.' . strtolower($action); @endphp
                            <td class="px-4 py-4 text-center">
                                <div class="flex justify-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="permissions[]" value="{{ $permissionString }}" 
                                               class="sr-only peer"
                                               :checked="activePermissions.includes('{{ $permissionString }}')"
                                               @change="togglePermission('{{ $permissionString }}')">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                    </label>
                                </div>
                            </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Save Footer -->
        <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <p class="text-sm text-gray-500" x-text="activePermissions.length + ' permissions selected'"></p>
            <button type="submit" 
                    class="px-6 py-2 bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors">
                Save Permissions
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function permissionManager() {
    return {
        roleId: '',
        activePermissions: [],
        async loadRolePermissions() {
            if (!this.roleId) { this.activePermissions = []; return; }
            try {
                const response = await fetch(`/dashboard/roles/${this.roleId}/permissions/names`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await response.json();
                this.activePermissions = data.permissions || [];
            } catch (error) {
                console.error("Error loading permissions:", error);
            }
        },
        togglePermission(permName) {
            if (this.activePermissions.includes(permName)) {
                this.activePermissions = this.activePermissions.filter(p => p !== permName);
            } else {
                this.activePermissions.push(permName);
            }
        }
    }
}
</script>
@endpush
@endsection