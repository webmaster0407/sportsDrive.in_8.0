$(document).ready( function() {

	$(".cancel-btn").click( function() {
	  
	  var order_id  = $(this).attr('data-val');
	  
	  $.ajax({
			type: "GET",
			dataType: "JSON",
			url: "/order/get-cancel-data/" + order_id,
			success: function ( data ) {
				$("#CancelOrder").html(data['cancelorderData']);
				$("#order_id").val(order_id);
			}
	 });
	});

	$(".cancel-btn").click(function() {
		
	  var order_id  = $(this).attr('data-val');
	  $.ajax({
			type: "GET",
			dataType: "json",
			url: "/order/get-return-data/"+order_id,
			success: function (data) {
				//console.log(data);
				$("#ReturnOrder").html(data['returnorderData']);
				$("#order_id1").val(order_id);
			}
	 });
	});	


});



