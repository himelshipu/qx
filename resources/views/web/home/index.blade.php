<x-web-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-3xl font-bold mb-4">Welcome to {{ $appName }}</h1>
                    <p class="text-lg mb-4">Version: {{ $appVersion }}</p>
                    <p class="text-lg mb-4">Total Users: {{ $totalUsers }}</p>

                    @if($users->count() > 0)
                        <div class="mt-6">
                            <h2 class="text-xl font-semibold mb-3">Users</h2>
                            <ul class="space-y-2">
                                @foreach($users as $user)
                                    <li class="bg-gray-100 dark:bg-gray-700 p-3 rounded">
                                        {{ $user->name }} ({{ $user->email }})
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-web-layout>
