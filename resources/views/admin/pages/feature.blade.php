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
							<a href="{{ admin_url('content-management/pages') }}" class="text-muted">Page</a>
						</li>
						<li class="breadcrumb-item">
							<a href="#" class="">{{ $id != 0 ? 'Edit' : 'Add' }} Page</a>
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
							<h3 class="card-label">{{ $id != 0 ? 'Edit' : 'Add' }} Page</h3>
						</div>
					</div>
					<div class="card-body">
						<!--begin::Example-->
						<div class="example mb-10">
							<form  class="form" id="add-edit-page-form" action="javascript:;" data-act="{{ admin_url('pages/add-update') }}" method="POST" enctype="multipart/form-data">
								<input type="hidden" name="item_id" value="{{ $id }}">
								<input type="hidden" name="item_id" id="item_id" value="{{ $id }}">
								<div class="row">
									<div class="col-md-12">
										<div class="card card-custom">
											<div class="card-body">
											@php $content = []; if($data->pageContents()->count()>0){ foreach($data->pageContents()->get() as $cont){ $content[$cont['param']] = $cont['value'];   } }
                                                     @endphp
												<div class="row">
													<div class="form-group col-md-12">
														<label class="form-control-label">Title</label>
														<select name="title" id="title" class="form-control col-md-12" disabled  required>
                                                        <option value="">Select</option>
															@foreach($pages as $key=>$page)
																<option value="{{ $page }}" {{ $page==$pag ? 'selected':''}}>{{ $page }}</option>
															@endforeach
														</select>
														<input type="hidden" name="title" value="{{ $pag }}">
													</div>
												</div>
												<div class="row">
													<div class="form-group col-md-12">
														<label class="form-control-label">Meta Title</label>
														<input type="text" name="meta_title" class="form-control" placeholder="Enter Title" value="{{ array_key_exists('meta_title',$content
                                                            ) ? $content['meta_title'] : old('meta_title') }}" required>
													</div>
												</div>
												<div class="row">
													<div class="form-group col-md-12">
														<label class="form-control-label">Meta Description</label>
														<textarea name="meta_description" class="form-control">{{ array_key_exists('meta_description',$content
                                                            ) ? $content['meta_description'] : old('meta_description') }}</textarea>
													</div>
												</div>
											
												<div class="row">
													<div class="form-group col-md-12">
														<label class="form-control-label">Banner Image</label>
											             <input type="file" id="banner-image" name="feature_banner_image" class="form-control" @if(!isset($content['banner_image'])) required  @endif> 
														@if(isset($content['banner_image'])) 
														 <div id="banner-image-wrap">
														  <img src="{{Storage::url($content['banner_image'])}}" width="130" height="130" >
														 {{-- <button type="button" class="btn-danger btn-block rounded mt-2" title="Remove" style="width:130px" onclick="deleteBannerImage(this);" data-id="{{$data->id}}"><i class="fa fa-trash text-secondary fs-3x" aria-hidden="true"></i></button>--}}
                                                         </div>
														@endif 
													</div>
												</div>
											  @if($id == 0)	
												<div class="alert alert-success alert-secondary" style="padding: 0.5rem 1rem;">
													<div class="alert-text text-center">Features</div>
												</div>		
											   <div id="feature-wrapper">	
																					
												<div class="row">

                                                    <div class="row">
														<div class="form-group col-md-8">
															<label class="form-control-label">Description 1</label>
															<textarea id="description_1" class="summernote" name="description_1" required></textarea>
														</div>
														<div class="form-group col-md-4">
														<label class="form-control-label"> Image 1</label>
											             <input type="file" id="image-1" name="images_1" class="form-control" @if(!isset($content['banner_image'])) required  @endif> 
														@if(isset($content['banner_image'])) 
														{{-- <div id="banner-image-wrap">
														  <img src="{{Storage::url($content['banner_image'])}}" width="130" height="130" >
														
                                                         </div> --}}
														@endif 
													   </div>
													</div>
													

												</div>
                                                {{-- <div class="row">  
												  <div class="row">
														<div class="form-group col-md-8">
															<label class="form-control-label">Description 2</label>
															<textarea id="description_2" class="summernote" name="description_2" required>{{ array_key_exists('description_2',$content
                                                            ) ? $content['description_2'] : old('description_2') }}</textarea>
														</div>
													  <div class="form-group col-md-4">
														<label class="form-control-label"> Image 2</label>
											             <input type="file" id="image-2" name="image_2" class="form-control" @if(!isset($content['banner_image'])) required  @endif> 
														@if(isset($content['banner_image'])) 
														 <div id="banner-image-wrap">
														  <img src="{{Storage::url($content['banner_image'])}}" width="200" height="200" >
														
                                                         </div>
														@endif 
													   </div>
													</div>

											    	</div> --}}
													
												</div> 
												&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" id="add-more-feature" class="btn btn-success font-weight-bold mr-2">Add Feature</a>
												@endif
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<div class="card-footer">
									<button type="submit" id="submit_form" class="btn btn-success font-weight-bold mr-2">Submit</button>
										<a href="{{ admin_url('content-management/pages') }}" class="btn btn-light-warning font-weight-bold">Cancel</a>
									</div>
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

	<script>
		// Initialization
		var PAGE_BASE        = "{{ admin_url('pages') }}";
		
		jQuery(document).ready(function() {
			$('.summernote').summernote({
				height: 150
			});
		});
		$(document).ready(function () {
            var form_id = 'add-edit-page-form'; //$(this).attr('id');
            var form = $("#" + form_id);
            var url = form.attr('data-act');
            var id = $("#item_id").val();
	        $("#"+form_id).validate({
                rules: {
                    meta_title: {
                        required: true,
                    },
					meta_description: {
                        required: true,
                    },
                   
                   
                },
                messages: {
                   
                   
                },
                submitHandler: function (form) {
                    infoLoadingBox();
                    var formData = new FormData(document.getElementById("add-edit-page-form"));
                  
                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                    });
                    $.ajax({
                        url: url,
                        type: "post",
                        data: formData,
                        success: function (res) {  
                            if (res.type == 'success') {
                                if (id == 0) {
                                    $("#add-edit-page-form")[0].reset();
                                }
                                //location.reload();
                                showCustomeMessage("Success!", res.msg, res.type);
                                setTimeout(function() {
					                window.location.href = '/admin/content-management/pages';
				                }, 2500);
                            } else {
                                showCustomeMessage("Error!", res.msg, res.type);
                            }
                        },
                        cache: false,
                        contentType: false,
                        processData: false,
                        error: function (err) {
                        
                            var errr = JSON.parse(err.responseText);
                      
                        },
                    });
                },
            });
	
          });
        $(document).ready(function() {
            var maxField = 5; //Input fields increment limitation
            var addButton = $('#add-more-feature'); //Add button selector
            var wrapper = $('#feature-wrapper'); //Input field wrapper
            var fieldHTML = ''; //New input field html 
            var x = 1; //Initial field counter is 1

            //Once add button is clicked
            $(addButton).click(function(){
                //Check maximum number of input fields
                if(x < maxField){ 
                    x++; //Increment field counter
                    $(wrapper).append('<div class=""><div class="row"><div class="row"><div class="form-group col-md-8"><label class="form-control-label">Description '+x+'</label><textarea id="description_'+x+'" class="summernote" name="description_'+x+'" required></textarea></div><div class="form-group col-md-4"><label class="form-control-label"> Image '+x+'</label><input type="file" id="image-2" name="images_'+x+'" class="form-control" required></div></div></div>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="remove-feature-button text-danger">Remove Feature</a><div id="product-image-'+x+'-preview-wrap"  style="display:none"><img id="product-image-'+x+'-preview" src="#" alt="your image" /></div></div><br><br><br>'); //Add field html
                }else{
					toastAlert("error", 'You can only add max 5 features.');  
				}
				$('.summernote').summernote({
				  height: 150
			    });
            });

            //Once remove button is clicked
            $(wrapper).on('click', '.remove-feature-button', function(e) {
                e.preventDefault();
                $(this).parent('div').remove(); //Remove field html
                x--; //Decrement field counter
            });
        });
		const toastAlert = (msg_type, message, msg_position = 'top-right', close_time = '4000', auto_close = true) => {
			Swal.fire({
				toast: true,
				icon: msg_type,
				title: message,
				position: msg_position,
				showConfirmButton: false,
				timer: close_time,
				timerProgressBar: true,
				didOpen: (toast) => {
					toast.addEventListener('mouseenter', Swal.stopTimer)
					toast.addEventListener('mouseleave', Swal.resumeTimer)
				}
			})
         }
	</script>
@endpush