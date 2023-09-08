"use strict";
// Class definition
var KTAppsDatatableFaq = function() {
	var _demo = function(pSize) { 
		var datatable = $('#kt_faq_datatable').KTDatatable({
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
					field: 'question',
					title: 'Question',
					sortable: 'asc',
					width: 100,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						return '<span class="">'+data.question+'</span>';
					}
				},
                 {
                    field: 'answer',
					title: 'Answer',
					sortable: 'asc',
					width: 200,
					type: 'string',
					selector: false,
					textAlign: 'left',
					template: function(data) {
                        var answer = data.answer;
                        if(answer.length > 100) answer = answer.substring(0,100);
						return '<span class="">'+answer+'</span>';
					} 
				},
				// {
					 
                //     field: 'status',
				// 	title: 'Status',
				// 	sortable: 'asc',
				// 	width: 100,
				// 	type: 'string',
				// 	selector: false,
				// 	textAlign: 'left',
				// 	template: function(data) {
				// 		var status_control = {
				// 			1: {'title': 'Active', 'class': ' label-light-success'},
				// 			0: {'title': 'In-Active', 'class': ' label-light-warning'},
				// 		};
				// 		return '<span class="label ' + status_control[data.status].class + ' label-inline font-weight-bold label-lg" onclick="changeStatus('+data.id+','+data.status+')"  style="cursor:pointer">' + status_control[data.status].title + '</span>' +
				// 		        '<br><span class="font-weight-bold text-default">' + moment(data.created_at, "YYYY-MM-DD").format("DD MMM, YY") + '</span>';
				// 	} 
				// },
				{
                    field: 'action',
					title: 'Action',
					sortable: false,
					width: 150,
					type: 'number',
					selector: false,
					textAlign: 'left',
					template: function(data) {
						var ret = '<a class="btn btn-sm btn-light-warning btn-icon" href="javascript:;" onclick="addFaq('+data.id+')"  title="Edit Feature"><i class="icon-1x text-dark-50 flaticon-edit"></i></a>&nbsp&nbsp\n\
                        <a class="btn btn-sm btn-light-danger btn-icon" href="javascript:;" onclick="deleteFaq('+data.id+');" title="Delete Faq"><i class="icon-1x text-dark-50 flaticon-delete"></i></a>';
					
					  return ret; 
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
			$('#kt_faq_datatable').KTDatatable('reload');
		}
	};
}();

jQuery(document).ready(function() {
	KTAppsDatatableFaq.init();
});
function deleteFaq(id)
{
	Swal.fire({
	  title: 'Are you sure?',
	  text: "You won't be able to revert this!",
	  icon: 'warning',
	  showCancelButton: true,
	  confirmButtonColor: '#3085d6',
	  cancelButtonColor: '#d33',
	  confirmButtonText: 'Yes, delete it!'
	}).then((result) => {
	   if (result.value == true) {
			infoLoadingBox();
			$.ajax({
				url: BASE_URL +'/admin/faqs/delete',
                data:'id='+id,
				type: 'POST',
				success: function (response) {
				    if(response.type == 'success'){
						showCustomeMessage("Deleted!", response.msg, response.type);
						KTAppsDatatableFaq.reload();
					}else{
						showCustomeMessage("Error!", response.msg, response.type);
					}
					
				}
			});
	   }
	})
}
function validateFaqsForm(form_id)
{
	var form = $("#"+form_id);
    form.validate();
    if (form.valid()) {
       infoLoadingBox();
		var formData = new FormData(form[0]);
        $.ajax({
            method: "POST",
            url: BASE_URL + "/admin/faqs/add-update",
            data: formData,
			enctype: 'multipart/form-data',
			contentType: false,
			processData: false,
        }).done(function (response) {
			if(response.type == 'error'){
				showCustomeMessage("Warning!", response.msg, response.type);
			}else{
			
				$('#common_modal').modal('hide');
				showCustomeMessage("Created!", response.msg, response.type);
                KTAppsDatatableFaq.reload();
			}
        });
    }
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
