var uaTest = /Android|webOS|iPhone|Windows Phone|ucweb|iPod|BlackBerry|ucbrowser|SymbianOS/i.test(navigator.userAgent.toLowerCase());var touchTest = 'ontouchend' in document;if(uaTest && touchTest){window.location.href = location.href.replace("//www","//m");}



$(function () {
 
  $(".searchBtn ").hover(
    function() {$(this).addClass("search ")},
    function() {$(this).removeClass("search ")}
  );

	// tab
    $('.tabMenu').find('li').on('click', function () {
        var times = $(this).index();
        $(this).addClass('current').siblings().removeClass('current');
        $(this).parents('.tabBox').find('.subBox').eq(times).show().siblings().hide();
    })
	
	$('.tabMenu').find('span').on('click', function () {
	    var times = $(this).index();
	    $(this).addClass('current').siblings().removeClass('current');
	    $(this).parents('.tabBox').find('.subBox').eq(times).show().siblings().hide();
	})
	
	$('.tab_menu').find('span').on('click', function () {
	    var times = $(this).index();
	    $(this).addClass('current').siblings().removeClass('current');
	    $(this).parents('.tab_box').find('.sub_box').eq(times).show().siblings().hide();
	})
	
	$('.tfBox').find('li').hover(function() {
		$(this).find('.tBox').removeClass('hide').siblings('.fBox').addClass('hide');
		$(this).siblings().find('.tBox').addClass('hide').siblings('.fBox').removeClass('hide');
	});
	

	$('.news_box').find('.news_item').hover(function() {
		$(this).addClass('cur').siblings().removeClass('cur');
	});
	
	$('.hRMenu').find('span').on('click', function() {
		var times = $(this).index();
		$(this).addClass('current').siblings().removeClass('current');
		$(this).parents('.hotRank').find('.hRList').eq(times).show().siblings().hide();
	})

	
	$('.allGMenu').find('span').on('click', function() {
		var times = $(this).index();
		$(this).addClass('current').siblings().removeClass('current');
		$(this).parents('.allGame').find('.allGCont>div').eq(times).show().siblings().hide();
	})
	$('.allSMenu').find('span').on('click', function() {
		var times = $(this).index();
		$(this).addClass('current').siblings().removeClass('current');
		$(this).parents('.allSoft').find('.allGCont>div').eq(times).show().siblings().hide();
	})
	
	$('.gsHRMenu').find('span').on('click', function() {
		var times = $(this).index();
		$(this).addClass('current').siblings().removeClass('current');
		$(this).parents('.gsHotRec').find('ul').eq(times).show().siblings().hide();
	})
	
	$('.gsHRMenu').find('span').on('click', function() {
		var times = $(this).index();
		$(this).addClass('current').siblings().removeClass('current');
		$(this).parents('.gsHotNew').find('ul').eq(times).show().siblings().hide();
	})
	
	$('.rankList ol li:eq(0)').addClass('current');
	$('.rankList ol').find('li').hover(function() {
		$(this).addClass('current').siblings().removeClass('current');
	});
	
	
	$('.ztList ul li:eq(0)').addClass('current');
	$('.ztList ul').find('li').hover(function() {
		$(this).addClass('current').siblings().removeClass('current');
	});
	
	
	// index slider
	var b = 0;
	var s_li = $('.hSlider').find('.hSImg li');
	var s_ul = $('.hSlider').find('.hSImg ul');
	var d_li = $('.hDot').find('li');
	var d_ul = $('.hDot').find('ul');
	var sLi = s_li.first().clone();
	s_ul.append(sLi);
	var sLiLength = s_ul.find('li').length;
	var sLiWidth = s_li.outerWidth();
	var dotImgLength = d_ul.find('li').length;
	var dotImgWidth = d_li.outerWidth() + 20;
	d_ul.css({ 'width': dotImgLength * dotImgWidth });
	var dotLiLength = sLiLength - 1;
	var dotLiWidth = 43;
	$('.hDot').css({  'width': dotLiLength * dotLiWidth   });
	$('.hDot ul').css({  'width': dotLiLength * dotLiWidth });

	function gameSlider() {
	        for (var a = 0; a < dotLiLength; a++) {  $('.hDot ul').append('<li><span></span></li>');  }
	        $('.hDot ul').find('li').first().addClass('current');
	        $('.hSImg ul').find('li').first().addClass('current');
	        $(".hSImg").find("ul li").eq(b).css({"z-index": 1,"opacity": 1 })

	        moveDot2();
	        nextTwo();
	        preTwo();
	        var timeTwo = setInterval(function() {
	            plusI();
	        }, 6000);
	        $('.hSlider').hover(function() {
	            clearInterval(timeTwo);
	        }, function() {
	            timeTwo = setInterval(function() {
	                plusI();
	            }, 6000);
	        });
	}
	gameSlider();
	function plusI() {
	        b++;
	        if (b === sLiLength) {
	            $('.hSlider').find('.hSImg ul').css({
	                left: 0
	            });
	            b = 1;
	            move2_2();
	        } else if (b === sLiLength - 1) {
	            $('.hDot ul').find('li').eq(0).addClass('current').siblings().removeClass('current');
	        }
	        move2_2();
	}
	function nextTwo() {
	        var next2 = $('.hSlider').find('.next');
	        next2.on('click', function() {
	            plusI();
	        });
	}
	function preTwo() {
	        var pre2 = $('.hSlider').find('.pre');
	        pre2.on('click', function() {
	            b--;
	            if (b === -1) {
	                $('.hSlider').find('.hSImg ul').css({
	                });
	                b = sLiLength - 2;
	                move2_2();
	            }
	            move2_2();
	        });
	}
	function moveDot2() {
	        $('.hDot').find('li').on('click', function() {
	            b = $(this).index();
	            move2_2();
	        })
	}
	function move2_2() { 
	    	$('.hDot').find('li').eq(b).addClass('current').siblings().removeClass('current');
	    	$('.hSImg ul').find('li').eq(b).addClass('current').siblings().removeClass('current'); 
	    	$(".hSImg").find("ul li").eq(b).css({"z-index": 1,"opacity": 1 }).siblings().css({  "z-index": 0, "opacity": 0 });
	}
	
	
	
	//htop
	if($(".subS1").length>0){MenuTab($(".subS1 .subSlider div"),$(".subS1 .subSlider a"),$(".subS1 .tab_l"),$(".subS1 .tab_r"),121,3)}
	if($(".subS2").length>0){MenuTab($(".subS2 .subSlider div"),$(".subS2 .subSlider a"),$(".subS2 .tab_l"),$(".subS2 .tab_r"),121,3)}
	if($(".personRec ").length>0){MenuTab($(".personRec .subSlider div"),$(".personRec .subSlider a"),$(".personRec .tab_l"),$(".personRec .tab_r"),155,8)}

function MenuTab(eleBox,ele,eLeft,eRight,eWidth,eLen){
	eleBox.css("width", (ele.length) * eWidth);
	if (ele.length <= eLen) {
	  eLeft.css("display", 'none');
	  eRight.css("display", 'none');
	}
	var defartIndexnav1 = 0;
	var ulNumnav1 = 0;
	var tabItemArrnav1 = ele;
	eRight.click(function () {
	  if (defartIndexnav1 >= tabItemArrnav1.length - eLen) { 
	  } else if (defartIndexnav1 >= tabItemArrnav1.length - eLen && defartIndexnav1 < tabItemArrnav1.length - 1) {
	    defartIndexnav1++;
	    ulNumnav1 = ulNumnav1 - eWidth;
	    var fixLeft = -eWidth * (tabItemArrnav1.length - eLen);
	    eleBox.animate({ left: fixLeft }, 300);
	  } else {
	    defartIndexnav1++;
	    ulNumnav1 = ulNumnav1 - eWidth;
	    eleBox.animate({ left: ulNumnav1 }, 300);
	  }
	});
	eLeft.click(function () {
	  if (defartIndexnav1 < 1) {
	  } else if (defartIndexnav1 > tabItemArrnav1.length - eLen && defartIndexnav1 <= tabItemArrnav1.length - 1) {
	    defartIndexnav1--;
	    ulNumnav1 = ulNumnav1 + eWidth;
	    var fixLeft = -eWidth * (tabItemArrnav1.length - eLen);
	    eleBox.animate({ left: fixLeft }, 300);
	  } else {
	    defartIndexnav1--;
	    ulNumnav1 = ulNumnav1 + eWidth;
	   eleBox.animate({ left: ulNumnav1 }, 300);
	  }
	});	
}	



//slider
function Sliders(els) {
  var sliderLength = els.find('li').length, sliderWidth = els.find('li').width(), dot = 0,dotCont = ' ', slider = '';

  els.find('ul').css({ 'width': sliderWidth * sliderLength + 10 });
  for (dot; dot < sliderLength; dot++) {
    dotCont += '<i></i>';
  }
  els.find('.dot').append(dotCont);
  els.find('.dot i').first().addClass('current');
  els.find('.dot').on('click', 'i', function () {
    slider = $(this).index();
    sliderMove();
  });

  var zidong = setInterval(run, 3600);

  function run() {
    slider++;
    if (slider > sliderLength - 1) {
      slider = 0;
    }
    ;
    sliderMove();
  };
  els.hover(function () {
    clearInterval(zidong);
  }, function () {
    zidong = setInterval(run, 3600);
  });

  function sliderMove() {
    els.find('.dot i').eq(slider).addClass('current').siblings().removeClass('current');
    els.find('ul').stop().animate({ 'left': -sliderWidth * slider }, 500);
  }
}

if($('.nSlider').length>0){Sliders($('.nSlider'))}
if($('.slider').length>0){Sliders($('.slider'))}



	
	//newscopy
	$(".newsCopy span").on("click", function() {
	    var pageUrl = location.href;
	    $("input[name=pageUrl]").val(pageUrl)
	    var inputTxt = $("input[name=pageUrl]");
	    inputTxt.select();
	    document.execCommand("copy");
	    alert("复制成功！")
	})
	
	if($('.gameTop').length>0){
		function moveAnimated(moveElement, targetLeft) {
			clearInterval(moveElement.timeId);
			moveElement.timeId = setInterval(function() {
				var currentLeft = moveElement.offsetLeft;
				var step = 10;
				step = currentLeft < targetLeft ? step : -step;
				currentLeft += step;
				if (Math.abs(targetLeft - currentLeft) > Math.abs(step)) {
					moveElement.style.left = currentLeft + "px";
				} else {
					clearInterval(moveElement.timeId);
					moveElement.style.left = targetLeft + "px";
				}
			}, 8)
		}
		
		var imgWidth = $(".caroList li").width();
		var circleIndex = 0;
		var imglilength = $(".caroList li").length + 1;
		console.log(imglilength,)
		$(".caroList").css("width", imglilength * imgWidth + "px");
		$(".caroList li:first").clone(true).appendTo($(".caroList"))
		$(".caroPre").on("click", function() {
			if (circleIndex == 0) {
				circleIndex = $(".caroList li").length - 1;
				$(".caroList").css("left", -circleIndex * imgWidth + "px");
			}
			circleIndex--;
			moveAnimated($(".caroList")[0], -circleIndex * imgWidth)
		})
		function clickRight() {
			if (circleIndex == $(".caroList li").length - 1) {
				circleIndex = 0;
				$(".caroList").css("left", "0px");
			}
			circleIndex++;
			moveAnimated($(".caroList")[0], -circleIndex * imgWidth)
		}
		$(".caroNext").on("click", clickRight)	
	}		
	
	
	
})




var httphost	= window.location.host;
var protocol	= window.location.protocol;
var reportUrl = protocol+"//"+httphost+'/';

var baseUrl = protocol+"//"+httphost+'/';
$(function(){

})









	























































