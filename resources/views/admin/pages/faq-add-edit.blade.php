<div class="d-flex flex-column-fluid">
	<div class="container">
		<form class="form" id="add-edit-faq" action="" method="POST" enctype="multipart/form-data">
			@csrf
			<div class="form-body">
				<input type="hidden" name="item_id" value="{{ !empty($data->id) ? $data->id : 0 }}">
				
			
              
                <div class="form-group ">
                    <label class="form-control-label">Question </label>
                    <input type="text" name="question" class="form-control" placeholder="Enter Question" value="{{ isset($data->question) ? $data->question : old('question') }}" required>
                </div>
             
            
                <div class="form-group ">
                    <label class="form-control-label">Answer </label>
                    <textarea name="answer" class="form-control" required>{{isset($data->answer) ? $data->answer : old('answer') }}</textarea>
                </div>
          
			
			
			</div>
			<hr>
			<div class="form-actions">
				<div class="row">
					<div class="col-md-offset-3 col-md-9">
						<a href="javascript:;" onclick="validateFaqsForm('add-edit-faq');" class="btn btn-success font-weight-bold mr-2">Submit</a>
						<button type="button" class="btn btn-light-warning font-weight-bold" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
