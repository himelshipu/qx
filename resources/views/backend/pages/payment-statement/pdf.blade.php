<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Payment Statement</title>
	<style>
		body {
			font-family: DejaVu Sans, sans-serif;
			font-size: 12px;
			color: #111827;
		}
		h1, h2, h3, p {
			margin: 0;
		}
		.header {
			margin-bottom: 18px;
			padding-bottom: 12px;
			border-bottom: 1px solid #e5e7eb;
		}
		.meta {
			margin-top: 6px;
			color: #6b7280;
		}
		.summary {
			width: 100%;
			border-collapse: collapse;
			margin: 16px 0 20px;
		}
		.summary td {
			border: 1px solid #e5e7eb;
			padding: 10px;
		}
		.table {
			width: 100%;
			border-collapse: collapse;
		}
		.table th, .table td {
			border: 1px solid #e5e7eb;
			padding: 8px;
			vertical-align: top;
		}
		.table th {
			background: #f9fafb;
			text-align: left;
		}
		.text-right {
			text-align: right;
		}
		.muted {
			color: #6b7280;
		}
	</style>
</head>
<body>
	<div class="header">
		<h1>Payment Statement</h1>
		<p class="meta">{{ $influencer->display_name }} | {{ $influencer->user?->email ?? 'N/A' }}</p>
		<p class="meta">Period: {{ $dateRange['start']->format('M d, Y') }} - {{ $dateRange['end']->format('M d, Y') }}</p>
	</div>

	<table class="summary">
		<tr>
			<td>
				<strong>Total Paid</strong><br>
				${{ number_format((float) $totalPaid, 2) }}
			</td>
			<td>
				<strong>Transactions</strong><br>
				{{ $itemCount }}
			</td>
		</tr>
	</table>

	<table class="table">
		<thead>
			<tr>
				<th>Type</th>
				<th>Campaign/Order</th>
				<th>Description</th>
				<th class="text-right">Amount</th>
				<th>Reference</th>
				<th>Marked By</th>
				<th>Date</th>
			</tr>
		</thead>
		<tbody>
			@forelse ($statement as $row)
				<tr>
					<td>{{ $row['type'] }}</td>
					<td>{{ $row['campaign_or_order'] }}</td>
					<td>{{ $row['description'] }}</td>
					<td class="text-right">${{ number_format((float) $row['amount'], 2) }}</td>
					<td>{{ $row['reference'] ?? 'N/A' }}</td>
					<td>{{ $row['marked_by'] }}</td>
					<td>{{ optional($row['paid_date'])->format('M d, Y h:i A') }}</td>
				</tr>
			@empty
				<tr>
					<td colspan="7" class="muted">No paid transactions found for this influencer.</td>
				</tr>
			@endforelse
		</tbody>
	</table>
</body>
</html>