"use strict";
// Class definition
var KTAppsProductDatatable = function() {
	
	var _demo = function(pSize) {
		var datatable = $('#kt_productTable').KTDatatable({
			// datasource definition
			data: {
				type: 'remote',
				source: {
					read: {
						url: data_url,
						params: {
						 "_token": token,
                         "created_by": user_id,
						}
					},
				},
				pageSize: pSize, // display 20 records per page
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
						var prevPageLength = datatable.getCurrentPage() != 1 ? (parseFloat(datatable.getCurrentPage() - 1) * parseFloat(pSize)) : 0
						index = prevPageLength + (index + 1);
						return '<span class="font-weight-bolder">' + index +  '.</span>';
					}
				},
				{
					field: 'image',
					title: 'Display Image',
					sortable: false,
					width: 200,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						return '<span class="font-weight-bolder"><img src="' + data.display_path + '" height="50px;" width="50px;" alt="product-image"></span>';
					}
				},
                {
                    field: 'title',
					title: 'Title',
					sortable: 'asc',
					width: 200,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						return '<span class=""> <a  href="'+url_product_detail+'/'+data.slug+'">' + data.title+'</a></span>';
					} 
				},
				{
                    field: 'quantity',
					title: 'Quantity',
					sortable: 'asc',
					width: 200,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						return '<span class="">' + data.quantity+'</span>';
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
						
						var status_control = [							
							{'title': 'In-Active', 'class': ' label-light-warning'},
							{'title': 'Active', 'class': ' label-light-success'},
						];
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
						return '<a class="btn btn-sm btn-light-warning btn-icon" href="'+url_page_base+'/add-edit/'+data.id+'" title="Edit"><i class="icon-1x text-dark-50 flaticon-edit"></i></a>&nbsp&nbsp\n\
						       <a class="btn btn-sm btn-light-danger btn-icon" href="javascript:;" onclick="deleteProduct('+data.id+');" title="Delete"><i class="icon-1x text-dark-50 flaticon-delete"></i></a>';
					} 
                }
             ],
		});
		
	};

	return {
		// public functions
		init: function() {
			_demo(10);
		},
		reload: function() {
		   // datatable.reload();
			$('#kt_productTable').KTDatatable('reload');
		}
		
	};
}();

jQuery(document).ready(function() {
	KTAppsProductDatatable.init();
});

const changeStatus = (id,status) => {
	if(status==1){
	  var changeToStatusText = 'Deactivate';
	  var changeToStatus = 0;
	}else{
	   var changeToStatusText = 'Activate';
	   var changeToStatus = 1;
	}	
	Swal.fire({
		 title: "Are you sure?",
		 text: "Want to "+changeToStatusText+" this product",
		 icon: "warning",
		 showCancelButton: true,
		 confirmButtonColor: "#3085d6",
		 cancelButtonColor: "#d33",
		 confirmButtonText: "Yes, "+changeToStatusText+" it!",
	 }).then((result) => {
		 if (result.value) { 
			 $.ajax({
				 url: url_activate_deactivate, 
				 type: "POST",
				 data: {
					 id: id,
					 status:status,
					 changeToStatus:changeToStatus,
					 changeToStatusText:changeToStatusText
				 },
				 success: function (response) {
 
					 if (response.type == 'success') {
						 toastAlert(response.type, response.msg);
						 setTimeout(function(){
						  location.reload();
						 },4000); 	
							 
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

 
 