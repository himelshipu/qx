{{-- Frontend Orders Index (Brand View) --}}
@extends('frontend.layouts.app')

@section('content')
	<div class="container mx-auto px-4 py-8">
		<h1 class="text-3xl font-bold mb-8">My Orders</h1>

		@if ($orders->isEmpty())
			<div class="alert alert-info">
				No orders yet. <a href="{{ route('frontend.packages.index') }}" class="link">Browse packages</a> to get started!
			</div>
		@else
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>Order #</th>
							<th>Creator</th>
							<th>Total</th>
							<th>Status</th>
							<th>Date</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($orders as $order)
							<tr>
								<td>{{ $order->order_number }}</td>
								<td>
									@foreach ($order->items as $item)
										{{ $item->creator->user->name ?? 'N/A' }}@if (!$loop->last)
											,
										@endif
									@endforeach
								</td>
								<td>${{ number_format($order->total_amount, 2) }}</td>
								<td><span class="badge">{{ ucfirst($order->status) }}</span></td>
								<td>{{ $order->created_at->format('M d, Y') }}</td>
								<td>
									<a href="{{ route('frontend.orders.show', $order) }}" class="btn btn-sm btn-outline">View</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		@endif
	</div>
@endsection
