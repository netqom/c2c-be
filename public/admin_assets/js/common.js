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

function getRatingStars(avg){
	avg = parseFloat(avg);
	console.log((avg/5)*(100),'avgx');
	var percentage = (avg/5)*(100);
	var html = '<div class="star-ratings" style="width:84px"><div class="fill-ratings"  style="width:'+percentage+'%" ><span class="neon-clr" style="width:84px">★★★★★</span></div><div class="empty-ratings"><span>★★★★★</span></div></div>';
	avg = avg.toPrecision(2);
	console.log(avg,'avg');

	    // if(avg==0){ console.log('0');
	    //  	html='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> </div>'; 
	    // }else if(avg>=1 && avg<=1.5){ console.log('1');
		//   html='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> </div>'; 
		// }else if(avg>=1.5 && avg<=2){ console.log('2');
		//   html='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> </div>'; 
		// }else if(avg>=2 && avg<=2.5){  console.log('3');
		//   html='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> </div>'; 
		// }else if(avg>=2.5 && avg<=3.0){  console.log('4');
		//  html='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> </div>'; 
		// }else if(avg>=3.0 && avg<=3.5){  console.log('5');
		//    html='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> </div>'; 
		// }else if(avg>=3.5 && avg<=4.0){ console.log('6');
		//    html='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> </div>'; 
		// }else if(avg>=4.0 && avg<=4.5){  console.log('7');
		//   html='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1"></i> </div> </div>'; 
		// }else if(avg>=4.5 && avg<=5.0){ console.log('8');
		//   html='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> </div>'; 
		// }else if(avg>=5.0 && avg<=5.5){ console.log('9');
		//    html='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> </div>'; 
		// }else if(avg>=5.0 && avg<=5.5){ console.log('10');
		//    html='<div class="rating d-flex"> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> <div class="rating-label me-2 checked"> <i class="flaticon-star fa-1x mr-1 text-warning"></i> </div> </div>'; 
		// }
		 return html;
  }