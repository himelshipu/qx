<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-800/50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Package</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Platform</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Price</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Delivery</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Revisions</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Usage</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Updated</th>
                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse ($packages as $package)
                @php
                    $usageCount = $package->cart_items_count + $package->order_items_count;
                @endphp
                <tr class="transition hover:bg-gray-50/70 dark:hover:bg-gray-800/40">
                    <td class="px-4 py-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $package->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ \Illuminate\Support\Str::limit($package->description ?? 'No description', 70) }}
                            </p>
                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                By: {{ $package->createdBy?->name ?? 'System' }}
                            </p>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                        <span class="inline-flex rounded bg-gray-100 px-2 py-1 text-xs font-medium uppercase dark:bg-gray-800">
                            {{ $package->platform }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                        {{ strtoupper($package->currency) }} {{ number_format((float) $package->base_price, 2) }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                        {{ $package->delivery_days ? $package->delivery_days . ' days' : '-' }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                        {{ $package->revisions_included !== null ? $package->revisions_included : '-' }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-xs text-gray-600 dark:text-gray-300">
                            <span class="inline-flex rounded bg-gray-100 px-2 py-1 dark:bg-gray-800">Cart:
                                {{ $package->cart_items_count }}</span>
                            <span class="inline-flex rounded bg-gray-100 px-2 py-1 dark:bg-gray-800">Order:
                                {{ $package->order_items_count }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center">
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input
                                    type="checkbox"
                                    class="peer sr-only"
                                    {{ $package->is_active ? 'checked' : '' }}
                                    data-package-toggle-status
                                    data-toggle-url="{{ route('dashboard.packages.toggle-status', $package) }}" />
                                <div class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-green-300 dark:bg-gray-700 dark:peer-focus:ring-green-800">
                                </div>
                            </label>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $package->updated_at?->format('M d, Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('dashboard.packages.view', $package) }}"
                                class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-blue-50 hover:text-blue-600 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-blue-900/20 dark:hover:text-blue-300"
                                title="View package details">
                                <x-icons.eye class="h-4 w-4" />
                            </a>
                            <a href="{{ route('dashboard.packages.edit', $package) }}"
                                class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
                                title="Edit package">
                                <x-icons.edit class="h-4 w-4" />
                            </a>
                            <form action="{{ route('dashboard.packages.destroy', $package) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" {{ $usageCount > 0 ? 'disabled' : '' }}
                                    data-confirm-title="Delete Package"
                                    data-confirm-message="Delete this package? This action cannot be undone."
                                    data-confirm-button="Delete"
                                    data-confirm-variant="danger"
                                    title="{{ $usageCount > 0 ? 'Cannot delete: package has linked records' : 'Delete package' }}"
                                    class="js-confirmable inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-red-50 hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-red-900/20 dark:hover:text-red-300">
                                    <x-icons.trash class="h-4 w-4" />
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-4 py-12 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No packages found for the current filters.</p>
                        <a href="{{ route('dashboard.packages.create') }}"
                            class="mt-3 inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
                            <x-icons.plus class="h-4 w-4" />
                            Create First Package
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($packages->hasPages())
    <div class="mt-5 border-t border-gray-200 pt-4 dark:border-gray-800">
        {{ $packages->links() }}
    </div>
@endif
