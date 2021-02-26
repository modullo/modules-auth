
@extends('layouts.themes.tabler.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')
<div class="pt-5">
   Welcome {{$user->first_name}}

   <a href="{{route('logout')}}" class="btn btn-sm w-25 mt-5 btn-block btn-danger">
      Logout
   </a>
</div>

@endsection
@section('body_js')

@endsection