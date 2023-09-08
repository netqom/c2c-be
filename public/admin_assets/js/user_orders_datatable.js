"use strict";
// Class definition
var KTAppsProductDatatable = function() {
	
	var _demo = function(pSize) {
		var datatable = $('#kt_ordersTable').KTDatatable({
			// datasource definition
			data: {
				type: 'remote',
				source: {
					read: {
						url: data_url,
						params: {
						 "_token": token,
                         "created_by":user_id,
						 "order_types":order_type,
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
					field: 'uuid',
					title: 'Order ID',
					sortable: 'asc',
					width: 80,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						return '<span class="font-weight-bolder">'+ data.uuid +'</span>';
					}
				},
                {
                    field: 'user_name',
					title: 'User Name',
					sortable: 'asc',
					width: 100,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						return '<span class="">' + data.user_name+'</span>';
					} 
				},
				{
                    field: 'amount',
					title: 'Amount',
					sortable: 'asc',
					width: 80,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						return '<span class="">$' + data.amount+'</span>';
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
							'succeeded' : {'title': 'succeeded', 'class': ' label-light-success'},
							'COMPLETED' : {'title': 'COMPLETED', 'class': ' label-light-success'},
							'SAVED' : {'title': 'SAVED', 'class': ' label-light-warning'},
							'APPROVED' : {'title': 'APPROVED', 'class': ' label-light-warning'},
							'VOIDED' : {'title': 'VOIDED', 'class': ' label-light-warning'},
							'PAYER_ACTION_REQUIRED' : {'title': 'PAYER ACTION REQUIRED', 'class': ' label-light-warning'},
							'CREATED' : {'title': 'CREATED', 'class': ' label-light-warning'},
						};
				        return '<span class="label ' + status_control[data.status].class + ' label-inline font-weight-bold label-lg">' + status_control[data.status].title + '</span>' +
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
						return '<a class="btn btn-sm btn-light-warning btn-icon" href="'+url_page_base+'/detail/'+data.id+'" title="View Detail"><i class="icon-1x text-dark-50 flaticon-eye"></i></a>';

						// return '<a class="btn btn-sm btn-light-warning btn-icon" href="'+url_page_base+'/detail/'+data.id+'" title="View Detail"><i class="icon-1x text-dark-50 flaticon-eye"></i></a>&nbsp&nbsp\n\
						//        <a class="btn btn-sm btn-light-danger btn-icon" href="javascript:;" onclick="deleteOrder('+data.id+');" title="Delete"><i class="icon-1x text-dark-50 flaticon-delete"></i></a>';
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
			$('#kt_ordersTable').KTDatatable('reload');
		}
		
	};
}();

jQuery(document).ready(function() {
	KTAppsProductDatatable.init();
});