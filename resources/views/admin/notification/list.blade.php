@extends('layouts.admin')
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
		<div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
			<div class="d-flex align-items-center flex-wrap mr-1">
				<div class="d-flex align-items-baseline mr-5">
					<ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
						<li class="breadcrumb-item">
							<a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a>
						</li>
						<li class="breadcrumb-item">
							<a href="#" class="">Notifications</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="dataTables_processing dts_loading"></div>
    <div class="d-flex flex-column-fluid">
        <div class="container">
			@include('admin.alerts.simple-alert')
            <div class="card card-custom">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">  List of Notifications
                    </div>
					<div class="card-toolbar">
						
					</div>
                </div>
                <div class="card-body">
					<div class="mb-7">
						<div class="row align-items-center">
							<div class="col-lg-9 col-xl-8">
								<div class="row align-items-center">
									<div class="col-md-4 my-2 my-md-0">
										<div class="input-icon">
											<input type="text" class="form-control" placeholder="Search..." id="kt_datatable_search_query" />
											<span>
												<i class="flaticon2-search-1 text-muted"></i>
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
                    <div class="datatable datatable-bordered datatable-head-custom" id="kt_notification_datatable">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script type="text/javascript">
     var data_url = "{{ admin_url('notifications/list-data') }}";
     var url_page_base = "{{ admin_url('notifications') }}";	 
	 var token = '{{ csrf_token() }}';
</script>
<script src="{{ asset('public/admin_assets/js/notifications_datatable.js') }}"></script>
@endpush