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
                                    <td align="center" valign="top" style="padding-top:20px;padding-bottom:5px;padding-left:20px;padding-right:20px" class="title"><h2 class="bigTitle" style="color:#313131;font-family:'Open Sans',Helvetica,Arial,sans-serif;font-size:26px;font-weight:600;font-style:normal;letter-spacing:normal;line-height:34px;text-align:center;padding:0;margin:0">User Registration</h2></td>
                                </tr>

                                <tr>
                                    <td align="left" valign="top" style="padding-bottom:20px;padding-left:20px;padding-right:20px" class="infoDate">
                                        <p style="color: #444444;font-family: 'Open Sans',Helvetica,Arial,sans-erif;    font-size: 14px;font-weight: 400;line-height: 22px;padding: 0;margin: 0;">Dear {{$first_name}}, </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left" valign="top" style="padding-bottom:20px;padding-left:20px;padding-right:20px" class="infoDate">
                                        <p style="color: #444444;font-family: 'Open Sans',Helvetica,Arial,sans-erif;    font-size: 14px;font-weight: 400;line-height: 22px;padding: 0;margin: 0;">Thank you for your interest in Sports Drive as Partner!</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left" valign="top" style="padding-bottom:20px;padding-left:20px;padding-right:20px" class="infoDate">
                                        <p style="color: #444444;font-family: 'Open Sans',Helvetica,Arial,sans-erif;    font-size: 14px;font-weight: 400;line-height: 22px;padding: 0;margin: 0;">A warm welcome from all of us here. We hope you enjoy using <Sports Drive.com link here> and wish you all the very best.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left" valign="top" style="padding-bottom:20px;padding-left:20px;padding-right:20px" class="infoDate">
                                        <p style="color: #444444;font-family: 'Open Sans',Helvetica,Arial,sans-erif;    font-size: 14px;font-weight: 400;line-height: 22px;padding: 0;margin: 0;">To be able to sign in to your account, please click the following link:</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left" valign="top" style="padding-bottom:20px;padding-left:20px;padding-right:20px" class="infoDate">
                                        <p style="color: #444444;font-family: 'Open Sans',Helvetica,Arial,sans-erif;    font-size: 14px;font-weight: 400;line-height: 22px;padding: 0;margin: 0;"><a href="{{env('DOMAIN_NAME')}}/partner/login" style="color:#C00" target="_blank">Click Here</a></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left" valign="top" style="padding-bottom:20px;padding-left:20px;padding-right:20px" class="infoDate">
                                        <p style="color: #444444;font-family: 'Open Sans',Helvetica,Arial,sans-erif;    font-size: 14px;font-weight: 400;line-height: 22px;padding: 0;margin: 0;">Login Details Are,</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left" valign="top" style="padding-bottom:20px;padding-left:20px;padding-right:20px" class="infoDate">
                                        <p style="color: #444444;font-family: 'Open Sans',Helvetica,Arial,sans-erif;    font-size: 14px;font-weight: 400;line-height: 22px;padding: 0;margin: 0;">Username: {{$email_address}}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left" valign="top" style="padding-bottom:20px;padding-left:20px;padding-right:20px" class="infoDate">
                                        <p style="color: #444444;font-family: 'Open Sans',Helvetica,Arial,sans-erif;    font-size: 14px;font-weight: 400;line-height: 22px;padding: 0;margin: 0;">Password: {{$password}}</p>
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
