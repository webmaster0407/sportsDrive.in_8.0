<!DOCTYPE html>
<html lang="en">
<head>
    <title>SportsDrive Offers</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<table style="width:100%;max-width: 600px;margin: auto;overflow: hidden;text-align: center;font-family: sans-serif;color: #616161;">
    <tbody>
    <tr>
        <td style="text-align: left;"><img src="{{URL::asset('images/logo_promotion.png')}}"></td>
        <td><img src="{{URL::asset('/images/arena_Square_Logo_540X540-1.jpg')}}"></td>
    </tr>
    <tr>
        <td colspan="2" style="font-weight: 18px;text-align: left;padding-bottom: 10px;"> Hi {{$customer['first_name']}} {{$customer['last_name']}},</td>
        <td></td>
    </tr>
    <tr>
        <td colspan="2"><img src="{{URL::asset('uploads/coupons_promotions/'.$banner_image_name)}}" style="width:100%;"></td>
        <td><td></td>
    </tr>
    <tr>
        <td colspan="2" style="font-size: 19px;padding: 10px 0;color: #616161;font-weight: 600;">Simply enter your registered mobile number (mentioned below) at check out, to activate your discount.</td><td></td>
    </tr>
    <tr>
        <td colspan="2">
           <a href="https://www.sportsdrive.in"><div style="display: inline-block;padding: 6px 25px;background: #e4e4e4;border-radius: 10px;"><span style="display: inline-block;vertical-align: middle;"><img src="{{URL::asset('images/am7.png')}}"></span><span style="font-weight: 600;font-size: 26px;vertical-align: middle;color: #616161;padding-left: 5px;">Shop Now</span></div></td><td></a>
        </td>
    </tr>
    <tr><td colspan="2" style="padding: 5px 0"></td><td></td></tr>
    <tr><td colspan="2"></td><td></td></tr>
    <tr><td colspan="2" style="font-size: 18px;padding: 5px 0;color: #616161;font-weight: 600;">Your Registered</td><td></td></tr>
    <tr><td colspan="2"><div style="padding: 5px 30px; border-radius: 30px;background: #e4e4e4;font-size: 18px;font-weight: 600;display: inline-block;">{{$customer['phone']}}</div></td><td></td></tr>
    <tr><td colspan="2" style="padding: 5px 0;font-size: 15px; "><div>MOBILE NUMBER</div></td><td></td></tr>

    <tr><td colspan="2" style="padding-bottom: 10px"></td><td></td></tr>
    <tr><td colspan="2"><div style="font-size: 20px;padding-bottom: 10px;">Let's stay in touch</div></td><td></td></tr>
    <tr>
        <td colspan="2"><div style="padding-bottom: 5px;">
                <a target="_blank" href="{{$siteDetails->instagram_url}}"><img src="{{URL::asset('images/am4.png')}}"></a>
            </div>
        </td>
        <td></td>
    </tr>
    <tr><td colspan="2">
            <div>
                <a target="_blank" href="{{$siteDetails->facebook_url}}"><img src="{{URL::asset('images/am5.png')}}"></a>
            </div></td><td></td></tr>
    </tbody>
</table>
</body>
</html>
