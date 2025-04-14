$(function () {
    //click fast
    if ('addEventListener' in document) { document.addEventListener('DOMContentLoaded', function () { FastClick.attach(document.body); }, false);}

    // tab
    $('.tabMenu').find('span').on('click', function () {
        var times = $(this).index();
        $(this).addClass('current').siblings().removeClass();
        $(this).parents('.tabBox').find('.subBox').eq(times).show().siblings().hide();
    });
    $('.tabMenu').find('li').on('click', function () {
        var times = $(this).index();
        $(this).addClass('current').siblings().removeClass();
        $(this).parents('.tabBox').find('.subBox').eq(times).show().siblings().hide();
    });
    // nav slider
    var liLength = $("nav a.current").index();
	liLength += 1;
	if (liLength >=5) {
		$('nav').addClass('nLiner')
		$('nav div').scrollLeft(parseInt($("nav a").width())-20);
		var navawidth = $("nav a").eq(0).width()
		$('nav div').css('margin-left',navawidth+'px')
	} else {
		$('nav div').scrollLeft(0);
		$('nav').removeClass('nLiner')
		$('nav div').css('margin-left','0px')
		$('nav div').css('display','flex')
		$('nav div').css('justify-content','space-between')
	}
    // ihnav
    $('.headNav').on('click', function () {
        var bodyH = $('body,html').height();
        var haaderH = $('header').height();
        if ($(this).hasClass("xs")) {
            $(this).removeClass('xs');
            $('.hideNav').hide();
            
            $('.searchBtn').show();
        } else {
            $(this).addClass('xs');
            $('.hideNav').show();
            $('.searchBtn').hide(); 
            $('.hideNav').css('height', bodyH - haaderH);
        }
    });
    
    // hslider
    if ($(".hSlider").length > 0) {
        var swiper = new Swiper(".hSlider .swiper-container", {
            autoplay:  {disableOnInteraction: false,},
            loop:true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },  
        });
    }
    if ($(".sub1Slider").length > 0) {
		var swiper = new Swiper(".sub1Slider", {
    	  	slidesPerView: "auto",
    	});
   	}
	if ($(".sub2Slider").length > 0) {
		var swiper = new Swiper(".sub2Slider", {
    	  	slidesPerView: "auto",
    	  	loop:true,
    	});
   	}
	if ($(".perSlider").length > 0) {sliderFun(".perSlider")}
	if ($(".recNew").length > 0) {sliderFun(".recNew")}
	if ($(".shareZt").length > 0) {sliderFun(".shareZt")}
	if ($(".rk1Slider").length > 0) {sliderFun(".rk1Slider")}
	if ($(".rk2Slider").length > 0) {sliderFun(".rk2Slider")}
	if ($(".rk3Slider").length > 0) {sliderFun(".rk3Slider")}
	if ($(".rk4Slider").length > 0) {sliderFun(".rk4Slider")}
	if ($(".rk5Slider").length > 0) {sliderFun(".rk5Slider")}
	if ($(".caroSlider").length > 0) {sliderFun(".caroSlider")}
	if ($(".featSlider").length > 0) {sliderFun(".featSlider")}
	if ($(".newsHot").length > 0) {sliderFun(".newsHot")}
	if ($(".rankRec").length > 0) {sliderFun(".rankRec")}
	
	function sliderFun(ele){
		var swiper = new Swiper( ele , {
    	  	slidesPerView: "auto",
    	});
	}
    
    //gstags
	$(".putTag").hide();
	$(".openTag").on("click",function() {
		$(".openTag").hide();
		$(".putTag").show();
		$(".gsTag a:nth-of-type(n+9)").css("display","block");
	}) 
	
	$(".putTag").on("click",function() {
		$(".openTag").show();
		$(".putTag").hide();
		$(".gsTag a:nth-of-type(n+9)").css("display","none");
	}) 
	
	if($(".gsTag a:nth-of-type(n+9)").hasClass("current")){
		$(".openTag").hide();
		$(".putTag").show();
		$(".gsTag a:nth-of-type(n+9)").css("display","block");
	}
	
	

	
	 
	 
	 
	 
	 
	 
	 

});

