@extends('layouts.admin')
@section('content')
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">

    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class=" container ">
            <!--begin::Chat-->
            <div class="d-flex flex-row">
                <!--begin::Aside-->
                <div class="flex-row-auto offcanvas-mobile w-350px w-xl-400px" id="kt_chat_aside">
                    <!--begin::Card-->
                    <div class="card card-custom">
                        <!--begin::Body-->
                        <div class="card-body lever-user">
                            <!--begin:Search-->
                            <div class="input-group input-group-solid">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <span
                                            class="svg-icon svg-icon-lg"><!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/icons/General/Search.svg--><svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24"></rect>
                                                    <path
                                                        d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z"
                                                        fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                                    <path
                                                        d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z"
                                                        fill="#000000" fill-rule="nonzero"></path>
                                                </g>
                                            </svg><!--end::Svg Icon--></span> </span>
                                </div>
                                <input type="text" class="form-control py-4 h-auto search-user" placeholder="Search user">
                            </div>
                            <!--end:Search-->

                            <!--begin:Users-->
                            <div class="mt-7 scroll scroll-pull" id="chat-user-list" >
                                <!-- Fill That Div With Ajax Response For User -->
                            </div>
                            <!--end:Users-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!-- <div class="offcanvas-mobile-overlay"></div> -->
                <!--end::Aside-->

                <!--begin::Content-->
                <div class="flex-row-fluid ml-lg-8" id="kt_chat_content">
                    <!--begin::Card-->
                    <div class="card card-custom">
                        <!--begin::Header-->
                        <div class="card-header align-items-center px-4 py-3">
                            <div class="d-flex flex-grow-1 justify-content-between text-left">
                                <div class="text-dark-75 font-weight-bold font-size-h5">Chat Messages</div>
                                <span class="chat-list-mob d-lg-none"><i class="fa-solid fa-comments"></i></span>
                            </div>
                        </div>
                        <!--end::Header-->

                        <!--begin::Body-->
                        <div class="card-body">
                            <!--begin::Scroll-->
                            <div class="scroll scroll-pull scroll-pull-message" data-mobile-height="350">
                                <!--begin::Messages-->
                                <div class="messages" id="chat-messages">
                                    <!-- Fill That Div With Ajax Response For User Messages -->
                                </div>
                                <!--end::Messages-->
                                <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                                    <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                                </div>
                                <div class="ps__rail-y" style="top: 0px; height: 338px; right: -2px;">
                                    <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 108px;"></div>
                                </div>
                            </div>
                            <!--end::Scroll-->
                        </div>
                        <!--end::Body-->

                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Chat-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
@endsection
@push('scripts')
<script type="text/javascript">
        $('.chat-list-mob').click(function(){
            $('#kt_chat_aside').addClass('offcanvas-mobile-on');
        })

        document.body.addEventListener('click', function(e) {
            if (!e.target.classList.contains('fa-comments')) {
                $('#kt_chat_aside').removeClass('offcanvas-mobile-on');
            }
        });

        $.ajaxSetup({  headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}'  } });

        /***********Search User Fun Start FRom Here*******************/
        var userPage = 1,stopCallAjaxForUser = true,currentRequest = null,searchKeyword= ''; 
        $('.search-user').keyup(function(){
            stopCallAjaxForMessage = true;
            searchKeyword = $(this).val();
            userPage = 1;
            getChatUserList();
            
        })
        /***********Search User Fun End Here*******************

        /*************Call Ajax To Get Chat Users List Start **************/
        function getChatUserList()
        {
            currentRequest = $.ajax({
                type:'POST',
                url:"{{ admin_url('chat-user-list') }}",
                data:{ pagination : { perpage : 10 , page : userPage , order_types : ''},user_id : `{{$user_id}}`, search_keyword : searchKeyword },
                beforeSend : function()    {           
                    if(currentRequest != null) {
                        currentRequest.abort();
                    }
                },
                success:function(data){
                    if(data.status){
                        if(userPage == 1){
                            $('#chat-user-list').html(data.data);
                        }else{
                            $('#chat-user-list').prepend(data.data);
                        }
                        $(".chat-click:first").trigger("click");
                    }else{
                        stopCallAjaxForUser = false;
                    }
                }
            });
        }

        getChatUserList();

        /*************Call Ajax To Get Chat Users List End **************/

        /*******************Scroll Related Users Div Start From Here *******/

        $('#chat-user-list').on('scroll', function() {
            if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
                if(stopCallAjaxForUser){
                    userPage = userPage + 1;
                    getChatUserList();
                }
            }
        });

        /*******************Scroll Related Users Div End From Here *******/


        /*************Call Ajax To Get Chat Messages Start **************/
        
        var chatId = '',userId = '',page = 1,stopCallAjaxForMessage = true;

        $(document).on('click','.chat-click',function(){
            stopCallAjaxForMessage = true; page = 1;
            $(this).parent().find('.active').removeClass('active')
            // add active class to clicked element
            $(this).addClass('active');
            chatId = $(this).data('chat-id');
            userId = $(this).data('user-id');
            getChatMessages(page);
        })

        function getChatMessages(page){
            if(chatId){
                $.ajax({
                   type:'POST',
                   url:"{{ admin_url('chat-messages') }}",
                   data:{ pagination : { perpage : 10 , page : page , order_types : '' }, sort : { field : 'created_at', sort : 'desc' }, chat_id : chatId,user_id : userId },
                   success:function(data){
                        if(page == 1){
                            $('#chat-messages').html(data.data);
                        }else{
                            $('#chat-messages').prepend(data.data);
                        }
                        if(data.status){
                            scroolBootomMessage();
                        }else{
                            scroolBootomMessage();
                            stopCallAjaxForMessage = false;
                        }
                      
                   }
                });    
            }
        }

        /*************Call Ajax To Get Chat Messages End **************/

        /*******************Scroll Related Message Div Start From Here *******/
        
        function scroolBootomMessage(){
          document.getElementsByClassName('last-message')[0].scrollIntoView();
        }

        $('.scroll-pull-message').on('scroll', function() {
          var scrollTop = $(this).scrollTop();
          if (scrollTop <= 0) {
            $('.last-message').removeClass('last-message');
            if(stopCallAjaxForMessage){
                page = page + 1 ;
                getChatMessages(page);    
            }
          }
        });

        /*******************Scroll Related Message Div End From Here *******/

</script>
<script src="{{ asset('public/admin_assets/js/chat.js') }}"></script>
@endpush