@extends('layouts.credentials')

@section('content')
<div class="limiter">
    <div class="container-login100">
        <div class="wrap-login100">
            <form class="login100-form validate-form" method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}
                <span class="login100-form-title p-b-34">
                    <img src="{{ asset('files/rsmm-logo.png') }}"/>
                    <br/>
                    <br/>
                    Login
                </span>

                <div class="wrap-input100 rs1-wrap-input100 validate-input m-b-20 {{ $errors->has('username') ? ' has-error' : '' }}" data-validate="Type user name">
                    <input id="username" class="input100" type="text" name="username" placeholder="User name" value="{{ old('username') }}">
                    <span class="focus-input100"></span>
                    @if ($errors->has('username'))
                        <span class="help-block">
                            <strong>{{ $errors->first('username') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="wrap-input100 rs2-wrap-input100 validate-input m-b-20{{ $errors->has('password') ? ' has-error' : '' }}" data-validate="Type password">
                    <input class="input100" type="password" name="password" placeholder="Password">
                    <span class="focus-input100"></span>
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="container-login100-form-btn">
                    <button class="login100-form-btn" type="submit">
                        Login
                    </button>
                </div>
            </form>

            <div class="login100-more" style="background-image: url('{{ asset("files/background.jpg") }}');"></div>
        </div>
    </div>
</div>
<!--<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Login</div>

                <div class="panel-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label for="username" class="col-md-4 control-label">Username</label>

                            <div class="col-md-6">
                                <input id="" type="username" class="form-control" name="username" value="" required autofocus>

                                
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>-->
@endsection
