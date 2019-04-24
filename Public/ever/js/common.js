$(window).load(function() {
	$("#status").fadeOut();
	$("#preloader").delay(350).fadeOut("slow");
});

$(function(){
	var flag = true;
	$('#showFloatMenu').click(function(){
		if(flag){
			$('.floatMenu').show();
			flag = false;
		}else{
			$('.floatMenu').hide();
			flag = true;
		}
	});
	
	
	$('.cate').click(function(){
		$(this).css('color','rgb(255, 80, 0)');
	});
});