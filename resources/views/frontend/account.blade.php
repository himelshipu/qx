{{-- Frontend Account Settings --}}
@extends('frontend.layouts.app')

@section('content')
	<div class="container mx-auto px-4 py-8 max-w-2xl">
		<h1 class="text-3xl font-bold mb-8">Account Settings</h1>

		@if ($errors->any())
			<div class="alert alert-error mb-6">
				<div class="font-bold">Errors:</div>
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		@if (session('success'))
			<div class="alert alert-success mb-6">
				{{ session('success') }}
			</div>
		@endif

		<div class="tabs">
			<input type="radio" name="account_tabs" id="tab_profile" class="tab-toggle" checked />
			<label for="tab_profile" class="tab">Profile Details</label>

			<input type="radio" name="account_tabs" id="tab_password" class="tab-toggle" />
			<label for="tab_password" class="tab">Password</label>

			<div class="tab-content">
				{{-- Profile Details Form --}}
				<form action="{{ route('frontend.account.details.update') }}" method="POST" class="space-y-4">
					@csrf

					<div class="form-group">
						<label class="label">Full Name</label>
						<input type="text" name="name" class="input input-bordered w-full" value="{{ old('name', $user->name) }}"
							required>
						@error('name')
							<span class="error">{{ $message }}</span>
						@enderror
					</div>

					<div class="form-group">
						<label class="label">Email</label>
						<input type="email" name="email" class="input input-bordered w-full" value="{{ old('email', $user->email) }}"
							required>
						@error('email')
							<span class="error">{{ $message }}</span>
						@enderror
					</div>

					<div class="form-group">
						<label class="label">Phone</label>
						<input type="tel" name="phone" class="input input-bordered w-full" value="{{ old('phone', $user->phone) }}">
						@error('phone')
							<span class="error">{{ $message }}</span>
						@enderror
					</div>

					<div class="form-group">
						<label class="label">Bio</label>
						<textarea name="bio" class="textarea textarea-bordered w-full" rows="4">{{ old('bio', $user->bio) }}</textarea>
						@error('bio')
							<span class="error">{{ $message }}</span>
						@enderror
					</div>

					<button type="submit" class="btn btn-primary">Save Changes</button>
				</form>
			</div>

			<div class="tab-content">
				{{-- Password Form --}}
				<form action="{{ route('frontend.account.password.update') }}" method="POST" class="space-y-4">
					@csrf

					<div class="form-group">
						<label class="label">Current Password</label>
						<input type="password" name="current_password" class="input input-bordered w-full" required>
						@error('current_password')
							<span class="error">{{ $message }}</span>
						@enderror
					</div>

					<div class="form-group">
						<label class="label">New Password</label>
						<input type="password" name="password" class="input input-bordered w-full" required>
						@error('password')
							<span class="error">{{ $message }}</span>
						@enderror
					</div>

					<div class="form-group">
						<label class="label">Confirm Password</label>
						<input type="password" name="password_confirmation" class="input input-bordered w-full" required>
					</div>

					<button type="submit" class="btn btn-primary">Update Password</button>
				</form>
			</div>
		</div>
	</div>
@endsection
