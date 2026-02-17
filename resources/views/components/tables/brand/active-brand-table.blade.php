<div x-data="{
    tableRowData: [
        {
            id: 'BR9921',
            BrandName: 'Hope lusk',
            BrandHandle: '@hopeyy',
            emailProtected: 'hopeiusk@gmail.com',
            mobileProtected: '+901234567890',
            joinedAt: '2026-02-16 10:30 AM',
            timeAgo: '1 day ago',
            balance: '15.00 USD',
        },
        {
            id: 'BR9922',
            BrandName: 'Saksham Malik',
            BrandHandle: '@saksham',
            emailProtected: 'saksham.malik@example.com',
            mobileProtected: '+901234567891',
            joinedAt: '2026-02-07 06:43 PM',
            timeAgo: '1 week ago',
            balance: '0.00 USD',
        },
        {
            id: 'BR9923',
            BrandName: 'James brown',
            BrandHandle: '@launcharm',
            emailProtected: 'james.brown@example.com',
            mobileProtected: '+901234567892',
            joinedAt: '2026-02-06 07:09 PM',
            timeAgo: '1 week ago',
            balance: '15.00 USD',
        },
        {
            id: 'BR9924',
            BrandName: 'Kukil Miya',
            BrandHandle: '@kukilmiya',
            emailProtected: 'kukil.miya@example.com',
            mobileProtected: '+901234567893',
            joinedAt: '2026-02-03 11:44 PM',
            timeAgo: '1 week ago',
            balance: '15.00 USD',
        }
    ],
    selectedRows: [],
    selectAll: false,
}">
    <div class="overflow-hidden flex flex-col gap-6">
        <!-- Table -->
        <div class="max-w-full overflow-x-auto border border-gray-100 dark:border-white/[0.05] rounded-lg">
            <table class="w-full border-collapse">
                <!-- Updated Header with Screenshot Colors -->
                <thead class="bg-gray-200 text-[#222] dark:bg-gray-800 dark:text-white/90">
                    <tr>
                        <th class="px-6 py-4 font-bold text-sm text-start uppercase tracking-wider">
                            <div class="flex items-center gap-3">
                                <span>Brand</span>
                            </div>
                        </th>
                        <th class="px-6 py-4 font-bold text-sm text-center uppercase tracking-wider">Email-Mobile</th>
                        <th class="px-6 py-4 font-bold text-sm text-center uppercase tracking-wider">Joined At</th>
                        <th class="px-6 py-4 font-bold text-sm text-center uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-4 font-bold text-sm text-center uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900">
                    <template x-for="row in tableRowData" :key="row.id">
                        <tr class="border-b border-gray-100 dark:border-white/[0.05] hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                            <!-- Brand ID & Name -->
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    
                                    <div>
                                        <span class="block font-bold text-gray-800 dark:text-gray-200" x-text="row.BrandName"></span>
                                        <span class="text-pink-300 font-medium text-sm" x-text="row.BrandHandle"></span>
                                    </div>
                                </div>
                            </td>
                            <!-- Email-Mobile -->
                            <td class="px-6 py-5 text-center">
                                <p class="text-gray-500 text-sm" x-text="row.emailProtected"></p>
                                <p class="text-gray-500 text-sm" x-text="row.mobileProtected"></p>
                            </td>
                            <!-- Joined At -->
                            <td class="px-6 py-5 text-center">
                                <p class="text-gray-600 dark:text-gray-400 font-medium" x-text="row.joinedAt"></p>
                                <p class="text-gray-400 text-sm" x-text="row.timeAgo"></p>
                            </td>
                            <!-- Balance -->
                            <td class="px-6 py-5 text-center">
                                <span class="font-bold text-gray-700 dark:text-gray-200" x-text="row.balance"></span>
                            </td>
                            <!-- Action (Details Button) -->
                            <td class="px-6 py-5 text-center">
                                <a href="#" class="inline-flex items-center gap-2 px-4 py-1.5 border border-pink-500 text-pink-500 rounded-md hover:bg-pink-500 hover:text-white transition-all group">
                                    <svg class="w-5 h-5 stroke-pink-500 group-hover:stroke-white transition-colors" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 8V9M12 11.5V16M18 2H6C3.79086 2 2 3.79086 2 6V18C2 20.2091 3.79086 22 6 22H18C20.2091 22 22 20.2091 22 18V6C22 3.79086 20.2091 2 18 2Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <span class="font-medium">Details</span>
                                </a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>