<!DOCTYPE html>
<html>
<head>
    @include('ewo.includes.head')
</head>
<body>

    @include('ewo.includes.header')

    @yield('content')

    @include('ewo.includes.footer')

    <a href="javascript:void(0);" class="totop"><i class="fa fa-angle-up"></i></a>

{{--    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>--}}
{{--    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>--}}
    <script src="{{ asset('js/jquery-3.5.1.js') }}"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>

    {{--  PDF  --}}
    <script src="{{asset('PDF_JS/pdfobject.min.js')}}"></script>
    <script src="{{asset('PDF_JS/build/pdf.js')}}"></script>
    {{--  簽名檔  --}}
    <script src="/js/signature/js/signature_pad.umd.min.js"></script>

{{--    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">--}}
    <link rel="stylesheet" href="{{ asset('cns/css/jquery-ui.css') }}">
    {{--  lazyload 圖片loading  --}}
    <script src="/js/lazyload.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

    @if($p_data['header'] === 'info')
        @include('ewo.includes.script')

    @else
{{--        @include('ewo.appInfo')--}}

{{--        @include('ewo.appmsg')--}}

{{--        @include('ewo.addsign')--}}

{{--        @include('ewo.appstatistics')--}}

{{--        @include('ewo.laborsafety')--}}

{{--        @include('ewo.planDevice')--}}

    @endif

    @yield('script')

</body>
</html>
