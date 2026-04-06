{{-- Frontend Campaigns Show --}}
@extends('frontend.layouts.app')

@section('content')
	<div class="container mx-auto px-4 py-8">
		<div class="mb-8">
			<a href="{{ route('frontend.campaigns.index') }}" class="link">← Back to Campaigns</a>
		</div>

		<div class="bg-white shadow-md rounded-lg p-8 space-y-6">
			<div>
				<h1 class="text-4xl font-bold">{{ $campaign->title }}</h1>
				<p class="text-gray-600 mt-2">{{ $campaign->campaign_type }}</p>
			</div>

			<div>
				<h2 class="text-xl font-semibold mb-3">Details</h2>
				<div class="grid grid-cols-2 gap-4">
					<div>
						<p class="text-gray-600">Status</p>
						<p class="font-semibold">{{ ucfirst($campaign->status) }}</p>
					</div>
					<div>
						<p class="text-gray-600">Budget</p>
						<p class="font-semibold">${{ number_format($campaign->budget_min, 0) }} -
							${{ number_format($campaign->budget_max, 0) }}</p>
					</div>
					<div>
						<p class="text-gray-600">Start Date</p>
						<p class="font-semibold">{{ $campaign->start_date->format('M d, Y') }}</p>
					</div>
					<div>
						<p class="text-gray-600">End Date</p>
						<p class="font-semibold">{{ $campaign->end_date->format('M d, Y') }}</p>
					</div>
				</div>
			</div>

			<div>
				<h2 class="text-xl font-semibold mb-3">Description</h2>
				<p class="text-gray-700">{{ $campaign->description }}</p>
			</div>

			@if (auth()->user()->user_type === 'brand' && $campaign->created_by_user_id === auth()->user()->id)
				<div class="pt-6 border-t">
					<a href="{{ route('frontend.campaigns.edit', $campaign) }}" class="btn btn-primary">Edit Campaign</a>
				</div>
			@endif
		</div>
	</div>
@endsection
