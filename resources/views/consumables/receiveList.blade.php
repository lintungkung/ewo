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

        <div class="container pt-2 bg-grey">
            <h3 class="m-0">回收設備 清單</h3>
            <hr>
            <p class="m-0 text-right">查詢時間 {{$p_data['runTime']}}</p>
            <p class="m-0 text-right">*{{date('Y-m-d H:i:s',strtotime('+10 minute',strtotime($p_data['runTime'])))}}後可重新查詢</p>
            @if($p_data['notInstore'])
                <label class="alert alert-danger w-100">
                    還有 {{$p_data['notInstore']}}台設備 還沒交回倉庫
                </label>
            @endif

            @foreach($p_data['redList'] as $vCompanyNo => $t)
                <div class="input-group-prepend p-0 col-12">
                    <label class="alert alert-info w-100">
                        系統台 {{$vCompanyNo}}|{{$p_data['companyNoAry'][$vCompanyNo]}}
                    </label>
                </div>
                @foreach($t as $vBookDate => $t2)
                    <div class="card mb-3">
                        <div class="card-header p-0" id="C{{$vCompanyNo.$vBookDate}}Head">
                            <h5 class="mb-0">
                                <button class="btn btn-primary collapsed btn_collapsed" data-toggle="collapse" data-target="#C{{$vCompanyNo.$vBookDate}}Body">
                                    預約日期 {{$vBookDate}}
                                    <svg  width="16" height="16" fill="currentColor" class="bi bi-arrows-collapse" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zm7-8a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 4.293V.5A.5.5 0 0 1 8 0zm-.5 11.707-1.146 1.147a.5.5 0 0 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 11.707V15.5a.5.5 0 0 1-1 0v-3.793z"/>
                                    </svg>
                                </button>
                            </h5>
                        </div>
                        <div class="collapse show" id="C{{$vCompanyNo.$vBookDate}}Body" data-parent="#C{{$vCompanyNo.$vBookDate}}Head">
                            <div class="card-body p-0">
                                <div class="input-group ">
                                    @foreach($t2 as $vAssignSheet => $t3)
                                        <div class="input-group-prepend p-0 col-12">
                                            <span class="input-group-text w-100 pl-1">
                                                單號 {{$vAssignSheet}}
                                            </span>
                                        </div>
                                        @foreach($t3 as $k4 => $t4)
                                            @foreach($t4 as $k5 => $t5)
                                                <div class="input-group-prepend p-0 col-12">
                                                    <span class="input-group-text">
                                                        {{$p_data['colAry'][$k5]}}
                                                    </span>
                                                    <span class="input-group-text bg-white w-100">
                                                        {{$t5}}
                                                        @if($k5 == 'instore')
                                                            @if(in_array($t5,['Y','N']))
                                                                @if($t5 == 'N')
                                                                    <b class="text-danger">&nbsp;(請至倉庫繳回)</b>
                                                                @else
                                                                    <b class="">&nbsp;(已繳回倉庫)</b>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </span>
                                                </div>
                                            @endforeach
                                        @endforeach
                                        <div class="mb-1">&nbsp;</div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach

        </div>

    </main>

    <script type="text/javascript" src="{{ asset('js/jquery.qrcode.min.js') }}"></script>

    <script type="text/javascript">

        $(document).ready(function () {

            $('.btn_collapsed').click(function(){
                chkLaborSafetyBtn($(this))
            });

        });

        // 按鈕，切換圖案
        function chkLaborSafetyBtn(obj) {
            let btn01 = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrows-expand" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zM7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10z"/>
</svg>`;
            let btn02 = `<svg  width="16" height="16" fill="currentColor" class="bi bi-arrows-collapse" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zm7-8a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 4.293V.5A.5.5 0 0 1 8 0zm-.5 11.707-1.146 1.147a.5.5 0 0 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 11.707V15.5a.5.5 0 0 1-1 0v-3.793z"/>
</svg>`;
            let pid = obj.data('target');
            let classStr = $(''+pid).attr('class');
            let classAry = classStr.split(' ');
            let chkClass = classAry.includes('show');
            obj.find('svg').remove();
            let btnStr = obj.html();
            if(chkClass) {
                htmlStr = btnStr+btn02;
            } else {
                htmlStr = btnStr+btn01;
            }
            obj.html(htmlStr);
        }

    </script>

</html>
