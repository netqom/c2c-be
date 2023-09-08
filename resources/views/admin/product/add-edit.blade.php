@extends('layouts.admin')

@section('content')
<style>
.dropzone {
    min-height: auto;
    padding: 1.5rem 1.75rem;
    text-align: center;
    cursor: pointer;
    border: 1px dashed #009ef7;
    background-color: #f1faff;
    border-radius: 0.475rem !important;
	width: 100%;
}
.dropzone .dz-remove {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 1.65rem;
    width: 1.65rem;
    font-size: 1rem;
    text-indent: -9999px;
    white-space: nowrap;
    position: absolute;
    z-index: 2;
    background-color: #FFFFFF !important;
    box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075);
    border-radius: 100%;
    top: -0.825rem;
    right: -0.825rem;
}
.dropzone .dz-preview .dz-image {
    border-radius: 20px;
    overflow: hidden;
    width: 100px;
    height: 100px;
    position: relative;
    display: block;
    z-index: 10;
}
</style>
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
							<a href="{{ admin_url('products') }}" class="text-muted">Product</a>
						</li>
						<li class="breadcrumb-item">
							<a href="#" class="">{{ $id != 0 ? 'Edit' : 'Add' }} Product</a>
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
		<!--begin::Container-->
		<div class="container">
			@include('admin.alerts.simple-alert')
			<!--begin::Card-->
			<div class="col-xl-12">
				<!--begin::Card-->
				<div class="card card-custom gutter-b">
					<div class="card-header">
						<div class="card-title">
							<h3 class="card-label">{{ $id != 0 ? 'Edit' : 'Add' }} Product</h3>
						</div>
					</div>
					<div class="card-body">
						<!--begin::Example-->
						<div class="example mb-10">
							<form class="form" id="add-edit-product" action="{{ admin_url('products/add-update') }}" method="POST" enctype="multipart/form-data">
								<input type="hidden" name="item_id" value="{{ $id }}">
								<div class="row">
									<div class="col-md-12">
										<div class="card card-custom">
											<div class="card-body">
												<div class="row">
													<div class="form-group col-md-12">
														<label class="form-control-label">Title</label>
														<input type="text" name="title" class="form-control" placeholder="Enter Title" value="{{ !empty($data->title) ? $data->title : old('title') }}" required>
													</div>
												</div>
												<div class="row">
													<div class="form-group col-md-12">
														<label class="form-control-label">Description</label>
														<textarea id="description" class="summernote" name="description" required>{{ !empty($data->description) ? $data->description : old('description') }}</textarea>
													</div>
												</div>
												<div class="row">
													<div class="form-group col-md-12">
														<label class="form-control-label">Product Category</label>
														<select name="category_id[]" id="category_id" class="form-select col-md-12" multiple="multiple" required>
															@foreach($categories as $category)
																<option value="{{ $category->id }}" {{in_array($category->id, $selectedCategories) ? 'selected':''}}>{{ $category->name }}</option>
															@endforeach
														</select>
													</div>
												</div>
												<div class="row">
													<div class="form-group col-md-12">
														<label class="form-control-label">Price</label>
														<input type="number" name="price" class="form-control" placeholder="Enter Price" value="{{ !empty($data->price) ? $data->price : old('price') }}" required>
													</div>
												</div>
												<div class="row">
													<div class="form-group col-md-12">
														<label class="form-control-label">Product Status</label>
														<select class="form-control col-md-12" name="status" required>
															<option value="">Select Status</option>
															<option value="1" @if($data->status == 1) selected @endif>Active</option>
															<option value="0" @if($data->status == 0) selected @endif>In-Active</option>
														</select>
													</div>
												</div>
												<div class="row">
													<div class="form-group col-md-12">
														<label class="form-control-label">Tags</label>
														<input id="kt_tagify_1" class="form-control tagify" name='tags' value="{{ !empty($data->tags) ? $data->tags : old('tags') }}" placeholder='type...'/>
													</div>
												</div>
												<div class="alert alert-success alert-secondary" style="padding: 0.5rem 1rem;">
													<div class="alert-text text-center">Specification Section</div>
												</div>
												<div class="row">
													<!-- <div class="form-group col-md-4">
														<label class="form-control-label">Item</label>
														<input type="text" name="game" class="form-control" placeholder="Enter Item" value="{{ !empty($data->game) ? $data->game : old('game') }}" required>
													</div> -->
													<div class="form-group col-md-6">
														<label class="form-control-label">Available Quantity</label>
														<input type="number" name="quantity" class="form-control" placeholder="Enter Quantity" value="{{ !empty($data->quantity) ? $data->quantity : old('quantity') }}" required>
													</div>
													{{--<div class="form-group col-md-4">
														<label class="form-control-label">Item Type</label>
														<select class="form-control" name="item_type" required>
															<option value="">Select Item Type</option>
															@foreach($item_types as $key => $value)
																<option value="{{ $key }}" @if($data->item_type == $key) selected @endif>{{ $value }}</option>
															@endforeach
														</select>
													</div>--}}
													<div class="form-group col-md-6">
														<label class="form-control-label">Estimated Delivery Time</label>
														<select class="form-control" name="delivery_time" required>
															@php $estimate_delivery_time = config('const.estimate_delivery_time'); @endphp
															<option value="">Select Estimated Delivery Time</option>
															@foreach($estimate_delivery_time as $value)
																<option value="{{ $value['id'] }}" @if($data->delivery_time == $value['id']) selected @endif>{{ $value['value'] }}</option>
															@endforeach
														</select>
													</div>
												</div>
												<div class="row">
													
													<div class="form-group col-md-6">
														<label class="form-control-label">Shipping options</label>
														<select class="form-control" name="delivery_method" id="delivery_method" required>
															<option value="">Select Shipping options</option>
															@foreach($delivery_methods as $key => $value)
																<option value="{{ $key }}" @if($data->delivery_method == $key) selected @endif>{{ $value }}</option>
															@endforeach
														</select>
													</div>
													<div class="form-group col-md-6 @if( empty($data->delivery_method) ) d-none @elseif($data->delivery_method==2) d-none @endif" id="wrap-shipping-price">
														<label class="form-control-label">Shipping price</label>
														<input type="number" name="delivery_price" class="form-control" placeholder="Enter Quantity" value="{{ !empty($data->delivery_price) ? $data->delivery_price : old('delivery_price') }}" min="0" required>
													</div>
												</div>
												<div class="alert alert-success alert-secondary" style="padding: 0.5rem 1rem;">
													<div class="alert-text text-center">Product Images</div>
												</div>															
												<div class="row">
													 <!--begin::Dropzone-->
													<div class="dropzone" id="kt_dropzonejs_example_1" data-item-id="{{ $id }}" data-limit="6" data-form-id="add-edit-product" data-submit="add-update" data-uploaded="get-uploaded-files" data-delete="delete-files">
														<!--begin::Message-->
														<div class="dz-message needsclick">
															<!--begin::Icon-->
															<i class="fa fa-sharp fa-solid fa-file-arrow-up text-primary fs-3x" aria-hidden='true'></i>
															<!--end::Icon-->

															<!--begin::Info-->
															<div class="ms-4">
																<h3 class="fs-5 fw-bold text-gray-900 mb-1">Drop files here or click to upload.</h3>
																<span class="fs-7 fw-semibold text-gray-400">Upload up to 10 files</span>
															</div>
															<!--end::Info-->
														</div>
													</div>
													<!--end::Dropzone-->
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<div class="card-footer">
										<a href="javascript:;" id="submit_form" class="btn btn-success font-weight-bold mr-2">Submit</a>
										<a href="{{ admin_url('products') }}" class="btn btn-light-warning font-weight-bold">Cancel</a>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
					
			</div>
		</div>
	</div>
@endsection
@push('scripts')
	<script src="{{ asset('public/admin_assets/custom_js/dropzone.js') }}"></script>
	<script>
		// Initialization
		var PAGE_BASE        = "{{ admin_url('products') }}";
		
		jQuery(document).ready(function() {
			$('.summernote').summernote({
				height: 150
			});
			
			$('#category_id').select2({
				 placeholder: "Select Category",
			});
			
			// init Tagify script on the above inputs
			new Tagify(document.getElementById('kt_tagify_1'));
			//Images Plugin
			createDropZoneInstance($('.dropzone'), PAGE_BASE);
		});
		$("#delivery_method").on("change",function(){
            if( $(this).val()==1 ){
              $("#wrap-shipping-price").removeClass('d-none');
			}else{
              $("#wrap-shipping-price").addClass('d-none');
			}
		});
	</script>
@endpush