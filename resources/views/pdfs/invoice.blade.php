<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>Invoice - {{ $order->order_number }}</title>

		<style>
			@charset "UTF-8";
			
			* {
				font-family: 'DejaVu Sans', 'Tahoma', 'Arial Unicode MS', 'Arial', sans-serif;
			}
			
			.invoice-box {
				max-width: 800px;
				margin: auto;
				padding: 30px;
				border: 1px solid #eee;
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
				font-size: 16px;
				line-height: 24px;
				font-family: 'DejaVu Sans', 'Tahoma', 'Arial Unicode MS', 'Arial', sans-serif;
				color: #555;
			}

			.invoice-box table {
				width: 100%;
				line-height: inherit;
				text-align: left;
			}

			.invoice-box table td {
				padding: 5px;
				vertical-align: top;
			}

			.invoice-box table tr td:nth-child(2) {
				text-align: right;
			}

			.invoice-box table tr.top table td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.top table td.title {
				font-size: 45px;
				line-height: 45px;
				color: #333;
			}

			.invoice-box table tr.information table td {
				padding-bottom: 40px;
			}

			.invoice-box table tr.heading td {
				background: #eee;
				border-bottom: 1px solid #ddd;
				font-weight: bold;
			}

			.invoice-box table tr.details td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.item td {
				border-bottom: 1px solid #eee;
			}

			.invoice-box table tr.item.last td {
				border-bottom: none;
			}

			.invoice-box table tr.total td:nth-child(2) {
				border-top: 2px solid #eee;
				font-weight: bold;
			}

			@media only screen and (max-width: 600px) {
				.invoice-box table tr.top table td {
					width: 100%;
					display: block;
					text-align: center;
				}

				.invoice-box table tr.information table td {
					width: 100%;
					display: block;
					text-align: center;
				}
			}

			/** RTL **/
			.invoice-box.rtl {
				direction: rtl;
				font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
			}

			.invoice-box.rtl table {
				text-align: right;
			}

			.invoice-box.rtl table tr td:nth-child(2) {
				text-align: left;
			}
		</style>
	</head>

	<body>
		<div class="invoice-box">
			<table cellpadding="0" cellspacing="0">
				<tr class="top">
					<td colspan="2">
						<table>
							<tr>
								<td class="title">
									@php
										$product = $order->product;
										$productImage = null;
										if ($product && $product->relationLoaded('media')) {
											$primaryImage = $product->getFirstMedia('images');
											if ($primaryImage) {
												$productImage = $primaryImage->getFullUrl();
											}
										}
									@endphp
									@if($productImage)
										<img
											src="{{ $order->product->media->first()->getFullUrl() }}"
											alt="{{ $product->name ?? 'Product' }}"
											style="width: 100%; max-width: 200px; height: 200px; object-fit: cover; border-radius: 8px;"
										/>
									@else
										<div style="width: 200px; height: 200px; background: #f3f4f6; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 14px;">
											{{ $product ? 'No image' : 'No product' }}
										</div>
									@endif
								</td>

								<td>
									Invoice #: {{ $order->order_number }}<br />
									Created: {{ $order->created_at->format('Y-m-d') }}<br />
									@if($order->delivery_date)
										Due: {{ $order->delivery_date->format('Y-m-d') }}
									@endif
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr class="information">
					<td colspan="2">
						<table>
							<tr>
								<td>
									<strong>Customer:</strong><br />
									{{ $order->customer->name ?? 'Not specified' }}<br />
									@if($order->customer->phone)
										{{ $order->customer->phone }}<br />
									@endif
									@if($order->customer->email)
										{{ $order->customer->email }}
									@endif
								</td>

								<td>
									<strong>Product:</strong><br />
									{{ $product->name ?? 'Not specified' }}
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr class="heading">
					<td>Item</td>
					<td>Price</td>
				</tr>

				<tr class="item last">
					<td>{{ $product->name ?? 'Not specified' }}</td>
					<td>{{ number_format($order->total_price, 2) }} {{ $order->currency ?? 'KWD' }}</td>
				</tr>

				<tr class="total">
					<td></td>
					<td>Total: {{ number_format($order->total_price, 2) }} {{ $order->currency ?? 'KWD' }}</td>
				</tr>
			</table>
		</div>
	</body>
</html>