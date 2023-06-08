<html>

    <head>
        <meta charset="UTF-8">
        <title>倉管連動</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <meta name="format-detection" content="telephone=no">

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

        <!-- BootStrap -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

        <!-- jquery -->
        <script src="{{asset('/js/jquery-3.5.1.min.js')}}"></script>
        <style>
            main {
                font-size: 15px;
            }

            .content {
                cursor: pointer;
                margin-right: 10px;
                color: blue;
            }

            /*.toTop-arrow {*/
            /*    width: 2.5rem;*/
            /*    height: 2.5rem;*/
            /*    padding: 0;*/
            /*    margin: 0;*/
            /*    border: 0;*/
            /*    border-radius: 33%;*/
            /*    opacity: 0.6;*/
            /*    background: #000;*/
            /*    cursor: pointer;*/
            /*    position:fixed;*/
            /*    right: 1rem;*/
            /*    bottom: 1rem;*/
            /*    display: none;*/
            /*}*/
            /*.toTop-arrow::before, .toTop-arrow::after {*/
            /*    width: 18px;*/
            /*    height: 5px;*/
            /*    border-radius: 3px;*/
            /*    background: #f90;*/
            /*    position: absolute;*/
            /*    content: "";*/
            /*}*/
            /*.toTop-arrow::before {*/
            /*    transform: rotate(-45deg) translate(0, -50%);*/
            /*    left: 0.5rem;*/
            /*}*/
            /*.toTop-arrow::after {*/
            /*    transform: rotate(45deg) translate(0, -50%);*/
            /*    right: 0.5rem;*/
            /*}*/
            /*.toTop-arrow:focus {outline: none;}*/


            /*!* 48px *!*/
            /*.toTop-arrow {*/
            /*    width: 3rem;*/
            /*    height: 3rem;*/
            /*    padding: 0;*/
            /*    margin: 0;*/
            /*    border: 0;*/
            /*    border-radius: 33%;*/
            /*    opacity: 0.6;*/
            /*    background: #000;*/
            /*    cursor: pointer;*/
            /*    position:fixed;*/
            /*    right: 1rem;*/
            /*    bottom: 1rem;*/
            /*    display: none;*/
            /*}*/
            /*.toTop-arrow::before, .toTop-arrow::after {*/
            /*    width: 25px;*/
            /*    height: 6px;*/
            /*    border-radius: 3px;*/
            /*    background: #f90;*/
            /*    position: absolute;*/
            /*    content: "";*/
            /*}*/
            /*.toTop-arrow::before {*/
            /*    transform: rotate(-45deg) translate(0, -50%);*/
            /*    left: 0.42rem;*/
            /*}*/
            /*.toTop-arrow::after {*/
            /*    transform: rotate(45deg) translate(0, -50%);*/
            /*    right: 0.42rem;*/
            /*}*/
            /*.toTop-arrow:focus {outline: none;}*/

            .reload-arrow {
                z-index: 999!important;
                width: 3.5rem;
                height: 3.5rem;
                padding: 0;
                margin: 0;
                border: 0;
                border-radius: 33%;
                opacity: 0.6;
                /*background: #000;*/
                cursor: pointer;
                position:fixed;
                right: 1rem;
                /*display: none;*/
            }

            /*!* 56px *!*/
            .toTop-arrow {
                z-index: 999!important;
                width: 3.5rem;
                height: 3.5rem;
                padding: 0;
                margin: 0;
                border: 0;
                border-radius: 33%;
                opacity: 0.6;
                background: #000;
                cursor: pointer;
                position:fixed;
                right: 1rem;
                bottom: 1rem;
                display: none;
            }
            .toTop-arrow::before, .toTop-arrow::after {
                width: 31px;
                height: 7px;
                border-radius: 3px;
                background: #f90;
                position: absolute;
                content: "";
            }
            .toTop-arrow::before {
                transform: rotate(-45deg) translate(0, -50%);
                left: 0.4rem;
            }
            .toTop-arrow::after {
                transform: rotate(45deg) translate(0, -50%);
                right: 0.4rem;
            }
            .toTop-arrow:focus {outline: none;}

            .bor-bot-1 {
                border-bottom: 1px solid;
            }
        </style>
    </head>

    <main class="pt-3">


        <button type="button" id="reload" class="reload-arrow" onclick="location.reload()">
            <svg width="32" height="32" fill="currentColor" class="bi bi-arrow-repeat" viewBox="0 0 16 16">
                <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/>
                <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"/>
            </svg>
        </button>
        <button type="button" id="BackTop" class="toTop-arrow"></button>

        <div class="container pt-2 bg-grey">

            <div class="bor-bot-1 mb-3">
                <button class="btn btn-outline-dark" onclick="location.href='/consumables/menu'">
                    <svg width="32" height="32" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                    </svg>
                </button>
                <h4 class="d-inline">工程:{{ $p_data['userCode'].' '.$p_data['userName'] }}</h4>
            </div>

            @foreach($companynoList as $k => $t)

                @foreach($list2[$t] as $k3 => $t3)
                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0" id="list_{{$t}}_header">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#list_{{$t}}_body">
                                {{ $t.' | '.$p_data['companynoary'][$t] }} ({{$p_data[$k3]}})設備清單
                            </button>
                        </h5>
                    </div>

                    <div id="list_{{$t}}_body" class="collapse show" data-parent="#list_{{$t}}_header">
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th scope="col" class="pt-0">
                                        <div class="alert alert-light ml-1" role="alert">型號</div>
                                    </th>
                                    <th scope="col" class="pt-0 m-0">
{{--                                        <div class="input-group">--}}
                                            <label class="alert alert-info pt-1 pb-1">序號</label>
                                            @if($k3 == 'recycle')
                                                <label class="alert alert-warning ml-1 pt-1 pb-1">回收時間</label>
                                            @else
                                                <label class="alert alert-info ml-1 pt-1 pb-1">調撥時間</label>
                                            @endif

                                        {{--                                        </div>--}}
                                    </th>
                                    <th>
                                        <div class="alert alert-light ml-1" role="alert">MTSpec</div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($list2[$t][$k3] as $k2 => $t2)
                                        @if($t2->CompanyNo === $t)
                                    <tr>
                                        <td class="pt-0">
                                            <input type="checkbox">
                                            {{$t2->CSModel}}
                                        </td>
                                        <td class="pt-0">
{{--                                            <div class="input-group">--}}

                                                <label class="btn btn-info pl-0 pr-0 @if(!empty($t2->BackTime)) disabled @endif">
                                                    <input type="button" class="btn btn-info d-none" name="qrcode_singlesn"
                                                           @if(!empty($t2->BackTime)) disabled @endif
                                                           data-tes="{{$k2}}"
                                                           data-toggle="modal" data-target="#exampleModalCenter" value="{{$t2->SingleSN}}"
                                                           data-companyno="{{$t}}" data-mtno="{{$t2->MTNo}}" data-csmodel="{{$t2->CSModel}}" >
                                                    {{$t2->SingleSN}}
                                                </label>

                                                @if($k3 == 'recycle')
{{--                                                    <div class="input-group-append">--}}
{{--                                                        <div class="alert alert-warning ml-1 pl-0 pr-0" role="alert">--}}
{{--                                                            {{substr($t2->BackTime,0,19)}}--}}
{{--                                                        </div>--}}
                                                        <label class="alert alert-warning ml-1 pl-0 pr-0">{{substr($t2->BackTime,0,19)}}</label>
{{--                                                    </div>--}}
                                                @else
{{--                                                    <div class="input-group-append">--}}
{{--                                                        <div class="alert alert-info ml-1 pl-0 pr-0" role="alert">--}}
{{--                                                            {{substr($t2->CreateTime,0,19)}}--}}
{{--                                                        </div>--}}
                                                        <label class="alert alert-info ml-1 pl-0 pr-0">{{substr($t2->CreateTime,0,19)}}</label>
{{--                                                    </div>--}}
                                                @endif
{{--                                            </div>--}}
                                        </td>
{{--                                        <td>{{substr($t2->CreateTime,0,19)}}</td>--}}
                                        <td>{{$t2->MTSpec}}</td>
                                    </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
                @endforeach

            @endforeach

            {{--        設備序號 qr code        --}}

            <div class="modal fade bd-example-modal-sm" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm">

                    <div class="modal-content">
                        <form class="p-3" id="form_modal">
                            <div class="form-group">
                                <label for="exampleFormControlInput1">公司別</label>
                                <input type="text" class="form-control bg-white" name="companyno" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlInput1">型號</label>
                                <input type="text" class="form-control bg-white" name="CSModel" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlInput1">料號</label>
                                <input type="text" class="form-control bg-white" name="mtno" readonly>
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlInput1">序號</label>
                                <input type="text" class="form-control bg-white" name="single" readonly>
                            </div>

                            <div class="form-group">
                                <label for="exampleFormControlInput1">QR Code</label>
                                <div id="qrcode"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        </div>

    </main>


    <script type="text/javascript" src="{{ asset('js/jquery.qrcode.min.js') }}"></script>

    <script>
        $(document).ready(function () {

            $('input[name="qrcode_singlesn"]').click(function(){
                var companyno = $(this).data('companyno');
                var single = $(this).val();
                var CSModel = $(this).data('csmodel');
                var mtno = $(this).data('mtno');
                var qrcode = companyno+','+single+','+mtno+','+CSModel;
console.log(qrcode)
                $('#form_modal input[name="companyno"]').val(companyno);
                $('#form_modal input[name="single"]').val(single);
                $('#form_modal input[name="CSModel"]').val(CSModel);
                $('#form_modal input[name="mtno"]').val(mtno);
                $('#qrcode canvas').remove();
                $('#qrcode').qrcode({width: 128,height: 128,text: qrcode});
            });



        });

        $('#BackTop').click(function(){
            $('html,body').animate({scrollTop:0}, 333);
        });

        $(window).scroll(function() {
            if ( $(this).scrollTop() > 300 ){
                $('#BackTop').fadeIn(222);
            } else {
                $('#BackTop').stop().fadeOut(222);
            }
        }).scroll();


        /*
        function laborsafetyDialog() {
            var p_window_height = $(window).height();
            var p_window_width = $(window).width();
            $('#laborsafetyDialog').removeClass('d-none');
            $('#laborsafetyDialog').dialog({
                autoOpen: false,
                width: p_window_width,
                height: p_window_height,
                open: function() {
                    // $('.ui-dialog-titlebar').css('display','none'); //toll display
                    $('.ui-dialog-titlebar-close').hide(); // close ican
                    $('.ui-dialog-content').css('height','auto'); //sign height
                    $('.ui-dialog-content').css('padding','0'); //sign paddign
                    $('.ui-dialog.ui-corner-all').css('height','100%'); //sign background height 100%
                },
            });
            $('#laborsafetyDialog').dialog("open");

            document.body.style.overflow = 'hidden';
        }
        */


    </script>

</html>
