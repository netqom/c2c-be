@extends('layouts.admin')
@section('content')
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
        <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <!--begin::Info-->
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <!--begin::Page Heading-->
                    <div class="d-flex align-items-baseline mr-5">
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                            <li class="breadcrumb-item">
                                <a href="{{ admin_url() }}" class="text-muted">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ admin_url('help-center') }}" class="text-muted">Tickets</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a href="javascript:;" class="">Ticket Thread</a>
                            </li>
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page Heading-->
                </div>
                <!--end::Info-->
                <div class="mr-5">
                    <a href="{{ admin_url('help-center') }}"><button class="btn btn-primary">Back to List</button></a>
                </div>
            </div>
        </div>
        <!--end::Subheader-->

        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <div class=" container ">
                <div class="card card-custom">
                    <div class="card-body">
                        @if($ticketData->product != null)
                        
                            <div class="d-flex justify-content-evenly text-center w-100 mb-4">
                            <h6 class="col">Product Name: <a href="{{ url('admin/products/detail/'.$ticketData->product->slug)}}"><span class="text-primary">{{ $ticketData->product->title}}</span></a></h6>
                                <h6 class="col">Price: <span class="text-primary">${{ $ticketData->product->price}}</span></h6>
                                <h6 class="col">Owner Name: <span class="text-primary">{{ $ticketData->product->users->name}}</span></h6>
                            </div>
                        @endif
                        <div class="chat-wrapper">
                            <section class="chat mw-1000 mx-auto w-100" style="border-left: 1px solid rgb(29, 77, 157);">
                                <div class='chat-loader' style="display:none;">
                                    <img src="{{ asset('admin_assets/media/images/spinner.gif') }}" alt="" title="" />
                                </div>
                                <div class="messages-chat combined-chat">
                                    <p class="message-load-more" style="margin-top: -11px;" id="loadMoreBtnId"><a onclick="loadMoreData()" class="text-white">Load
                                            More...</a>
                                    </p>
                                    <!-- All Message Display here -->
                                    <div id="msg-out-div"></div>
                                    <div id="end-div-msg"></div>
                                </div>
                                <div class="footer-chat">

                                    <div class="small-img-box align-items-center justify-content-between mb-3 p-2 rounded-2 attached-file-wrap" style="display:none;">
                                        <div class=" uploaded-attachment" key={index}>
                                            <h6 class="attachment-name" id="urlCreateImageName">
                                                
                                            </h6>
                                            <img src="" id="urlCreateImage" height=40 width=40 />
                                            <i
                                                class="fa fa-times"
                                                aria-hidden="true"
                                                data-id={index}
                                                data-type="local"
                                                onclick="deleteImage()"
                                            ></i>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="attach-btn">
                                            <svg aria-hidden="true" focusable="false" id="fileuploadOtherIcon" data-prefix="fas"
                                                data-icon="paperclip" class="svg-inline--fa fa-paperclip " role="img"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                <path fill="currentColor"
                                                    d="M360.2 83.8c-24.4-24.4-64-24.4-88.4 0l-184 184c-42.1 42.1-42.1 110.3 0 152.4s110.3 42.1 152.4 0l152-152c10.9-10.9 28.7-10.9 39.6 0s10.9 28.7 0 39.6l-152 152c-64 64-167.6 64-231.6 0s-64-167.6 0-231.6l184-184c46.3-46.3 121.3-46.3 167.6 0s46.3 121.3 0 167.6l-176 176c-28.6 28.6-75 28.6-103.6 0s-28.6-75 0-103.6l144-144c10.9-10.9 28.7-10.9 39.6 0s10.9 28.7 0 39.6l-144 144c-6.7 6.7-6.7 17.7 0 24.4s17.7 6.7 24.4 0l176-176c24.4-24.4 24.4-64 0-88.4z">
                                                </path>
                                            </svg>
                                            <input type="hidden" name="unique_ticket" value="{{ request()->segment(count(request()->segments())) }}" />
                                            <input type="file" onchange="onChangeFile(this)" id="fileupload" style="display: none;">
                                        </span>
                                        <textarea row="3" cols="40" id="message" class="write-message" placeholder="Type your message here" spellcheck="false"></textarea>
                                        <span class="msg-send" id="sendMessage">
                                            <svg aria-hidden="true"  focusable="false" data-prefix="fas"
                                                data-icon="paper-plane" class="svg-inline--fa fa-paper-plane "
                                                role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                <path fill="currentColor"
                                                    d="M498.1 5.6c10.1 7 15.4 19.1 13.5 31.2l-64 416c-1.5 9.7-7.4 18.2-16 23s-18.9 5.4-28 1.6L284 427.7l-68.5 74.1c-8.9 9.7-22.9 12.9-35.2 8.1S160 493.2 160 480V396.4c0-4 1.5-7.8 4.2-10.7L331.8 202.8c5.8-6.3 5.6-16-.4-22s-15.7-6.4-22-.7L106 360.8 17.7 316.6C7.1 311.3 .3 300.7 0 288.9s5.9-22.8 16.1-28.7l448-256c10.7-6.1 23.9-5.5 34 1.4z">
                                                </path>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('public/admin_assets/js/common.js') }}"></script>
    <script type="text/javascript">


        let page = 1;

        let messageId = 0;
    
        let unique_ticket =  `{{ request()->segment(count(request()->segments())) }}`;

        let finalMessageArray = [];
        
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        //Delete File From Files Array
        function deleteImage(){
            $('#fileupload').val('');
            $('.small-img-box').css('display','none')
        }
        
        // Onchange file function 
        function onChangeFile (event){
            let file = event.files[0];
            const fileSizeKiloBytes = file.size / 1024;
            if (fileSizeKiloBytes > 5120) {
                toastAlert("error","File size is greater than maximum limit 5MB.");
                document.getElementById("fileupload").value = "";
                return false;
            }
            if (!file.name.toLowerCase().match(/\.(jpg|jpeg|png|gif)$/)) {
                toastAlert("error","Select file format is not allowed,Only jpg,jpeg,png allowed");
                document.getElementById("fileupload").value = "";
                return false;
            }
            console.log('file',file)
            $('.small-img-box').css('display','block')
            $('#urlCreateImage').attr("src",URL.createObjectURL(file))
            $('#urlCreateImageName').html(file.name)
        }

        //Call Get Chat Message
        getChatMessage();


        setInterval(() => {
            page = 1; 
            getChatMessage() 
        },60000*5);

        //when user select an image
        $('#fileuploadOtherIcon').click(function(event) {
            $('#fileupload').trigger('click');
        });


        // Send Message Function
        $('#sendMessage').click(function(event){
            let that = $(this);
            that.attr('disabled','');
            $('.chat-loader').css('display','block')
            let files = document.getElementById('fileupload').files;
            let message = $('#message').val();
            let receiver_id = `{{$ticketCreator}}`;
            if (message === "" && files.length === 0) {
                toastAlert('error', "Please add some text or select a file to send a message.");
                return false;
            }
            
            /** Files Validation ***/
            if (files.length != 0) {
                var current_file = files[0];
                const fileSizeKiloBytes = current_file.size / 1024;
                if (fileSizeKiloBytes > 5120) {
                    toastAlert("error","File size is greater than maximum limit 5MB.");
                    return false;
                }
                if (!current_file.name.toLowerCase().match(/\.(jpg|jpeg|png|gif)$/)) {
                    toastAlert("error","Select file format is not allowed");
                    return false;
                }
            }

            let formData = new FormData();
            for (let i = 0; i < files.length; i++) {
                formData.append(`file[${i}]`, files[i]);
            }
            formData.append('message', message);
            formData.append('receiver_id', receiver_id);
            formData.append('unique_ticket', unique_ticket,);

            $.ajax({
				url: BASE_URL +'/admin/help-center/send-message',
                data:formData,
				type: 'POST',
                contentType: false,
                cache: false,
                processData:false,
				success: function (response) {
                    that.removeAttr('disabled');
                    $('.chat-loader').css('display','none')
				    if(response.status == false){
                        toastAlert("error",response.message);
                    }else{
                        $('.small-img-box').css('display','none')
                        $('#message').val('')
                        $('#fileupload').val('')
                        //During send message prepare fresh finalMessageArray array
                        finalMessageArray = [];
                        page = 1; 
                        getChatMessage(); 
                    }				
				}
			});
            
        })

        /** Load More Data On Button Click **/
        function loadMoreData(){
            page = page + 1;
            messageId = $('#msg-out-div').find('div:first-child').attr('id');
            setTimeout(() => { document.getElementById(messageId).focus(); }, 150);
            getChatMessage();
        }

        /** Get chat message related to specfic ticket **/
        function getChatMessage(){
            let data = { pagination : { perpage : 10 , page : page },query : { unique_ticket : unique_ticket}};
            $.ajax({
				url: BASE_URL +'/admin/help-center/get-ticket-chat',
                data:data,
				type: 'POST',
				success: function (response) {
				    if(response.status == false){
                        toastAlert("error",response.message);
                    }else{

                        //check if page less than current page then show load more button  otherwise hide
                        if(response.meta.pages <= page){
                            $('#loadMoreBtnId').css('display','none')
                        }else{
                            $('#loadMoreBtnId').css('display','block')
                        }

                        //Merge Array 
                        finalMessageArray = [...response.data.reverse(), ...finalMessageArray];
                        if(finalMessageArray.length !== 0){   
                            $('#msg-out-div').html(prepareMessageHtml());
                            if(page == '1'){
                                // Reach to scroll down
                                let endDivMsg = document.getElementById('end-div-msg');
                                setTimeout(()=>{
                                    endDivMsg.scrollIntoView({ behavior: 'smooth', block: 'end', inline: 'nearest' });
                                },150)
                            }
                            
                        }
                    }					
				}
			});
        }

        function prepareMessageHtml(){
            let userId = `{{Auth::id()}}`;
            let htmlCreate = '';
            finalMessageArray.map((item,index) => {
                    let justifyCls = item.sender_id == userId ? 'justify-content-end' : '';
                    let dynImage = item.sender.display_user_image;
                    let dynUserName = item.sender.name;
                    let supportFile = "";
                    let supportFileVisibility = "none";
                    let msgVisibility = "none";
                    if(item.message){
                        msgVisibility = "block";
                    }
                    if(item.support_ticket_file) {
                        supportFile = BASE_URL +'/storage/'+ item.support_ticket_file.file_path;
                        supportFileVisibility = "block";
                    }
                    htmlCreate += `<div id=`+item.id+` tabindex="0"
                                    class="message align-items-start `+justifyCls+`">
                                    <div class="chat-pic"><img class="rounded-circle"
                                            src=`+dynImage+`
                                            alt="no-image" width="45" height="45">
                                        <div class="online"></div>
                                    </div>
                                    <div class="chat-txt">
                                        <div class="title-txt">
                                            <span class="name">`+dynUserName+`, 
                                               <span class="time">`+ moment(new Date(item.created_at)).fromNow() +`</span>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column response ">
                                            <p class="text" style="display:`+msgVisibility+`">`+item.message+`</p>
                                            <div className="attachetment-files mb-3" onclick="callMe('${supportFile}')" style="display:`+supportFileVisibility+`" >
                                                <div>
                                                    <img className="rounded-1" src="`+supportFile+`" alt="file" height="50px;" width="50px;" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
            })
            
            return htmlCreate;
        }

        function callMe(fileLink){
            window.open(fileLink, "_blank", 'noopener,noreferrer')
        }

    </script>
@endpush
