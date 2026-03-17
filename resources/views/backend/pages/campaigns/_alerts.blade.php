@php
	$flashStyles = [
	    'success' =>
	        'border-green-200 bg-green-50 text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300',
	    'error' => 'border-red-200 bg-red-50 text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300',
	    'warning' =>
	        'border-yellow-200 bg-yellow-50 text-yellow-700 dark:border-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300',
	    'info' => 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-300',
	];
@endphp

@foreach ($flashStyles as $flashType => $flashClass)
	@if (session($flashType))
		<div class="rounded-lg border p-3 text-sm {{ $flashClass }}">
			<p>{{ session($flashType) }}</p>
		</div>
	@endif
@endforeach

@if (($showValidationSummary ?? true) && $errors->any())
	<div
		class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
		<p class="font-medium">Please fix the highlighted fields and try again.</p>
		<ul class="mt-2 list-inside list-disc space-y-1">
			@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
@endif
