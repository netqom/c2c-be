$(document).ready(function () {
    //ajax add csrf token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
	
	//custom password validation method
	$.validator.addMethod("pwcheck", function(value) {
	   return /^[A-Za-z0-9\d=!\-@._*]*$/.test(value) // consists of only these
		   && /[a-z]/.test(value) // has a lowercase letter
		   && /\d/.test(value) // has a digit
	}, 'Must be 8 characters long, must contain special character, letters and numbers');
	
	// connect it to a css class
	jQuery.validator.addClassRules({
		pwcheck : { pwcheck : true }    
	});

	
	//custom email validation method
	jQuery.validator.addMethod("validate_email", function(value, element, param) {
		return value.match(/^[a-zA-Z0-9_\.%\+\-]+@[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,}$/);
	},'Please enter a valid Email');
	
	// connect it to a css class
	jQuery.validator.addClassRules({
		validate_email : { validate_email : true }    
	});
	
	//custom phone no validation method
	jQuery.validator.addMethod("validate_phone", function (phone_number, element) {
        phone_number = phone_number.replace(/\s+/g, "");
        return this.optional(element) || phone_number.length > 9 &&
              phone_number.match(/\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/);
    }, "Please enter a valid phone number");
	
	// connect it to a css class
	jQuery.validator.addClassRules({
		validate_phone : { validate_phone : true }    
	}); 
	
	//validate non zero
	jQuery.validator.addMethod("notZero", function (value, element, param) {
		//return this.optional(element) || parseInt(value) > 0;
		//var regex = /(?:\d*\.\d{1,2}|\d+)$/;
		//return this.optional(element) || parseInt(value) != 0 && /(?:\d*\.\d{1,2}|\d+)$/.test(value);
		return (value != 0) && (value == parseFloat(value, 10)); 
	}, "Please enter value greater then zero");
	

	// connect it to a css class
	jQuery.validator.addClassRules({
		notZero : { notZero : true }    
	});
	
	//validate greater then
	jQuery.validator.addMethod("checkGreaterThen", function(value, element, params) {
		if ($(params).val() != '') {    
			if (!/Invalid|NaN/.test(new Date(value))) {
				return new Date(value) > new Date($(params).val());
			}    
			return isNaN(value) && isNaN($(params).val()) || (Number(value) > Number($(params).val()));
		};
		return true; 
	});

	// connect it to a css class
	jQuery.validator.addClassRules({
		checkGreaterThen : { checkGreaterThen : true }    
	});
	
	jQuery.validator.addMethod("greaterThan", function (value, element, param) {
          var $otherElement = $(param);
          //return parseInt(value, 10) < parseInt($otherElement.val(), 10);
          return parseFloat(value) < parseFloat($otherElement.val());
    });
	
	jQuery.validator.addClassRules({
		greaterThan : { greaterThan : true }    
	});

	//Jquery validator default setting
	jQuery.validator.setDefaults({
		/*onfocusout: function (e) {
			this.element(e);
		},
		onkeyup: false,*/
		onfocusout: false,
		invalidHandler: function(form, validator) {
			var errors = validator.numberOfInvalids();
			if (errors) {                    
				validator.errorList[0].element.focus();
			}
		}, 
		highlight: function (element) {
			jQuery(element).closest('.form-control').addClass('is-invalid');
		},
		unhighlight: function (element) {
			jQuery(element).closest('.form-control').removeClass('is-invalid');
			jQuery(element).closest('.form-control').addClass('is-valid');
		},

		errorElement: 'div',
		errorClass: 'invalid-feedback',
		errorPlacement: function (error, element) {
			if ( element.prop( "type" ) === "checkbox" ) {
				error.insertAfter( element.parent( "div" ) );
			}else if ( element.prop( "type" ) === "radio" ) {
				error.insertAfter( element.parent( "div" ) );
			} else {
				error.insertAfter( element );
			}
		},
	});
	
	//Validate login form
	$('#login_submit').on('click', function (e) {
		e.preventDefault();
		var form = $('#login-form');
		form.validate();
		if (form.valid()) {
			$('#login-form').submit();
		}
	});
});

/*******************************
 Message Section
 *******************************/

function infoLoadingBox() {
	var load_html = '<div class="spinner spinner-track spinner-primary"> Please wait...</div>';
	swal.fire({
        html:load_html,
		heightAuto: false,
		width: 150,
		padding: 0,
        showConfirmButton: false,
        allowOutsideClick: false
    });
	$('.swal2-html-container').css('margin-top', '-16px')
	$('.swal2-html-container').css('margin-left', '15px')
	$('.swal2-html-container').css('overflow-y', 'hidden');
	$('.swal2-html-container').css('overflow-x', 'hidden');
	$('.swal2-popup').css('height', '35px');
	$('.swal2-popup').css('background-color', '#f9f7f8');
	
}

function showCustomeMessage(msg_title, message, msg_type) {
    swal.fire({title:msg_title, text:message, icon:msg_type, heightAuto: false, width: 350, timer:ALERT_CLOSE});
	$('.swal2-popup').css('height', '250px');
}

/*******************************
 Random Section
 *******************************/
 
 function validateForm(form_id)
{
	var form = $("#"+form_id);
    form.validate();
    if (form.valid()) {
		return true
       //form[0].submit();
	}
	return false;
}
 
/*******************************
 User Section
 *******************************/
function addEditUsers(id = 0)
{
	infoLoadingBox();
	$.ajax({
		url: BASE_URL +'/admin/users/get-add-edit-form/' + id,
		type: 'GET',
		success: function (response) {
			if(response.type == 'success'){
				swal.close();
				$('#common_modal').find('.modal-title').text(response.title);
				$('#common_modal').find('.modal-body').html(response.html);
				$('#common_modal').modal('show');
			}else{
				showCustomeMessage("Error!", response.msg, response.type);
			}
		}
	});
}

function validateUsersForm(form_id)
{
	var form = $("#"+form_id);
    form.validate();
    if (form.valid()) {
       infoLoadingBox();
		var formData = new FormData(form[0]);
        $.ajax({
            method: "POST",
            url: BASE_URL + "/admin/users/add-update",
            data: formData,
			enctype: 'multipart/form-data',
			contentType: false,
			processData: false,
        }).done(function (response) {
			if(response.type == 'error'){
				showCustomeMessage("Warning!", response.msg, response.type);
			}else{
				$("#admin-profile").css({"background-image": "url('"+response.data.display_user_image+"')"});  
				$('#common_modal').modal('hide');
				showCustomeMessage("Created!", response.msg, response.type);
			}
        });
    }
}

function deleteUser(id)
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
				url: BASE_URL +'/admin/users/' + id,
				type: 'DELETE',
				success: function (response) {
				    if(response.type == 'success'){
						showCustomeMessage("Deleted!", response.msg, response.type);
						KTAppsDatatableUser.reload();
					}else{
						showCustomeMessage("Error!", response.msg, response.type);
					}
					
				}
			});
	   }
	})
}

function updateAdminPassword()
{
	$('#update_password_modal').modal('show');
}

function validateSubmitChangePassword(form_id)
{
	var form = $("#"+form_id);
	form.validate({
		rules: {
			password:{
				required:true,
				minlength:8,
				pwcheck:true
			},
			confirm_password:{
				required:true,
				minlength:8,
				equalTo :'#password'
			}
		}
    });
    if (form.valid()) {
       infoLoadingBox();
		var formData = form.serialize();
        $.ajax({
            method: "POST",
            url: BASE_URL + "/admin/users/change-password",
            data: formData,
        }).done(function (response) {
			if(response.type == 'error'){
				showCustomeMessage("Warning!", response.msg, response.type);
			}else{
				$('#update_password_modal').modal('hide');
				showCustomeMessage("Updated!", response.msg, response.type);
			}
        });
    }
}
/*******************************
 Product Category Section
 *******************************/
function addEditProductCatForm(id = 0)
{
	infoLoadingBox();
	$.ajax({
		url: BASE_URL +'/admin/categories/add-edit/' + id,
		type: 'GET',
		success: function (response) {
			if(response.type == 'success'){
				swal.close();
				$('#common_modal').find('.modal-title').text(response.title);
				$('#common_modal').find('.modal-body').html(response.html);
				$('#common_modal').modal('show');
			}else{
				showCustomeMessage("Error!", response.msg, response.type);
			}
		}
	});
}

function validateProductCatForm(form_id)
{
	var form = $("#"+form_id);
    form.validate();
    if (form.valid()) {
       infoLoadingBox();
		var formData = new FormData(form[0]);
        $.ajax({
            method: "POST",
            url: BASE_URL + "/admin/categories/add-update",
            data: formData,
			enctype: 'multipart/form-data',
			contentType: false,
			processData: false,
        }).done(function (response) {
			if(response.type == 'error'){
				showCustomeMessage("Warning!", response.msg, response.type);
			}else{
				KTAppsDatatable_cat.reload();
				$('#common_modal').modal('hide');
				showCustomeMessage("Created!", response.msg, response.type);
				
			}
        });
    }else{
		alert('ss')
	}
}

function deleteProductCat(id)
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
				url: BASE_URL +'/admin/categories/' + id,
				type: 'DELETE',
				success: function (response) {
				    if(response.type == 'success'){
						showCustomeMessage("Deleted!", response.msg, response.type);
						KTAppsDatatable_cat.reload();
					}else{
						showCustomeMessage("Error!", response.msg, response.type);
					}
					
				}
			});
	   }
	})
}

/*******************************
 Product Section
 *******************************/

function validateProduct(form_id)
{
	var form = $("#"+form_id);
    form.validate();
    if (form.valid()) {
		return true
       //form[0].submit();
	}
	return false;
}

function deleteProduct(id)
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
				url: BASE_URL +'/admin/products/delete/' + id,
				type: 'DELETE',
				success: function (response) {
				    if(response.type == 'success'){
						showCustomeMessage("Deleted!", response.msg, response.type);
						KTAppsProductDatatable.reload();
					}else{
						showCustomeMessage("Error!", response.msg, response.type);
					}
					
				}
			});
	   }
	})
}

/*******************************
 Notification Section
 *******************************/

function deleteNotificaion(id)
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
				url: BASE_URL +'/admin/notifications/' + id,
				type: 'DELETE',
				success: function (response) {
				    if(response.type == 'success'){
						showCustomeMessage("Deleted!", response.msg, response.type);
						KTAppsDatatableNotification.reload();
					}else{
						showCustomeMessage("Error!", response.msg, response.type);
					}
					
				}
			});
	   }
	})
}