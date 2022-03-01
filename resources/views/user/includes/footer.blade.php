<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TQ7Z23H"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<!-- START SECTION SUBSCRIBE NEWSLETTER -->
<div class="section bg_default small_pt small_pb">
	<div class="container">	
    	<div class="row align-items-center">	
            <div class="col-md-6">
            	<div class="newsletter_text text_white">
                    <h3>Join Our Newsletter Now</h3>
                    <p> Register now to get updates on promotions. </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="newsletter_form2">
                    <form>
                        <input type="text" required="" class="form-control rounded-0" placeholder="Enter Email Address">
                        <button type="submit" class="btn btn-dark rounded-0" name="submit" value="Submit">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- START SECTION SUBSCRIBE NEWSLETTER -->

</div>
<!-- END MAIN CONTENT -->

<!-- START FOOTER -->
<footer class="bg_gray">
	@php
		$data  = servicePages();
		$servicePages = $data['servicePages'];
		$addressData = $data['admin'];
		$footerPages = $data['footerPages'];
	@endphp
	<div class="footer_top small_pt pb_20">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-md-5">
                	<div class="widget">
                        <div class="footer_logo">
                            <a href="{{ route('index') }}"><img src="{{ asset('assets/images/logo-footer.png')}}" alt="logo" height="50" /></a>
                        </div>
                        <p class="mb-3"><strong>SPORTIFF INDIA PVT LTD</strong></p>
                        <ul class="contact_info">
                            <li>
                                {!! $addressData->contact_address !!}
                            </li>
                            <li>
                                <p><strong>MOBILE: </strong>{{$addressData->contact_telephone}}</p>
                            </li>
                        </ul>
                    </div>
        		</div>
                <div class="col-lg-3 col-md-3">
                	<div class="widget">
                        <h6 class="widget_title">Customer Services</h6>
                        <ul class="widget_links">
                            <li><a href="/disclaimer-policies">Disclaimer Policies</a></li>
                            <li><a href="/refund-policies">Refund Policies</a></li>
                            <li><a href="/shipping-policies">Shipping Policies</a></li>
                            <li><a href="/exchange-policies">Exchange Policies</a></li>
                            <li><a href="/payment-policies">Payment Policies</a></li>
                            <li><a href="/privacy-notice">Privacy Notice</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                	<div class="widget">
                        <h6 class="widget_title">About US</h6>
                        <ul class="widget_links">
                            <li><a href="/about-us">About US</a></li>
                            <li><a href="/terms-and-conditions">Term & Conditions</a></li>
                        </ul>
                    </div>
                    <div class="widget">
                    	<h6 class="widget_title">Social</h6>
                        <ul class="social_icons">
                            <li><a href="{{$addressData->facebook_url}}" target="_blank" class="sc_facebook"><i class="ion-social-facebook"></i></a></li>
                            <li><a href="{{$addressData->twitter_url}}" target="_blank" class="sc_twitter"><i class="ion-social-twitter"></i></a></li>
                            <li><a href="{{$addressData->googleplus_url}}" target="_blank" class="sc_google"><i class="ion-social-googleplus"></i></a></li>
                            <li><a href="{{$addressData->youtube_url}}" target="_blank" class="sc_youtube"><i class="ion-social-youtube-outline"></i></a></li>
                            <li><a href="{{$addressData->instagram_url}}" target="_blank" class="sc_instagram"><i class="ion-social-instagram-outline"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bottom_footer border-top-tran">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-center text-md-left mb-md-0">Copyright <script language="JavaScript" type="text/javascript">
		                now = new Date
		                theYear=now.getYear()
		                if (theYear < 1900)
		                theYear=theYear+1900
		                document.write(theYear)
		            </script> <a href="/">Sportsdrive</a></p>
                </div>
                <div class="col-lg-6">
                    <ul class="footer_payment text-center text-md-right">
                        <li><a href="http://www.visa.com"><img src="{{ asset('assets/images/visa.png')}}" alt="visa"></a></li>
                        <li><a href="http://www.discover.com"><img src="{{ asset('assets/images/discover.png')}}" alt="discover"></a></li>
                        <li><a href="http://www.mastercard.com"><img src="{{ asset('assets/images/master_card.png')}}" alt="master_card"></a></li>
                        <li><a href="http://www.paypal.com"><img src="{{ asset('assets/images/paypal.png')}}" alt="paypal"></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- END FOOTER -->

<a href="#" class="scrollup" style="display: none;"><i class="ion-ios-arrow-up"></i></a> 

<!-- Latest jQuery --> 
<script src="{{ asset('assets/js/jquery-3.6.0.min.js')}}"></script> 
<!-- popper min js -->
<script src="{{ asset('assets/js/popper.min.js')}}"></script>
<!-- Latest compiled and minified Bootstrap --> 
<script src="{{ asset('assets/bootstrap/js/bootstrap.min.js')}}"></script> 
<!-- owl-carousel min js  --> 
<script src="{{ asset('assets/owlcarousel/js/owl.carousel.min.js')}}"></script> 
<!-- magnific-popup min js  --> 
<script src="{{ asset('assets/js/magnific-popup.min.js')}}"></script> 
<!-- waypoints min js  --> 
<script src="{{ asset('assets/js/waypoints.min.js')}}"></script> 
<!-- parallax js  --> 
<script src="{{ asset('assets/js/parallax.js')}}"></script> 
<!-- countdown js  --> 
<script src="{{ asset('assets/js/jquery.countdown.min.js')}}"></script> 
<!-- imagesloaded js --> 
<script src="{{ asset('assets/js/imagesloaded.pkgd.min.js')}}"></script>
<!-- isotope min js --> 
<script src="{{ asset('assets/js/isotope.min.js')}}"></script>
<!-- jquery.dd.min js -->
<script src="{{ asset('assets/js/jquery.dd.min.js')}}"></script>
<!-- slick js -->
<script src="{{ asset('assets/js/slick.min.js')}}"></script>
<!-- elevatezoom js -->
<script src="{{ asset('assets/js/jquery.elevatezoom.js')}}"></script>
<!-- scripts js --> 
<script src="{{ asset('assets/js/scripts.js')}}"></script>
