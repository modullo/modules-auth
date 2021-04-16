@extends('layouts.themes.tabler.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')
    <section class="antialiased border-top-wide border-primary d-flex flex-colum">
        <div class="flex-fill d-flex flex-column justify-content-center py-4">
            <div class="container-tight py-6">
                <div class="text-center mb-4">
                    <a ><img src="{{$company_logo}}" height="36" alt=""></a>
                </div>
                @if ($errors->any())
                    <div  class="alert alert-danger" role="alert">
                        @foreach ($errors->all() as $error)
                            <li class="text-decoration-none">{{ $error }}</li>
                        @endforeach
                    </div>
                @endif


                <form class="card card-md" action="{{route('auth.register')}}" method="post" autocomplete="off">
                    @csrf
                    <div class="card-body">

                        <h2 class="card-title text-center mb-4">Register  your account</h2>
                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input class="form-control" type="text" name="first_name" value="{{ old('first_name') }}"
                                   placeholder="First Name">
                            <div class="text-danger">@error('first_name') {{ $message }} @enderror</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input class="form-control" type="text" name="last_name" value="{{ old('last_name') }}"
                                   placeholder="Last Name">
                            <div class="text-danger">@error('last_name') {{ $message }} @enderror</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email address
                            <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="Email">
                            </label>
                            <div class="text-danger">@error('email') {{ $message }} @enderror</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number
                                <input class="form-control" type="phone" name="phone_number" value="{{ old
                                ('phone_number') }}"
                                       placeholder="Phone Number">
                            </label>
                            <div class="text-danger">@error('phone_number') {{ $message }} @enderror</div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Password</label>
                            <div class="mb-3">
                                <input class="form-control" type="password" name="password" value="{{ old('password') }}"
                                       placeholder="Password">
                                <div class="text-danger">@error('password') {{ $message }} @enderror</div>

                            </div>
                        </div>

                        @if ($custom_form_fields)
                            @foreach($form_fields as $field)
                                <div class="mb-2">
                                    <label class="form-label">{{$field['field_name']}}</label>
                                    <div class="mb-3">
                                        @switch($field['field_type'])
                                            @case('string')
                                                <input class="form-control"
                                                       type="text"
                                                       name="{{$field['field_name']}}"
                                                       value="{{ old($field['field_name']) }}"
                                                       placeholder="{{$field['field_name']}}"

                                                >
                                                <div class="text-danger">@error($field['field_name']) {{ $message }}
                                                    @enderror
                                                </div>
                                            @break
                                            @case('enum')
                                            <select class="form-control"
                                                    name="{{$field['field_name']}}"
                                                    required
                                            >
                                                <option value="">Select your {{$field['field_name']}}</option>
                                                @foreach($field['enum_data'] as $data)
                                                <option>     {{$data}}</option>
                                                @endforeach
                                            </select>
                                            <div class="text-danger">@error($field['field_name']) {{ $message }}
                                                @enderror
                                            </div>
                                            @break
                                            @default
                                            @break

                                        @endswitch


                                    </div>
                                </div>
                            @endforeach
                        @endif
                        <div class="mb-2">
                            <label class="form-check">
                                <input type="checkbox" class="form-check-input"/>
                                <span class="form-check-label">Remember me on this device</span>
                            </label>
                        </div>
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </div>
                    </div>

                </form>
                <div class="text-center text-muted mt-3">
                    Already have an account? <a href="{{route('login')}}" tabindex="-1">Login</a>

                </div>
            </div>
        </div>

    </section>

@endsection
@section('body_js')

@endsection