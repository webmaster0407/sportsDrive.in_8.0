@extends('layouts.cms-pages')
@section('content')


<!-- START SECTION BREADCRUMB -->
<div class="breadcrumb_section bg_gray page-title-mini">
    <div class="container"><!-- STRART CONTAINER -->
        <div class="row align-items-center">
          <div class="col-md-6">
                <div class="page-title">
                <h1>{{$cmsPage['page_title']}}</h1>
                </div>
            </div>
            <div class="col-md-6">
                <ol class="breadcrumb justify-content-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                    <li class="breadcrumb-item active">{{$cmsPage['page_title']}}</li>
                </ol>
            </div>
        </div>
    </div><!-- END CONTAINER-->
</div>
<!-- END SECTION BREADCRUMB -->

<div class="content">
	<div class="listingContent">
 		<div class="container">
			<div class="cmsPageClass">
				
				<?php if(Request::path() == "register"){?>
					@include('user.register')
				<?php }elseif(Request::path() == "login"){?>
					@include('user.login')
				<?php }elseif(Request::path() == "contact-us"){?>
					@include('user.contact-us')
				<?php }else{?>
					<!-- START MAIN CONTENT -->
					<div class="main_content">
					    <div  class="row mt-3">
					        <div class="col-lg-12 col-md-12 col-sm-12 form-container">
								{!! $cmsPage['description'] !!}
					        </div>
    					</div>
					</div>
					<!-- END MAIN CONTENT -->
				<?php } ?>
				
			</div>
		</div>
	</div>
</div>
@endsection