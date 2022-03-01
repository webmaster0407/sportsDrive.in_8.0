$(document).ready(function() {

	$('.multiple-items').slick({
   	infinite: true,
  	slidesToShow: 5,
  	slidesToScroll: 5
});

	$('.responsive').slick({
        dots: true,
        infinite: true,
        speed: 1500,
        slidesToShow: 1,
        slidesToScroll: 1,
		cssEase: 'ease-in',
		autoplay: true,
		fade: true,
		focusOnSelect: true,
		lazyLoad: 'ondemand',
	  	autoplaySpeed: 6000,
		asNavFor : '.slideContent',
        mobileFirst : true,
		onBeforeChange: function() {
                $('.slick-active > .display').addClass('animated fadeInDown');
                $('.slick-active > .display').addClass('hidden');
            },
            onAfterChange: function() {
                $('.slick-active > .display').removeClass('hidden');
                $('.slick-active > .display').addClass('animated fadeInDown');
            }
    });
	
	  $('#owl-demo').owlCarousel({
	autoPlay : 3000,
	items : 5,
navigation:true,
	 
  });

	
	
	
	$(".mob-menu").click(function() {
		if($(".dropdown").hasClass("show")){
			$(".dropdown").removeClass("show");
			$(".mob-menu").removeClass("show");
		}else{
			$(".dropdown").addClass("show");
			$(".mob-menu").addClass("show");
		}
	});
	
	$(".rightMenu .menu a").click(function() {
		if($(".menuBar").hasClass("show")){
			$(".menuBar").removeClass("show");
			$(".rightMenu .menu a").removeClass("show");
		}else{
			$(".menuBar").addClass("show");
			$(".rightMenu .menu a").addClass("show");
		}
	});
	
$(".searchS, .close_form").click(function() {
		if($(".searchWrp").hasClass("show")){
			$(".searchWrp").removeClass("show");
			$(".searchS").removeClass("show");
		}else{
			$(".searchWrp").addClass("show");
			$(".searchS").addClass("show");
		}
	});
	
	$('.toggle').on('click', function() {
  $('.loginMiddle').stop().addClass('active');
});

$('.close').on('click', function() {
  $('.loginMiddle').stop().removeClass('active');
});
	
	function toggleChevron(e) {
    $(e.target)
        .prev('.filter-heading')
        .find("span")
        .toggleClass('icon-plus icon-minus');
}
	
	// Catalog
    if($('#catlogFilters').length > 0) {
        $('.filter-item').on('hidden.bs.collapse', toggleChevron);
        $('.filter-item').on('shown.bs.collapse', toggleChevron);

        $("#filterToggle").click(function(e){
            e.preventDefault();
            $(this).toggleClass('active').find('span').toggleClass('icon-minus');
            $('#catlogFilters').slideToggle();
        });
        $(".show-more").click(function(e){
            e.preventDefault();
            var $this = $(this);
            $this.parent().find('.filter-expandable').show();
            $this.parent().find('.show-fewer').show();
            $this.hide();
        });
        $(".show-fewer").click(function(e){
            e.preventDefault();
            var $this = $(this);
            $this.parent().find('.filter-expandable').hide();
            $this.parent().find('.show-more').show();
            $this.hide();
        });
    }
	
	$('.collapsed').on('click', function() {
		var id= $(this).attr('data-id');		
		$('#'+id).toggle();
        //$('#'+id+' span').toggleClass("icon-plus icon-minus");
	});
	
	/*$('.collapsed').on('click', function() {
		var id= $(this).attr('data-id')		
		$('#'+id+'.checkList').toggle();
                $('#'+id+' span').toggleClass("icon-plus icon-minus");
	});
	*/
	
});



