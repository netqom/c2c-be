@if($errors->any())
    <div class="alert alert-danger" role="alert">
		<div class="alert-close">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true"><i class="ki ki-close"></i></span>
			</button>
		</div>	
        <h4 class="alert-heading">Something Went wrong!</h4>
		
		<div class="border-bottom border-white opacity-20 mb-5"></div>
		<ul>
			@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
		
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success" role="alert">
		<div class="alert-close">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true"><i class="ki ki-close"></i></span>
			</button>
		</div>
		{{session('success')}}
		@php Session::pull('success'); @endphp
	</div>
@endif

@if(session('error'))
    <div class="alert alert-danger" role="alert">
		<div class="alert-close">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true"><i class="ki ki-close"></i></span>
			</button>
		</div>
		{{session('error')}}
	</div>
@endif