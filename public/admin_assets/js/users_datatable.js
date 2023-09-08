"use strict";
// Class definition

var datatable;
var startDate = moment().subtract(12, 'month');
var endDate = moment();

var KTAppsDatatableUser = function () {
	var _demo = function (pSize) {
		datatable = $('#kt_user_datatable').KTDatatable({
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
					template: function (data, index) {
						var prevPageLength = datatable.getCurrentPage() != 1 ? (parseFloat(datatable.getCurrentPage() - 1) * parseFloat(pSize)) : 0
						index = prevPageLength + (index + 1);
						return '<span class="font-weight-bolder">' + index + '.</span>';
					}
				},
				{
					field: 'name',
					title: 'Name',
					sortable: 'asc',
					width: 100,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function (data) {
						return '<span class="">' + data.name + '</span>';
					}
				},
				{
					field: 'email',
					title: 'Email',
					sortable: 'asc',
					width: 200,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function (data) {
						return '<span class="">' + data.email + '</span>';
					}
				},
				{
					field: 'phone',
					title: 'Phone',
					sortable: 'asc',
					width: 100,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function (data) {
						var phone = '';
						if (data.phone != null) {
							var phone = data.phone;
						}
						return '<span class="">' + phone + '</span>';
					}
				},
				// {
				//     field: 'role',
				// 	title: 'Role',
				// 	sortable: 'asc',
				// 	width: 100,
				// 	type: 'string',
				// 	selector: false,
				// 	textAlign: 'left',
				// 	template: function(data) {
				// 		return '<span class="">' + data.role_name+'</span>';
				// 	} 
				// },
				{

					field: 'status',
					title: 'Status',
					sortable: 'asc',
					width: 100,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function (data) {
						var status_control = {
							1: { 'title': 'Active', 'class': ' label-light-success' },
							0: { 'title': 'In-Active', 'class': ' label-light-warning' },
						};
						return '<span class="label ' + status_control[data.status].class + ' label-inline font-weight-bold label-lg" onclick="changeStatus(' + data.id + ',' + data.status + ')"  style="cursor:pointer">' + status_control[data.status].title + '</span>' +
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
					template: function (data) {
						return '<a class="btn btn-sm btn-light-primary btn-icon" href="' + url_page_base + '/profile/' + data.id + '" title="View Profile"><i class="icon-1x text-dark-50 flaticon-eye"></i></a>'
						// &nbsp&nbsp\n\
						// <a class="btn btn-sm btn-light-warning btn-icon" href="javascript:;" onclick="addEditUsers('+data.id+');" title="Edit User"><i class="icon-1x text-dark-50 flaticon-edit"></i></a>'
						// &nbsp&nbsp\n\
						// <a class="btn btn-sm btn-light-danger btn-icon" href="javascript:;" onclick="deleteUser('+data.id+');" title="Delete User"><i class="icon-1x text-dark-50 flaticon-delete"></i></a>';
					}
				}
			],
		});

		return {
			datatable: function () {
				return datatable;
			}
		};

	};

	return {
		// public functions
		init: function () {
			_demo(10);
			$('#kt_user_datatable').KTDatatable({}).setDataSourceParam('start_date', startDate.format('YYYY-MM-DD'));
			$('#kt_user_datatable').KTDatatable({}).setDataSourceParam('end_date', endDate.format('YYYY-MM-DD'));
		},
		reload: function () {
			// datatable.reload();
			$('#kt_user_datatable').KTDatatable('reload');
		}
	};
}();





jQuery(document).ready(function () {


	KTAppsDatatableUser.init();

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

	if (datatable) {
		datatable.setDataSourceParam('start_date', startDate.format('YYYY-MM-DD'));
		datatable.setDataSourceParam('end_date', endDate.format('YYYY-MM-DD'));
		datatable.reload();
	}

	$('#reportrange span').html(startDate.format('MMMM D, YYYY') + ' - ' + endDate.format('MMMM D, YYYY'));
}

const changeStatus = (id, status) => {
	//console.log(id,'id'); console.log(status,'status'); 
	if (status == 1) {
		var changeToStatusText = 'Deactivate';
		var changeToStatus = 0;
	} else {
		var changeToStatusText = 'Activate';
		var changeToStatus = 1;
	}
	Swal.fire({
		title: "Are you sure?",
		text: "Want to " + changeToStatusText + " this  user",
		icon: "warning",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Yes, " + changeToStatusText + " it!",
	}).then((result) => {
		if (result.value) {
			$.ajax({
				url: url_activate_deactivate,
				type: "POST",
				data: { id: id, status: status, changeToStatus: changeToStatus, changeToStatusText: changeToStatusText },
				success: function (response) {
					if (response.type == 'success') {
						toastAlert(response.type, response.msg);
						KTAppsDatatableUser.reload();
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

const toastAlert = (msg_type, message, msg_position = 'top-right', close_time = '4000', auto_close = true) => {
	Swal.fire({
		toast: true,
		icon: msg_type,
		title: message,
		position: msg_position,
		showConfirmButton: false,
		timer: close_time,
		timerProgressBar: true,
		didOpen: (toast) => {
			toast.addEventListener('mouseenter', Swal.stopTimer)
			toast.addEventListener('mouseleave', Swal.resumeTimer)
		}
	})
}