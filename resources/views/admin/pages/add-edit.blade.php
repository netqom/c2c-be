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
														<label class="form-control-label">Title</label>
														<input type="text" name="home_page_title" class="form-control" placeholder="Enter Title" value="{{ array_key_exists('home_page_title',$content
                                                            ) ? $content['home_page_title'] : old('home_page_title') }}" required>
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
														<label class="form-control-label">Title Description</label>
														<input type="text" name="home_page_title_description" class="form-control" placeholder="Enter Title Description" value="{{ array_key_exists('home_page_title_description',$content
                                                            ) ? $content['home_page_title_description'] : old('home_page_title_description') }}" required>
													</div>
												</div>
                                                <div class="row">
													<div class="form-group col-md-12">
														<label class="form-control-label">How it works ? (section 1)</label>
														<input type="text" name="how_it_works_section_subtitle_1" class="form-control" placeholder="" value="{{ array_key_exists('how_it_works_section_subtitle_1',$content
                                                            ) ? $content['how_it_works_section_subtitle_1'] : old('how_it_works_section_subtitle_1') }}" required>
													</div>
												</div>	 
                                                <div class="row">
													<div class="form-group col-md-12">
														<label class="form-control-label">How it works ? (section 2)</label>
														<input type="text" name="how_it_works_section_subtitle_2" class="form-control" placeholder="" value="{{ array_key_exists('how_it_works_section_subtitle_2',$content
                                                            ) ? $content['how_it_works_section_subtitle_2'] : old('how_it_works_section_subtitle_2') }}" required>
													</div>
												</div>
                                                <div class="row">
													<div class="form-group col-md-12">
														<label class="form-control-label">How it works ? (section 3)</label>
														<input type="text" name="how_it_works_section_subtitle_3" class="form-control" placeholder="" value="{{ array_key_exists('how_it_works_section_subtitle_3',$content
                                                            ) ? $content['how_it_works_section_subtitle_3'] : old('how_it_works_section_subtitle_3') }}" required>
													</div>
												</div>															
												<div class="row">
													<div class="form-group col-md-12">
														<label class="form-control-label">How it works ? (section 4)</label>
														<input type="text" name="how_it_works_section_subtitle_4" class="form-control" placeholder="" value="{{ array_key_exists('how_it_works_section_subtitle_4',$content
                                                            ) ? $content['how_it_works_section_subtitle_4'] : old('how_it_works_section_subtitle_4') }}" >
													</div>
												</div>	
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
                    how_it_works_section_subtitle_1: {
                        maxlength: 100,
                    },
                    how_it_works_section_subtitle_2: {
                        maxlength: 100,
                    },
                    how_it_works_section_subtitle_3: {
                        maxlength: 100,
                    },
                    how_it_works_section_subtitle_4: {
                        maxlength: 100,
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
	</script>
@endpush