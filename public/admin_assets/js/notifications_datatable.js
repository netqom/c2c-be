"use strict";
// Class definition
var KTAppsDatatableNotification = function() {
	var _demo = function(pSize) { 
		var datatable = $('#kt_notification_datatable').KTDatatable({
			// datasource definition
			data: {
				type: 'remote',
				source: {
					read: {
						url: data_url,
						params: {
						 "_token": token
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
					field: 'from',
					title: 'From',
					sortable: false,
					width: 100,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						return '<span class="">'+data.from.name+'</span>';
					}
				},
                 {
                    field: 'description',
					title: 'Description',
					sortable: 'asc',
					width: 600,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						return '<span class="">' + data.description+'</span>';
					} 
				},
				{
					 
                    field: 'created_at',
					title: 'date',
					sortable: 'asc',
					width: 100,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						return '<br><span class="font-weight-bold text-default">' + moment(data.created_at, "YYYY-MM-DD").format("DD MMM, YY") + '</span>';
					} 
				},
				{
                    field: 'action',
					title: 'Action',
					sortable: false,
					width: 150,
					type: 'number',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						return '<a class="btn btn-sm btn-light-danger btn-icon" href="javascript:;" onclick="deleteNotificaion('+data.id+');" title="Delete Notification"><i class="icon-1x text-dark-50 flaticon-delete"></i></a>';
					} 
                }
                   ],
		});
		
		return {
            datatable: function() {
                return datatable;
            }
        };
		
	};

	return {
		// public functions
		init: function() {
			_demo(10);
		},
		reload: function() {
		   // datatable.reload();
			$('#kt_notification_datatable').KTDatatable('reload');
		}
	};
}();

jQuery(document).ready(function() {
	KTAppsDatatableNotification.init();
});

