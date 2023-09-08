"use strict";
// Class definition
var KTAppsDatatableUser = function() {
	var _demo = function(pSize) { 
		var datatable = $('#kt_help_datatable').KTDatatable({
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
					field: 'unique_ticket',
					title: 'ticket id',
					sortable: 'asc',
					width: 120,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						return '<span class="">'+data.unique_ticket+'</span>';
					}
				},
				{
					field: 'users.email',
					title: 'email',
					sortable: 'asc',
					width: 200,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						return '<span class="">'+data.users.email+'</span>';
					}
				},
				{
					field: 'subject',
					title: 'Subject',
					sortable: 'asc',
					width: 100,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						return '<span class="">'+data.subject+'</span>';
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
							'Resolve' : {'title': 'Resolve', 'class': ' label-light-success'},
							'Reject' : {'title': 'Reject', 'class': ' label-light-danger'},
							'Pending' : {'title': 'Pending', 'class': ' label-light-warning'},
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
						if(data.status != 'Pending'){
							return `<a class="btn btn-sm btn-light-primary btn-icon" href="`+url_page_base+`/ticket-threads/`+data.unique_ticket+`" title="View"><i class="icon-1x text-dark-50 flaticon-eye"></i></a>`;
						}else{
								return `<div class="dropdown dropdown-inline">
									<a href="javascript:;" class="btn btn-sm btn-light-primary btn-icon" data-toggle="dropdown" aria-expanded="false">
											<i class="la la-cog"></i>
									</a>
									<div class="dropdown-menu dropdown-menu-sm dropdown-menu-right" style="display: none;">
										<ul class="nav nav-hoverable flex-column">							    		
											<li class="nav-item act-help-cls" data-id="`+data.id+`" data-status="Resolve">
												<a class="nav-link" href="javascript:;">
													<i class="flaticon2-check-mark mr-2"></i>
													<span class="nav-text">Resolve</span>
												</a>
											</li>							    		
											<li class="nav-item act-help-cls" data-id="`+data.id+`" data-status="Reject">
												<a class="nav-link" href="javascript:;">
													<i class="flaticon2-delete mr-2"></i>
													<span class="nav-text">Reject</span>
												</a>
											</li>	
										</ul>							  	
									</div>
								</div>
								<a class="btn btn-sm btn-light-primary btn-icon" href="`+url_page_base+`/ticket-threads/`+data.unique_ticket+`" title="View"><i class="icon-1x text-dark-50 flaticon-eye"></i></a>`;
						}
						
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
			$('#kt_help_datatable').KTDatatable('reload');
		}
	};
}();

jQuery(document).ready(function() {
	KTAppsDatatableUser.init();
});


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


/** Ticket Status Update Ajax Here  **/
$(document).on('click','.act-help-cls',function(){
	let id = $(this).data('id');
	let status = $(this).data('status');
	$.ajax({
		url: BASE_URL +'/admin/help-center/ticket-action',
        data:{'id': id, 'status':status} ,
		type: 'POST',
		success: function (response) {
		    if(response.type == 'success'){
				showCustomeMessage("Ticket!", response.msg, response.type);
				KTAppsDatatableUser.reload();
			}else{
				showCustomeMessage("Error!", response.msg, response.type);
			}
			
		}
	});
})