<!DOCTYPE html>
<html>

	<head>
		<meta charset="utf-8">
		<title>Payment Statement</title>
		<style>
			* {
				margin: 0;
				padding: 0;
				box-sizing: border-box;
			}

			body {
				font-family: Arial, sans-serif;
				color: #333;
				line-height: 1.6;
			}

			.container {
				max-width: 8.5in;
				margin: 0 auto;
				padding: 20px;
			}

			.header {
				border-bottom: 3px solid #4f46e5;
				padding-bottom: 20px;
				margin-bottom: 30px;
			}

			.logo {
				font-size: 24px;
				font-weight: bold;
				color: #4f46e5;
				margin-bottom: 10px;
			}

			.title {
				font-size: 28px;
				font-weight: bold;
				color: #1f2937;
				margin-bottom: 20px;
			}

			.statement-info {
				display: flex;
				justify-content: space-between;
				margin-bottom: 30px;
				flex-wrap: wrap;
			}

			.info-box {
				flex: 1;
				min-width: 200px;
				margin-right: 20px;
				margin-bottom: 10px;
			}

			.info-label {
				font-weight: bold;
				font-size: 12px;
				color: #6b7280;
				text-transform: uppercase;
				margin-bottom: 5px;
			}

			.info-value {
				font-size: 14px;
				color: #1f2937;
			}

			.summary {
				background-color: #f3f4f6;
				border-left: 4px solid #4f46e5;
				padding: 20px;
				margin-bottom: 30px;
				border-radius: 4px;
			}

			.summary-grid {
				display: grid;
				grid-template-columns: 1fr 1fr 1fr;
				gap: 20px;
				margin-top: 15px;
			}

			.summary-item {
				text-align: center;
			}

			.summary-item-label {
				font-size: 12px;
				color: #6b7280;
				text-transform: uppercase;
				margin-bottom: 5px;
			}

			.summary-item-value {
				font-size: 24px;
				font-weight: bold;
				color: #4f46e5;
			}

			table {
				width: 100%;
				border-collapse: collapse;
				margin-bottom: 20px;
			}

			thead {
				background-color: #f9fafb;
				border-bottom: 2px solid #e5e7eb;
			}

			th {
				padding: 12px;
				text-align: left;
				font-weight: bold;
				font-size: 12px;
				text-transform: uppercase;
				color: #374151;
			}

			td {
				padding: 12px;
				border-bottom: 1px solid #e5e7eb;
				font-size: 13px;
			}

			tbody tr:hover {
				background-color: #f9fafb;
			}

			.type-badge {
				display: inline-block;
				padding: 3px 8px;
				border-radius: 3px;
				font-weight: bold;
				font-size: 11px;
				text-transform: uppercase;
			}

			.type-package {
				background-color: #dbeafe;
				color: #1e40af;
			}

			.type-campaign {
				background-color: #e0e7ff;
				color: #3730a3;
			}

			.amount {
				text-align: right;
				font-weight: bold;
				color: #10b981;
			}

			.date {
				text-align: center;
				color: #6b7280;
			}

			tfoot tr {
				background-color: #f3f4f6;
				border-top: 2px solid #e5e7eb;
			}

			tfoot th,
			tfoot td {
				padding: 12px;
				font-weight: bold;
			}

			.page-break {
				page-break-after: always;
				margin-top: 40px;
			}

			.footer {
				border-top: 1px solid #e5e7eb;
				padding-top: 20px;
				margin-top: 30px;
				text-align: center;
				color: #6b7280;
				font-size: 12px;
			}

			.grand-total {
				background-color: #ecfdf5;
				border-left: 4px solid #10b981;
				padding: 20px;
				margin-bottom: 30px;
				border-radius: 4px;
			}

			.grand-total-grid {
				display: grid;
				grid-template-columns: 1fr 1fr;
				gap: 40px;
			}

			.grand-total-item {
				text-align: center;
			}

			.grand-total-label {
				font-size: 12px;
				color: #6b7280;
				text-transform: uppercase;
				margin-bottom: 5px;
			}

			.grand-total-value {
				font-size: 28px;
				font-weight: bold;
				color: #10b981;
			}

			@media print {
				body {
					padding: 0;
				}
			}
		</style>
	</head>

	<body>
		<div class="container">
			<!-- Header -->
			<div class="header">
				<div class="logo">Rockies</div>
				<div class="title">Payment Statement</div>
			</div>

			<!-- Statement Info -->
			<div class="statement-info">
				<div class="info-box">
					<div class="info-label">Influencer</div>
					<div class="info-value">{{ $influencer->display_name }}</div>
				</div>
				<div class="info-box">
					<div class="info-label">Email</div>
					<div class="info-value">{{ $influencer->user->email }}</div>
				</div>
				<div class="info-box">
					<div class="info-label">Generated Date</div>
					<div class="info-value">{{ now()->format('F d, Y') }}</div>
				</div>
			</div>

			<!-- Summary -->
			<div class="summary">
				<div style="font-weight: bold; margin-bottom: 10px;">Statement Summary</div>
				<div class="summary-grid">
					<div class="summary-item">
						<div class="summary-item-label">Total Paid</div>
						<div class="summary-item-value">${{ number_format($totalPaid, 2) }}</div>
					</div>
					<div class="summary-item">
						<div class="summary-item-label">Total Transactions</div>
						<div class="summary-item-value">{{ $itemCount }}</div>
					</div>
					<div class="summary-item">
						<div class="summary-item-label">Period</div>
						<div class="summary-item-value" style="font-size: 14px;">
							{{ $dateRange['start']->format('M d, Y') }} - {{ $dateRange['end']->format('M d, Y') }}
						</div>
					</div>
				</div>
			</div>

			<!-- Transactions Table -->
			@if ($statement->count() > 0)
				<table>
					<thead>
						<tr>
							<th style="width: 15%;">Type</th>
							<th style="width: 25%;">Campaign/Order</th>
							<th style="width: 30%;">Description</th>
							<th style="width: 15%;" class="amount">Amount</th>
							<th style="width: 15%;" class="date">Date</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($statement as $item)
							<tr>
								<td>
									<span class="type-badge {{ $item['type'] === 'Package Work' ? 'type-package' : 'type-campaign' }}">
										{{ $item['type'] === 'Package Work' ? 'Package' : 'Campaign' }}
									</span>
								</td>
								<td>{{ $item['campaign_or_order'] }}</td>
								<td>{{ $item['description'] }}</td>
								<td class="amount">${{ number_format($item['amount'], 2) }}</td>
								<td class="date">{{ $item['paid_date']->format('M d, Y') }}</td>
							</tr>
						@endforeach
					</tbody>
					<tfoot>
						<tr>
							<th colspan="3" style="text-align: right;">Total:</th>
							<th class="amount">${{ number_format($totalPaid, 2) }}</th>
							<th></th>
						</tr>
					</tfoot>
				</table>
			@else
				<div style="text-align: center; padding: 40px; color: #6b7280;">
					No transactions found for this period.
				</div>
			@endif

			<!-- Grand Total -->
			<div class="grand-total">
				<div class="grand-total-grid">
					<div class="grand-total-item">
						<div class="grand-total-label">Grand Total Paid</div>
						<div class="grand-total-value">${{ number_format($totalPaid, 2) }}</div>
					</div>
					<div class="grand-total-item">
						<div class="grand-total-label">Total Transactions</div>
						<div class="grand-total-value">{{ $itemCount }}</div>
					</div>
				</div>
			</div>

			<!-- Footer -->
			<div class="footer">
				<p>This payment statement was generated on {{ now()->format('F d, Y \a\t H:i A') }}.</p>
				<p>For questions, please contact support@qxmarketplace.com</p>
			</div>
		</div>
	</body>

</html>
