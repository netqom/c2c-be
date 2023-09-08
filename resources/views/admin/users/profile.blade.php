@extends('layouts.admin')
@section('content')

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
	<div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
		<div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
			<!--begin::Info-->
			<div class="d-flex align-items-center flex-wrap mr-1">
				<!--begin::Page Heading-->
				<div class="d-flex align-items-baseline mr-5">
					<!--begin::Breadcrumb-->
					<ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
						<li class="breadcrumb-item">
							<a href="{{ admin_url()}}" class="text-muted">Dashboard</a>
						</li>
						<li class="breadcrumb-item">
							<a href="{{ admin_url('users')}}" class="text-muted">Users</a>
						</li>
						<li class="breadcrumb-item active">
							<a href="javascript:;" class="">User Profile</a>
						</li>
					</ul>
					<!--end::Breadcrumb-->
				</div>
				<!--end::Page Heading-->
			</div>
			<!--end::Info-->
			
		</div>
	</div>
	<!--end::Subheader-->
	
    <!--begin::Entry-->
	<div class="d-flex flex-column-fluid">
		<div class=" container ">
			<div class="card card-custom">
				<div class="card-body">
					<div class="d-flex mb-9">
						<div class="flex-shrink-0 mr-7 mt-lg-0 mt-3">
							<div class="symbol symbol-50 symbol-lg-120">
								<img src="{{ $user->display_user_image }}" alt="image">
							</div>
							<div class="symbol symbol-50 symbol-lg-120 symbol-primary d-none">
								<span class="font-size-h3 symbol-label font-weight-boldest">JM</span>
							</div>
						</div>
						<div class="flex-grow-1">
							<div class="d-flex justify-content-between flex-wrap mt-1">
								<div class="d-flex mr-3">
									<a href="#" class="text-dark-75 text-hover-primary font-size-h5 font-weight-bold mr-3">{{ $user->name }}</a>
									<a href="#"><i class="flaticon2-correct text-success font-size-h5"></i></a>
								</div>
								<div class="card-toolbar">
									<!--begin::Button-->
									<a href="{{ route('admin.watch-user-thread',$user->id)}}" class="btn btn-light-primary btn-sm font-weight-bolder"><i class="la la-eye"></i>Communication Thread</a>
									<!--end::Button-->
								</div>
							</div>
							<div class="d-flex flex-wrap justify-content-between mt-1">
								<div class="d-flex flex-column flex-grow-1 pr-8">
									<div class="d-flex flex-wrap mb-4">
										<a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2"><i class="flaticon2-new-email mr-2 font-size-lg"></i>{{ $user->email }}</a>
										<a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2"><i class="flaticon2-calendar-3 mr-2 font-size-lg"></i>{{ $user->phone }}</a>
										@if($user->address)
										<a href="#" class="text-dark-50 text-hover-primary font-weight-bold"><i class="flaticon2-placeholder mr-2 font-size-lg"></i>{{ $user->address ? $user->address : ''}} </a>
										@endif
									</div>
								</div>
							</div>
							<span class="font-weight-bold text-dark-50">{{ $user->about_me }}</span><br/>
							<span class="font-weight-bold text-dark-50">{{ $user->tag_line }}</span>
						</div>
					</div>
					<div class="separator separator-solid"></div>
					<div class="d-flex align-items-center flex-wrap mt-8">						
							<div class="mr-5 mb-2 rounded linked-card @if( $tab==null || $tab=='products') active-card @endif ">
								<a class="p-3 align-items-center d-flex flex-lg-fill" href="{{admin_url('users/profile/'.$user->id.'/products')}}">
								<span class="mr-4">
									<i class="flaticon2-list-2 display-4  font-weight-bold "></i>
								</span>
								<div class="d-flex flex-column ">
									<span class="font-weight-bolder font-size-sm"   >Active Products</span>
									<span class="font-weight-bolder font-size-h5 "><span class=" font-weight-bold"></span>{{ $product_count }}</span>
								</div>
								</a>
							</div>
						
						<div class="mr-5 mb-2 rounded linked-card @if( $tab=='orders') active-card @endif">
							<a class="p-3 align-items-center d-flex flex-lg-fill"  href="{{admin_url('users/profile/'.$user->id.'/orders')}}" id="link-sold-products" data-order-type="{{config('const.order_types')[3]}}">
							
								<span class="mr-4">
									<i class="flaticon-cart display-4  font-weight-bold"></i>
								</span>
								<div class="d-flex flex-column ">
									<span class="font-weight-bolder font-size-sm" >Sold Products</span>
									<span class="font-weight-bolder font-size-h5 "><span class=" font-weight-bold"></span>{{ $order_count }}</span>
								</div>
							</a>
						</div>
						<div class="mr-5 mb-2 rounded linked-card @if( $tab=='purchased') active-card @endif">
							<a class="p-3 align-items-center d-flex flex-lg-fill" href="{{admin_url('users/profile/'.$user->id.'/purchased')}}" id="link-purchased-products" data-order-type="{{config('const.order_types')[2]}}">
								<span class="mr-4">
									<i class="flaticon-cart display-4 font-weight-bold"></i>
								</span>
								<div class="d-flex flex-column">
									<span class="font-weight-bolder font-size-sm" >Purchased Products</span>
									<span class="font-weight-bolder font-size-h5"><span class=" font-weight-bold"></span>{{ $purchase_count}}</span>
								</div>
							</a>
						</div>
						<div class="align-items-center d-flex flex-lg-fill mb-2 mr-5 p-3 rounded non-linked-card">
							<span class="mr-4">
								<i class="flaticon-pie-chart display-4  font-weight-bold"></i>
							</span>
							<div class="d-flex flex-column">
								<span class="font-weight-bolder font-size-sm">Revenue</span>
								<span class="font-weight-bolder font-size-h5"><span class=" font-weight-bold">$</span>{{ $revenue }}</span>
							</div>
						</div>
						<div class="align-items-center d-flex flex-lg-fill mb-2 mr-5 p-3 rounded non-linked-card">
							<span class="mr-4">
								<i class="flaticon-star display-4  font-weight-bold"></i>
							</span>
							<div class="d-flex flex-column flex-lg-fill">
								<span class= "font-weight-bolder font-size-sm">Avg. Ratings</span>
								<span class="font-weight-bolder font-size-h5"><span class="font-weight-bold"></span>{{$user->avg_rating }}</span>
							</div>
						</div>
						<div class="align-items-center d-flex flex-lg-fill mb-2 mr-5 p-3 rounded non-linked-card">
							<span class="mr-4">
								<i class="flaticon-chat-1 display-4  font-weight-bold"></i>
							</span>
							<div class="d-flex flex-column">
								<span class="font-weight-bolder font-size-sm">Rating Count</span>
								<span class="font-weight-bolder font-size-h5"><span class="font-weight-bold"></span>{{ $user->rating_count }}</span>
							</div>
						</div>
					</div>
              </div>
            </div> 	
			<div class="card card-custom">
				 <!--begin::Header-->
				 <div class="card-header">	
                    <div class="card-title">
                        <h3 class="card-label">@if($tab==null || $tab=='products') Products @endif
						@if($tab=='orders') Orders @endif 
							List
                    </div>
					<div class="card-toolbar">
						<!--begin::Button-->
						<!-- <a href="{{ admin_url('products/add-edit/0') }}" class="btn btn-light-primary btn-sm font-weight-bolder"><i class="la la-plus"></i>Add Product</a> -->
						<!--end::Button-->
					</div>
                </div>
                <!--end::Header-->
				<div class="card-body"> 
		            <!--begin::Search Form-->
					<div class="mb-7">
						<div class="row align-items-center">
							<div class="col-lg-9 col-xl-8">
								<div class="row align-items-center">
									<div class="col-md-4 my-2 my-md-0">
										<div class="input-icon">
											<input type="text" class="form-control" placeholder="Search..." id="kt_datatable_search_query" />
											<span>
												<i class="flaticon2-search-1 text-muted"></i>
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!--end::Search Form-->
					@if($tab==null || $tab=='products')	
				    <div class="datatable datatable-bordered datatable-head-custom" id="kt_productTable">
                    </div>
					@endif
					@if($tab=='orders')	
					<div class="datatable datatable-bordered datatable-head-custom" id="kt_ordersTable"></div>
					@endif
					@if($tab=='purchased')	
					<div class="datatable datatable-bordered datatable-head-custom" id="kt_purchasedTable"></div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>



@endsection
@push('scripts')
<script src="{{ asset('public/admin_assets/js/common.js') }}"></script>
<script type="text/javascript">
    //  var data_url = "{{ admin_url('users/list-data') }}";
	//  var url_page_base = "{{ admin_url('users') }}";
   var tab = "{{$tab}}";
   if(!tab || tab=='products'){	
	var data_url = "{{ admin_url('products/list-data') }}";
	var url_page_base = "{{ admin_url('products') }}";
	var url_product_detail = "{{ admin_url('products/detail') }}";
	var url_activate_deactivate = "{{admin_url('products/change-status')}}";
   }
   if(tab=='orders'){ 
    var data_url = "{{ admin_url('orders/list-data') }}";
    var url_page_base = "{{ admin_url('orders') }}";	
	var order_type = $("#link-sold-products").data('order-type');   
   }
   if(tab=='purchased'){ 
    var data_url = "{{ admin_url('orders/list-data') }}";
    var url_page_base = "{{ admin_url('orders') }}";	   
	var order_type = $("#link-purchased-products").data('order-type'); 
  }
   var user_id = "{{encryptDataId($user->id)}}";
   var token = '{{ csrf_token() }}';
</script>
<!-- <script src="{{ asset('public/admin_assets/js/users_datatable.js') }}"></script> -->
 @if($tab==null || $tab=='products')
  <script src="{{ asset('public/admin_assets/js/user_products_datatable.js') }}"></script>
 @endif
 @if($tab=='orders')
 <script src="{{ asset('public/admin_assets/js/user_orders_datatable.js') }}"></script>
 @endif
 @if($tab=='purchased')
 <script src="{{ asset('public/admin_assets/js/user_purchased_datatable.js') }}"></script>
 @endif
@endpush