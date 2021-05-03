@extends('layouts.themes.tabler.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')
    <section class="antialiased border-top-wide border-primary row">
        <div class="col-lg-5 col-md-5 col-12">
            <div class="flex-fill d-flex flex-column justify-content-center py-4">
                <div class="container-tight py-6">
                    <form class="card card-md" action="{{route('auth.login')}}" method="post" autocomplete="off">
                        @csrf
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <a ><img src="{{$company_logo}}" height="100" alt=""></a>
                            </div>
                            @if ($errors->any())
                                <div  class="alert alert-danger" role="alert">
                                    @foreach ($errors->all() as $error)
                                        <li class="text-decoration-none">{{ $error }}</li>
                                    @endforeach
                                </div>
                            @endif
                            <h2 class="card-title text-center mb-4">Login to your account</h2>
                            <div class="mb-3">
                                <label class="form-label">Email address</label>
                                <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="Email">
                                <div class="text-danger">@error('email') {{ $message }} @enderror</div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Password</label>
                                <div class="mb-3">
                                    <input class="form-control" type="password" name="password" value="{{ old('password') }}"
                                           placeholder="Password">
                                    <div class="text-danger">@error('password') {{ $message }} @enderror</div>

                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-check">
                                    <input type="checkbox" class="form-check-input"/>
                                    <span class="form-check-label">Remember me on this device</span>
                                </label>
                            </div>
                            <div class="form-footer">
                                <button type="submit" class="btn btn-primary w-100">Sign in</button>
                            </div>
                        </div>

                    </form>
                    <div class="text-center text-muted mt-3">
                        Don't have account yet? <a href="{{route('register')}}" tabindex="-1">Sign up</a>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7 col-md-6 col-12" style="padding-top:5rem ">
            <div class="" style="background-image: url('{{ asset('dev.jpeg') }}');background-size: cover;background-position: center center; height: 60vh; "></div>
            {{--            <img src="{{ asset('dev.jpeg') }}" alt="" style="height: 45vh">--}}
        </div>
{{--        <div class="col-lg-6 col-md-6 col-12">--}}
{{--            <img src="https://images.unsplash.com/photo-1593642634367-d91a135587b5?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" class="d-block w-100" style="height: 65vh; object-fit: cover" alt="...">--}}
{{--        </div>--}}

    </section>

@endsection
@section('body_js')

@endsection