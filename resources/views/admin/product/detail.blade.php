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
                                <a href="{{ admin_url('products') }}" class="text-muted">Products</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a href="javascript:;" class="">Product Detail</a>
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
            <div class=" container ">
                <div class="card card-custom">
                    <div class="card-body">
                        <div class="d-flex mb-9">
                            <div class="flex-shrink-0 mr-7 mt-lg-0 mt-3">
                                <div class="symbol symbol-50 symbol-lg-120">
                                    <img src="{{ $product->display_path }}" alt="image">
                                </div>
                                <div class="symbol symbol-50 symbol-lg-120 symbol-primary d-none">
                                    <span class="font-size-h3 symbol-label font-weight-boldest">JM</span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between flex-wrap mt-1">
                                    <div class="d-flex mr-3">
                                        <a href="#"
                                            class="text-dark-75 text-hover-primary font-size-h5 font-weight-bold mr-3">{{ $product->title }}</a>
                                        <a href="#"><i class="flaticon2-correct text-success font-size-h5"></i></a>
                                    </div>
                                </div>

                            </div>
                        </div>
                        {{-- <div class="separator separator-solid"></div> --}}
                        {{-- <div class="d-flex align-items-center flex-wrap mt-8">
                            <div class="d-flex align-items-center flex-lg-fill mr-5 mb-2">
                                <span class="mr-4">
                                    <i class="flaticon2-list-2 display-4 text-muted font-weight-bold"></i>
                                </span>
                                <div class="d-flex flex-column text-dark-75">
                                    <a href="{{ admin_url('products/detail/' . $product->id . '/details') }}"><span
                                            class="font-weight-bolder font-size-sm"
                                            @if ($tab == null || $tab == 'details') style="color:#1BC5BD" @endif>Details</span>
                                    </a>
                                    <span class="font-weight-bolder font-size-h5"><span
                                            class="text-dark-50 font-weight-bold"></span></span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center flex-lg-fill mr-5 mb-2">
                                <span class="mr-4">
                                    <i class="flaticon2-list-2 display-4 text-muted font-weight-bold"></i>
                                </span>
                                <div class="d-flex flex-column text-dark-75">
                                    <a href="{{admin_url('products/detail/'.$product->id.'/reviews')}}"><span class="font-weight-bolder font-size-sm"  @if ($tab == 'reviews') style="color:#1BC5BD" @endif>Reviews</span>
                                    </a>
                                    <span class="font-weight-bolder font-size-h5"><span class="text-dark-50 font-weight-bold"></span></span>
                                </div>
                            </div>


                            <div class="d-flex align-items-center flex-lg-fill mr-5 mb-2">
                                <span class="mr-4">
                                    <i class="flaticon-star display-4 text-muted font-weight-bold"></i>
                                </span>
                                <div class="d-flex flex-column flex-lg-fill">
                                    <span class="text-dark-75 font-weight-bolder font-size-sm">Avg. Ratings</span>
                                    <span class="font-weight-bolder font-size-h5"><span
                                            class="text-dark-50 font-weight-bold"></span>
                                        @php $avgRating = getAverage($product->review_ratings()->sum('rating'),$product->review_ratings()->count());  @endphp
                                        {{ number_format($avgRating, 1) }}</span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center flex-lg-fill mr-5 mb-2">
                                <span class="mr-4">
                                    <i class="flaticon-chat-1 display-4 text-muted font-weight-bold"></i>
                                </span>
                                <div class="d-flex flex-column">
                                    <span class="text-dark-75 font-weight-bolder font-size-sm">Rating Count</span>
                                    <span class="font-weight-bolder font-size-h5"><span
                                            class="text-dark-50 font-weight-bold"></span>{{ $product->review_ratings()->count() }}</span>
                                </div>
                            </div> 

                        </div> --}}
                    </div>
                </div>
                <div class="card card-custom">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-label">Product @if ($tab == null || $tab == 'details')
                                    Details
                                @endif
                                @if ($tab == 'reviews')
                                    Reviews
                                @endif

                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            <!-- <a href="{{ admin_url('products/add-edit/0') }}" class="btn btn-light-primary btn-sm font-weight-bolder"><i class="la la-plus"></i>Add Product</a> -->
                            <!--end::Button-->
                        </div>
                    </div>
                    <!--end::Header-->
                    <div class="card-body">
                        @if ($tab == 'reviews')
                            <!--begin::Search Form-->
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Search Form-->
                            <div class="datatable datatable-bordered datatable-head-custom" id="kt_productReviewsTable">
                            </div>
                        @endif
                        @if ($tab == null || $tab == 'details')
                            <form class="form" id="kt_form">
                                <div class="row">
                                    <div class="col-xl-2"></div>
                                    <div class="col-xl-8">
                                        <div class="my-5">
                                            <h3 class=" text-dark font-weight-bold mb-10">General:</h3>
                                            <div class="form-group row">
                                                <label class="col-3">Title</label>
                                                <div class="col-9">
                                                    <span> {{ $product->title }} </span>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="form-group row">
                                                <label class="col-3">Description</label>
                                                <div class="col-9">
                                                    <span> {!! $product->description !!} </span>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="form-group row">
                                                <label class="col-3">Product Categories</label>
                                                <div class="col-9">
                                                    {{ implode(',', $catNameArray) }}
                                                </div>
                                            </div>
                                            <br>
                                            <div class="form-group row">
                                                <label class="col-3">Price</label>
                                                <div class="col-9">
                                                    <div class="input-group">
                                                        <span><strong>£</strong>{{ $product->price }} </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="form-group row">
                                                <label class="col-3">Product Status</label>
                                                <div class="col-9">
                                                    <div class="input-group">

                                                        <span>
                                                            @if ($product->status == 1)
                                                                Active
                                                                @endif @if ($product->status == 0)
                                                                    In-Active
                                                                @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="form-group row">
                                                <label class="col-3">Tags</label>
                                                <div class="col-9">
                                                    <div class="input-group">
                                                        @if (!empty($product->tags))
                                                            @php
                                                                $tagsCount = count(json_decode($product->tags));
                                                                $i = 1;
                                                            @endphp
                                                            @foreach (json_decode($product->tags) as $tag)
                                                                {{ $tag->value }} @if ($i < $tagsCount)
                                                                    ,
                                                                @endif
                                                                @php $i++; @endphp
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="separator separator-dashed my-10"></div>
                                        <div class="my-5">
                                            <h3 class=" text-dark font-weight-bold mb-10">Specification Section:</h3>
                                            <!-- <div class="form-group row">
                                                        <label class="col-3">Avaiable Quantity</label>
                                                        <div class="col-9">
                                                            <span>{{ $product->quantity }}</span>
                                                        </div>
                                                    </div>
                                                    <br> -->
                                            <div class="form-group row">
                                                <label class="col-3">Estimated Delivery Days</label>
                                                <div class="col-9">
                                                    <span>{{ $product->delivery_time }}</span>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="form-group row">
                                                <label class="col-3">Shipping Available</label>
                                                <div class="col-9">
                                                    @foreach ($delivery_methods as $key => $value)
                                                        @if ($product->delivery_method == $key)
                                                            {{ $value }}
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                            <br>
                                            @if ($product->delivery_method == 1)
                                                <div class="form-group row">
                                                    <label class="col-3">Shipping Price</label>
                                                    <div class="col-9">
                                                        <strong>£</strong>{{ number_format($product->delivery_price, 2) }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="separator separator-dashed my-10"></div>
                                        <div class="my-5">
                                            <h3 class=" text-dark font-weight-bold mb-10">Product Images:</h3>

                                            <div class="form-group row">
                                                @foreach ($product->product_images as $image)
                                                    <div class="col-3 mb-2">

                                                        <img src="{{ Storage::url($image->image_path) }}" width="130"
                                                            height="130">
                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>
                                        <div class="separator separator-dashed my-10"></div>

                                        <div class="my-5">
                                            <h3 class=" text-dark font-weight-bold mb-10">Product Video:</h3>
                                            <div class="row">
                                                @foreach ($product->product_videos as $video)
                                                    <div class="col">
                                                        <video height="150" controls>
                                                            <source src="{{ Storage::url($video->video_path) }}">
                                                        </video>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-2"></div>
                    </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>



@endsection
@push('scripts')
    <script src="{{ asset('public/admin_assets/js/common.js') }}"></script>
    <script type="text/javascript">
        var tab = "{{ $tab }}";
        if (tab == 'reviews') {
            var data_url = "{{ admin_url('reviews/list-data') }}";
            var url_delete = "{{ admin_url('reviews/delete') }}";
            var url_page_base = "{{ admin_url('products') }}";
            var url_product_detail = "{{ admin_url('products/detail') }}";
        }
        var product_id = "{{ encryptDataId($product->id) }}";
        var token = '{{ csrf_token() }}';
    </script>
    @if ($tab == 'reviews')
        <script src="{{ asset('public/admin_assets/js/product_reviews_datatable.js') }}"></script>
    @endif

@endpush
