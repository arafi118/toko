@extends('layouts.auth')
@section('title', __('lang_v1.login'))

@section('content')
<style type="text/css">
    @import url("//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css");
    .login-block{
        background: #64748b;  /* fallback for old browsers */
    
    float:left;
    width:100%;
    height: 100%;
    padding : 50px 0;
    }
    .banner-sec{background:url(https://frconsultantindonesia.com/id/wp-content/uploads/2022/04/Kasir-1024x683.jpg)  no-repeat left bottom; background-size:cover; min-height:500px; border-radius: 0 10px 10px 0; padding:0;}
    .container{background:#fff; border-radius: 10px; 	box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);}
    .carousel-inner{border-radius:0 10px 10px 0;}
    .carousel-caption{text-align:left; left:5%;}
    .login-sec{padding: 30px 30px; position:relative;}
    .login-sec .copy-text{position:absolute; width:80%; bottom:20px; font-size:13px; text-align:center;}
    .login-sec .copy-text i{color:#FEB58A;}
    .login-sec .copy-text a{color:#E36262;}
    .login-sec h2{margin-bottom:30px; font-weight:800; font-size:30px; color: #DE6262;}
    /*.login-sec h2:after{content:" "; width:100px; height:5px; background:#FEB58A; display:block; margin-top:20px; border-radius:3px; margin-left:auto;margin-right:auto}*/
    .btn-login{background: #334155; color:#fff; font-weight:600; }
    .btn-login:hover, .btn-login:focus{background: #1e293b; color:#fff; font-weight:600; }
    .banner-text{width:70%; position:absolute; bottom:40px; padding-left:20px;}
    .banner-text h2{color:#fff; font-weight:600;}
    .banner-text h2:after{content:" "; width:100px; height:5px; background:#FFF; display:block; margin-top:20px; border-radius:3px;}
    .banner-text p{color:#fff;} 
    
    .login-page, .register-page {
        background: #64748b;
    }
</style>
<section class="login-block">
    <div class="container">
    <div class="row">
         <form class="form-horizontal" method="POST" action="{{ route('login') }}">
         {{ csrf_field() }}
            <div class="col-md-4 login-sec">
                <h2 class="text-center"><img style="width: 45%;" src="{{asset('images/ber-niaga.png')}}"></h2>
                <form class="login-form">
                  <div class="form-group">
                    <label for="exampleInputEmail1" class="">Nama User</label>
                    <input type="text" class="form-control" name="username" placeholder="Nama User">
                    @if ($errors->has('username'))
                        <span class="help-block">
                            <strong>{{ $errors->first('username') }}</strong>
                        </span>
                    @endif
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1" class="">Kata Sandi</label>
                    <input type="password" name="password" class="form-control" placeholder="Kata Sandi">
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                  </div>
                  <div class="form-group">
                      
                    <button type="submit" style="width: 100%;" class="btn btn-login">Masuk</button>
                  </div>
                
                  
                </form>
                <div class="copy-text">ber-niaga <i class="fa fa-heart"></i></div>
            </div>
        </form>
        <div class="col-md-8 banner-sec">
        <!-- <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
             <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
              </ol>
            <div class="carousel-inner" role="listbox">


            </div>     
        </div> -->
    </div>
</div>
</section>
<!-- <div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">@lang('lang_v1.login')</div>
                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                            <label for="username" class="col-md-4 control-label">@lang('lang_v1.username')</label>

                            <div class="col-md-6">
                                @php
                                    $username = old('username');
                                    $password = null;
                                    if(config('app.env') == 'demo'){
                                        $username = 'admin';
                                        $password = '123456';

                                        $demo_types = array(
                                            'all_in_one' => 'admin',
                                            'super_market' => 'admin',
                                            'pharmacy' => 'admin-pharmacy',
                                            'electronics' => 'admin-electronics',
                                            'services' => 'admin-services',
                                            'restaurant' => 'admin-restaurant',
                                            'superadmin' => 'superadmin',
                                            'woocommerce' => 'woocommerce_user'
                                        );
                                        if( !empty($_GET['demo_type']) && array_key_exists($_GET['demo_type'], $demo_types) ){
                                            $username = $demo_types[$_GET['demo_type']];
                                        }
                                    }
                                @endphp
                                <input id="username" type="text" class="form-control" name="username" value="{{ $username }}" required autofocus>

                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">@lang('lang_v1.password')</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password"
                                value="{{ $password }}" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> @lang('lang_v1.remember_me')
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    @lang('lang_v1.login')
                                </button>
                                @if(config('app.env') != 'demo')
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    @lang('lang_v1.forgot_your_password')
                                </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if(config('app.env') == 'demo')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Demo Shops <small><i> Demos are for example purpose only, this application <u>can be used in many other similar businesses.</u></i></small></h4></div>
                <div class="panel-body">
                    <div class="col-md-12 text-center">
                                <a href="?demo_type=all_in_one" class="btn btn-app bg-olive" data-toggle="tooltip" title="Showcases all feature available in the application." >
                                    <i class="fa fa-star"></i>
                                All In One</a>
                                <a href="?demo_type=pharmacy" class="btn bg-maroon btn-app" data-toggle="tooltip" title="Shops with products having expiry dates." >
                                <i class="fa fa-medkit"></i>
                                Pharmacy</a>
                                <a href="?demo_type=services" class="btn bg-orange btn-app" data-toggle="tooltip" title="For all service providers like Web Development, Restaurants, Repairing, Plumber, Salons, Beauty Parlors etc.">
                                <i class="fa fa-wrench"></i>
                                Multi-Service Center</a>
                                <a href="?demo_type=electronics" class="btn bg-purple btn-app" data-toggle="tooltip" title="Products having IMEI or Serial number code." >
                                <i class="fa fa-laptop"></i>
                                Electronics & Mobile Shop</a>
                                <a href="?demo_type=super_market" class="btn bg-navy btn-app" data-toggle="tooltip" title="Super market & Similar kind of shops." >
                                <i class="fa fa-shopping-cart"></i>
                                Super Market</a>
                                <a href="?demo_type=restaurant" class="btn bg-red btn-app" data-toggle="tooltip" title="Restaurants, Salons and other similar kind of shops." >
                                <i class="fa fa-cutlery"></i>
                                Restaurant</a>
                    </div>

                    <div class="col-md-12">
                        <hr>
                    </div>
                    <div class="col-md-12">
                        <div class="alert alert-success alert-dismissible">
                            <i class="icon fa fa-plug"></i> Premium optional modules:
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <a href="?demo_type=superadmin" class="btn bg-red-active btn-app" data-toggle="tooltip" title="SaaS & Superadmin extension Demo">
                            <i class="fa fa-university"></i>
                            SaaS / Superadmin</a>

                        <a href="?demo_type=woocommerce" class="btn bg-woocommerce btn-app" data-toggle="tooltip" title="WooCommerce demo user - Open web shop in minutes!!" style="color:white !important">
                            <i class="fa fa-wordpress"></i>
                            WooCommerce</a>
                    </div>
                </div>
            </div>
         </div>
    </div>           
    @endif
</div> -->
@stop
@section('javascript')
<script type="text/javascript">
    $(document).ready(function(){
        $('#change_lang').change( function(){
            window.location = "{{ route('login') }}?lang=" + $(this).val();
        });
    })
</script>
@endsection
