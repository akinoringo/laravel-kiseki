@extends('layouts/app')

@section('title', '一覧')

@section('content')
  @include('layouts.nav')
  
  <div class="container">
  	@include('efforts.search')
    @foreach($efforts as $effort) 
      @include('efforts.card')
    @endforeach 
  </div>
@endsection