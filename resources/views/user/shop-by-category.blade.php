@extends('layouts.user')
@section('content')

<style type="text/css">
    h5 {
        margin-top: 20px;
    }
    .subSubCategories a {
        margin-left: 15px;
    }
</style>

<!-- START SECTION BREADCRUMB -->
<div class="breadcrumb_section bg_gray page-title-mini">
    <div class="container"><!-- STRART CONTAINER -->
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="page-title">
                    <h1>My Addresses</h1>
                </div>
            </div>
            <div class="col-md-6">
                <ol class="breadcrumb justify-content-md-end">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                    <li class="breadcrumb-item active">My Addresses</li>
                </ol>
            </div>
        </div>
    </div><!-- END CONTAINER-->
</div>
<!-- END SECTION BREADCRUMB -->

<!-- START MAIN CONTENT -->
<div class="main_content">
    <div class="section">
        <div class="container">
            <div class="row shop-by-cat">
                @foreach($categories as $mainKey=>$category)
                <div class="col-md-4 col-sm-6 category">
                    <h5>
                        <a href="/category/{{$category['slug']}}?page=1">
                            {{$category['name']}}
                        </a>
                        @if(count($category['sub_categories']) > 0)
                        <i class="plus-icon"></i>
                        @endif
                    </h5>
                    @foreach($category['sub_categories'] as $subKey=>$subCategories)
                       <div class="subCategories">
                            <h6>
                                <a href="/category/{{$subCategories['slug']}}?page=1">{{$subCategories['name']}}</a>
                            </h6>
                            @foreach($subCategories['subSubCategories'] as $subSubCategories)
                                <div class="subSubCategories">
                                    <a href="/category/{{$subSubCategories['slug']}}?page=1">{{$subSubCategories['name']}}</a>
                                </div>
                            @endforeach
                       </div> 
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.shop-by-cat i.plus-icon').on('click',function(){
            var that = $(this),
                parent = that.parents('div');
            parent.toggleClass('sub');
        });
    });
</script>
@endsection