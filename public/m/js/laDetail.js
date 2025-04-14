$(function() {

	if ($('.gameCont .cont').length > 0) {
        $('.gameCont .cont  img').each(function(k, v) {
            let widthImg = $(this).width();
            let heightImg = $(this).height();
            if (widthImg > 0 && heightImg > 0) {
            	$(this).css("height", "auto");
                $(this).css("width", "auto");
                $(this).css("max-width", "100%");
                if (widthImg < heightImg) {
                    $(this).css("max-height", "6.6rem");
                } else {
                    $(this).css("max-height", "4rem");
                }
            }
        })
	}


	// video
	if ($(".gaVideo").length > 0) {
		$(".gaVideo video").on('playing', function() {
			$('.gaVideo .replay').addClass("hide");
		})
		$(".gaVideo video").on('ended', function() {
			$('.gaVideo .replay').removeClass("hide");
		})
		$(".gaVideo video").on('pause', function() {
			$('.gaVideo .replay').removeClass("hide");
		})
	}
	// video1
	$('.gaVideo .replay').on('click', function() {
		$(".hideVideo").removeClass("hide");
		$(".tpVideo .replay1").addClass("hide");
		if ($(".tpVideo").length > 0) {
			$('.tpVideo').find('video')[0].play();
			$(".tpVideo video").on('playing', function() {
				$('.tpVideo .replay1').addClass("hide");
			})
			$(".tpVideo video").on('ended', function() {
				$('.tpVideo .replay1').removeClass("hide");
			})
			$(".tpVideo video").on('pause', function() {
				$('.tpVideo .replay1').removeClass("hide");
			})
			$('.tpVideo .replay1').on('click', function() {
				$(this).parents('.tpVideo').find('video')[0].play();
				$(this).addClass("hide");
			})
		}
	})
	$('.vClose').on('click', function() {
		$(".hideVideo").addClass("hide");
	})
    
	// pswpHtml
	if($('.showImg').length>0 || $('.gameCont .cont').length>0 || $('.newsCont').length>0){
		var pswpHtml = '';
		pswpHtml +=`<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true"><div class="pswp__bg"></div><div class="pswp__scroll-wrap"><div class="pswp__container"><div class="pswp__item"></div><div class="pswp__item"></div><div class="pswp__item"></div></div><div class="pswp__ui pswp__ui--hidden"><div class="pswp__top-bar"><div class="pswp__counter"></div><button class="pswp__button pswp__button--close" title="Close (Esc)"></button><button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button><button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button><div class="pswp__preloader"><div class="pswp__preloader__icn"><div class="pswp__preloader__cut"><div class="pswp__preloader__donut"></div></div></div></div></div><button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button><button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button><div class="pswp__caption"><div class="pswp__caption__center"></div></div></div></div></div>`
	 	$("body").append(pswpHtml);
	}
	
	function pswpFun(els){
		if(els.length>0){
			els.find('img').on('click',function(){
	   	 		var pswpElement = document.querySelectorAll('.pswp')[0];
	    		var items = new Array();
	    		$.each(els.find('img'), function(i, v) {
	      	  		$(v).attr("rel", i);
	        		items.push({
	            		src: $(v).attr("src"),
	            		w: $(v).width(),
	            		h: $(v).height()
	        		});
	    		});
	    		var options = { index: parseInt($(this).attr("rel")) };
	    		var gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
	    
	    		if($(this).parents("a").length>0){
   					return;
   				}else{
   					gallery.init();
   				}
			});

		}
	}

	pswpFun($('.gameCont .cont'));
	pswpFun($('.newsCont'));
	
	if($('.showImg').length>0){
		$('.showImg').find('img').on('click',function(){
	   	 	var pswpElement = document.querySelectorAll('.pswp')[0];
	    	var items = new Array();
	    	$.each($('.showImg').find('img'), function(i, v) {
	      	  	$(v).attr("rel", i);
	        	items.push({
	           		src: $(v).attr("src"),
	           		w: $(v).width(),
	           		h: $(v).height()
	        	});
	    	});
	    	var options = { index: parseInt($(this).attr("rel")) };
	    	var gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
   				
   			gallery.init();
		});
	}
	

	//rizhi
	$(".rLog").on('click', function () { $(".gLog").show();});
	$(".lClose").on('click', function () { $(".gLog").hide();});
	$(".lSure").on('click', function () { $(".gLog").hide();});
	$(".logMain").find('p').each(function () {
		if($(this).html() === ""){
			$(this).css("display","none")
			$(this).remove()
		}
	})
	$(".logMain").find('strong').parent("p").each(function () {
		$(this).css("text-indent","0")
	})
	$(".logMain").find('strong').parent("p").each(function () {
		$(this).css("text-indent","0")
	})
	$(".logMain").find('p').each(function () {
		if($(this).text().includes("版本更新内容")){
			$(this).css("text-indent","0")
			$(this).css("font-weight","bold")
			$(this).css("font-size","14px")
		}	
	})
	$(".logMain").find('p').each(function () {
		if($(this).text().includes("版本更新内容")){
			$(this).css("text-indent","0")
			$(this).css("font-weight","bold")
			$(this).css("font-size","16px")
		}	
	})
	
	if ($('.gLog').length > 0) {
		var rzp =$(".gLog .logMain p")
		var rzpp =Array.prototype.slice.call(rzp)
		for( var i=0;i<rzpp.length;i++){
			if (rzpp[i].innerHTML == "") {
				rzpp[i].remove();
			}
		}
	}
		

	
})
$.trim = function(str) { return str == null ? '' : String.prototype.trim.call(str); };
$(".showImg").html($('.hideImg').html());






