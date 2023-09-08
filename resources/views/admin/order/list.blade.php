@extends('layouts.admin')
@section('content')
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
                                <a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#" class="">Orders</a>
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
                <div class="card card-custom">
                    <div class="align-items-center card-header d-flex">
                        <div class="card-title">
                            <h3 class="card-label">Orders List
                            </h3>
                        </div>
                        <div class="ml-md-auto d-inline-flex align-items-center mt-2 mt-md-0">
                            <div class="d-inline-flex mr-3">
                                <span class="d-block svg-icon svg-icon-3x svg-icon-danger">
                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Design/Layers.svg-->
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"></rect>
                                            <rect fill="#000000" opacity="0.3" x="13" y="4" width="3"
                                                height="16" rx="1.5"></rect>
                                            <rect fill="#000000" x="8" y="9" width="3" height="11"
                                                rx="1.5"></rect>
                                            <rect fill="#000000" x="18" y="11" width="3" height="9"
                                                rx="1.5"></rect>
                                            <rect fill="#000000" x="3" y="13" width="3" height="7"
                                                rx="1.5"></rect>
                                        </g>
                                    </svg>
                                    <!--end::Svg Icon-->
                                </span>
                                <span
                                    class="card-title line-height-md d-block font-size-h4 font-weight-bolder mb-0 mt-0 text-dark-75">£<span class="cstm-total-trans">0.00</span>
                                    <a href="javascript:;" class="d-block font-size-sm font-weight-bold text-muted">Total
                                        Transaction</a></span>
                            </div>
                            <div class="d-inline-flex">
                                <span class="svg-icon svg-icon-3x svg-icon-success d-block">
                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Design/Layers.svg-->
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"></rect>
                                            <rect fill="#000000" opacity="0.3" x="13" y="4" width="3"
                                                height="16" rx="1.5"></rect>
                                            <rect fill="#000000" x="8" y="9" width="3" height="11"
                                                rx="1.5"></rect>
                                            <rect fill="#000000" x="18" y="11" width="3" height="9"
                                                rx="1.5"></rect>
                                            <rect fill="#000000" x="3" y="13" width="3"
                                                height="7" rx="1.5"></rect>
                                        </g>
                                    </svg>
                                    <!--end::Svg Icon-->
                                </span>
                                <span
                                    class="card-title  line-height-md d-block font-size-h4 font-weight-bolder mb-0 mt-0 text-dark-75">£<span class="cstm-total-reve">0.00</span><a
                                        href="javascript:;"
                                        class="d-block font-size-sm font-weight-bold text-muted">Revenue</a></span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-7">
                            <div class="row align-items-center">
                                <div class="col-lg-9 col-xl-8">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 my-2 my-md-0">
                                            <div class="input-icon">
                                                <input type="text" class="form-control" placeholder="Search..."
                                                    id="kt_datatable_search_query" />
                                                <span>
                                                    <i class="flaticon2-search-1 text-muted"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div id="reportrange"
                                            style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                                            <i class="fa fa-calendar"></i>&nbsp;
                                            <span></span> <i class="fa fa-caret-down"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="datatable datatable-bordered datatable-head-custom" id="kt_ordersTable"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript">
        var data_url = "{{ admin_url('orders/list-data') }}";
        var url_page_base = "{{ admin_url('orders') }}";
        var token = '{{ csrf_token() }}';
    </script>
    <script src="{{ asset('public/admin_assets/js/order_datatable.js') }}"></script>
@endpush
