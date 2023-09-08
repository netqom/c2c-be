"use strict";
// Class definition
var KTAppsProductReviewsDatatable = function() {
	
	var _demo = function() {
		var datatable = $('#kt_productReviewsTable').KTDatatable({
			// datasource definition
			data: {
				type: 'remote',
				source: {
					read: {
						url: data_url,
						params: {
						 "_token": token,
                         "product_id": product_id,
						}
					},
				},
				pageSize: 10, // display 20 records per page
				serverPaging: true,
				serverFiltering: true,
				serverSorting: true,
			},

			// layout definition
			layout: {
				scroll: false, // enable/disable datatable scroll both horizontal and vertical when needed.
				footer: false, // display/hide footer
			},

			// column sorting
			sortable: true,

			pagination: true,

			search: {
				input: $('#kt_datatable_search_query'),
                key: 'search_string',
				delay: 400,
			},
			
			// columns definition
			columns: [
				{
					field: 'id',
					title: '#',
					sortable: 'desc',
					width: 40,
					type: 'number',
					selector: false,
					textAlign: 'left',
					template: function(data, index) {
						index = index+1;
						return '<span class="font-weight-bolder">' + index + '.</span>';
					}
				},
				{
                    field: 'review',
					title: 'Review',
					sortable: 'asc',
					width: 200,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						return '<span class="">'+data.review+'</span>';
					} 
				},
				{
                    field: 'rating',
					title: 'Rating',
					sortable: 'asc',
					width: 200,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						 return getRatingStars(data.rating);
					} 
				},
                {
                    field: 'review_by',
					title: 'Review By',
					sortable: 'asc',
					width: 200,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						return '<span class="">'+data.created_by_user_name+'</span>';
					} 
				},
				{
					 
                    field: 'status',
					title: 'Status',
					sortable: 'asc',
					width: 100,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						var status_control = {
							1: {'title': 'Active', 'class': ' label-light-success'},
							0: {'title': 'In-Active', 'class': ' label-light-warning'},
						};
						return '<span class="label ' + status_control[data.status].class + ' label-inline font-weight-bold label-lg" onclick="changeStatus('+data.id+','+data.status+')"  style="cursor:pointer" >' + status_control[data.status].title + '</span>';
					} 
				},
				{
                    field: 'action',
					title: 'Action',
					sortable: false,
					width: 100,
					type: 'number',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						return '<a class="btn btn-sm btn-light-danger btn-icon" href="javascript:;" onclick="deleteReview('+data.id+')"  title="Delete"><i class="icon-1x text-dark-50 flaticon-delete"></i></a>';
					} 
                }
             ],
		});
		
	};

	return {
		// public functions
		init: function() {
			_demo();
		},
		reload: function() {
		   // datatable.reload();
			$('#kt_productReviewsTable').KTDatatable('reload');
		}
		
	};
}();

jQuery(document).ready(function() {
	KTAppsProductReviewsDatatable.init();
});
const deleteReview = (id) => {
   	
    Swal.fire({
         title: "Are you sure?",
         text: "Want to delete this review?",
         icon: "warning",
         showCancelButton: true,
         confirmButtonColor: "#3085d6",
         cancelButtonColor: "#d33",
         confirmButtonText: "Yes, delete it!",
     }).then((result) => {
         if (result.value) { 
			 $.ajax({
                 url: url_delete, 
                 type: "POST",
                 data: {id: id },
                 success: function (response) {
                     if (response.type == 'success') {
                         toastAlert(response.type, response.msg);
                         KTAppsProductReviewsDatatable.reload();
                     } else {
                         toastAlert('error', response.msg);
                     }
                 },
                 error: function (err) {
                     var errr = JSON.parse(err.responseText);
                     toastAlert("error", errr.error);
                 },
             });
         }
     });
 }