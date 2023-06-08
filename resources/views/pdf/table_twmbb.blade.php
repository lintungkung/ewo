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
            @if(!empty($data['TWMBB']['head_logo_img']))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($data['TWMBB']['head_logo_img'])) }}" width="150">
            @endif
        </td>
        <td class="col-6 text-center">
            <p class="m-0 font-s30">{{ $data['TWMBB']['head_title01'] }}</p>
            <p class="m-0 font-s25">{{ $data['TWMBB']['head_title02'] }}</p>
        </td>
        <td class="col-3 text-center">
            <p class="m-0 line-h10 font-s15">台灣之星委託件</p>
            <p class="m-0 line-h10 font-s15">中嘉寬頻股份有限公司</p>
            <p class="m-0 line-h10 font-s15">訂編：{{ $data['TWMBB']['head_worksheet'] }}</p>
        </td>
    </tr>
</table>


<table class="w-100">
    <tr>
        <td class="col-6">
            <p class="m-0 line-h08 font-s15">服務電話：{{ $data['TWMBB']['head_tel'] }}</p>
            <p class="m-0 line-h08 font-s15">地址：{{ $data['TWMBB']['head_addres'] }}</p>

        </td>
        <td class="col-6">
            <p class="m-0 line-h08 font-s15 text-right">網址：{{ $data['TWMBB']['head_homeURL'] }}</p>
            <p class="m-0 line-h08 font-s15 text-right"> </p>
            <p class="m-0 line-h08 font-s15">NO:{{ $data['TWMBB']['head_no'] }}</p>
        </td>
    </tr>
</table>


<table id="table101">
    <tr>
        <td class="col-4 line-h10 text-center">客戶基本資料</td>
        <td class="col-2 line-h10 text-center">派工類別</td>
        <td class="col-3 line-h10 text-center">IVR簡碼</td>
        <td class="col-3 line-h10 text-center">派工單序號</td>
    </tr>
    <tr>
        <td rowspan="2">
            <p class="m-0 line-h08 font-s10">客戶編號：{{ $data['TWMBB']['CustID'] }}</p>
            <p class="m-0 line-h08 font-s10">姓名：{{ $data['TWMBB']['CustName'] }}</p>
            <p class="m-0 line-h08 font-s10">電話(家)：{{ $data['TWMBB']['hometel'] }}</p>
            <p class="m-0 line-h08 font-s10">行動電話:{{ $data['TWMBB']['phonetel'] }}</p>
            <p class="m-0 line-h08 font-s10">裝機地址:{{ $data['TWMBB']['InstAddrName'] }}</p>
            <p class="m-0 line-h08 font-s10">收費地址:{{ $data['TWMBB']['InstAddrName'] }}</p>
            <p class="m-0 line-h08 font-s10">大樓(社區)名稱：</p>
            <p class="m-0 line-h08 font-s10">移機舊址：</p>
        </td>
        <td class="h-30p line-h10 text-center">{{ explode(' ',$data['TWMBB']['WorkKind'])[1] }}</td>
        <td class="h-30p line-h10 text-center wordBreakAll">
            @for($i=0; $i < count($data['TWMBB']['dstbIVR']);$i++)
                @if($i%3 === 0)
                    <p class="m-0 line-h08 font-s15">
                @endif
                    {{ $data['TWMBB']['dstbIVR'][$i] }}
                @if($i%3 === 2)
                    </p>
                @endif
            @endfor
        </td>
        <td class="h-30p line-h10 text-center">{{ $data['TWMBB']['WorkSheet'] }}</td>
    </tr>
    <tr>
        <td colspan="2">
            <p class="m-0 line-h08 font-s10">工程組別:{{ $data['TWMBB']['WorkTeam'] }}</p>
            <p class="m-0 line-h08 font-s10">網路編號:{{ $data['TWMBB']['NetID'] }}</p>
            <p class="m-0 line-h08 font-s10">下次收費日:</p>
            <p class="m-0 line-h08 font-s10">方案別(合約起迄日):{{ $data['TWMBB']['SaleCampaign'] }}</p>

        </td>
        <td>
            <p class="m-0 line-h08 font-s10">受理人:{{ $data['TWMBB']['CreateName'] }}</p>
            <p class="m-0 line-h08 font-s10">受理日期時間:{{ $data['TWMBB']['CreateTime'] }}</p>
            <p class="m-0 line-h08 font-s10">預約日期時間:{{ $data['TWMBB']['BookDate'] }}</p>
        </td>
    </tr>
</table>


<table id="table101">
    <tr>
        <td class="text-center font-s15" style="width:12%">設備型號</td>
        <td class="text-center font-s15" style="width:12%">設備序號</td>
        <td class="text-center font-s15" style="width:29%">收費項目</td>
        <td class="text-center font-s15" style="width:20%">收費期間</td>
        <td class="text-center font-s15" style="width:8%">金額</td>
        <td class="text-center font-s15" colspan="2">總應收金額</td>
    </tr>
    <tr>
        <td class="" rowspan="8"></td>
        <td class="" rowspan="8"></td>
        <td class="align-top" rowspan="8">
            @foreach($data['TWMBB']['chargeNameAry'] as $t1)
                <p class="m-0 mb-1 font-s10 line-h08">{{ $t1 }}</p>
            @endforeach
        </td>
        <td class="align-top text-center" rowspan="8">
            @foreach($data['TWMBB']['chargeDateAry'] as $t2)
                <p class="m-0 mb-1 font-s10 line-h08">{{ $t2 }}</p>
            @endforeach
        </td>
        <td class="align-top text-right" rowspan="8">
            @foreach($data['TWMBB']['billAmtAry'] as $t3)
                <p class="m-0 mb-1 font-s10 line-h08 mr-2">{{ $t3 }}</p>
            @endforeach
        </td>
        <td class="text-center" colspan="2">${{ $data['TWMBB']['recvAmt'] }}</td>
    </tr>
    <tr>
        <td class="text-center line-h10 font-s15" colspan="2">總實收金額</td>
    </tr>
    <tr>
        <td class="text-center line-h10 font-s15">本票</td>
        <td class="text-center line-h10 font-s15">金額</td>
    </tr>
    <tr>
        <td class="text-center">$</td>
        <td class="text-center">${{ $data['TWMBB']['recvAmt'] }}</td>
    </tr>
    <tr>
        <td class="text-center line-h10 font-s15" colspan="2">工程人員</td>
    </tr>
    <tr>
        <td class="text-center line-h10" colspan="2">{{ $data['TWMBB']['WorkerName'] }}</td>
    </tr>
    <tr>
        <td class="text-center font-s10" colspan="2"></td>
    </tr>
    <tr>
        <td class="text-center font-s10" colspan="2"></td>
    </tr>
</table>


<table id="table101">
    <tr>
        <td class="col-5 wordWrap align-top" rowspan="2">
            <p class="m-0 font-s10 line-h08">
                備註二：{{ $data['TWMBB']['MSComment1'] }}
            </p>
        </td>
        <td class="col-2 line-h08 m-0 p-0 text-center font-s12" style="height: 15px;">舊吊牌編碼</td>
        <td class="col-1 line-h08 m-0 p-0 text-center font-s12" style="height: 15px;"></td>
        <td class="col-4 line-h08 m-0 p-0 text-center font-s12" style="height: 15px;">設備/贈品/證件繳交確認</td>
    </tr>
    <tr>
        <td class="text-center font-s10">
            <p class="">未完工填寫代碼</p>
            <p class="">(完工貼吊牌)</p>
        </td>
        <td class="text-center font-s10"></td>
        <td class="font-s10 line-h08">
            <p class="m-0 line-h08"><input type="checkbox" class="h-15p" {{ $data['TWMBB']['checkId'] }}>身分證正反面影本</p>
            <p class="m-0 line-h08"><input type="checkbox" class="h-15p" {{ $data['TWMBB']['checkHealth'] }}>健保卡</p>
            <p class="m-0 line-h08"><input type="checkbox" class="h-15p" {{ $data['TWMBB']['checkDriver'] }}>駕照影本({{ $data['TWMBB']['checkDriverRem'] }})</p>
            <p class="m-0 line-h08"><input type="checkbox" class="h-15p" {{ $data['TWMBB']['checkCompany'] }}>公司變更登記事項表</p>
            <p class="m-0 line-h08"><input type="checkbox" class="h-15p" {{ $data['TWMBB']['checkOther'] }}>其他{{ $data['TWMBB']['checkOtherRem'] }}</p>
        </td>
    </tr>
{{--</table>--}}
{{--<table id="table101">--}}
    <tr>
        <td colspan="4" class="col-12 wordWrap">
            <p class="m-0 text-top font-s8 line-h08">
                特約條款：
            </p>
            <p class="m-0 mr-1 text-top font-s8 line-h08">
                {{ $data['TWMBB']['MSContract'] }}
            </p>
        </td>
    </tr>
</table>

<table id="table101">
    <tr>
        <td colspan="7" class="col-12 wordWrap h-70p align-bottom">
            <p class="m-0 text-bot font-s10 line-h08">
                申裝人(簽名):
                @if(!empty($data['TWMBB']['signImage']))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($data['TWMBB']['signImage'])) }}" height="50px">
                @endif
                ，法定代理人/代表人:{{ $data['TWMBB']['checkLegal'] }}({{ $data['TWMBB']['checkTitle'] }})
            </p>
            <p class="m-0 text-bot font-s10 line-h08">
                （個人用戶請簽名，代理人請另附身分證正反面影本，未滿二十歲者，須附法定代理人之簽章及身分證明文件）
            </p>
        </td>
    </tr>
    <tr>
        <td class="text-center font-s15" style="width: 30px">
            <p class="m-0 font-s15">個人</p>
            <p class="m-0 font-s15">資料</p>
        </td>
        <td class="font-s15">
            <p class="m-0 font-s15">電話:</p>
        </td>
        <td class="text-center font-s15" style="width: 30px">
            <p class="m-0 font-s15">配件</p>
            <p class="m-0 font-s15">確認</p>
        </td>
        <td class="wordWrap font-s10">
            <input type="checkbox" class="h-15p" {{ $data['TWMBB']['check_notest'] }}>CM一台、乙太網路線一條、USB連接線一條、電源線一條、說明書及驅動程式光碟
        </td>
        <td class="text-center" style="width: 15px">
            <p class="m-0 font-s15">裝</p>
            <p class="m-0 font-s15">置</p>
            <p class="m-0 font-s15">點</p>
        </td>
        <td></td>
        <td class="font-s10">
            戶籍地址: <input type="checkbox" class="h-15p" {{ $data['TWMBB']['check_domicile'] }}>同裝機地址
        </td>
    </tr>
</table>

<div class="w-100">
    <p class="text-right font-s8">V:{{ date('YmdHis') }}</p>
</div>

<p class=" font-s15 line-h08">
    <input type="checkbox" class="h-15p" {{ $data['TWMBB']['check_notest'] }}>未備電腦、未為供裝速率實測
    <input type="checkbox" class="h-15p" {{ $data['TWMBB']['check_standalone'] }}>單機實測為
    <u> {{ $data['TWMBB']['check_standalone_desc'] }} </u> ，
    在供裝速率範圍 <input type="checkbox" class="h-15p" {{ $data['TWMBB']['check_notest_standalone'] }}>
    無法單機測試 <u> {{ $data['TWMBB']['check_notest_standalone_desc'] }} </u>，
</p>
<p class=" font-s15 line-h08">
    <input type="checkbox" class="h-15p" {{ $data['TWMBB']['check_equipmentdiscord_test'] }}>電腦設備不合標準
    ，單機實測為 <u> {{ $data['TWMBB']['check_equipmentdiscord_test_desc'] }} </u>。
</p>

<i class="page_break"></i>
