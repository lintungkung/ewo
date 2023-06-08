@extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message')
    頁面錯誤，返回<a href="/ewo/login">登入</a>
    <p id="p_url"></p>
<script>
    window.load=getUrl();
    function getUrl() {
        var url = window.location.href;
        window.document.getElementById('p_url').innerHTML = url;
    }
</script>
@endsection
