<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="<?php echo e(asset('/cns/css/bootstrap.min.css')); ?>">

    </head>

    <style>
        .bg-00968f {
            background-color: #00968f;
        }
        /*
        .line{ // 斜線線條
            display: flex;
        }
        .lineLeft{
            width: 100%;
            height: 20px;
            border-top: 16px solid #00968f;
        }
        .lineRigth{
            width: 300px;
            height: 20px;
            border-top: 16px solid #262626;
            border-left: 16px solid transparent;
            margin-left: -16px;
        }
        */

        .line02 {
            background-image: url({{ asset('/img/pdf_v3_title_line.png') }});
            height: 10px;
            margin-bottom: 5px;
            background-position: right;
        }

        table {
            width: 100%;
        }
        .tdR {
            width: 49%;
            height: 50px;
            font-size: 40px;
            line-height: 40px;
            text-align: right;
        }
        .tdL {
            width: 50%;
            height: 50px;
            vertical-align: bottom;
        }
        .tdL img{
            max-height: 40px;
            margin-bottom: 5px;
        }
        /*.div_img {*/
        /*    width: 50%;*/
        /*    padding-top: 10px;*/
        /*    padding-right: 10px;*/
        /*}*/
        /*.div_img img {*/
        /*    float: right;*/
        /*}*/

        .tdLink {
            text-align: right;
            padding-right: 10px
        }


    </style>

    <body>
        <table>
            <tr>
                <td class="tdL">
                    <img src="{{ asset("/img/logo$so.png") }}">
                </td>
                <td class="tdR" valign="bottom">
{{--                @if($so == 'fet2')--}}
{{--                    <span style="font-size: 30px;">遠傳大雙網方案</span> <span style="font-size: 20px;">派工/竣工單/收據</span>--}}
{{--                @else--}}
{{--                @endif--}}
                @switch($so)
                    @case('v3fet')
                        <span style="font-size: 30px;">遠傳大雙網方案</span> <span style="font-size: 20px;">派工/竣工單/收據</span>
                    @break
                    @case('fet')
                    @default
                        服務申請書
                    @break
                @endswitch
                </td>
            </tr>
        </table>

        <div class="line02"></div>

{{--        <div class="w-100 d-flex bg-00968f">--}}
{{--            <div class="text-white w-50 p-2">--}}
{{--                <p class="mb-2">服務電話：412-8811(手機請加區碼)</p>--}}
{{--                <p class="mb-2">地址：{{ config("company.addres.$so") }}</p>--}}
{{--                <p class="mb-2">網址：{{ config("company.html.$so") }}</p>--}}
{{--            </div>--}}
{{--            <img src="{{ asset('img/LineQrCode/qrCodeS.png') }}">--}}
{{--        </div>--}}

        <table>
            <tr class="bg-00968f">
                <td class="text-white p-2">
{{--                    @if($so == 'fet')--}}
{{--                        <p class="mb-2">服務電話：遠傳免付費手機直撥123。市話付費專線449-5000</p>--}}
{{--                        <p class="mb-2">&nbsp;</p>--}}
{{--                        <p class="mb-2">&nbsp;</p>--}}
{{--                    @else--}}
{{--                    <p class="mb-2">服務電話：412-8811(手機請加區碼)</p>--}}
{{--                    <p class="mb-2">地址：{{ config("company.addres.$so") }}</p>--}}
{{--                    <p class="mb-2">網址：{{ config("company.html.$so") }}</p>--}}
{{--                    @endif--}}
                    @switch($so)
                        @case('fet')
                        @case('v3fet')
                            <p class="mb-2">服務電話：遠傳免付費手機直撥123。市話付費專線449-5000</p>
                            <p class="mb-2">&nbsp;</p>
                            <p class="mb-2">&nbsp;</p>
                        @break
                        @default
                            <p class="mb-2">服務電話：412-8811(手機請加區碼)</p>
                            <p class="mb-2">地址：{{ config("company.addres.$so") }}</p>
                            <p class="mb-2">網址：{{ config("company.html.$so") }}</p>
                        @break
                    @endswitch
                </td>
                <td class="tdLink">
                    <img src="{{ asset('img/LineQrCode/qrCodeS.png') }}">
                </td>
            </tr>
        </table>
    </body>
</html>
