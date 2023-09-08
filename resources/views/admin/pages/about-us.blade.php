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
							<form class="form" id="add-edit-page" action="{{ admin_url('pages/add-update') }}" method="POST" enctype="multipart/form-data">
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
											             <input type="file" id="banner-image" name="banner_image" class="form-control"> 
														@if(isset($content['banner_image'])) 
														 <div id="banner-image-wrap">
														  <img src="{{Storage::url($content['banner_image'])}}" width="130" height="130" >
														 {{-- <button type="button" class="btn-danger btn-block rounded mt-2" title="Remove" style="width:130px" onclick="deleteBannerImage(this);" data-id="{{$data->id}}"><i class="fa fa-trash text-secondary fs-3x" aria-hidden="true"></i></button> --}}
                                                         </div>
														@endif 
													</div>
												</div>
												
												<div class="alert alert-success alert-secondary" style="padding: 0.5rem 1rem;">
													<div class="alert-text text-center">Images</div>
												</div>															
												<div class="row">
													 <!--begin::Dropzone-->
													<div class="dropzone" id="kt_dropzonejs_example_1" data-item-id="{{ $id }}" data-limit="2" data-form-id="add-edit-page" data-submit="add-update" data-uploaded="get-uploaded-files" data-delete="delete-image">
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
													
													<div class="row">
														<div class="form-group col-md-12">
															<label class="form-control-label">Description 1</label>
															<textarea id="description_1" class="summernote" name="description_1" required>{{ array_key_exists('description_1',$content
                                                            ) ? $content['description_1'] : old('description_1') }}</textarea>
														</div>
													</div>
													<div class="row">
														<div class="form-group col-md-12">
															<label class="form-control-label">Description 2</label>
															<textarea id="description_2" class="summernote" name="description_2" required>{{ array_key_exists('description_2',$content
                                                            ) ? $content['description_2'] : old('description_2') }}</textarea>
														</div>
													</div>

												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="card-body">
									<div class="card-footer">
										<a href="javascript:;" id="submit_form" class="btn btn-success font-weight-bold mr-2">Submit</a>
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
function createDropZoneInstance(obj, BASE_URL){
	//console.log('dropzone object', obj)
    var file_limit   = $(obj).data('limit');
    var submit_url   = $(obj).data('submit');
    var uploaded_url = $(obj).data('uploaded');
    var delete_url   = $(obj).data('delete');
    var form_id      = $(obj).data('form-id');
    var element_id   = $(obj).attr('id');
    var item_id      = $(obj).data('item-id');
   
	var myDropzone = new Dropzone("#"+element_id, {
		url: BASE_URL + '/' + submit_url, // Set the url for your upload script location
		paramName: "image", // The name that will be used to transfer the file
		maxFilesize: 10, // MB
		autoProcessQueue: false,
		parallelUploads: 2,
		acceptedFiles: "image/*",
		params: {'item_id': item_id, 'form_id': form_id, 'uploaded_path': BASE_URL + '/' + uploaded_url, 'delete_path': BASE_URL + '/' + delete_url},
		maxFiles: file_limit,
		uploadMultiple:true,
		maxfilesexceeded: function(file) {
			alert('You Can Upload Only '+this.options.maxFiles+' Files....!');
			this.removeFile(file);
		},
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		init: function () {
			var myDropzone = this,
				submitButton = document.getElementById("submit_form");
			//get pre saved image for edit case
			if(myDropzone.options.params.item_id > 0){
				var data = {'item_id' : myDropzone.options.params.item_id};
				
				$.ajax({type: 'GET', url: myDropzone.options.params.uploaded_path, data: data})
				.done(function(response) {
					if(response.type == 'success'){
						$.each(response.data, function( index, value ) {
							var mockFile = response.data[index];
							console.log(mockFile,'mockFile');
							// Call the default addedfile event handler
							myDropzone.emit("addedfile", mockFile);
							// And optionally show the thumbnail of the file:
							myDropzone.emit("thumbnail", mockFile, response.data[index].url);
							myDropzone.files.push(mockFile);
						});
					}
				})
				.fail(function(xhr) {
					console.log('error callback ', xhr);
				});
			}
			//Add a remove file button when a file is added
			this.on("addedfile", function(file) {
				//check if total file reached max file add limit
				var total_count = myDropzone.files.length;
				if(total_count > myDropzone.options.maxFiles){
					myDropzone.removeFile(file);
					showCustomeMessage("Warning!", 'You Can Upload Only ' + myDropzone.options.maxFiles + ' Files....!', 'warning');
				}
				// Create the remove button
				$('.dz-progress').hide();
				var removeButton = Dropzone.createElement("<button class='btn-danger btn-block rounded mt-2' title='Remove'><i class='fa fa-trash text-secondary fs-3x' aria-hidden='true'></i></button>");
				// Listen to the click event
				removeButton.addEventListener("click", function(e) {
					// Make sure the button click doesn't submit the form:
					e.preventDefault();
					e.stopPropagation();
					if(file.id != undefined){
						//first remove the file from server
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
								infoLoadingBox();
								$.ajax({ type: 'POST', url: myDropzone.options.params.delete_path, data: {'item_id': file.id} })
								.done(function(response) {
									swal.close()
									if(response.type == 'success'){
										//Remove the file preview.
										myDropzone.removeFile(file);
									}
								})
								.fail(function(xhr) {
									swal.close()
									console.log('error callback ', xhr);
								});
						   }
						})
					}else{
						//Remove the file preview.
						myDropzone.removeFile(file);
					}
				});
				file.previewElement.appendChild(removeButton);
			});
			
			//submit file and form data
			submitButton.addEventListener('click', function(e) {
				e.preventDefault();
				e.stopPropagation();
				if(validateForm(myDropzone.options.params.form_id)){
					if (myDropzone.getQueuedFiles().length > 0) {                        
						myDropzone.processQueue();  
					} else { 
						var blob = new Blob();
						blob.upload = { 'chunked': myDropzone.defaultOptions.chunking };
						myDropzone.uploadFile(blob);
					} 
				}
			});
			
			myDropzone.on('sendingmultiple', function(files, xhr, formData) {
				infoLoadingBox();
				//console.log('total files', files)
				if(files.length > 0){
					if(files.length == 1){
						console.log('file size', files[0].size)
						if(files[0].size > 0){
							formData.append('have_files', 'yes')
						}else{
							formData.append('have_files', 'no')
						}
					}else{
						formData.append('have_files', 'yes')
					}
				}else{
					formData.append('have_files', 'no');
				}
				var banner_image = $('#banner-image')[0].files[0];
	                 console.log(banner_image,'svb');
				var data = $('#' + myDropzone.options.params.form_id).serializeArray();
				$.each(data, function(key, el) {
					formData.append(el.name, el.value);
				});
				formData.append('about_us_banner_image', banner_image);
			});
			
			myDropzone.on('success', function(files, response) {
				showCustomeMessage("Success!", response.msg, response.type);
				 setTimeout(function() {
					window.location.href = '/admin/content-management/pages';
				}, 2500);
			});
			
			myDropzone.on('error', function(files, response) {
				showCustomeMessage("Error!", response.msg, response.type);
			});
		}
	});
}
</script>
	<script>
		// Initialization
		var PAGE_BASE        = "{{ admin_url('pages') }}";
		
		jQuery(document).ready(function() {
			$('.summernote').summernote({
				height: 150
			});
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