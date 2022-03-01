<!doctype html>
<html>
<head>
<link rel="shortcut icon" href="/images/favicon.png" type="image/x-icon">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE10" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE11" />
<meta http-equiv="X-UA-Compatible" content="IE=IE8" />
<meta http-equiv="X-UA-Compatible" content="IE=IE9" />
<meta http-equiv="X-UA-Compatible" content="IE=IE10" />
<meta http-equiv="X-UA-Compatible" content="IE=IE11" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="csrf_token" content="{{ csrf_token() }}">

<title>Sports Drive</title>
<meta name="title" content="Sports Drive">
<meta name="keywords" content="Sports Drive">
<meta name="description" content="Sports Drive">


<!-- Animation CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/animate.css')}}">   
<!-- Latest Bootstrap min CSS -->
<link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css')}}">
<!-- Google Font -->
<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900&display=swap" rel="stylesheet"> 
<!-- Icon Font CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/all.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/ionicons.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/themify-icons.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/linearicons.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/flaticon.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/simple-line-icons.css')}}">
<!--- owl carousel CSS-->
<link rel="stylesheet" href="{{ asset('assets/owlcarousel/css/owl.carousel.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/owlcarousel/css/owl.theme.css')}}">
<link rel="stylesheet" href="{{ asset('assets/owlcarousel/css/owl.theme.default.min.css')}}">
<!-- Magnific Popup CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.css')}}">
<!-- Slick CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/slick.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/slick-theme.css')}}">
<!-- Style CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/style.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/responsive.css')}}">

<!-- Custom Common Css -->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom-common.css') }}">



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>


@stack('stylesheets')
</head>
<body>
<!-- LOADER -->
<div class="preloader">
    <div class="lds-ellipsis">
        <span></span>
        <span></span>
        <span></span>
    </div>
</div>
<!-- END LOADER -->
<div class="full_wrapper">
    <header>
        @include('user.includes.header')
    </header>
    @yield('content')

</div>
<footer>
    @include('user.includes.footer')
</footer>

<script>
    var label=document.querySelector('.all-search span'),
        catfilter = document.querySelector('.category-select');
    catfilter.addEventListener('change',updateLabel);
    function updateLabel(){
        var ind=this.selectedIndex+1;
        if (label) {
            label.innerHTML = $('option:nth-child('+ind+')',catfilter).text();
        }
    }
</script>

<script src="{{asset('js/search.js')}}" type="text/javascript" language="javascript"></script>
@stack('scripts')

</body>
</html>

