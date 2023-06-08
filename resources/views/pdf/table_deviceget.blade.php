<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDFv2</title>
    <link rel="stylesheet" href="{{ asset('/cns/css/bootstrap.min.css') }}">
    @include('pdf.css')
</head>

<table class="w-100">
    <tr>
        <td class="col-3" >
            @if(!empty($data['DEVICEGET']['head_logo_img']))
                <img src="{{ $data['DEVICEGET']['head_logo_img'] }}" width="150">
            @endif
        </td>
        <td class="col-6 text-center">
            <p class="m-0 font-s30">{{ $data['DEVICEGET']['head_title01'] }}</p>
            <p class="m-0 font-20">設備取回單</p>
        </td>
        <td class="col-3">
            @if(!empty($data['DEVICEGET']['lineQrCode']))
                <img src="{{ $data['DEVICEGET']['lineQrCode'] }}" width="100" height="100">
            @endif
        </td>
    </tr>
</table>


<table class="w-100">
    <tr>
        <td class="col-6">
            <p class="m-0 line-h08 font-s15">服務電話：{{ $data['DEVICEGET']['head_tel'] }}</p>
            <p class="m-0 line-h08 font-s15">地址：{{ $data['DEVICEGET']['head_addres'] }}</p>
            <p class="m-0 line-h08 font-s15">工單單號：{{ $data['DEVICEGET']['head_worksheet'] }}</p>

        </td>
        <td class="col-6">
            <p class="m-0 line-h08 font-s15 text-right">網址：{{ $data['DEVICEGET']['head_homeURL'] }}</p>
        </td>
    </tr>
</table>


<table id="table101">
    <tr>
        <td class="col-4 line-h10 text-center">客戶基本資料</td>
        <td class="col-4 line-h10 text-center" colspan="2">工程登錄資料</td>
    </tr>
    <tr>
        <td class="align-top">
            <p class="m-0 line-h08 font-s10">客戶編號：{{ $data['DEVICEGET']['CustID'] }}</p>
            <p class="m-0 line-h08 font-s10">姓名：{{ $data['DEVICEGET']['CustName'] }}</p>
            <p class="m-0 line-h08 font-s10">電話(家)：{{ $data['DEVICEGET']['hometel'] }}</p>
            <p class="m-0 line-h08 font-s10">行動電話:{{ $data['DEVICEGET']['phonetel'] }}</p>
            <p class="m-0 line-h08 font-s10">裝機地址:{{ $data['DEVICEGET']['InstAddrName'] }}</p>
            <p class="m-0 line-h08 font-s10">收費地址:{{ $data['DEVICEGET']['InstAddrName'] }}</p>
            <p class="m-0 line-h08 font-s10">移機新址：</p>
            <p class="m-0 line-h08 font-s10">電子郵件信箱：</p>
        </td>
        <td class="align-top line-h08 font-s10">
            <p class="m-0 line-h08 font-s10">派工單序號：{{ $data['DEVICEGET']['WorkSheet'] }}</p>
            <p class="m-0 line-h08 font-s10">客戶編號：{{ $data['DEVICEGET']['CustID'] }}</p>
            <p class="m-0 line-h08 font-s10">服務區域：</p>
            <p class="m-0 line-h08 font-s10">Node NO：</p>
            <p class="m-0 line-h08 font-s10">客戶類別：</p>
            <p class="m-0 line-h08 font-s10">大樓(社區)名稱：</p>
        </td>
        <td class="align-top line-h08 font-s10">
            <p class="m-0 line-h08 font-s10">受理日期時間：{{ $data['DEVICEGET']['create_at'] }}</p>
            <p class="m-0 line-h08 font-s10">預約日期時間：{{ $data['DEVICEGET']['BookDate'] }}</p>
            <p class="m-0 line-h08 font-s10">受理人員：{{ $data['DEVICEGET']['CreateName'] }}</p>
            <p class="m-0 line-h08 font-s10">工程組別：{{ $data['DEVICEGET']['WorkTeam'] }}</p>
            <p class="m-0 line-h08 font-s10">工程人員：{{ $data['DEVICEGET']['WorkerName'] }}</p>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="h-100p align-top">
            來電備註：
            <p class="font-s12">{{ $data['DEVICEGET']['MSComment1'] }}</p>
        </td>
        <td class="align-top">
            工單條碼：
            <p></p>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="align-top">
            設備取回清單：
            <table class="w-100 border-white" style="height: 500px;">
                <tr class="text-center">
                    <td class="border-white" style="vertical-align:top;">訂編</td>
                    <td class="border-white" style="vertical-align:top;">服務別</td>
                    <td class="border-white" style="vertical-align:top;">設備</td>
                    <td class="border-white" style="vertical-align:top;">序號</td>
                </tr>
                @foreach($data['DEVICEGET']['deviceList'] as $k => $t)
                    <tr class="align-top">
                        <td class="border-white">
                            <p class="m-0 mt-3 line-h08 font-s15">{{ $t['subsId'] }}</p>
                        </td>
                        <td class="border-white">
                            <p class="m-0 mt-3 line-h08 font-s15">{{ $t['serviceName'] }}</p>
                        </td>
                        <td class="border-white">
                            <p class="m-0 mt-3 line-h08 font-s15">{{ $t['chargeName'] }}</p>
                        </td>
                        <td class="border-white">
                            <p class="m-0 mt-3 line-h08 font-s15">{{ $t['orgSingleSn'] }}</p>
                        </td>
                    </tr>
                @endforeach
            </table>
        </td>
        <td class="align-top">
            <p class="m-0 line-h08 font-s15">客戶簽名:</p>
            @if(!empty($data['DEVICEGET']['mcustSignUrl']))
                <img class="pl-5" src="{{ $data['DEVICEGET']['mcustSignUrl'] }}" height="80px">
            @endif
            <p class="m-0 line-h08 font-s15">工程簽名:</p>
            @if(!empty($data['DEVICEGET']['mengineeSignUrl']))
                <img class="pl-5" src="{{ $data['DEVICEGET']['mengineeSignUrl'] }}" height="80px">
            @endif
        </td>
    </tr>
</table>




