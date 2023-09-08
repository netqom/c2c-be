<form class="form" id="add-edit-cat" action="" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="item_id" value="{{ !empty($data->id) ? $data->id : 0 }}">
    <div class="form-body">
        {{-- <div class="form-group">
			<label class="form-control-label">Parent Category</label>
			<select class="form-control" name="parent_id">
				<option value="">Select Parent Category</option>
				@php foreach($categories as $category){ @endphp
					<option value="{{ $category->id }}" @if ($data->parent_id == $category->id) selected @endif>{{ $category->name }}</option>
				@php } @endphp
			</select>
		</div> --}}
        <div class="form-group">
            <label class="form-control-label">Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter Category Name"
                value="{{ !empty($data->name) ? $data->name : old('name') }}" required>
        </div>
        {{-- <div class="form-group col-md-12">
			<label class="form-control-label">Display Image</label>
			<div class="col-lg-12 col-xl-12 text-center">
				@php $display_image_path = '';
					if(!empty($data->display_path)){
						$display_image_path = $data->display_path;
					}
				@endphp
				<div class="image-input image-input-empty image-input-outline" id="display_image" style="background-image: url({{ asset('public/admin_assets/media/images/dummy.jpg') }})">
					<div class="image-input-wrapper" style="background-image: url({{ $display_image_path }})"></div>

					<label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change Display">
						<i class="fa fa-pen icon-sm text-muted"></i>
						<input type="file" name="display_image">
						<input type="hidden" name="display_image_remove">
					</label>
					<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="" data-original-title="Cancel Display">
						<i class="ki ki-bold-close icon-xs text-muted"></i>
					</span>
					<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="" data-original-title="Remove Display">
						<i class="ki ki-bold-close icon-xs text-muted"></i>
					</span>
				</div>
			</div>
		</div> --}}
    </div>
    <hr>
    <div class="form-actions">
        <div class="row">
            <div class="col-md-offset-3 col-md-9">
                <button type="button" class="btn btn-light-success font-weight-bold" id="add-edit-cat-btn" onclick="validateProductCatForm('add-edit-cat');"  class="btn btn-success font-weight-bold mr-2">Submit</button>
                <button type="button" class="btn btn-light-warning font-weight-bold"
                    data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</form>
<script>
    // Initialization
    jQuery(document).ready(function() {
        //Images Plugin
        new KTImageInput('display_image');


		$('#add-edit-cat').on('keydown', 'input', function(e) {
			if (e.key === 'Enter') {
				e.preventDefault(); // Prevent the form from submitting
				$('#add-edit-cat-btn').trigger('click');
				// Add your custom logic here if needed
				return false;
			}
		});
    });
	
</script>
