<div class="relative" x-data="{ open: false }" @click.away="open = false">
	{{-- Dropdown Trigger --}}
	<button @click="open = !open" class="flex items-center gap-2 text-gray-700 dark:text-gray-400">
		@if ($avatarUrl)
			<div class="w-10 h-10 rounded-full overflow-hidden">
				<img src="{{ $avatarUrl }}" alt="{{ $user->name ?? 'User' }}" class="w-full h-full object-cover">
			</div>
		@else
			<div
				class="w-10 h-10 rounded-full bg-[#FFE4C4] flex items-center justify-center text-sm font-bold text-black uppercase">
				{{ $initials }}
			</div>
		@endif

		<span>{{ $firstName }}</span>

		<svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
			viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
		</svg>
	</button>

	{{-- Dropdown Menu --}}
	<div x-show="open" x-transition
		class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50"
		style="display: none;">

		<div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
			<p class="font-medium text-gray-800 dark:text-gray-200">{{ $user->name }}</p>
			<p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
		</div>

		<div class="py-1">
			@if ($user->brand)
				<a href="{{ route('brand.profile', ['slug' => $user->slug]) }}"
					class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
					<x-icons.user-circle class="w-5 h-5" />
					View profile
				</a>
				<a href="{{ route('brand.profile.edit', ['slug' => $user->slug]) }}"
					class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
					<x-icons.edit class="w-5 h-5" />
					Edit profile
				</a>
			@elseif($user->creator)
				<a href="{{ route('creator.profile', ['slug' => $user->slug]) }}"
					class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
					<x-icons.user-circle class="w-5 h-5" />
					View profile
				</a>
				<a href="{{ route('creator.profile.edit', ['slug' => $user->slug]) }}"
					class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
					<x-icons.edit class="w-5 h-5" />
					Edit profile
				</a>
			@elseif($user->user_type === 'admin' || $user->user_type === 'moderator')
				<a href="{{ route('dashboard.account.edit', ['slug' => $user->slug]) }}"
					class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
					<x-icons.edit class="w-5 h-5" />
					Account settings
				</a>
			@else
				<a href="{{ route('dashboard.account.edit', ['slug' => $user->slug]) }}"
					class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
					<x-icons.user-circle class="w-5 h-5" />
					View profile
				</a>
				<a href="{{ route('dashboard.account.edit', ['slug' => $user->slug]) }}"
					class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
					<x-icons.edit class="w-5 h-5" />
					Account settings
				</a>
			@endif

			<a href="{{ route('support') }}"
				class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
				<x-icons.check class="w-5 h-5" />
				Support
			</a>
		</div>

		<div class="border-t border-gray-200 dark:border-gray-700 pt-1">
			<form method="POST" action="{{ route('logout') }}">
				@csrf
				<button type="submit"
					class="w-full flex items-center gap-3 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
					<x-icons.logout class="w-5 h-5" />
					Sign out
				</button>
			</form>
		</div>
	</div>
</div>
