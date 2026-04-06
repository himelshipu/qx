{{-- Frontend Campaigns Edit --}}
@extends('frontend.layouts.app')

@section('content')
	<div class="container mx-auto px-4 py-8 max-w-2xl">
		<h1 class="text-3xl font-bold mb-8">Edit Campaign</h1>

		@if ($errors->any())
			<div class="alert alert-error mb-6">
				@foreach ($errors->all() as $error)
					<div>{{ $error }}</div>
				@endforeach
			</div>
		@endif

		<form action="{{ route('frontend.campaigns.update', $campaign) }}" method="POST" class="space-y-4">
			@csrf
			@method('PUT')

			<div class="form-group">
				<label class="label">Campaign Title</label>
				<input type="text" name="title" class="input input-bordered w-full @error('title') error @enderror"
					value="{{ old('title', $campaign->title) }}" required>
				@error('title')
					<span class="error text-sm">{{ $message }}</span>
				@enderror
			</div>

			<div class="form-group">
				<label class="label">Description</label>
				<textarea name="description" class="textarea textarea-bordered w-full @error('description') error @enderror"
				 rows="4" required>{{ old('description', $campaign->description) }}</textarea>
				@error('description')
					<span class="error text-sm">{{ $message }}</span>
				@enderror
			</div>

			<div class="form-group">
				<label class="label">Campaign Type</label>
				<select name="campaign_type" class="select select-bordered w-full @error('campaign_type') error @enderror" required>
					<option value="product_launch"
						{{ old('campaign_type', $campaign->campaign_type) === 'product_launch' ? 'selected' : '' }}>Product Launch</option>
					<option value="brand_awareness"
						{{ old('campaign_type', $campaign->campaign_type) === 'brand_awareness' ? 'selected' : '' }}>Brand Awareness
					</option>
					<option value="ugc" {{ old('campaign_type', $campaign->campaign_type) === 'ugc' ? 'selected' : '' }}>UGC</option>
				</select>
				@error('campaign_type')
					<span class="error text-sm">{{ $message }}</span>
				@enderror
			</div>

			<div class="grid grid-cols-2 gap-4">
				<div class="form-group">
					<label class="label">Budget Min</label>
					<input type="number" name="budget_min" class="input input-bordered w-full @error('budget_min') error @enderror"
						value="{{ old('budget_min', $campaign->budget_min) }}" required>
					@error('budget_min')
						<span class="error text-sm">{{ $message }}</span>
					@enderror
				</div>

				<div class="form-group">
					<label class="label">Budget Max</label>
					<input type="number" name="budget_max" class="input input-bordered w-full @error('budget_max') error @enderror"
						value="{{ old('budget_max', $campaign->budget_max) }}" required>
					@error('budget_max')
						<span class="error text-sm">{{ $message }}</span>
					@enderror
				</div>
			</div>

			<div class="grid grid-cols-2 gap-4">
				<div class="form-group">
					<label class="label">Start Date</label>
					<input type="date" name="start_date" class="input input-bordered w-full @error('start_date') error @enderror"
						value="{{ old('start_date', $campaign->start_date->format('Y-m-d')) }}" required>
					@error('start_date')
						<span class="error text-sm">{{ $message }}</span>
					@enderror
				</div>

				<div class="form-group">
					<label class="label">End Date</label>
					<input type="date" name="end_date" class="input input-bordered w-full @error('end_date') error @enderror"
						value="{{ old('end_date', $campaign->end_date->format('Y-m-d')) }}" required>
					@error('end_date')
						<span class="error text-sm">{{ $message }}</span>
					@enderror
				</div>
			</div>

			<div class="pt-6 flex gap-4">
				<button type="submit" class="btn btn-primary">Update Campaign</button>
				<a href="{{ route('frontend.campaigns.show', $campaign) }}" class="btn btn-outline">Cancel</a>
			</div>
		</form>
	</div>
@endsection
