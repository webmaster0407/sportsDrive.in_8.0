<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Sports Drive</title>
    <style type="text/css">
        #outlook a {padding:0;}
        body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;}
        .ExternalClass {width:100%;}  
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;}
        #backgroundTable {margin:0; padding:0; width:100% !important; line-height: 100% !important; background-color: #fff;}
        img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic;} 
        a img {border:none;} 
        .image_fix {display:block;}
        p {margin: 1em 0;}
        h1, h2, h3, h4, h5, h6 {color:
         black !important;}
        h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {color: #C00 !important;}
        h1 a:active, h2 a:active,  h3 a:active, h4 a:active, h5 a:active, h6 a:active {
        color: #C00 !important; 
        }
        h1 a:visited, h2 a:visited,  h3 a:visited, h4 a:visited, h5 a:visited, h6 a:visited {
        color: #C00 !important;
        }
        table td {border-collapse: collapse;}
        table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
        a {color:#C00;}
/*      @media only screen and (max-device-width: 720px) and (orientation:portrait) {*/
        @media only screen and (max-width: 600px) { 
            table[class="emailBody"]{ width:100%!important; min-width:320px!important;}
            td[class="emailWrapper"]{padding:0 !important}
            td[class="heading"]{ padding:10px!important; font-size:20px!important;}
            td[class="h30"] img{ height:30px!important; width:auto!important;}
            a[href^="tel"], a[href^="sms"] {
                        text-decoration: none;
                        color: blue; /* or whatever your want */
                        pointer-events: none;
                        cursor: default;
                    }

            .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
                        text-decoration: default;
                        color: #666666 !important;
                        pointer-events: auto;
                        cursor: default;
                    }
            *[class*="mobileOnly"] {
                    display: block !important;
                    max-height: none !important;
                    font-size: 12px !important;
                    line-height: 1.35em !important;
             }
             *[class*="mobile_link"] {
                    display: block !important;
                    max-height: none !important;
                    font-size: 16px !important;
                    line-height: 1.35em !important;
             }
            *[class*="desktopOnly"]{ display:none;}

        }

        /* More Specific Targeting */

        @media only screen and (-webkit-min-device-pixel-ratio: 2) {
        /* Put your iPhone 4g styles in here */ 
        }
        @media only screen and (-webkit-device-pixel-ratio:.75){
        /* Put CSS for low density (ldpi) Android layouts in here */

        }
        @media only screen and (-webkit-device-pixel-ratio:1){
        /* Put CSS for medium density (mdpi) Android layouts in here */

        }
        @media only screen and (-webkit-device-pixel-ratio:1.5){
        /* Put CSS for high density (hdpi) Android layouts in here */
        }

    </style>
    
    <!-- Targeting Windows Mobile -->
    <!--[if IEMobile 7]>
    <style type="text/css">
        
    </style>
    <![endif]-->   

    <!--[if gte mso 9]>
        <style>
        /* Target Outlook 2007 and 2010 */
        </style>
    <![endif]-->
</head>
<body style="background-color: #fff; -webkit-text-size-adjust: none; margin:0;">
<table cellpadding="0" cellspacing="0" border="0" id="backgroundTable" width="100%">
    <tr>
        <td valign="top" align="center">
        <table cellpadding="0" cellspacing="0" border="0" align="center" class="emailBody" width="600" style="min-width:600px;">
            <tr class="desktopOnly">
                <td valign="top" height="20px" class="desktopOnly">&nbsp;</td>
            </tr>
          <tr>
                <td valign="top" style="padding:0; text-align:center; background-color:#ffffff;outline-style: none;" class="emailWrapper">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td valign="middle" align="center" style="font: 22px/1.35em Arial,Helvetica,Tahoma; margin: 0px; padding: 10px 0 25px; color: #ffffff;outline-style: none; border-bottom:2px solid #d1d2d4;" class="heading"><img src="{{URL::asset('images/logo.png')}}" class="image_fix" style="display:block" width="197" height="60" alt="Heatco Direct" /></td>
                    </tr>
                    <tr>
                      <td valign="middle" style="font: 16px/1.35em Arial,Helvetica,Tahoma; color:#6d6e70; font-weight:bold; padding: 15px 20px 0 20px; text-align: left; background-color:#ffffff; outline-style: none">Dear {{$admin_name}}, </td>
                    </tr>
                    <tr>
                      <td valign="middle" style="font: 28px/1.35em Arial,Helvetica,Tahoma; color:#57c0b8; font-weight:normal; padding: 15px 20px 0 20px; text-align: left; background-color:#ffffff; outline-style: none">Reset Password Link</td>
                    </tr>
                    <tr>
                    
                        <td valign="middle" style="font: 14px/1.35em Arial,Helvetica,Tahoma; color:#6d6e70; padding: 15px 20px 0 20px; text-align: left; background-color:#ffffff; outline-style: none; ">You are receiving this email because we received a forgot password request for <b> {{$admin_email}} </b> account.<br/>
                       
                        </td>
                    </tr>
                    <tr>
                        <td style="font-family:Avenir,Helvetica, sans-serif; box-sizing: border-box;">
                            <br/>
                            <a href="{{ "http://".env('DOMAIN_NAME')."/administrator/admin-reset-password/$remember_token" }}" class="button button-blue" target="_blank" style="margin-left:33%;font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; border-radius: 3px; box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16); color: #FFF; display: inline-block; text-decoration: none; -webkit-text-size-adjust: none; background-color: #3097D1; border-top: 10px solid #3097D1; border-right: 18px solid #3097D1; border-bottom: 10px solid #3097D1; border-left: 18px solid #3097D1;">Reset Password</a>
                        </td>
                    </tr>
                    <tr>
                    
                        <td valign="middle" style="font: 14px/1.35em Arial,Helvetica,Tahoma; color:#6d6e70; padding: 15px 20px 0 20px; text-align: left; background-color:#ffffff; outline-style: none; ">OR visit the link:<br/> <a href="{{ "http://".env('DOMAIN_NAME')."/administrator/admin-reset-password/$remember_token" }}" class="button button-blue" target="_blank" style="color:#3097D1"> {{ "http://".env('DOMAIN_NAME')."/administrator/admin-reset-password/$remember_token" }}</a><br/>
                       
                        </td>
                    </tr>
                    <tr>
                    
                        <td valign="middle" style="font: 14px/1.35em Arial,Helvetica,Tahoma; color:#6d6e70; padding: 15px 20px 0 20px; text-align: left; background-color:#ffffff; outline-style: none; ">If you don't want to change your password or didn't request this, just ignore and delete this message.<br/>
                        To keep your account secure, please do not forward this email to anyone.<br/>
                       
                        </td>
                    </tr>
                    <tr>
                        <td valign="middle" style="font: 14px/1.35em Arial,Helvetica,Tahoma; color:#6d6e70; padding: 15px 20px; text-align: left; background-color:#ffffff; outline-style: none">Warm Regards,
                        <br />
                       Sports Drive Team</td>
                    </tr>
                    </table>
              </td>
            </tr>
            <tr>
                <td valign="top" style="padding:0; text-align:center; background-color:#89d3cd;outline-style: none; ">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tableFooter">
                    <tr>
                      <td valign="top" align="center" style="padding:10px 0 0">
                        <table width="150" border="0" cellspacing="0" cellpadding="0" class="tableSocial">
                             <tr>
                                 @if($siteDetails->facebook_url!=null)
                                 <td valign="top" align="center" style="padding:5px"><a href="{{$siteDetails->facebook_url}}" target="_blank" title="Facebook"><img src="{{{URL::asset('images/facebook.jpg')}}}" width="32" height="32" alt="Facebook" /></a></td>
                                 @endif
                                 @if($siteDetails->twitter_url!=null)
                                 <td valign="top" align="center" style="padding:5px"><a href="{{$siteDetails->twitter_url}}" target="_blank" title="Twitter"><img src="{{{URL::asset('images/twitter.jpg')}}}" width="32" height="32" alt="Twitter" /></a></td>
                                 @endif
                                 @if($siteDetails->instagram_url!=null)
                                 <td valign="top" align="center" style="padding:5px"><a href="{{$siteDetails->instagram_url}}" target="_blank" title="Instagram"><img src="{{{URL::asset('images/instagram.jpg')}}}" width="32" height="32" alt="Instagram" /></a></td>
                                 @endif
                             </tr>
                         </table>
                     </td>
                     </tr>
                  
                    <<tr>
                        <td valign="top" style="font: 12px/1.35em Arial,Helvetica,Tahoma; background:#89d3cd; color:#6d6e70; padding:5px 5px 10px;text-align:center">© Copyright   <script language="JavaScript" type="text/javascript">
                        now = new Date
                        theYear=now.getYear()
                        if (theYear < 1900)
                        theYear=theYear+1900
                        document.write(theYear)
                        </script><a href="http://www.sportsdrive.in" style="color:#6d6e70; text-decoration:none" target="_blank"> Sports Drive </a> All rights reserved. </td>
                      </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td valign="top" height="20px">&nbsp;</td>
            </tr>
        </table>
        </td>
    </tr>
</table>  
<!-- End of wrapper table -->
</body>
</html>
