<div class="relative" x-data="{ open: false, notifying: true }" @click.away="open = false">
    <button @click="open = !open; notifying = false" class="relative flex items-center justify-center w-11 h-11 text-gray-500 bg-white border border-gray-200 rounded-full hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white">
        <span x-show="notifying" class="absolute top-0.5 right-0 w-2 h-2 bg-orange-400 rounded-full">
            <span class="absolute w-full h-full bg-orange-400 rounded-full opacity-75 animate-ping"></span>
        </span>
        <x-icons.bell class="w-5 h-5" />
    </button>

    <div x-show="open" x-transition class="absolute right-0 mt-2 w-[350px] bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-3 z-50 sm:w-[361px]" style="display: none;">
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-200 dark:border-gray-700">
            <h5 class="text-lg font-semibold text-gray-800 dark:text-white">Notification</h5>
            <button @click="open = false" class="text-gray-500 dark:text-gray-400">
                <x-icons.close class="w-5 h-5" />
            </button>
        </div>

        <ul class="overflow-y-auto max-h-[400px]">
            @php
                $notifications = [
                    ['id' => 1, 'userName' => 'Terry Franci', 'userImage' => '/images/user/user-02.jpg', 'action' => 'requests permission to change', 'project' => 'Project - Nganter App', 'type' => 'Project', 'time' => '5 min ago', 'status' => 'online'],
                    ['id' => 2, 'userName' => 'Alex Johnson', 'userImage' => '/images/user/user-03.jpg', 'action' => 'requests permission to change', 'project' => 'Project - Nganter App', 'type' => 'Project', 'time' => '10 min ago', 'status' => 'offline'],
                    ['id' => 3, 'userName' => 'Sarah Williams', 'userImage' => '/images/user/user-04.jpg', 'action' => 'requests permission to change', 'project' => 'Project - Dashboard UI', 'type' => 'Project', 'time' => '15 min ago', 'status' => 'online'],
                    ['id' => 4, 'userName' => 'Mike Brown', 'userImage' => '/images/user/user-05.jpg', 'action' => 'requests permission to change', 'project' => 'Project - E-commerce', 'type' => 'Project', 'time' => '20 min ago', 'status' => 'online'],
                    ['id' => 5, 'userName' => 'Emma Davis', 'userImage' => '/images/user/user-06.jpg', 'action' => 'requests permission to change', 'project' => 'Project - Mobile App', 'type' => 'Project', 'time' => '25 min ago', 'status' => 'offline'],
                    ['id' => 6, 'userName' => 'John Smith', 'userImage' => '/images/user/user-07.jpg', 'action' => 'requests permission to change', 'project' => 'Project - Landing Page', 'type' => 'Project', 'time' => '30 min ago', 'status' => 'online'],
                    ['id' => 7, 'userName' => 'Lisa Anderson', 'userImage' => '/images/user/user-08.jpg', 'action' => 'requests permission to change', 'project' => 'Project - Blog System', 'type' => 'Project', 'time' => '35 min ago', 'status' => 'online'],
                    ['id' => 8, 'userName' => 'David Wilson', 'userImage' => '/images/user/user-09.jpg', 'action' => 'requests permission to change', 'project' => 'Project - CRM Dashboard', 'type' => 'Project', 'time' => '40 min ago', 'status' => 'online'],
                ];
            @endphp

            @foreach ($notifications as $notification)
                <li>
                    <a href="#" class="flex gap-3 p-3 border-b border-gray-200 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-white/5" @click="open = false">
                        <span class="relative w-10 h-10">
                            <img src="{{ $notification['userImage'] }}" alt="{{ $notification['userName'] }}" class="rounded-full">
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full border-2 border-white dark:border-gray-800 {{ $notification['status'] === 'online' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                        </span>
                        <span class="flex-1">
                            <span class="block mb-1 text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-medium text-gray-800 dark:text-white">{{ $notification['userName'] }}</span>
                                {{ $notification['action'] }}
                                <span class="font-medium text-gray-800 dark:text-white">{{ $notification['project'] }}</span>
                            </span>
                            <span class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $notification['type'] }}</span>
                                <span class="w-1 h-1 bg-gray-400 rounded-full"></span>
                                <span>{{ $notification['time'] }}</span>
                            </span>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>

        <a href="#" class="flex justify-center p-3 mt-3 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700" @click.prevent="open = false">
            View All Notification
        </a>
    </div>
</div>