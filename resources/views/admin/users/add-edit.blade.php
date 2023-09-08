<div class="d-flex flex-column-fluid">
	<div class="container">
		<form class="form" id="add-edit-user" action="" method="POST" enctype="multipart/form-data">
			@csrf
			<div class="form-body">
				<input type="hidden" name="item_id" value="{{ !empty($data->id) ? $data->id : 0 }}">
				<input type="hidden" name="company" class="form-control" value="Asia Pacific Group">
				<div class="form-group">
					<label class="form-control-label">Name</label>
					<input type="text" name="name" class="form-control" placeholder="Enter Name" value="{{ !empty($data->name) ? $data->name : old('name') }}" required>
				</div>
				<div class="form-group">
					<label class="form-control-label">Email</label>
					<input type="email" name="email" class="form-control" placeholder="Enter Email" value="{{ !empty($data->email) ? $data->email : old('email') }}" required>
				</div>
				<div class="form-group">
					<label class="form-control-label">Phone No</label>
					<input type="text" name="phone" class="form-control" placeholder="Enter Phone No" value="{{ !empty($data->phone) ? $data->phone : old('phone') }}" required>
				</div>
				<div class="form-group">
					<label class="form-control-label">Display Image</label>
					<div class="col-lg-12 col-xl-12 text-center">
						@php $display_user_image = '';
							if(!empty($data->display_user_image)){
								$display_user_image = $data->display_user_image;
							}
						@endphp
						<div class="image-input image-input-empty image-input-outline" id="image" style="background-image: url({{ asset('public/admin_assets/media/images/dummy.jpg') }})">
							<div class="image-input-wrapper" style="background-image: url({{ $display_user_image }})"></div>

							<label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change Display">
								<i class="fa fa-pen icon-sm text-muted"></i>
								<input type="file" name="image">
								<input type="hidden" name="image_remove">
							</label>
							<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="" data-original-title="Cancel Display">
								<i class="ki ki-bold-close icon-xs text-muted"></i>
							</span>
							<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="" data-original-title="Remove Display">
								<i class="ki ki-bold-close icon-xs text-muted"></i>
							</span>
						</div>
					</div>
				</div>
				@if($data->role != 1)
					<div class="form-group">
						<label class="form-control-label">Role</label>
						<select class="form-control" name="role" required>
							<option value="">Select Role</option>
							@php foreach($positions as $key => $value){ @endphp
								<option value="{{ $key }}" @if($data->role == $key) selected @endif>{{ $value }}</option>
							@php } @endphp
						</select>
					</div>
				@else
					<input type="hidden" name="role" value="{{ !empty($data->role) ? $data->role : 1 }}">	
				@endif
				@if($data->role != 1)
				<div class="form-group">
					<label class="form-control-label">State</label>
					<select class="form-control" id="state-id" name="state_id" required>
						<option value="">Select State</option>
						@php foreach($states as $state){ @endphp
							<option value="{{ $state->id }}" @if($data->state_id == $state->id) selected @endif>{{ $state->name }}</option>
						@php } @endphp
					</select>
				</div>
				<div class="form-group">
					<label class="form-control-label">City</label>
					<select class="form-control" id="city-id" name="city_id" >
						<option value="">Select City</option>
					   @if( !empty($data->id) )	
						@php foreach($cities as $city){ @endphp
							<option value="{{ $city->id }}" @if($data->city_id == $city->id) selected @endif>{{ $city->name }}</option>
						@php } @endphp
					  @endif	
					</select>
				</div>
				<!-- <div class="form-group">
				<label class="form-control-label">Address</label>	
				 <input type="text" class="form-control ui-autocomplete-input @error('business_address') is-invalid @enderror"
                                                    name="business_address" value="{{ old('business_address') }}"
                                                    autocomplete="off" autofocus id="input_address"
                                                    placeholder="Business Address" required>
				</div>												 -->
				@endif
			</div>
			<hr>
			<div class="form-actions">
				<div class="row">
					<div class="col-md-offset-3 col-md-9">
						<a href="javascript:;" onclick="validateUsersForm('add-edit-user');" class="btn btn-success font-weight-bold mr-2">Submit</a>
						<button type="button" class="btn btn-light-warning font-weight-bold" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<script>
	// Initialization
	jQuery(document).ready(function() {
		//Images Plugin
		new KTImageInput('image');
	});
</script>	