<?php 
foreach ($data['data'] as $key => $value) { ?>
     <div class="d-flex align-items-center justify-content-between bg-hover-gray-400 mb-5 chat-click rounded-sm p-2 @if($key==0) last-user @endif" data-chat-id="{{$value->chat_id}}" data-user-id="{{$user->id}}">
         <div class="d-flex align-items-center">
             <div class="symbol symbol-circle symbol-50 mr-3">
                 <img alt="Pic" class="min-50" src="{{$value->product->display_path }}">
             </div>
             <div class="d-flex flex-column">
                <a href="javascript::void(0)" class="text-dark-75 font-weight-bold font-size-lg">{{ $value->product->title }}</a>
                <a href="javascript::void(0)" class="text-dark-75 font-weight-bold font-size-lg">
                    {{ $value->user->name}} <i class="fa-solid fa-arrows-left-right"></i> {{ $user->name}}
                </a>
             </div>
         </div>
     </div>
<?php }  ?>