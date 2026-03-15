@php
	/** @var \App\Models\User|null $moderator */
	$moderator = $moderator ?? null;
	$isEditMode = $moderator !== null;
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
	<div class="space-y-5 lg:col-span-2">
		<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
			<div>
				<label for="name" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
					Full Name <span class="text-red-500">*</span>
				</label>
				<input id="name" name="name" type="text" value="{{ old('name', $moderator?->name) }}" required
					placeholder="e.g., Alex Martin"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('name')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<label for="email" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
					Email <span class="text-red-500">*</span>
				</label>
				<input id="email" name="email" type="email" value="{{ old('email', $moderator?->email) }}" required
					placeholder="moderator@example.com"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('email')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<label for="phone" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
				<input id="phone" name="phone" type="text" value="{{ old('phone', $moderator?->phone) }}"
					placeholder="+1 202 555 0100"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('phone')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<label for="password" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
					{{ $isEditMode ? 'New Password' : 'Password' }}
					@if (!$isEditMode)
						<span class="text-red-500">*</span>
					@endif
				</label>
				<input id="password" name="password" type="password" {{ $isEditMode ? '' : 'required' }}
					placeholder="{{ $isEditMode ? 'Leave blank to keep current password' : 'Enter secure password' }}"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('password')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<label for="password_confirmation" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
					Confirm Password
					@if (!$isEditMode)
						<span class="text-red-500">*</span>
					@endif
				</label>
				<input id="password_confirmation" name="password_confirmation" type="password" {{ $isEditMode ? '' : 'required' }}
					placeholder="Confirm password"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
			</div>
		</div>
	</div>

	<div class="space-y-5 rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
		<h4 class="text-sm font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Account Flags</h4>

		<div class="rounded-lg border border-gray-200 bg-white px-3 py-3 dark:border-gray-700 dark:bg-gray-900">
			<input type="hidden" name="is_active" value="0">
			<label for="is_active" class="flex cursor-pointer items-center justify-between gap-3">
				<span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active Status</span>
				<input id="is_active" name="is_active" type="checkbox" value="1"
					{{ old('is_active', $moderator?->is_active ?? true) ? 'checked' : '' }}
					class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-500 dark:border-gray-600 dark:bg-gray-800">
			</label>
			@error('is_active')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>
	</div>
</div>
