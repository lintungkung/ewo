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
            @if(!empty($data['MAINTAIN']['head_logo_img']))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($data['MAINTAIN']['head_logo_img'])) }}" width="150">
            @endif
        </td>
        <td class="col-6 text-center">
            <p class="m-0 font-s30">{{ $data['MAINTAIN']['head_title01'] }}</p>
            <p class="m-0 font-20"><u>  {{ $data['MAINTAIN']['head_title02'] }}  </u>維修服務單</p>
        </td>
        <td class="col-3">
            @if(!empty($data['MAINTAIN']['lineQrCode']))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($data['MAINTAIN']['lineQrCode'])) }}" width="100" height="100">
            @endif
        </td>
    </tr>
</table>


<table class="w-100">
    <tr>
        <td class="col-6">
            <p class="m-0 line-h08 font-s15">服務電話：{{ $data['MAINTAIN']['head_tel'] }}</p>
            <p class="m-0 line-h08 font-s15">地址：{{ $data['MAINTAIN']['head_addres'] }}</p>
            <p class="m-0 line-h08 font-s15">工單單號：{{ $data['MAINTAIN']['head_worksheet'] }}</p>

        </td>
        <td class="col-6">
            <p class="m-0 line-h08 font-s15 text-right">網址：{{ $data['MAINTAIN']['head_homeURL'] }}</p>
            <p class="m-0 line-h08 font-s15">IVR簡碼:{{ $data['MAINTAIN']['dstbIVR'] }}</p>
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
            <p class="m-0 line-h08 font-s10">客戶編號：{{ $data['MAINTAIN']['CustID'] }}</p>
            <p class="m-0 line-h08 font-s10">姓名：{{ $data['MAINTAIN']['CustName'] }}</p>
            <p class="m-0 line-h08 font-s10">電話(家)：{{ $data['MAINTAIN']['hometel'] }}</p>
            <p class="m-0 line-h08 font-s10">行動電話:{{ $data['MAINTAIN']['phonetel'] }}</p>
            <p class="m-0 line-h08 font-s10">裝機地址:{{ $data['MAINTAIN']['InstAddrName'] }}</p>
            <p class="m-0 line-h08 font-s10">收費地址:{{ $data['MAINTAIN']['InstAddrName'] }}</p>
            <p class="m-0 line-h08 font-s10">移機新址：</p>
            <p class="m-0 line-h08 font-s10">電子郵件信箱：</p>
            <p class="m-0 line-h08 font-s10">維修申告：{{ $data['MAINTAIN']['WorkCause'] }}</p>
        </td>
        <td class="align-top line-h08 font-s10">
            <p class="m-0 line-h08 font-s10">派工單序號：{{ $data['MAINTAIN']['WorkSheet'] }}</p>
            <p class="m-0 line-h08 font-s10">客戶編號：{{ $data['MAINTAIN']['CustID'] }}</p>
            <p class="m-0 line-h08 font-s10">服務區域：</p>
            <p class="m-0 line-h08 font-s10">Node NO：</p>
            <p class="m-0 line-h08 font-s10">客戶類別：</p>
            <p class="m-0 line-h08 font-s10">大樓(社區)名稱：</p>
        </td>
        <td class="align-top line-h08 font-s10">
            <p class="m-0 line-h08 font-s10">受理日期時間：{{ $data['MAINTAIN']['create_at'] }}</p>
            <p class="m-0 line-h08 font-s10">預約日期時間：{{ $data['MAINTAIN']['BookDate'] }}</p>
            <p class="m-0 line-h08 font-s10">受理人員：{{ $data['MAINTAIN']['CreateName'] }}</p>
            <p class="m-0 line-h08 font-s10">工程組別：{{ $data['MAINTAIN']['WorkTeam'] }}</p>
            <p class="m-0 line-h08 font-s10">工程人員：{{ $data['MAINTAIN']['WorkerName'] }}</p>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="h-100p align-top">
            維修來電備註：
            <p class="font-s8">{{ $data['MAINTAIN']['MSComment1'] }}</p>
        </td>
        <td class="align-top">
            維修工單條碼：
            <p></p>
        </td>
    </tr>
</table>


<table id="table101" class="text-center">
    <tr>
        <td>CM訂購速率</td>
        <td>CM設備台數</td>
        <td>DTV雙向設備台數</td>
        <td>DTV單向設備台數</td>
        <td>PVR設備台數</td>
    </tr>
    <tr>
        <td>{{ $data['MAINTAIN']['CMBAUDRATE'] }}</td>
        <td>{{ $data['MAINTAIN']['I_CNT'] }}</td>
        <td>{{ $data['MAINTAIN']['D_DUBLECNT'] }}</td>
        <td>{{ $data['MAINTAIN']['D_SINGLECNT'] }}</td>
        <td>{{ $data['MAINTAIN']['PVR_CNT'] }}</td>
    </tr>
</table>

<table id="table101">
    <tr>
        <td colspan="6">歷史維修紀錄：</td>
    </tr>
    <tr class="text-center">
        <td>維修日期</td>
        <td>結案人員</td>
        <td>工程組別</td>
        <td>維修申告</td>
        <td>故障原因一</td>
        <td>故障原因二</td>
    </tr>
    @foreach($data['MAINTAIN']['maintainHistory'] as $k => $t)
        <tr>
            <td>{{ $t->FINTIME }}</td>
            <td>{{ $t->SIGNNAME }}</td>
            <td>{{ $t->GROUPNAME }}</td>
            <td>{{ $t->SERVICENAME }}</td>
            <td>{{ $t->MFNAME1 }}</td>
            <td>{{ $t->MFNAME2 }}</td>
        </tr>
    @endforeach
</table>


<table id="table101">
    <tr>
        <td colspan="2">設備型號序號：</td>
    </tr>
    <tr>
        <td class="col-8 align-top">
            <table class="w-100 border-white">
                <tr class="text-center">
                    <td class="border-white">I型號</td>
                    <td class="border-white">I序號</td>
                    <td class="border-white">D型號</td>
                    <td class="border-white">D序號</td>
                </tr>
                <tr class="align-top">
                    <td class="border-white">
                        @foreach($data['MAINTAIN']['CMMODELNAME'] as $t)
                            <p class="m-0 mt-3 line-h08 font-s15">{{ $t }}</p>
                        @endforeach
                    </td>
                    <td class="border-white text-center">
                        @foreach($data['MAINTAIN']['CMFACISNO'] as $t)
                            <p class="line-h08 font-s15">{{ $t }}</p>
                        @endforeach
                    </td>
                    <td class="border-white">
                        @foreach($data['MAINTAIN']['DSTBMODELNAME'] as $t)
                            <p class="m-0 mt-3 line-h08 font-s15">{{ $t }}</p>
                        @endforeach
                    </td>
                    <td class="border-white text-center">
                        @foreach($data['MAINTAIN']['DSTBFACISNO'] as $t)
                            <p class="line-h08 font-s15">{{ $t }}</p>
                        @endforeach
                    </td>
                </tr>
            </table>
        </td>

        <td class="col-4 align-top">
            <table class="w-100" style="border-collapse: collapse">
                <tr class="align-top">
                    <td>
                        <p class="m-0 line-h08 font-s15">
                            客戶簽名:
                            @if(isset($data['MAINTAIN']['sign_mcust_select']))
                                @if($data['MAINTAIN']['sign_mcust_select'] != '本人簽名' || $data['MAINTAIN']['sign_mcust_select'] != '')
                                    ({{ $data['MAINTAIN']['sign_mcust_select'] }})
                                @endif
                            @endif
                        </p>
                        <br>
                        @if(!empty($data['MAINTAIN']['mcustSignUrl']))
                            <img class="pl-5" src="data:image/png;base64,{{ base64_encode(file_get_contents($data['MAINTAIN']['mcustSignUrl'])) }}" height="50px">
                        @endif
                    </td>
                </tr>
                <tr class="align-top">
                    <td>
                        <p class="m-0 line-h08 font-s15">工程簽名:</p>
                        <br>
                        @if(!empty($data['MAINTAIN']['mengineeSignUrl']))
                            <img class="pl-5" src="data:image/png;base64,{{ base64_encode(file_get_contents($data['MAINTAIN']['mengineeSignUrl'])) }}" height="50px">
                        @endif
                    </td>
                </tr>
                <tr class="">
                    <td>
                        完成工時:
                        <p class="m-0 line-h08 font-s15">1.車程時間(起)</p>
                        <p class="m-0 line-h08 font-s15">2.車程時間(迄)</p>
                        <p class="m-0 line-h08 font-s15">3.施工時間(起)</p>
                        <p class="m-0 line-h08 font-s15">4.施工時間(迄)</p>
                    </td>
                </tr>
                <tr class="">
                    <td>
                        維修代碼:
                        <p class="m-0 line-h08 font-s15">故障原因一</p>
                        <p class="m-0 line-h08 font-s15">故障原因二</p>
                        <p class="m-0 line-h08 font-s15">故障原因三</p>
                    </td>
                </tr>
                <tr class="">
                    <td>
                        退單原因：
                        <p class="m-0 line-h08 font-s15">Null</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<i class="page_break"></i>

