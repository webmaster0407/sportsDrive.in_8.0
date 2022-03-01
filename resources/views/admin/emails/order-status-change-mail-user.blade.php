<!DOCTYPE html> 
<html>
<head>
<!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/> -->
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
<title> Sportsdrive:User Registration</title>
<link href="{{asset('css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">
<link href="{{asset('css/font-awesome.css')}}" rel="stylesheet" type="text/css">
<!-- <script src="http://cdn.webrupee.com/js" type="text/javascript"></script> -->
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i,600,600i,700,700i,800" rel="stylesheet">
<style>
    /*font-family: 'Open Sans', sans-serif;*/
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
                                <td align="center" valign="top" style="padding-top:20px;padding-bottom:5px;padding-left:20px;padding-right:20px" class="title"><h2 class="bigTitle" style="color:#313131;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:26px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:34px;text-align:center;padding:18px;margin:0">Order Status Changed #{{$orderNo}} </h2></td>
                            </tr>
                            <tr>
                            <td align="left" valign="top" style="padding-bottom:20px;padding-left:20px;padding-right:20px" class="infoDate">
                            <p style="color: #444444;font-family: 'Open Sans',Helvetica,Arial,sans-erif;    font-size: 14px;font-weight: 400;line-height: 22px;padding: 0;margin: 0;"><strong>Dear {{$user}}, </strong></p>
                            </td>
                            </tr>
                            <tr>
                            <td align="left" valign="top" style="padding-bottom:20px;padding-left:20px;padding-right:20px" class="infoDate">
                            <p style="color: #444444;font-family: 'Open Sans',Helvetica,Arial,sans-erif;    font-size: 14px;font-weight: 400;line-height: 22px;padding: 0;margin: 0;">{{$emailMessage}}</p>
                            </td> 
                            </tr>
                            @if($order_status_db!=$orderStatus)
                            <tr>
                            <td align="left" valign="top" style="padding-bottom:20px;padding-left:20px;padding-right:20px" class="infoDate">
                            <p style="color: #444444;font-family: 'Open Sans',Helvetica,Arial,sans-erif;    font-size: 14px;font-weight: 400;line-height: 22px;padding: 0;margin: 0;">Your Order Status is : <strong>{{$orderStatus}}</strong></p>
                            </td>
                            </tr>
                            @endif
                            @if($payment_status_db!=$paymentStatus)
                            <tr>
                            <td align="left" valign="top" style="padding-bottom:20px;padding-left:20px;padding-right:20px" class="infoDate">
                            <p style="color: #444444;font-family: 'Open Sans',Helvetica,Arial,sans-erif;    font-size: 14px;font-weight: 400;line-height: 22px;padding: 0;margin: 0;">Your Payment Status is :<strong> {{$paymentStatus}}</strong></p>
                            </td>
                            </tr>
                            @endif
<tr>
                                <td>
                                <table width="96%" cellspacing="1" cellpadding="1" style="border-bottom:1px solid #dedede;margin-top:15px;margin:0px auto;" class="product-table">
                                    <thead>
                                        <tr><th style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:26px;text-align:center;padding:0;margin:0">Product</th>
                                        <th style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:26px;text-align:center;padding:0;margin:0">Product Name</th>
                                        <th style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:26px;text-align:center;padding:0;margin:0">Qty.</th>
                                        <th style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:26px;text-align:center;padding:0;margin:0">Total Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach($cart as $val)

                                             <tr>
                                                <td width="15%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;text-align:center;padding:0;margin:0;background:#fff;"> <div style="display: inline-block;vertical-align: top;margin-top:3px;">

                                                        @if($val->configuration_image != null)
                                                            <?php $image = "uploads/products/images/$val->product_id/80x85/$val->configuration_image";?>
                                                            <img src="{{URL::asset($image)}}" alt="image" width="100%" style="width:80px;max-height:80px;">

                                                        @elseif($val->image!= null)
                                                            <?php $image = "uploads/products/images/$val->product_id/80x85/$val->image";?>
                                                            <img src="{{URL::asset($image)}}" alt="image" width="100%" style="width:80px;max-height:80px;">
                                                        @else
                                                             <img src="{{URL::asset('images/no-image-available.png')}}" alt="image" width="100%" style="width:80px;max-height:80px;">
                                                        @endif
                                                      </div></td>
                                                <td width="50px" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:20px;padding:0px 5px;margin:0;background:#fff;"><a href="{{ url('/product/details/'.$val->product_slug) }}">
                                                {{$val->product_name}}</a>
                                                    <div class="other-info">
                                                        @if($val->color!=null)
                                                         <p style="line-height:22px; font-size:13px;"><span style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:22px;text-align:center;padding:0;margin:0;background:#fff;">Color:</span>{{$val->color}}</p>
                                                        @endif
                                                        @if($val->size!=null)
                                                         <p style="line-height:22px; font-size:13px;"><span style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:22px;text-align:center;padding:0;margin:0;background:#fff;">Size:</span>{{$val->size}}</p>
                                                        @endif
                                                        @if($val->sku!=null)
                                                            <p style="line-height:22px; font-size:13px;"><span style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:22px;text-align:center;padding:0;margin:0;background:#fff;">SKU:</span>{{$val->sku[0]}}</p>
                                                        @endif
                                                    </div>
                                                </td>
                                            <td width="20%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;text-align:center;padding:0;margin:0;background:#fff;"><p>
                                            {{$val->quantity}}</p>                                </td>
                                            <td width="20%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;text-align:center;padding:0;margin:0;background:#fff;"><p><span style="display: inline-block;
                                    }
                                    }
            margin-right: 28px;">₹</span> <span style="display: inline-block;    float: right;    margin-right: 20px;">{{number_format($val->final_price,2)}}</span></p> </td>
                                        </tr>
                                    @endforeach
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
        margin-right: 20px;">{{number_format($orders->sub_total+$orders->discount+$orders->coupon_discount,2)}}</span></p></td>

                                        </tr>
                                        <tr>
                                            <td width="15%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;"></td>
                                            <td width="50px" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:20px;padding:0px 5px;margin:0;background:#fff;"></td>
                                            <td width="20%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-weight:500;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;text-align:right;"><p>Shipping Charges</p>                               </td>
                                            <td width="20%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;padding-left: 20px;"><p><span style="display: inline-block;
        margin-right: 28px;">₹</span> <span style="display: inline-block;
        float: right;
        margin-right: 20px;">{{number_format($orders->total_shipping_amount,2)}}</span> </p>  </td>

                                        </tr>
                                        @if($orders->discount>0)
                                            <tr>
                                                <td width="15%" style="color:red;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;"></td>
                                                <td width="50px" style="color:red;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:20px;padding:0px 5px;margin:0;background:#fff;"></td>
                                                <td width="20%" style="color:red;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-weight:500;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;text-align:right;"><p>Offer Discount</p>                               </td>
                                                <td width="20%" style="color:red;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;padding-left: 20px;"><p><span style="display: inline-block;
            margin-right: 28px;">₹</span> <span style="display: inline-block;
            float: right;
            margin-right: 20px;">{{number_format($orders->discount,2)}}</span> </p>  </td>
                                            </tr>
                                        @endif
                                        @if($orders->coupon_discount>0)
                                            <tr>
                                                <td width="15%" style="color:red;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;"> </td>
                                                <td width="50px" style="color:red;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:20px;padding:0px 5px;margin:0;background:#fff;"></td>
                                                <td width="20%" style="color:red;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-style:normal;letter-spacing:normal;line-height:22px;padding:0;margin:0;background:#fff;text-align:right;"><p>Coupon Discount({{$orders->coupon_code}})</p></td>
                                                <td width="20%" style="color:red;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;padding-left: 20px;"><p><span style="display: inline-block;
		margin-right: 28px;">- ₹</span><span style="display: inline-block;
		float: right;
		margin-right: 20px;"> {{(number_format($orders->coupon_discount,2))}}</span></p></td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td width="15%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-weight:300;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;"></td>
                                            <td width="50px" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:13px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:20px;padding:0px 5px;margin:0;background:#fff;"></td>
                                            <td width="20%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;text-align:right;"><p>Total Amount</p></td>
                                            <td width="20%" style="color:#444444;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;font-style:normal;letter-spacing:normal;line-height:26px;padding:0;margin:0;background:#fff;padding-left: 20px;"><p><span style="display: inline-block;
        margin-right: 28px;">₹</span> <span style="display: inline-block;
        float: right;
        margin-right: 20px;">{{number_format($orders->total,2)}}</span></p>   </td>

                                        </tr>
                                    </tbody>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                            <td align="left" valign="top" style="padding-bottom:20px;padding-left:20px;padding-right:20px" class="infoDate">
                            <p style="color: #444444;font-family: 'Open Sans',Helvetica,Arial,sans-erif;    font-size: 14px;font-weight: 400;line-height: 22px;padding: 0;margin: 0;">Warm Regards,
                        <br />
                        Sports Drive Team</p>
                            </td>
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
