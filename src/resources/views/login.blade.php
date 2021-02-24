@extends('layouts.themes.tabler.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')
    <section class="antialiased border-top-wide border-primary d-flex flex-colum">
        <div class="flex-fill d-flex flex-column justify-content-center py-4">
            <div class="container-tight py-6">
                <div class="text-center mb-4">
                    <a ><img src="{{asset('modullo-png.png')}}" height="36" alt=""></a>
                </div>
                <form class="card card-md" action="{{route('auth.login')}}" method="post" autocomplete="off">
                    @csrf
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Login to your account</h2>
                        <div class="mb-3">
                            <label class="form-label">Email address</label>
                            <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="Email">
                            @error('email') {{ $message }} @enderror
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Password</label>
                            <div class="input-group input-group-flat">
                                <input class="form-control" type="password" name="password" value="{{ old('password') }}"
                                        placeholder="Password">
                                @error('password') {{ $message }} @enderror

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
                    Don't have account yet? <a href="./sign-up.html" tabindex="-1">Sign up</a>
                </div>
            </div>
        </div>

    </section>

@endsection
@section('body_js')

@endsection