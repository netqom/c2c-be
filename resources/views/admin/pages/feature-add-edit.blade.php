<div class="d-flex flex-column-fluid">
	<div class="container">
		<form class="form" id="add-edit-feature" action="" method="POST" enctype="multipart/form-data">
			@csrf
			<div class="form-body">
				<input type="hidden" name="item_id" value="{{ !empty($data->id) ? $data->id : 0 }}">
				
			
              
                <div class="form-group ">
                    <label class="form-control-label">Description </label>
                    <textarea name="description" class="summernote" required>{{isset($data->description) ? $data->description : old('description') }}</textarea>
                </div>
             
            
                <div class="form-group ">
                    <label class="form-control-label">Image </label>
                    <input type="file" name="image" class="form-control" @if(empty($data->id)) required @endif> 
                    @if( isset($data->image) ) 
                        <div id="banner-image-wrap">
                        <img src="{{Storage::url($data->image)}}" width="130" height="130" >
                       
                    @endif 
                </div>
          
			</div>
			<hr>
			<div class="form-actions">
				<div class="row">
					<div class="col-md-offset-3 col-md-9">
						<a href="javascript:;" onclick="validateFeaturesForm('add-edit-feature');" class="btn btn-success font-weight-bold mr-2">Submit</a>
						<button type="button" class="btn btn-light-warning font-weight-bold" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
