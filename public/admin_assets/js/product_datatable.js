"use strict";
// Class definition
var datatable = null;
var startDate = moment().subtract(12, 'month');
var endDate = moment();

var KTAppsProductDatatable = function() {
	var _demo = function(pSize) {

		datatable = $('#kt_productTable').KTDatatable({
			// datasource definition
			data: {
				type: 'remote',
				source: {
					read: {
						url: data_url,
						params: {
						 "_token": token,
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
					title: 'Image',
					sortable: false,
					width: 50,
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
						var title_text = data.title;
						return title_text.length > 50 
						? 
						'<span class="" id="outer" data-shrink="' + title_text.substr(0, 50) + '" title="' + title_text + '"><a  href="'+url_product_detail+'/'+data.id+'">' +title_text.substr(0, 50)+'...</a>' 
						: 
						'<span class=""><a  href="'+url_product_detail+'/'+data.slug+'">' +title_text+'</a></span>';
					} 
				},

				{
                    field: 'name',
					title: 'Seller',
					sortable: false,
					width: 150,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						return `<a  href=` + BASE_URL +'/'+ USER_ROLE + '/users/profile/' + data.users.id + `>`+data.users.name+`</a>`
					} 
				},

				{
                    field: 'quantity',
					title: 'Sold Out',
					sortable: false,
					width: 150,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						if(data.quantity == 0){
							var className = 'label-light-danger'
							var title = 'Yes';
						}else{
							var className = 'label-light-success'
							var title = 'No';
						}
						return '<span class="label ' + className + ' label-inline font-weight-bold label-lg" >' + title + '</span>';
					} 
				},
				{
					 
                    field: 'status',
					title: 'Status',
					sortable: false,
					width: 100,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						var status_control = {
							1: {'title': 'Active', 'class': ' label-light-success'},
							0: {'title': 'In-Active', 'class': ' label-light-warning'},
						};
						return '<span class="label ' + status_control[data.status].class + ' label-inline font-weight-bold label-lg" onclick="changeStatus('+data.id+','+data.status+')"  style="cursor:pointer" >' + status_control[data.status].title + '</span>' +
						'<br><span class="font-weight-bold text-default">' + moment(data.created_at, "YYYY-MM-DD").format("DD MMM, YY") + '</span>';
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
						//Add Edit Product AlreadY Exist
						//return '<a class="btn btn-sm btn-light-warning btn-icon" href="'+url_page_base+'/add-edit/'+data.id+'" title="Edit"><i class="icon-1x text-dark-50 flaticon-edit"></i></a>&nbsp<a class="btn btn-sm btn-light-primary btn-icon" target="_blank" href="'+frontend_app_url+'/product-detail/'+data.slug+'"><i class="icon-1x text-dark-50 flaticon-eye"></i></a>&nbsp<a class="btn btn-sm btn-light-danger btn-icon" href="javascript:;" onclick="deleteProduct('+data.id+');" title="Delete"><i class="icon-1x text-dark-50 flaticon-delete"></i></a>';
						return '<a class="btn btn-sm btn-light-primary btn-icon" target="_blank" href="'+url_page_base+'/detail/'+data.slug+'"><i class="icon-1x text-dark-50 flaticon-eye"></i></a>&nbsp<a class="btn btn-sm btn-light-danger btn-icon" href="javascript:;" onclick="deleteProduct('+data.id+');" title="Delete"><i class="icon-1x text-dark-50 flaticon-delete"></i></a>';
					} 
                }
             ],
		});
		
	};

	return {
		// public functions
		init: function() {
			_demo(10);
			$('#kt_productTable').KTDatatable().setDataSourceParam('start_date', startDate.format('YYYY-MM-DD'));
			$('#kt_productTable').KTDatatable().setDataSourceParam('end_date', endDate.format('YYYY-MM-DD'));
		}		
	};
}();




jQuery(document).ready(function() {

	KTAppsProductDatatable.init();
	
	$('#reportrange').daterangepicker({
		startDate: startDate,
		endDate: endDate,
		ranges: {
		'Today': [moment(), moment()],
		'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
		'Last 7 Days': [moment().subtract(6, 'days'), moment()],
		'Last 30 Days': [moment().subtract(29, 'days'), moment()],
		'This Month': [moment().startOf('month'), moment().endOf('month')],
		'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		}
	}, cb);

	cb(startDate, endDate);

});


function cb(startDate, endDate) {
	startDate = startDate;
	endDate = endDate;
	if(datatable){
		datatable.setDataSourceParam('start_date', startDate.format('YYYY-MM-DD'));
		datatable.setDataSourceParam('end_date', endDate.format('YYYY-MM-DD'));
		datatable.reload();
	}
	
	$('#reportrange span').html(startDate.format('MMMM D, YYYY') + ' - ' + endDate.format('MMMM D, YYYY'));
}

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
 

// function showMore() {
//     var text = $('#outer').attr('title');
// 	console.log(text);
//     // $(this).text(text);
//     $('#show_more').after('<span id="less" onclick="showLess()" href="#"> Show less</span>');
//     $('#outer a').text(text);
// };


// function showLess() {
//     $('#less').remove();
//     var txt = $('#outer').attr('data-shrink');
// 	console.log("text", txt);
//     $('#show_more').text('');
//     $('#outer a').text(txt);
//     $('#show_more').text('...');
// }
 