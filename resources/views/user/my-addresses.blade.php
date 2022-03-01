@extends('layouts.user')
@section('content')

<style type="text/css">
    .add_new_btn_div {
        text-align: right;
        padding-left: 0;
        padding-right: 0;
        margin-bottom: 20px;
    }
    .address-detail {
        padding: 20px;
    }
    .address-detail p {
        margin: 0;
    }
    .order_review_container {
        padding: 20px;
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
        <div class="container order">
            <div class="alert alert-success update_success" style="display: none;">Default Address updated successfully!</div>
            <form>
                <input type="hidden" name="_token_" id="csrf-token" value="{{ csrf_token() }}" />
                @if(Session::has('error'))
                    <div class="alert alert-danger address-fail">
                        {{Session::get('error')}}
                    </div>
                @endif
                @if(Session::has('success'))
                    <div class="alert alert-success address-success">
                        {{Session::get('success')}}
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-12 add_new_btn_div">
                        <a href="{{  url('/address/add') }}">
                            <button type="button" class="btn btn-fill-out btn-sm add_new_btn">Add New Address</button>
                        </a>     
                    </div>
                    @foreach($Address as $val) 
                    <div class="col-md-4 col-sm-6 order_review_container">
                        <div class="order_review">
                            <div class="select-address">
                                <input type="radio" value="{{$val['id']}}" class="default_Add" 
                                    <?php if($val['is_default']=="Y") echo "checked";?> 
                                    id="{{$val['id']}}" name="radio_button"
                                >
                                <label for="{{$val['id']}}">Set as default address</label> 
                            </div>
                            <div class="address-detail">
                                <a href="{{ url('/address/edit/'.$val['id']) }}"><h5> {{$val['address_title']}}</h5></a>
                                <p>{{$val['full_name']}}</p>
                                <p>{{$val['address_line_1']}}</p>
                                <p>{{$val['address_line_2']}}</p>
                                <p>{{$val['city']}}</p>
                                <p>{{$val['state']}}</p>
                                <p>{{$val['country']}}</p>
                                <p>{{$val['pin_code']}}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                    @if(count($Address)==0)
                    <div class="col-md-12 empty_address" > {{"Sorry ! No address found."}}</div>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>


<script type="text/javascript">
$(document).ready(function() {

    $(document).on("click", '.default_Add', function(event){
        var id = $(this).val();
        var token = $('input[name=_token_]').val();
        //alert(token);
         $.ajax({
              url: "/address/update_default",  // this is just update the db 
              headers: {'X-CSRF-TOKEN': token},
              type: "POST",
              data: {"id":id},
              dataType: "JSON",
              success: function(data) {
                $('.update_success').fadeIn(400);
                setTimeout(() => {
                    $('.update_success').fadeOut(400);
                }, 2000);
              }
        });
    });

});


</script>
@endsection