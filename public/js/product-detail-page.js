$(document).ready(function(){
   
    if($(".colorselect").length == 1) {
        $(".colorselect").addClass('selColorInit');
	}

    $(document).on('click', '.add_review_star', function() {
        var elem = $(this);
        var num_stars = elem.attr('data-value');
        $('#rating').val(num_stars);
    });


    $('.colorselect').on('click', function() {
        $('#selectedColor').val($(this).attr('data-val'));
    });

    $('.sizeselect').on('click', function() {
        $('#selectedSize').val($(this).attr('data-val'));
    });

    $('.add-cart').click(function(event){
        var selectedColor= $('#selectedColor').val();
        var selectedSize = $('#selectedSize').val();
        if(selectedColor=="" || selectedSize== ""){
            alert("Please Select configuration attributes.");
            event.preventDefault();
            return false;
        }
    });


    $(document).on('change', '.input-number', function() {
        var minVal = $(this).attr('min');
        var maxVal = $(this).attr('max');
        var value = $(this).val();
        if ( value < minVal ) {
            alert('Sorry, the minium value was reached.');
            $(this).val( $(this).attr('min') );
            return;
        }
        if ( value > maxVal ) {
            alert('Sorry, the maximum value was reached.');
            $(this).val( $(this).attr('max') );
            return;
        }
    })



    $(document).on("click", 'ul.pagination a', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        var nextPage = url.split('page=')[1];
        var  review_url = $("#review_url").val();
        var reviewVal = review_url.replace(/,\s*$/, "");
        var n = reviewVal.includes("page=");
        if(n){
            var currentPage = reviewVal.split('page=')[1];
            if(currentPage == "undefined"){
                currentPage = 1;
            }else{
                currentPage = currentPage.split('&')[0];
            }
        }else{
            currentPage = 1;
        }
        var pid = $('input[name=pid]').val();
        var token = $('input[name=_token]').val();
        $.ajax({
            type: "POST",
            dataType: "json",
            headers: {'X-CSRF-TOKEN': token},
            url: "/product/rating-review/"+pid,
            data: {"reviewPara": reviewVal,"page":nextPage},
            success: function (data) {
                $("#reviewList").html(data['result']);
                $(".pagination").replaceWith(data['links']);

            }
        });

    });

    /*close video on close button*/
    $('#youtube_button_close').click(function(){
        var video = $("#youtube_player").attr("src");
            $("#youtube_player").attr("src","");
            $("#youtube_player").attr("src",video);
    });

});
/*// display w.r.t sizes available on select color
    $('.colorselect').click(function () {
        var img = $(this);
        var color = $(this).attr('data-val');
        var token = $('input[name=_token]').val();
        var pid = $('input[name=pid]').val();
        var changeImgName = $(this).attr('data-img');

        $('#'+color).closest('li').siblings().css({
            'border':'none',
        });
        //alert(changeImg);
        $.ajax({

            url: "/product/display-size",
            headers: {'X-CSRF-TOKEN': token},
            data: {"color":color,"pid":pid},
            type: 'POST',
            datatype: 'JSON',
            success: function (resp) {
                $('#size-list').html(resp['sizeList']);
                $('#colorimgList').html(resp['imgList']);
                $('#selectedColor').val(color);
                $('#'+color).closest('li').css({
                    'border':'2px',
                    'border-color':'black',
                    'border-style': 'solid',
                });
                if(changeImgName != ''){

                    var imghtm ="<img src='/uploads/products/images/"+pid+"/1024x1024/"+changeImgName+"'>";
                    $('#ex3').html(imghtm);
                    
                    $('#ex3').zoom(options);
                }

            }
        });
    });*/
// size select  & display price w.r.t selected config
    // if($('#size-list li').length>0) {
    //     $("#size-list").on("click",".sizeselect", function(){
    //         var size = $(this).attr('data-val');
    //         $(this).siblings().css({
    //             'border-color':'#fff',
    //         });
    //         $('#selectedSize').val(size);
    //         $(this).css({
    //             'border-color':'black',
    //         });
    //         var token = $('input[name=_token]').val();
    //         var pid = $('input[name=pid]').val();
    //         var selectedColor = $('#selectedColor').val();

    //         if(selectedColor == ""){
    //             event.preventDefault();
    //             return false;
    //         }

    //         var selectedSize = size;
    //         $.ajax({
    //             url: "/product/display-price",
    //             headers: {'X-CSRF-TOKEN': token},
    //             data: {"selectedColor":selectedColor,"pid":pid,"selectedSize":selectedSize},
    //             type: 'POST',
    //             datatype: 'JSON',
    //             success: function (resp) {
    //                 $('#configPrice').html(resp);
    //             }
    //         });
    //     });
    // }else{
    //   /*  $("#color-list").on("click",".colorselect", function(){
    //         var color = $(this).attr('data-val');

    //         $('#selectedColor').val(color);

    //         var token = $('input[name=_token]').val();
    //         var pid = $('input[name=pid]').val();
    //         var selectedSize = $('#selectedSize').val();

    //         if(selectedSize == ""){
    //             event.preventDefault();
    //             return false;
    //         }

    //         var selectedColor = color;
    //         $.ajax({
    //             url: "/product/display-price",
    //             headers: {'X-CSRF-TOKEN': token},
    //             data: {"selectedColor":selectedColor,"pid":pid,"selectedSize":selectedSize},
    //             type: 'POST',
    //             datatype: 'JSON',
    //             success: function (resp) {
    //                 $('#configPrice').html(resp);
    //             }
    //         });
    //     });*/
    // }
// change image of large box onclick config images       
	/*$(document).on("click",".changeImg", function(){
        var imgName = $(this).attr('data-val');
        var cid = $(this).attr('data-config');
        var token = $('input[name=_token]').val();
        var pid = $('input[name=pid]').val();

        $.ajax({
            url: "/product/display-image",
            headers: {'X-CSRF-TOKEN': token},
            data: {"imgName":imgName,"pid":pid,"cid":cid},
            type: 'POST',
            datatype: 'JSON',
            success: function (resp) {
            	console.log(resp);
                $('#ex3').html(resp);
                // $('#ex3').zoom({ on:'click' });
                $('#ex3').zoom(options);
            }
        });

    });*/
       
	// quantity count plus/minus 
    //plugin bootstrap minus and plus
    //http://jsfiddle.net/laelitenetwork/puJ6G/
