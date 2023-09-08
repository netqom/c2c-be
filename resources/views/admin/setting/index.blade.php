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
                                <a href="{{ admin_url() }}" class="text-muted">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ admin_url('settings/save') }}" class="text-muted">Settings</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#" class=""></a>
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
                
                <div class="col-xl-12">
                @include('admin.alerts.simple-alert')
                    <form class="form" id="settings-form" action="{{ admin_url('settings/save') }}" method="POST"
                        enctype="multipart/form-data">
                        <div class="card card-custom gutter-b">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label">Settings</h3>
                                </div>
                            </div>
                            <div class="card-body">

                                <div class="example mb-10">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card card-custom">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="form-group col-md-6">
                                                            <label>Commission in (%)</label>
                                                            <div class="input-group">
                                                                <input type="text" name="admin_commission_value"
                                                                    class="form-control" oninput="validateCommision(this)"
                                                                    placeholder="Enter Commission value"
                                                                    value="{{ !empty(old('admin_commission_value')) ? old('admin_commission_value') : (isset($settings['admin_commission_value']) ? $settings['admin_commission_value'] : '') }}">
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label class="form-control-label">Email</label>
                                                            <input type="text" name="admin_email" class="form-control"
                                                                placeholder="Enter Email"
                                                                value="{{ !empty(old('admin_email')) ? old('admin_email') : (isset($settings['admin_email']) ? $settings['admin_email'] : '') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-success font-weight-bold mr-2">Submit</button>
                                        <a href="{{ admin_url('settings/save') }}"
                                            class="btn btn-light-warning font-weight-bold">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('public/admin_assets/custom_js/dropzone.js') }}"></script>
    <script>
        function validateCommision(e) {
            var t = e.value;
            e.value = t.indexOf(".") >= 0 ? t.slice(0, t.indexOf(".") + 3) : t;
        }
        //$(document).on("click", "#submit_form", function() {
        //$("#settings-form").submit();
        //});
    </script>
@endpush
