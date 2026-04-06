{{-- Frontend Packages Create --}}
@extends('frontend.layouts.app')

@section('content')
	<div class="container mx-auto px-4 py-8 max-w-2xl">
		<h1 class="text-3xl font-bold mb-8">Create Package</h1>

		@if ($errors->any())
			<div class="alert alert-error mb-6">
				@foreach ($errors->all() as $error)
					<div>{{ $error }}</div>
				@endforeach
			</div>
		@endif

		<form action="{{ route('frontend.packages.store') }}" method="POST" class="space-y-4">
			@csrf

			<div class="form-group">
				<label class="label">Package Name</label>
				<input type="text" name="name" class="input input-bordered w-full @error('name') error @enderror"
					value="{{ old('name') }}" required>
				@error('name')
					<span class="error text-sm">{{ $message }}</span>
				@enderror
			</div>

			<div class="form-group">
				<label class="label">Description</label>
				<textarea name="description" class="textarea textarea-bordered w-full @error('description') error @enderror"
				 rows="4" required>{{ old('description') }}</textarea>
				@error('description')
					<span class="error text-sm">{{ $message }}</span>
				@enderror
			</div>

			<div class="form-group">
				<label class="label">Platform</label>
				<select name="platform" class="select select-bordered w-full @error('platform') error @enderror" required>
					<option value="">Select Platform</option>
					<option value="instagram" {{ old('platform') === 'instagram' ? 'selected' : '' }}>Instagram</option>
					<option value="tiktok" {{ old('platform') === 'tiktok' ? 'selected' : '' }}>TikTok</option>
					<option value="youtube" {{ old('platform') === 'youtube' ? 'selected' : '' }}>YouTube</option>
					<option value="ugc" {{ old('platform') === 'ugc' ? 'selected' : '' }}>UGC</option>
				</select>
				@error('platform')
					<span class="error text-sm">{{ $message }}</span>
				@enderror
			</div>

			<div class="form-group">
				<label class="label">Base Price</label>
				<input type="number" step="0.01" name="base_price"
					class="input input-bordered w-full @error('base_price') error @enderror" value="{{ old('base_price') }}" required>
				@error('base_price')
					<span class="error text-sm">{{ $message }}</span>
				@enderror
			</div>

			<div class="form-group">
				<label class="label">Currency</label>
				<select name="currency" class="select select-bordered w-full @error('currency') error @enderror">
					<option value="USD" {{ old('currency', 'USD') === 'USD' ? 'selected' : '' }}>USD</option>
					<option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR</option>
				</select>
				@error('currency')
					<span class="error text-sm">{{ $message }}</span>
				@enderror
			</div>

			<div class="form-group">
				<label class="label">Delivery Days</label>
				<input type="number" name="delivery_days"
					class="input input-bordered w-full @error('delivery_days') error @enderror" value="{{ old('delivery_days') }}"
					required>
				@error('delivery_days')
					<span class="error text-sm">{{ $message }}</span>
				@enderror
			</div>

			<div class="pt-6 flex gap-4">
				<button type="submit" class="btn btn-primary">Create Package</button>
				<a href="{{ route('frontend.packages.index') }}" class="btn btn-outline">Cancel</a>
			</div>
		</form>
	</div>
@endsection
