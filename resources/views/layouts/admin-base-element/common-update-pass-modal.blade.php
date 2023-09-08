<!--begin::Modal-->
<div class="modal fade" id="update_password_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Change Password</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i aria-hidden="true" class="ki ki-close"></i>
				</button>
			</div>
			<div class="modal-body">
				<form class="form-horizontal" role="form" id="update-password-form">
					@csrf
					<div class="form-group">
						<label>Password</label>
						<input type="password" name="password" id="password" class="form-control" placeholder="password" autocomplete="off">
					</div>
					<div class="form-group">
						<label>Confirm Password</label>
						<input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm password" autocomplete="off">
					</div>
					<hr>
					<div class="row">
						<div class="col-md-offset-3 col-md-9">
							<button type="button" class="btn btn-success" onclick="validateSubmitChangePassword('update-password-form')">Submit</button>
							<button class="btn btn-warning" data-dismiss="modal">Close</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!--end::Modal-->