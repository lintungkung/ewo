@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('message')
  頁面錯誤，返回<a href="/ewo/login">登入</a>
@endsection
