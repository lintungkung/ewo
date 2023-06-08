<!DOCTYPE html>
<html>
<head>
    @include('func.includes.head')
</head>
<body>

    @include('func.includes.header')

    @yield('content')

    @include('func.includes.footer')

    <a href="javascript:void(0);" class="totop"><i class="fa fa-angle-up"></i></a>

    <script src="{{ asset('js/jquery-3.5.1.js') }}"></script>
    <script src="{{ asset('js/jquery-ui.js') }}"></script>

    {{--  PDF  --}}
    <script src="{{asset('PDF_JS/pdfobject.min.js')}}"></script>
    <script src="{{asset('PDF_JS/build/pdf.js')}}"></script>

    {{--  簽名檔  --}}
    <script src="/js/signature/js/signature_pad.umd.min.js"></script>

    <link rel="stylesheet" href="{{ asset('cns/css/jquery-ui.css') }}">

    {{--  lazyload 圖片loading  --}}
    <script src="/js/lazyload.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

    @yield('script')

</body>

<script>
    function TOP() {
        $('html,body').animate({scrollTop: 0}, 'slow');
    }

</script>

</html>
