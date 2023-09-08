"use strict";
var KTClipboardDemo={ 
	init:function(){
		new ClipboardJS('.clipboard_button').on("success",(function(e){
			e.clearSelection(),alert("Copied!")}))
		}
	};
jQuery(document).ready((function(){KTClipboardDemo.init()}));