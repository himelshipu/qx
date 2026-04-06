{{-- Frontend Orders Index (Creator View) --}}
@extends('frontend.layouts.app')

@section('content')
	<div class="container mx-auto px-4 py-8">
		<h1 class="text-3xl font-bold mb-8">My Order Items</h1>

		@if ($orders->isEmpty())
			<div class="alert alert-info">
				No orders yet.
			</div>
		@else
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>Order #</th>
							<th>Brand</th>
							<th>Package</th>
							<th>Amount</th>
							<th>Status</th>
							<th>Due Date</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($orders as $order)
							<tr>
								<td>{{ $order->order_number }}</td>
								<td>{{ $order->buyer->brand->name ?? 'N/A' }}</td>
								<td>
									@foreach ($order->items->where('creator_id', auth()->user()->creator?->id) as $item)
										{{ $item->package->name ?? 'N/A' }}@if (!$loop->last)
											,
										@endif
									@endforeach
								</td>
								<td>
									${{ number_format($order->items->where('creator_id', auth()->user()->creator?->id)->sum('unit_price'), 2) }}
								</td>
								<td><span class="badge">{{ ucfirst($order->status) }}</span></td>
								<td>
									{{ optional($order->items->where('creator_id', auth()->user()->creator?->id)->first())->due_date?->format('M d, Y') }}
								</td>
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
