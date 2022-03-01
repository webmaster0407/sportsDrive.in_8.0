/*cart methods start here*/

/*update qty*/
$(document).ready(function () {
    $('.ti-check').on('click', function(e) {
        e.preventDefault();

        var cart_id = $(this).attr('data-cart-id');
        var qty = $('#'+ cart_id +"_qty").val();
        var token = $('input[id=token]').val();

        if(qty <= 0) return false;

        $.ajax({
            type: 'POST',
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': token
            },
            url: "/cart/update",
            data: {
                "cart_id": cart_id,
                "qty":qty
            },
            success: function( data ) {
                if ( data['status'] != "200" ) {
                    var error = "<div class='error-msg'> <span>"+data['message']+"</span></div>";
                    $('#errMsg').fadeIn(700);
                    $('#errMsg').html(error);
                    setTimeout(() => { 
                        $('#errMsg').fadeOut(700);
                        }, 3000
                    );
                    $('#'+cart_id+"_qty").val(data['quantity']);
                } else {
                    $('#successMsg').fadeIn(700);
                    $('#successMsg').html('Cart Updated Succesfully!');
                    setTimeout(() => { 
                        $('#successMsg').fadeOut(700);
                        }, 3000
                    );
                    $('#'+cart_id+"_original_price").text(data['originalPricePerProduct']);
                    $('#'+cart_id+"_final_price").text(data['finalPricePerProduct']);
                    $('#'+cart_id+"_total").text(data['cart_total']);
                    $('#subtotal').text(data['final_total']);
                    $('#offer_discount').text(data['final_discount']);
                    $('#estimated_total').text(data['estimated_total']);
                }
            }
        })
    });

    $('.ti-close').on('click', function (e) {
        e.preventDefault();

        var token = $('input[id=token]').val();
        var cart_id = $(this).attr("data-cart-id");

        $.ajax({
            type: "POST",
            dataType: "json",
            headers: {'X-CSRF-TOKEN': token},
            url: "/cart/remove",
            data: {"cart_id": cart_id},
            success: function (data) {
                if(data==403){
                    location.reload();
                }else{

                    $('#successMsg').fadeIn(700);
                    $('#successMsg').html('Removed Succesfully!');
                    setTimeout(() => { 
                        $('#successMsg').fadeOut(700);
                        }, 3000
                    );
                    //hide the deleted cart
                    $("#"+cart_id).hide();
                    
                    if( data['total_cart_count'] > 0 ) {
                        $('#subtotal').text(data['final_total']);
                        $('#offer_discount').text(data['final_discount']);
                        $('#estimated_total').text(data['estimated_total']);
                        $("#cart_count").text(data['total_cart_count']);
                        $('#product_count').text(data['total_cart_count']);
                    } else {
                        $('#subtotal').text(data['final_total']);
                        $('#offer_discount').text(data['final_discount']);
                        $('#estimated_total').text(data['estimated_total']);
                        $("#shop_cart_table").hide();
                        $("#empty_cart_show").show();
                        $("#cart_count").hide();
                    }
                }
            }
        });

    });

    /* carts methods end here  */



    /*checkout methods starts  here*/
    $('.delivery_type').click(function(){
        var val = $(this).val();
        var std_del_ch = $("#std_del_ch").val();
        var ex_del_ch = $("#ex_del_ch").val();
        var checkout_subtotal = $("#hidden_subtotal").val();
        var checkout_estimated_total=+checkout_subtotal+(+ex_del_ch);
        if(val=="express"){
            $("#checkout_estimated_total").text(checkout_estimated_total+".00");
            $("#shipping_charges").text(ex_del_ch);
        }else{
            var checkout_estimated_total=+checkout_subtotal+(+std_del_ch);
            $("#checkout_estimated_total").text(checkout_estimated_total+".00");
            $("#shipping_charges").text(std_del_ch);
        }
    });


    $(document).on('click', '.select_shipping', function() {
        var val = $(this).val();
        var divData = $("#all_shipping_"+val).html();
        $("#default_shipping_address").html(divData);
    });


    $(document).on('click', '.select_billing', function() {
        if ( $('#same_as_ship').prop('checked') == true ) {
            return;
        }

        var val = $(this).val();
        var divData = $("#all_billing_"+val).html();
        $("#default_billing_address").html(divData);
    });



    $(document).on('click', '#same_as_ship', function() {
        if ($(this).prop('checked') != true ) {
            return;
        }
        var divData = $("#default_shipping_address").html();
        $("#default_billing_address").html(divData);
    });
    /*checkout methods ends  here*/


    /*other methods*/
    $('#Continue').click(function(){
        var val =  $('#shipping_address_id').val();
        if(val == "")
          $("#error").html("<div class='alert alert-danger'>Please add address.</div>");
    });


});





