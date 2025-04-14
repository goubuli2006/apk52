$(function(){ 
	
if ($('.gameCont').length > 0) {
	$('.gameCont .cont img').each(function(k, v) {
            let widthImg = $(this).width();
            let heightImg = $(this).height();
            if (widthImg > 0 && heightImg > 0) {
            	$(this).css("height", "auto");
                $(this).css("width", "auto");
                $(this).css("max-width", "100%");
                if (widthImg < heightImg) {
                    $(this).css("max-height", "660px");
                } else {
                    $(this).css("max-height", "420px");
                }
            }
        })
}
	
if ($('.showImg').length > 0) {
	lightbox.option({
		'resizeDuration': 200,
		'wrapAround': true,
		'showImageNumberLabel':false
	});
}

const numInput = document.getElementById('numInput');
const rangeInput = document.querySelector('.scoreWrap input[type="range"]');
rangeInput.addEventListener('input', function() {
    numInput.textContent = parseFloat(this.value).toFixed(1);
});
numInput.addEventListener('click', function() {
    rangeInput.value = parseFloat(this.textContent);
    numInput.textContent = parseFloat(rangeInput.value).toFixed(1);

});


//rizhi
$(".rLog").on('click', function () { $(".gLog").show();});
$(".gLclose").on('click', function () { $(".gLog").hide();});
$(".logMain").find('p').each(function () {
	if($(this).html() === ""){
		$(this).css("display","none")
		$(this).remove()
	}
})

$(".logMain").find('strong').parent("p").each(function () {
	$(this).css("text-indent","0")
})

$(".logMain").find('p').each(function () {
	if($(this).text().includes("版本更新内容")){
		$(this).css("text-indent","0")
		$(this).css("font-weight","bold")
		$(this).css("font-size","16px")
	}	
})




	const myDiv = document.getElementById('sliderId');
    const divWidth = myDiv.offsetWidth;
    const divCenter = divWidth / 2;
    myDiv.addEventListener('mouseover', function(event) {
        const mouseX = event.clientX - myDiv.getBoundingClientRect().left;
        if (mouseX < divCenter) {
            $(".gLeft").css("left","0")
        } else {
            $(".gRight").css("right","0")
        }
    });
    myDiv.addEventListener('mouseleave', function(event) {
        const mouseX = event.clientX - myDiv.getBoundingClientRect().left;
        if (mouseX < divCenter) {
            $(".gLeft").css("left","-22px")
        } else {
            $(".gRight").css("right","-22px")
        }
    });
    
      myDiv.addEventListener('mouseleave', function(event) {
       		$(".gLeft").css("left","-22px")
            $(".gRight").css("right","-22px")
    });


	
})

 $(".showImg").html($(".hideImg").html());
 $.trim = function(str) { return str == null ? '' : String.prototype.trim.call(str); };
  //left<->right
 function hscroll(id, flag, min, move, childlevel, time) {
 	min = min || 2;
 	move = move || 1;
 	time = time || 300;
 	childlevel = childlevel || 1;
 	var parent = $("#" + id + ":not(:animated)");
 	if (childlevel == 1) {
 		var kids = parent.children();
 	} else {
 		var kids = parent.children().eq(0).children();
 	}
 
 	if (kids.length < min) return false;
 	var kid = kids.eq(1);
 	var kidWidth = kid.width() + parseInt(kid.css("paddingLeft")) + parseInt(kid.css("paddingRight")) + parseInt(kid.css(
 		"marginLeft")) + parseInt(kid.css("marginRight"));
 	var margin = (kidWidth * move);
 	if (flag == "left") {
 		var s = parent.scrollLeft() + margin;
 		parent.animate({
 			'scrollLeft': s
 		}, time);
 	} else {
 		var s = parent.scrollLeft() - margin;
 		parent.animate({
 			'scrollLeft': s
 		}, time);
 	}
 	return false;
 }
 
 
		

