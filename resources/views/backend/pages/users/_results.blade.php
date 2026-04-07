<div id="users-results">
	<div class="overflow-x-auto">
		<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
			<thead class="bg-gray-50 dark:bg-gray-800/50">
				<tr>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">User
					</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Type
					</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
						Email</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
						Phone</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
						Location</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
						Status</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
						Joined</th>
					<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
						Actions</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
				@forelse ($users as $user)
					@php
						$previewPath = $user->profile_image_path ?: $user->cover_image_path;
						$previewUrl = null;
						if (!empty($previewPath)) {
						    $isExternal = str_starts_with($previewPath, 'http://') || str_starts_with($previewPath, 'https://');
						    $previewUrl = $isExternal ? $previewPath : asset($previewPath);
						}

						$viewUrl = null;
						$editUrl = null;

						if ($user->user_type === 'brand' && $user->brand) {
						    $viewUrl = route('dashboard.brands.view', $user->brand);
						    $editUrl = route('dashboard.brands.edit', $user->brand);
						} elseif ($user->user_type === 'influencer' && $user->influencer) {
						    $viewUrl = route('dashboard.influencers.view', $user->influencer);
						    $editUrl = route('dashboard.influencers.edit', $user->influencer);
						} elseif ($user->user_type === 'moderator') {
						    $viewUrl = route('dashboard.moderators.show', $user);
						    $editUrl = route('dashboard.moderators.edit', $user);
						}
					@endphp
					<tr class="transition hover:bg-gray-50/70 dark:hover:bg-gray-800/40">
						<td class="px-4 py-3">
							<div class="flex items-center gap-3">
								<div
									class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
									@if ($previewUrl)
										<img src="{{ $previewUrl }}" alt="{{ $user->name }}" class="h-10 w-10 object-cover">
									@else
										<span
											class="text-sm font-semibold text-gray-500 dark:text-gray-300">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</span>
									@endif
								</div>
								<div>
									<p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">ID: #{{ $user->id }}</p>
								</div>
							</div>
						</td>
						<td class="px-4 py-3">
							<span
								class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->user_type === 'brand' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : ($user->user_type === 'influencer' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' : ($user->user_type === 'moderator' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300')) }}">
								{{ ucfirst($user->user_type) }}
							</span>
						</td>
						<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $user->email }}</td>
						<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $user->phone ?: 'N/A' }}</td>
						<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
							{{ $user->city ?: 'N/A' }}{{ $user->country ? ', ' . $user->country : '' }}
						</td>
						<td class="px-4 py-3">
							<div class="flex items-center">
								<label class="relative inline-flex cursor-pointer items-center">
									<input type="checkbox" {{ $user->is_active ? 'checked' : '' }}
										onchange="toggleUserStatus({{ $user->id }}, this)" class="peer sr-only" />
									<div
										class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-green-300 dark:bg-gray-700 dark:peer-focus:ring-green-800">
									</div>
								</label>
							</div>
						</td>
						<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $user->created_at?->format('M d, Y') }}</td>
						<td class="px-4 py-3">
							<div class="flex items-center justify-end gap-2">
								@if ($viewUrl)
									<a href="{{ $viewUrl }}"
										class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
										title="View user resource">
										<x-icons.eye class="h-4 w-4" />
									</a>
								@endif

								@if ($editUrl)
									<a href="{{ $editUrl }}"
										class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
										title="Edit user resource">
										<x-icons.edit class="h-4 w-4" />
									</a>
								@endif

								@if (!$viewUrl && !$editUrl)
									<span class="text-xs text-gray-400 dark:text-gray-500">N/A</span>
								@endif
							</div>
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="8" class="px-4 py-12 text-center">
							<p class="text-sm text-gray-500 dark:text-gray-400">No users found for the current filters.</p>
						</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>

	@if ($users->hasPages())
		<div class="mt-5 border-t border-gray-200 pt-4 dark:border-gray-800">
			{{ $users->links() }}
		</div>
	@endif
</div>
