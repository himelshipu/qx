<div class="space-y-8">
	@if ($errors->any())
		<div class="rounded-lg bg-red-50 p-4 dark:bg-red-900/20">
			<div class="flex">
				<div class="flex-shrink-0">
					<svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
						aria-hidden="true">
						<path fill-rule="evenodd"
							d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
							clip-rule="evenodd" />
					</svg>
				</div>
				<div class="ml-3">
					<h3 class="text-sm font-medium text-red-800 dark:text-red-300">There were errors with your submission
					</h3>
					<div class="mt-2 text-sm text-red-700 dark:text-red-400">
						<ul role="list" class="list-disc space-y-1 pl-5">
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				</div>
			</div>
		</div>
	@endif

	<div>
		<label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Package Name</label>
		<input type="text" name="name" id="name"
			class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-600 sm:text-sm"
			value="{{ old('name', $package->name ?? '') }}" placeholder="e.g., 1 Facebook Feed Post">
	</div>

	<div>
		<label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
		<textarea name="description" id="description" rows="4"
		 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-600 sm:text-sm"
		 placeholder="Describe what's included in this package...">{{ old('description', $package->description ?? '') }}</textarea>
	</div>

	<div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
		<div class="sm:col-span-3">
			<label for="platform" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Platform</label>
			<select id="platform" name="platform"
				class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-600 sm:text-sm">
				@foreach ($platformOptions as $option)
					<option value="{{ $option['value'] }}"
						{{ old('platform', $package->platform ?? '') == $option['value'] ? 'selected' : '' }}>
						{{ $option['label'] }}</option>
				@endforeach
			</select>
		</div>

		<div class="sm:col-span-3">
			<label for="base_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price
				(USD)</label>
			<input type="number" name="base_price" id="base_price" step="0.01"
				class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-600 sm:text-sm"
				value="{{ old('base_price', $package->base_price ?? '') }}" placeholder="e.g., 250.00">
		</div>

		<div class="sm:col-span-3">
			<label for="delivery_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery
				(Days)</label>
			<input type="number" name="delivery_days" id="delivery_days"
				class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-600 sm:text-sm"
				value="{{ old('delivery_days', $package->delivery_days ?? '') }}" placeholder="e.g., 7">
		</div>

		<div class="sm:col-span-3">
			<label for="revisions_included" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Revisions
				Included</label>
			<input type="number" name="revisions_included" id="revisions_included"
				class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-600 sm:text-sm"
				value="{{ old('revisions_included', $package->revisions_included ?? '1') }}" placeholder="e.g., 1">
		</div>
	</div>

	<div class="flex items-center">
		<div class="flex h-11 items-center">
			<input id="is_active" name="is_active" type="checkbox" value="1"
				{{ old('is_active', isset($package) && $package->is_active) ? 'checked' : '' }}
				class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
		</div>
		<div class="ml-3 text-sm leading-5">
			<label for="is_active" class="font-medium text-gray-700 dark:text-gray-300">Publish Package</label>
			<p class="text-gray-500 dark:text-gray-400">Make this package visible to brands on the marketplace.</p>
		</div>
	</div>
</div>
