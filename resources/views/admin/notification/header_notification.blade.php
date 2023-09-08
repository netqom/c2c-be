@foreach ($notifications as $key => $value)
	<div class="d-flex align-items-center mb-6">
	    <div class="symbol symbol-40 symbol-light-primary mr-5">
	        <span class="symbol-label">
	        	<i class="fa-solid fa-bell"></i>
	        </span>
	    </div>
	    <div class="d-flex flex-column font-weight-bold">
	        <a href="#" class="text-dark text-hover-primary mb-1 font-size-lg">{{ $notificationType[$value->type] }}</a>
	        <span class="text-muted"> {!! $value->description !!}</span>
	    </div>
	</div>
@endforeach