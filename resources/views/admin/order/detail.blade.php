@extends('layouts.admin')

@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
		<div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
			<div class="d-flex align-items-center flex-wrap mr-1">
				<div class="d-flex align-items-baseline mr-5">
					<ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
						<li class="breadcrumb-item">
							<a href="{{ admin_url()}}" class="text-muted">Dashboard</a>
						</li>
						<li class="breadcrumb-item">
							<a href="{{ admin_url('orders') }}" class="text-muted">Orders</a>
						</li>
						<li class="breadcrumb-item">
							<a href="#" class="">Order Details</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="d-flex flex-column-fluid">
		<div class="container">
			@include('admin.alerts.simple-alert')
			<div class="flex-row-fluid ml-lg-8">
        <div class="card card-custom gutter-b">
            <div class="card-body p-0">
                <div class="row justify-content-center py-8 px-8 py-md-27 px-md-0">
                    <div class="col-md-10">
                        <div class="d-flex justify-content-between pb-10 pb-md-20 flex-column flex-md-row">
                            <h1 class="display-4 font-weight-boldest mb-10">ORDER DETAILS</h1>
                            <div class="d-flex flex-column align-items-md-end px-0">
                                <a href="#" class="mb-5">
                                    <img src="/metronic/theme/html/demo1/dist/assets/media/logos/logo-dark.png" alt="">
                                </a>
                                <!-- <span class=" d-flex flex-column align-items-md-end opacity-70">
                                    <span>{{ $data->address }}</span>
                                    <span>Mississippi 96522</span>
                                </span> -->
                            </div>
                        </div>
                        <div class="border-bottom w-100"></div>
                        <div class="d-flex justify-content-between pt-6">
                            <div class="d-flex flex-column flex-root">
                                <span class="font-weight-bolder mb-2">ORDER DATE</span>
                                <span class="opacity-70">{{ date('M d, Y', strtotime($data->created_at)) }}</span>
                            </div>
                            <div class="d-flex flex-column flex-root">
                                <span class="font-weight-bolder mb-2">ORDER NO.</span>
                                <span class="opacity-70">{{ $data->uuid }}</span>
                            </div>
                            <div class="d-flex flex-column flex-root">
                                <span class="font-weight-bolder mb-2">DELIVERED TO.</span>
                                <span class="opacity-70">{{ $data->address }}</span>
                            </div>
                        </div>
                    </div>
                </div>
				@php $product = $data->product; @endphp
                <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                    <div class="col-md-10">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="pl-0 font-weight-bold text-muted  text-uppercase">Ordered Items</th>
                                        <th class="text-right font-weight-bold text-muted text-uppercase">Qty</th>
                                        <th class="text-right font-weight-bold text-muted text-uppercase">Unit Price</th>
                                        <th class="text-right font-weight-bold text-muted text-uppercase">Shipping Price</th>
                                        <th class="text-right pr-0 font-weight-bold text-muted text-uppercase">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="font-weight-boldest">
                                        <td class="border-0 pl-0 pt-7 d-flex align-items-center">
                                            <div class="symbol symbol-40 flex-shrink-0 mr-4 bg-light">
                                                <div class="symbol-label" style="background-image: url({!! $product->display_path !!})"></div>
                                            </div>
                                            {{ $product->title }}
                                        </td>
                                        <td class="text-right pt-7 align-middle">1</td>
                                        <td class="text-right pt-7 align-middle">${{ $data->price }}</td>
                                        <td class="text-right pt-7 align-middle">${{ number_format($data->delivery_price, 2, '.', ''); }}</td>
                                        <td class="text-primary pr-0 pt-7 text-right align-middle">${{  $data->amount }}</td>
                                    </tr>
                                   
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center bg-gray-100 py-8 px-8 py-md-10 px-md-0 mx-0">
                    <div class="col-md-10">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="font-weight-bold text-muted  text-uppercase">PAYMENT TYPE</th>
                                        <th class="font-weight-bold text-muted  text-uppercase">PAYMENT STATUS</th>
                                        <th class="font-weight-bold text-muted  text-uppercase">PAYMENT DATE</th>
                                        <th class="font-weight-bold text-muted  text-uppercase text-right">TOTAL PAID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="font-weight-bolder">
                                        <td>{{ $data->payment_method_name==1 ? 'Credit Card' : 'Stripe' }}</td>
                                        <td>{{ $data->payment_status_name }}</td>
                                        <td>{{ date('M d, Y', strtotime($data->created_at)) }}</td>
                                        <td class="text-primary font-size-h3 font-weight-boldest text-right">${{ $data->amount }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                    <div class="col-md-10">
                        <div class="d-flex justify-content-between">
                            <a class="btn btn-light-primary font-weight-bold" href="{{admin_url('orders/export-order/'.$data->id)}}">Download PDF</a>
                            <button type="button" class="btn btn-primary font-weight-bold" onclick="window.print();">Print Order Details</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>		
		</div>
	</div>
</div>
@endsection
