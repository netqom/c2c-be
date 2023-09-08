<?php
    if(count($data['data']) > 0)
    {
        foreach ($data['data'] as $key => $value) { ?>
            <div class="d-flex flex-column mb-5 {{ $user_id == $value->user->id ?'align-items-end' : 'align-items-start' }} @if($key==0) last-message @endif">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-circle symbol-40 mr-3">
                        <img alt="Pic" src="{{ $value->user->display_user_image }}">
                    </div>
                    <div>
                        <a href="javascript::void(0)" class="text-dark-75 text-hover-primary font-weight-bold font-size-h6">{{ $value->user->name }}</a>
                        <span class="text-muted font-size-sm">{{ \Carbon\Carbon::parse($value->created_at)->diffForHumans() }}</span>
                    </div>
                </div>
                @if($value->content)
                    <div class="mt-2 rounded p-5 {{ $user_id == $value->user->id ? 'bg-light-primary' : 'bg-light-success'}} text-dark-50 font-weight-bold font-size-lg text-left max-w-400px">
                        {{ $value->content }}
                    </div>
                @endif
                @if($value->message_files != null)
                    <a href="{{$value->message_files->file_path}}" target="_blank" > <img src="{{$value->message_files->file_path}}" height="70" width="70" /> </a>
                @endif
                
            </div><?php 
        }    
    }else{ ?>
        <div class="d-flex flex-column mb-5 last-message">
            <img src="{{ asset('admin_assets/media/images/new-no-message1.png')}}" height="500px" />
        </div><?php 
    }
?>
