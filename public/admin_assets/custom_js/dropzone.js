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
		parallelUploads: 4,
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
				
				var data = $('#' + myDropzone.options.params.form_id).serializeArray();
				$.each(data, function(key, el) {
					formData.append(el.name, el.value);
				});
			});
			
			myDropzone.on('success', function(files, response) {
				showCustomeMessage("Success!", response.msg, response.type);
				 setTimeout(function() {
					window.location.href = '/admin/products';
				}, 2500);
			});
			
			myDropzone.on('error', function(files, response) {
				showCustomeMessage("Error!", response.msg, response.type);
			});
		}
	});
}