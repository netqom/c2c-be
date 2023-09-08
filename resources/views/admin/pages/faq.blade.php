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
							<form class="form" id="add-edit-page-form" action="javascript:;" data-act="{{ admin_url('pages/add-update') }}" method="POST" enctype="multipart/form-data">
								<input type="hidden" name="item_id" id="item_id" value="{{ $id }}">
								<div class="row">
									<div class="col-md-12">
										<div class="card card-custom">
											<div class="card-body">
												
                                                <div class="row">
													<div class="form-group col-md-12">
														<label class="form-control-label">Page</label>
														<select  id="title" class="form-control col-md-12" disabled  required>
                                                        <option value="">Select</option>
															@foreach($pages as $key=>$page)
																<option value="{{ $page }}" {{ $page==$pag ? 'selected':''}}>{{ $page }}</option>
															@endforeach
														</select>
                                                        <input type="hidden" name="title" value="{{ $pag }}">
													</div>
												</div>
											    @php $content = []; if($data->pageContents()->count()>0){ foreach($data->pageContents()->get() as $cont){ $content[$cont['param']] = $cont['value'];   } }
                                                @endphp
                                               
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
											             <input type="file" id="banner-image" name="faq_banner_image" class="form-control"> 
														@if(isset($content['banner_image'])) 
														 <div id="banner-image-wrap">
														  <img src="{{Storage::url($content['banner_image'])}}" width="130" height="130" >
														  <button type="button" class="btn-danger btn-block rounded mt-2" title="Remove" style="width:130px" onclick="deleteBannerImage(this);" data-id="{{$data->id}}"><i class="fa fa-trash text-secondary fs-3x" aria-hidden="true"></i></button>
                                                         </div>
														@endif 
													</div>
												</div>
                                             @if($id==0)
                                               <div id="faq-wrapper"> 
												 <div class="row">
													<div class="form-group col-md-12">
														<label class="form-control-label">Question 1</label>
														<input type="text" name="question[]" class="form-control" placeholder="Enter Question" value="" required>
													</div>
												 </div>
                                                 <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <label class="form-control-label">Answer 1</label>
                                                        <textarea name="answer[]" class="form-control"></textarea>
                                                    </div>
                                                 </div>
                                                 <div class="row">
													<div class="form-group col-md-12">
														<label class="form-control-label">Question 2</label>
														<input type="text" name="question[]" class="form-control" placeholder="Enter Question" value="" required>
													</div>
												 </div>
                                                 <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <label class="form-control-label">Answer 2</label>
                                                        <textarea name="answer[]" class="form-control"></textarea>
                                                    </div>
                                                 </div>
                                                 <div class="row">
													<div class="form-group col-md-12">
														<label class="form-control-label">Question 3</label>
														<input type="text" name="question[]" class="form-control" placeholder="Enter Question" value="" required>
													</div>
												 </div>
                                                 <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <label class="form-control-label">Answer 3</label>
                                                        <textarea name="answer[]" class="form-control"></textarea>
                                                    </div>
                                                 </div>
                                                </div>
                                             @endif 
                                                <!-- &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" id="add-more-question" class="btn btn-success font-weight-bold mr-2">Add More</a> -->
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
			//Images Plugin
		
		});
        $(document).ready(function () {
            var form_id = 'add-edit-page-form'; //$(this).attr('id');
            var form = $("#" + form_id);
            var url = form.attr('data-act');
            var id = $("#item_id").val();
	        $("#"+form_id).validate({
                rules: {
                    home_page_title: {
                        required: true,
                        maxlength: 100,
                    },
                 
                   
                },
                messages: {
                   
                   
                },
                submitHandler: function (form) {
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
            var maxField = 10; //Input fields increment limitation
            var addButton = $('#add-more-question'); //Add button selector
            var wrapper = $('#faq-wrapper'); //Input field wrapper
            var fieldHTML = ''; //New input field html 
            var x = 1; //Initial field counter is 1

            //Once add button is clicked
            $(addButton).click(function(){
                //Check maximum number of input fields
                if(x < maxField){ 
                    x++; //Increment field counter
                    $(wrapper).append('<hr/><div class=""><div class="row"><div class="form-group col-md-12"><label class="form-control-label">Question '+x+'</label><input type="text" name="question_'+x+'" class="form-control" placeholder="Enter Question"  required></div></div><div class="row"><div class="form-group col-md-12"><label class="form-control-label">Answer '+x+'</label><textarea name="answer_'+x+'" class="form-control"></textarea></div></div>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="remove-product-image-button text-danger"> <i class="bx bxs-trash"></i></a><div id="product-image-'+x+'-preview-wrap"  style="display:none"><img id="product-image-'+x+'-preview" src="#" alt="your image" /></div></div>'); //Add field html
                }
            });

            //Once remove button is clicked
            $(wrapper).on('click', '.remove-product-image-button', function(e) {
                e.preventDefault();
                $(this).parent('div').remove(); //Remove field html
                x--; //Decrement field counter
            });
        });
        url = BASE_URL+'/admin/pages/delete-banner-image'; 
		function deleteBannerImage(obj){
			var id = $(obj).data('id');
			Swal.fire({
						  title: 'Are you sure?',
						  text: "You won't be able to revert this!",
						  icon: 'warning',
						  showCancelButton: true,
						  confirmButtonColor: '#3085d6',
						  cancelButtonColor: '#d33',
						  confirmButtonText: 'Yes, delete it!'
						}).then((result) => {
						   if (result.value == true) {
								
								$.ajax({
										url: url,
										type: "post",
										data: 'id='+id,
										success: function (res) {  
											if (res.type == 'success') {
												$("#banner-image-wrap").addClass('d-none');
											} else {
												showCustomeMessage("Error!", res.msg, res.type);
											}
										},
									    });
						   }
						})
		}
	</script>
@endpush