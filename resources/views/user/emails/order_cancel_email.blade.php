<!doctype html>
<html>
<head>
<meta name="keywords" content="">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE10" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE11" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<!--[if lt IE 7 ]> <html class="ie6"> <![endif]-->
<!--[if IE 7 ]> <html class="ie7"> <![endif]-->
<!--[if IE 8 ]> <html class="ie8"> <![endif]-->
<!--[if IE 9 ]> <html class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <!--<![endif]-->
<!--[if lt IE 9]>
   <script>
      document.createElement('header');
      document.createElement('nav');
      document.createElement('section');
      document.createElement('article');
      document.createElement('aside');
      document.createElement('footer');
   </script>
<![endif]-->
<title> Sportsdrive:Order Cancellation</title>
<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link href="css/font-awesome.css" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i,600,600i,700,700i,800" rel="stylesheet">
<style>
:focus{ outline:none;}
a{color:#0075cf;}
a:hover{text-decoration: underline;}
	h1,h2,h3,h4,h5,h6{margin:0px;padding: 0px;}
	p{margin:0px;padding:0px;}
	a{text-decoration:none;}
	table.thank-u tr td p{margin-bottom:10px;}
	
	table.thank-u tr td.para p{margin-bottom:4px;}
	table.thank-u tr td.para h4{margin-bottom:8px;}
	table tr th{padding:3px;background:#f7f7f7;font-weight:bold;font-size:12px;}
	.other-info p{color:grey;}
	.product-table{margin-bottom:15px;}
	.manage-order{background:#1963a9;padding:5px 9px;color:#fff;border: none;border-radius: 2px;display: inline-block;cursor: pointer;}
	.manage-order:hover{background:#2871b7;}
</style>
</head>
<body style="background-color:#f9f9f9">
<center>
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed;background-color:#f9f9f9" id="bodyTable">
<tbody>
<tr>
<td align="center" valign="top" style="padding-right:10px;padding-left:10px" id="bodyCell">

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="wrapperTable" style="max-width:700px">
		<tbody>
			<tr>
				<td align="center" valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="logoTable">
						<tbody>
						<tr>
							<td align="center" valign="middle" style="padding-top:20px;padding-bottom:20px"><a href="#" style="text-decoration:none" target="_blank"><img alt="" border="0" src="{{URL::asset('images/logo.png')}}" style="height:auto;display:block" width="180"></a></td>
						</tr>
	   				</tbody>
	 				</table>
	 			</td>
	 		</tr>
	 	</tbody>
</table>
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="wrapperTable" style="max-width:700px">
			<tbody>
				<tr>
					<td align="center" valign="top">
						<table border="0" cellpadding="0" cellspacing="0" width="100%" class="oneColumn" style="background-color: rgb(255, 255, 255); box-shadow: rgb(216, 216, 216) 0px 0px 10px;"><tbody>
							<tr>
								<td style="background-color:#1e7ed0;font-size:1px;line-height:3px" class="topBorder" height="3">&nbsp;</td>
							</tr>
							<tr>
								<td align="center" valign="top" style="padding-top:20px;padding-bottom:5px;padding-left:20px;padding-right:20px" class="title"><h2 class="bigTitle" style="color:#313131;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:26px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:34px;text-align:center;padding:0;margin:0">Order Cancellation</h2></td>
							</tr>
							<tr>
								<td align="center" valign="top" style="padding-bottom:20px;padding-left:20px;padding-right:20px" class="subTitle"><h4 class="midTitle" style="color:#919191;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:15px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;text-align:center;padding:0;margin:0">Your Order successfully Cancel.</h4>
									<h5 style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;text-align:center;padding:0;margin:0">Your Order No {{$order->id}}</h5>
								</td>
							</tr>
							<tr>
							<td>	
							<table width="80%;" style="margin:0px auto 10px;">
								<tr>
								<td valign="top" style="width:50%:padding-bottom:10px" class="infoTitle"><h4 style="color: #444444;
    font-family: 'Open Sans',Helvetica,Arial,sans-serif;font-size: 15px;font-weight: 600;font-style: normal;    letter-spacing: normal;line-height: 26px;padding: 0;margin: 0;">Shipping Address :</h4>
									<p style="color: #444444;font-family: 'Open Sans',Helvetica,Arial,sans-erif;    font-size: 14px;font-weight: 400;line-height: 22px;padding: 0;margin: 0;">{{$shipping_address_details->full_name}}<br>{{$shipping_address_details->address_line_1}}<br>{{$shipping_address_details->address_line_2}}<br>{{$shipping_address_details->city}}<br>
									{{$shipping_address_details->state}}<br>{{$shipping_address_details->country}}</p>
								</td>
									<td valign="top" style="width:40%;padding-bottom:10px;padding-left:20px;" class="infoTitle"><h4 style="color: #444444;
    font-family: 'Open Sans',Helvetica,Arial,sans-serif;font-size: 15px;font-weight: 600;font-style: normal;    letter-spacing: normal;line-height: 26px;padding: 0;margin: 0;">Billing Address :</h4>
									<p style="color: #444444;font-family: 'Open Sans',Helvetica,Arial,sans-erif;    font-size: 14px;font-weight: 400;line-height: 22px;padding: 0;margin: 0;">{{$billing_address_details->full_name}}<br>{{$billing_address_details->address_line_1}}<br>{{$billing_address_details->address_line_2}}<br>{{$billing_address_details->city}}<br>
									{{$billing_address_details->state}}<br>{{$billing_address_details->country}}</p>
								</td>
								</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td>
								<table width="96%" cellspacing="1" cellpadding="1" style="border-bottom:1px solid #dedede;margin-top:15px;margin:0px auto;" class="product-table">
								<thead>
									<tr><th style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:26px;text-align:center;padding:0;margin:0">Product</th>
									<th style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:26px;text-align:center;padding:0;margin:0"></th>
									<th style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:26px;text-align:center;padding:0;margin:0">Qty.</th>
									<th style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:26px;text-align:center;padding:0;margin:0">Total Price</th>
									</tr>
								</thead>
								<tbody>
								 <?php 
									foreach($cart as $val){ ?>
									<tr>
										<td width="15%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;text-align:center;padding:0;margin:0;background:#fff;"> <div style="display: inline-block;vertical-align: top;margin-top:3px;"><img src="images/image-1.jpg" alt="image" width="100%" style="max-width:80px;max-height:80px;"></div></td>
										<td width="50px" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:20px;padding:0px 5px;margin:0;background:#fff;"><a href="{{ url('/product/details/'.$val->product_slug) }}">{{$val->product_name}}</a>
										<div class="other-info">
											<p style="line-height:22px; font-size:13px;"><span style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:22px;text-align:center;padding:0;margin:0;background:#fff;">Color:</span>{{$val->color}}</p>
											<p style="line-height:22px; font-size:13px;"><span style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:22px;text-align:center;padding:0;margin:0;background:#fff;">Size:</span>{{$val->size}}</p>
										</div>
								</td> 
								<td width="20%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;text-align:center;padding:0;margin:0;background:#fff;"><p>{{$val->quantity}}</p>								</td>
								<td width="20%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;    padding-left: 20px;"><p><span style="display: inline-block;
    margin-right: 28px;">₹ </span><span style="display: inline-block;
    float: right;
    margin-right: 20px;">{{$val->final_price}}</span></p>	</td>
								
		</tr>
		<?php }?>
		</tbody>
		</table>
							
								</td>
							</tr>
							
							<tr>
								<td>
								<table width="96%" cellspacing="1" cellpadding="1" style="border-bottom:1px solid #dedede;margin-top:15px;margin:0px auto;margin-bottom:20px;" class="product-table">
								
								<tbody>
									<tr>
										<td width="15%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;"> </td>
										<td width="50px" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:20px;padding:0px 5px;margin:0;background:#fff;"></td> 
										<td width="20%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-style:normal;letter-spacing:normal;line-height:22px;padding:0;margin:0;background:#fff;text-align:right;"><p>Subtotal</p></td>
										<td width="20%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;padding-left: 20px;"><p><span style="display: inline-block;
    margin-right: 28px;">₹</span><span style="display: inline-block;
    float: right;
    margin-right: 20px;">{{($order->sub_total)}}</span></p></td>
								
		</tr>
		<tr>
										<td width="15%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;"></td>
										<td width="50px" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:20px;padding:0px 5px;margin:0;background:#fff;"></td> 
								<td width="20%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-weight:500;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;text-align:right;"><p>Shipping Charges</p>								</td>
								<td width="20%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;padding-left: 20px;"><p><span style="display: inline-block;
    margin-right: 28px;">₹</span> <span style="display: inline-block;
    float: right;
    margin-right: 20px;">{{$order->total_shipping_amount}}</span></p>	</td>
								
		</tr>
				<tr>
										<td width="15%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;"></td>
										<td width="50px" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:20px;padding:0px 5px;margin:0;background:#fff;"></td> 
								<td width="20%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-weight:500;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;text-align:right;"><p>GST</p></td>
								<td width="20%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;padding-left: 20px;"><p><span style="display: inline-block;
    margin-right: 28px;">₹</span><span style="display: inline-block;
    float: right;
    margin-right: 20px;"> {{$order->discount}}</span></p>	</td>
								
		</tr>
				<tr>
										<td width="15%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-weight:300;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;"></td>
										<td width="50px" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:20px;padding:0px 5px;margin:0;background:#fff;"></td> 
								<td width="20%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;text-align:right;"><p>Total Amount</p></td>
								<td width="20%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;padding-left: 20px;"><p><span style="display: inline-block;
    margin-right: 28px;">₹</span> <span style="display: inline-block;
    float: right;
    margin-right: 20px;">{{$order->total}}</span></p>	</td>
								
		</tr>
		</tbody>
		</table>
							
								</td>
							</tr>
							<tr>
							<td align="center" valign="top" style="padding-bottom:20px;padding-left:20px;padding-right:20px" class="infoDate">
							<p style="color: #444444;font-family: 'Open Sans',Helvetica,Arial,sans-erif;    font-size: 13px;font-weight: 400;line-height: 22px;padding: 0;margin: 0;">Dummy Data added here. Welcome Sportsdrive. Lorum Ipsum Content.Dummy Data added here. Welcome Sportsdrive. Lorum Ipsum Content.</p>
							</td>
							</tr>
							<tr>
								<td align="center" valign="top" style="padding-bottom:5px;padding-left:20px;padding-right:20px" class="btnCard"><table border="0" cellpadding="0" cellspacing="0" align="center"><tbody><tr><td align="center" style="background-color:#1e7ed0;padding-top:10px;padding-bottom:10px;padding-left:25px;padding-right:25px;border-radius:2px" class="postButton"><a href="{{ url('/order/list') }}" style="color:#fff;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:12px;font-weight:600;letter-spacing:1px;line-height:20px;text-transform:uppercase;text-decoration:none;display:block" target="_blank">Manage Orders</a></td></tr></tbody></table></td>
							</tr>
							<tr>
								<td align="center" valign="top" style="padding-bottom:0px;padding-left:20px;padding-right:20px" class="infoDate"><p class="midText" style="color:#313131;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:11px;font-weight:700;line-height:20px;text-align:center;padding:0;margin:0">Payment Method :<?php if(($order->payment_mode)=="cod"){ echo "Cash On Delivery";}
							              else{echo $order->payment_mode;}?></p>
							</td>
							</tr>
							<tr>
								<td align="center" valign="top" style="padding-left:20px;padding-right:20px" class="infoDate"><p class="midText" style="color:#313131;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:11px;font-weight:700;line-height:20px;text-align:center;padding:0;margin:0">Ordered on : {{date("d M Y",strtotime($order->updated_at))}}</p>
							</td>
							</tr>
							<tr>
								<td style="font-size:1px;line-height:1px" height="10">&nbsp;</td>
							</tr>
						</tbody>
					</table>
					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="space">
						<tbody>
							<tr>
								<td style="font-size:1px;line-height:1px" height="30">&nbsp;</td>
							</tr>
						</tbody>
					</table>
			</td>
		</tr>
	</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</center>
</body>

</html>
