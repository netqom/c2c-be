"use strict";
// Class definition
var datatable;
var startDate = moment().subtract(12, 'month');
var endDate = moment();

var KTAppsProductDatatable = function() {
	
	var _demo = function(pSize) {
		datatable = $('#kt_ordersTable').KTDatatable({
			// datasource definition
			data: {
				type: 'remote',
				source: {
					read: {
						url: data_url,
						complete: function(response) {
							// Access the entire AJAX response
							$('.cstm-total-trans').html('0.00')
							$('.cstm-total-reve').html('0.00')
							if(response.responseJSON){
								$('.cstm-total-trans').html(response.responseJSON.total_transaction == '0' ? '0.00' :  response.responseJSON.total_transaction)
								$('.cstm-total-reve').html(response.responseJSON.total_revenue == '0' ? '0.00' : response.responseJSON.total_revenue)
							}
						},
						params: {
						 "_token": token,
						},
						
					},
				},
				
				
				pageSize: pSize, // display 10 records per page
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
					width: 90,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						return '<span class="font-weight-bolder">'+ data.uuid +'</span>';
					}
				},
                {
                    field: 'user_name',
					title: 'Buyer Name',
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
						return '<span class="">£' + data.amount+'</span>';
					} 
				},
				{
                    field: 'admin_commission_value',
					title: 'Revenue',
					sortable: 'asc',
					width: 80,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						return '<span class="">$' + parseFloat(data.admin_commission_value).toFixed(2)+'</span>';
					} 
				},
				{
					 
                    field: 'payment_status',
					title: 'Status',
					sortable: 'asc',
					width: 100,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						var status_control = {
							'COMPLETED' : {'title': 'COMPLETED', 'class': ' label-light-success'},
							'REFUNDED' : {'title': 'REFUNDED', 'class': ' label-light-danger'},
							'SAVED' : {'title': 'SAVED', 'class': ' label-light-warning'},
							'APPROVED' : {'title': 'APPROVED', 'class': ' label-light-warning'},
							'VOIDED' : {'title': 'VOIDED', 'class': ' label-light-warning'},
							'PAYER_ACTION_REQUIRED' : {'title': 'PAYER ACTION REQUIRED', 'class': ' label-light-warning'},
							'CREATED' : {'title': 'CREATED', 'class': ' label-light-warning'},
							'succeeded' : {'title': 'succeeded', 'class': ' label-light-success'},
						};
				        return '<span class="label ' + status_control[data.payment_status].class + ' label-inline font-weight-bold label-lg">' + status_control[data.payment_status].title + '</span>' +
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
						var ret =  '<a class="btn btn-sm btn-light-warning btn-icon" href="'+url_page_base+'/detail/'+data.id+'" title="View Detail"><i class="icon-1x text-dark-50 flaticon-eye"></i></a>';
						if(data.payment_intent_id != null && (data.order_payouts != null && data.order_payouts.transfer_id == null)&& data.payment_status != 'REFUNDED'){	 
							ret+='&nbsp&nbsp\n\
							<a class="btn btn-sm btn-light-warning btn-icon pay_id_'+data.id+'" href="javascript:;" onclick="refundPayment('+data.id+');" title="Refund" data-id='+data.payment_intent_id+' data-amount='+data.amount+'><i class="icon-1x text-dark-50 fa fa-undo"></i></a>';
						}
						
						// '&nbsp&nbsp\n\
						//        <a class="btn btn-sm btn-light-danger btn-icon" href="javascript:;" onclick="deleteOrder('+data.id+');" title="Delete"><i class="icon-1x text-dark-50 flaticon-delete"></i></a>';

						return ret; 
					} 
                }
			],
		
		});
		
	};

	return {
		// public functions
		init: function() {
			_demo(10);
			$('#kt_ordersTable').KTDatatable({}).setDataSourceParam('start_date', startDate.format('YYYY-MM-DD'));
			$('#kt_ordersTable').KTDatatable({}).setDataSourceParam('end_date', endDate.format('YYYY-MM-DD'));
		},
		reload: function() {
		   // datatable.reload();
			$('#kt_ordersTable').KTDatatable('reload');
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

function refundPayment(id) 
{
	var payment_intent_id = $('.pay_id_'+id).attr('data-id');
	var amount = $('.pay_id_'+id).attr('data-amount');

	Swal.fire({
	  title: 'Are you sure?',
	  text: "You won't be able to revert this!",
	  icon: 'warning',
	  showCancelButton: true,
	  confirmButtonColor: '#3085d6',
	  cancelButtonColor: '#d33',
	  confirmButtonText: 'Yes, Refund the payment!'
	}).then((result) => {
	   if (result.value == true) {
			infoLoadingBox();
			$.ajax({
				url: BASE_URL +'/admin/refund-payment',
                data:{'payment_intent_id': payment_intent_id, 'amount':amount} ,
				type: 'POST',
				success: function (response) {
				    if(response.type == 'success'){
						showCustomeMessage("Refund!", response.msg, response.type);
						KTAppsProductDatatable.reload();
					}else{
						showCustomeMessage("Error!", response.msg, response.type);
					}
					
				}
			});
	   }
	})
}