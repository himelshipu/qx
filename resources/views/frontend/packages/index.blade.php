{{-- Frontend Packages Index --}}
@extends('frontend.layouts.app')

@section('content')
	<div class="container mx-auto px-4 py-8">
		<div class="flex justify-between items-center mb-8">
			<h1 class="text-3xl font-bold">{{ auth()->user()->user_type === 'creator' ? 'My Packages' : 'Available Packages' }}
			</h1>
			@if (auth()->user()->user_type === 'creator')
				<a href="{{ route('frontend.packages.create') }}" class="btn btn-primary">Create Package</a>
			@endif
		</div>

		@if ($packages->isEmpty())
			<div class="alert alert-info">
				No packages available. {{ auth()->user()->user_type === 'creator' ? 'Create your first package!' : '' }}
			</div>
		@else
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
				@foreach ($packages as $package)
					<div class="card bg-white shadow-md rounded-lg p-6">
						<h2 class="text-xl font-semibold mb-2">{{ $package->name }}</h2>
						<p class="text-gray-600 mb-2">{{ $package->platform }}</p>
						<p class="text-lg font-bold text-primary mb-4">${{ number_format($package->base_price, 2) }}</p>
						<div class="flex justify-between">
							<a href="{{ route('frontend.packages.show', $package) }}" class="btn btn-sm btn-outline">View</a>
							@if (auth()->user()->user_type === 'creator' && $package->creator_id === auth()->user()->creator?->id)
								<a href="{{ route('frontend.packages.edit', $package) }}" class="btn btn-sm btn-outline">Edit</a>
							@endif
						</div>
					</div>
				@endforeach
			</div>
		@endif
	</div>
@endsection
