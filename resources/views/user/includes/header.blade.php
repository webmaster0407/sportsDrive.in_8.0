<!-- START HEADER -->
<header class="header_wrap">
    <div class="middle-header dark_skin">
    	<div class="container">
            <div class="nav_block">
                <a class="navbar-brand" href="{{ route('index') }}">
                    <img class="logo_dark" src="{{ asset('images/logo.png')}}" alt="logo">
<!--                     <img class="logo_dark" src="{{ asset('assets/images/logo_dark.png')}}" alt="logo"> -->
                </a>
               	<?php $cartCount = getCartCount();
	            	$categories = getCategories();
				?>
                <div class="product_search_form radius_input search_form_btn">
                    <form  class="search-S" name="search" method="get" action="/search">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="custom_select">
                                    <select class="first_null not_chosen category-select" name="c" id="categoryList">
                                     	<option value="0" selected>All Category</option>
										@foreach($categories as $category)
											<option value="{{$category['id']}}">{{$category['name']}}</option>
										@endforeach
                                    </select>
                                </div>
                            </div>
                            <input class="form-control" name="keyword" id="searchKeyword" placeholder="Search Product..." minlength="3" type="text">
                            <button type="submit" class="search_btn3">Search</button>
                        </div>
                        <div class="input-group">

                            <div class="auto-show" id="searchList" style="display: none">
							</div>
                        </div>
                    </form>
                </div>
                <ul class="user-links navbar-nav attr-nav align-items-center">
                    <!-- <li><a href="#" class="nav-link"><i class="linearicons-user"></i></a></li> -->
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="nav-link dropdown-toggle" href="#"><i class="linearicons-user"></i></a>
                        <div class="dropdown-menu">
                            <ul style="list-style-type: none; padding: 0 20px;"> 
                            <?php $user = \Illuminate\Support\Facades\Auth::user(); ?>
                            @if($user==null)
                                <li><a class="dropdown-item nav-link nav_item" href="/login">Login</a></li>
                                <li><a class="dropdown-item nav-link nav_item" href="/register">Register</a></li>
                            @elseif($user!=null)
                                <li><a class="dropdown-item nav-link nav_item" href="/order/list">Your Orders</a></li>
                                <li><a class="dropdown-item nav-link nav_item" href="/address">Your Addresses</a></li>
                                <li><a class="dropdown-item nav-link nav_item" href="/my-profile">Your Profile</a></li>
                                <li><a class="dropdown-item nav-link nav_item" href="/change-password">Change Password</a></li>
                                <li><a class="dropdown-item nav-link nav_item" href="/logout">Logout</a></li>
                            @endif
                            </ul>
                        </div>   
                    </li>
                    
                    <li class="dropdown cart_dropdown">
                        <a class="nav-link cart_trigger" href="#" data-toggle="dropdown">
                            <i class="linearicons-bag2"></i>
                            @if($cartCount>0)
                            <span class="cart_count" id="cart_count">{{$cartCount}}</span>
                            @endif
                        </a>
                        <div class="cart_box cart_right dropdown-menu dropdown-menu-right">
                            <div class="cart_footer">
                                <p class="cart_buttons">
                                    <a href="/cart/view" class="btn btn-fill-line view-cart">View Cart</a>
                                    <a href="/checkout/1" class="btn btn-fill-out checkout">Checkout</a>
                                </p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="bottom_header dark_skin main_menu_uppercase border-top">
    	<div class="container">
            <div class="row align-items-center"> 
            	<div class="col-lg-3 col-md-4 col-sm-6 col-3">
                	<div class="categories_wrap">
                        
                        <button type="button" data-toggle="collapse" data-target="#navCatContent" aria-expanded="false" class="categories_btn categories_menu">
                            <span>Shop by Categories </span><i class="linearicons-menu"></i>
                        </button>
                        <a href="/shop-by-category"></a>
                        <div id="navCatContent" class="navbar collapse">
                            <ul> 
                                @foreach($categories as $category)
                                <li class="dropdown dropdown-mega-menu">
                                    <a class="nav-link" href="/category/{{$category['slug']}}?page=1">
                                        <span>{{$category['name']}}</span>
                                    </a>
                                    <a class="dropdown-item nav-link dropdown-toggler" data-toggle="dropdown"></a>
                                    <div class="dropdown-menu">
                                        <ul class="mega-menu d-lg-flex">
                                            @if($category['image']!=null)
                                            <li class="mega-menu-col col-lg-8">
                                            @else
                                            <li class="mega-menu-col col-lg-12">
                                            @endif
                                                <ul class="d-lg-flex">
                                                    <?php $subCatCount = count($category['sub_categories']);?>
                                                    @foreach($category['sub_categories'] as $subCatKeys=>$subCategory)
                                                    <li class="mega-menu-col col-lg-6">
                                                        @if($category['sub_sub_categories_count'] == 0)
                                                        <ul>
                                                            <li>
                                                                <a class="dropdown-item nav-link nav_item" href="/category/{{$subCategory['slug']}}?page=1">{{$subCategory['name']}}
                                                                </a>
                                                            </li>
                                                        </ul>
                                                        @else
                                                        <ul> 
                                                            <li class="dropdown-header">
                                                                <a href="/category/{{$subCategory['slug']}}?page=1">
                                                                    <span>{{$subCategory['name']}}</span>
                                                                </a>
                                                            </li>
                                                            @foreach($subCategory['subSubCategories'] as $subSubCategories)
                                                                <li>
                                                                    <a class="dropdown-item nav-link nav_item" href="/category/{{$subSubCategories['slug']}}?page=1">{{$subSubCategories['name']}}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                        @endif
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                            @if($category['image']!=null)
                                            <li class="mega-menu-col col-lg-4">
                                                <div class="header-banner2">
                                                    <img src="/uploads/categories/426x210/{{$category['image']}}" alt="menu_banner1">
                                                    <div class="banne_info">
                                                        <h6>10% Off</h6>
                                                        <h4>Computers</h4>
                                                        <a href="#">Shop now</a>
                                                    </div>
                                                </div>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-md-8 col-sm-6 col-9">
                	<nav class="navbar navbar-expand-lg">
                    	<button class="navbar-toggler side_navbar_toggler" type="button" data-toggle="collapse" data-target="#navbarSidetoggle" aria-expanded="false"> 
                            <span class="ion-android-menu"></span>
                        </button>
                        <div class="pr_search_icon">
                            <a href="javascript:void(0);" class="nav-link pr_search_trigger"><i class="linearicons-magnifier"></i></a>
                        </div> 
                        <?php $cmsPages = cmsHeaderMenu();?>
                        <div class="collapse navbar-collapse mobile_side_menu" id="navbarSidetoggle">
                            <ul class="navbar-nav">
                                <li class="dropdown">
                                    <a class="nav-link  active" href="{{ route('index') }}">Home</a>
                                </li>
                                @foreach($cmsPages as $page)
                                    <li><a class="nav-link" href="/{{$page->slug}}">{{$page->page_title}}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="contact_phone contact_support">
                                @php
                                    $data  = servicePages();
                                    $addressData = $data['admin'];
                                @endphp
                            <i class="linearicons-phone-wave"></i>
                            <span>{{$addressData->contact_telephone}}</span>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- END HEADER -->

<script>
    window.onscroll = function() {
        myFunction()
    };
    var header = document.getElementById("myHeader11");
    if (header) {
    	var sticky = header.offsetTop;    	
    }

    function myFunction() {
        if (window.pageYOffset > sticky) {
        	if (header) {
        		header.classList.add("sticky");
        	}
        } else {
        	if (header) {
        		header.classList.remove("sticky");
        	}
        }
    }
</script>
<meta name="google-site-verification" content="NBp6CA9871quqS2gevVZ9AOHvEoqDrQbMbU2Ga7Hlkc" />
	<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TQ7Z23H');</script>
<!-- End Google Tag Manager -->
	

