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
            @if(!empty($data['FTTH']['head_logo_img']))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($data['FTTH']['head_logo_img'])) }}" height="150">
            @endif
        </td>
        <td class="col-6 text-center">
            <p class="m-0 font-s30">{{ $data['FTTH']['head_title01'] }}</p>
            <p class="m-0">{{ $data['FTTH']['head_title02'] }}</p>
        </td>
        <td class="col-3">
            @if(!empty($data['FTTH']['lineQrCode']))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($data['FTTH']['lineQrCode'])) }}" width="100" height="100">
            @endif
        </td>
    </tr>
</table>


<table class="w-100">
    <tr>
        <td class="col-6">
            <p class="m-0 line-h08 font-s15">服務電話：{{ $data['FTTH']['head_tel'] }}</p>
            <p class="m-0 line-h08 font-s15">地址：{{ $data['FTTH']['head_addres'] }}</p>
            <p class="m-0 line-h08 font-s15">工單單號：{{ $data['FTTH']['head_worksheet'] }}</p>

        </td>
        <td class="col-6">
            <p class="m-0 line-h08 font-s15 text-right">網址：{{ $data['FTTH']['head_homeURL'] }}</p>
            <p class="m-0 line-h08 font-s15 text-right"> </p>
            <p class="m-0 line-h08 font-s15">NO:{{ $data['FTTH']['head_no'] }}</p>
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
            <p class="m-0 line-h08 font-s10">客戶編號：{{ $data['FTTH']['CustID'] }}</p>
            <p class="m-0 line-h08 font-s10">姓名：{{ $data['FTTH']['CustName'] }}</p>
            <p class="m-0 line-h08 font-s10">電話(家)：{{ $data['FTTH']['hometel'] }}</p>
            <p class="m-0 line-h08 font-s10">行動電話:{{ $data['FTTH']['phonetel'] }}</p>
            <p class="m-0 line-h08 font-s10">裝機地址:{{ $data['FTTH']['InstAddrName'] }}</p>
            <p class="m-0 line-h08 font-s10">收費地址:{{ $data['FTTH']['InstAddrName'] }}</p>
            <p class="m-0 line-h08 font-s10">大樓(社區)名稱：</p>
            <p class="m-0 line-h08 font-s10">移機舊址：</p>
        </td>
        <td class="h-30p line-h10 text-center">{{ explode(' ',$data['FTTH']['WorkKind'])[1] }}</td>
        <td class="h-30p line-h10 text-center wordBreakAll">
            @for($i=0; $i < count($data['FTTH']['dstbIVR']);$i++)
                @if($i%3 === 0)
                    <p class="m-0 line-h08 font-s15">
                @endif
                    {{ $data['FTTH']['dstbIVR'][$i] }}
                @if($i%3 === 2)
                    </p>
                @endif
            @endfor
        </td>
        <td class="h-30p line-h10 text-center">{{ $data['FTTH']['WorkSheet'] }}</td>
    </tr>
    <tr>
        <td colspan="2">
            <p class="m-0 line-h08 font-s10">工程組別:{{ $data['FTTH']['WorkTeam'] }}</p>
            <p class="m-0 line-h08 font-s10">網路編號:{{ $data['FTTH']['NetID'] }}</p>
            <p class="m-0 line-h08 font-s10">下次收費日:</p>
            <p class="m-0 line-h08 font-s10">方案別(合約起迄日):{{ $data['FTTH']['SaleCampaign'] }}</p>

        </td>
        <td>
            <p class="m-0 line-h08 font-s10">受理人:{{ $data['FTTH']['CreateName'] }}</p>
            <p class="m-0 line-h08 font-s10">受理日期時間:{{ $data['FTTH']['CreateTime'] }}</p>
            <p class="m-0 line-h08 font-s10">預約日期時間:{{ $data['FTTH']['BookDate'] }}</p>
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
        <td class="align-top mt-1" rowspan="8">
            @foreach($data['FTTH']['chargeNameAry'] as $t1)
                <p class="m-0 mb-1 font-s10 line-h08">{{ $t1 }}</p>
            @endforeach
        </td>
        <td class="align-top mt-1 text-center" rowspan="8">
            @foreach($data['FTTH']['chargeDateAry'] as $t2)
                <p class="m-0 mb-1 font-s10 line-h08">{{ $t2 }}</p>
            @endforeach
        </td>
        <td class="align-top mt-1 text-right" rowspan="8">
            @foreach($data['FTTH']['billAmtAry'] as $t3)
                <p class="m-0 mb-1 font-s10 line-h08 mr-2">{{ $t3 }}</p>
            @endforeach
        </td>
        <td class="text-center" colspan="2">${{ $data['FTTH']['recvAmt'] }}</td>
    </tr>
    <tr>
        <td class="text-center line-h10 font-s15" colspan="2">總實收金額</td>
    </tr>
    <tr>
        <td class="text-center line-h10 font-s15">本票</td>
        <td class="text-center line-h10 font-s15">金額</td>
    </tr>
    <tr>
        <td class="text-center">$0</td>
        <td class="text-center">${{ $data['FTTH']['recvAmt'] }}</td>
    </tr>
    <tr>
        <td class="text-center line-h10 font-s15" colspan="2">工程人員</td>
    </tr>
    <tr>
        <td class="text-center line-h10" colspan="2">{{ $data['FTTH']['WorkerName'] }}</td>
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
                備註二：{{ $data['FTTH']['MSComment1'] }}
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
            <p class="m-0 line-h08"><input type="checkbox" class="h-15p" {{ $data['FTTH']['checkId'] }}>身分證正反面影本</p>
            <p class="m-0 line-h08"><input type="checkbox" class="h-15p" {{ $data['FTTH']['checkHealth'] }}>健保卡</p>
            <p class="m-0 line-h08"><input type="checkbox" class="h-15p" {{ $data['FTTH']['checkDriver'] }}>駕照影本({{ $data['FTTH']['checkDriverRem'] }})</p>
            <p class="m-0 line-h08"><input type="checkbox" class="h-15p" {{ $data['FTTH']['checkCompany'] }}>公司變更登記事項表</p>
            <p class="m-0 line-h08"><input type="checkbox" class="h-15p" {{ $data['FTTH']['checkOther'] }}>其他{{ $data['FTTH']['checkOtherRem'] }}</p>
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
                {{ $data['FTTH']['MSContract'] }}
            </p>
        </td>
    </tr>
</table>

<table id="table101">
    <tr>
        <td colspan="7" class="col-12 wordWrap">
            <p class="m-0 mr-3 mb-3 text-top font-s8 line-h08">
                本人茲確認及同意 1.本申裝方案及抨約條款內容 2.申請書背面之寬頻連線服務契約 3.申裝設備用戶須附上雙證（身份證影本十駕照或健保卡）正反面影本 4.貴公司保護及使用用戶資料權益聲明:為提供服務我們將保存及使用您所提供之「寬頻上網及加值服務」用戶資料，包括您與我們聯絡所提供之用戶資料（例 :帳務、資訊流服務調查等）。用戶資料之保存與使用主要是用於提昇產品服務品質、加強個人化服務及停止服務後之服務產品訊息告知（包括以電郵、簡訊、語音及視訊等方式提供適合您的服務及行銷訊息，例:有線電視匯流相關服務產品、視訊服務後之產品銷售訊息通知、節目及服務數位資訊流匯整等），未經您的同意，不會另外將您的用戶資料揭露於與本服務無關之第三人或非上述目的以外之用途。若您不想再收到我們的訊息或用戶資訊需要更新等，請與客服聯絡，我們將由專人為您服務。5申裝光纖寬頻網路服務，本人了解終止服務時至營業櫃擡返還之義務及法律責任。
            </p>
            <p class="m-0 mr-3 mb-3 text-top font-s8 line-h08">
                此致 @if($data['FTTH']['CompanyNo'] == '209') 寶島聯網股份有限公司@else 中嘉寬頻股份有限公司@endif
            </p>
            <p class="m-0 text-bot font-s10 line-h08">
                申裝人(簽名):
                @if(!empty($data['FTTH']['signImage']))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($data['FTTH']['signImage'])) }}" height="50px">
                @endif
                ，法定代理人/代表人:{{ $data['FTTH']['checkLegal'] }}(關係或稱謂:{{ $data['FTTH']['checkTitle'] }})
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
            <input type="checkbox" class="h-15p" {{ $data['FTTH']['check_notest'] }}>CM一台、乙太網路線一條、USB連接線一條、電源線一條、說明書及驅動程式光碟
        </td>
        <td class="text-center" style="width: 15px">
            <p class="m-0 font-s15">裝</p>
            <p class="m-0 font-s15">置</p>
            <p class="m-0 font-s15">點</p>
        </td>
        <td></td>
        <td class="font-s10">
            戶籍地址: <input type="checkbox" class="h-15p" {{ $data['FTTH']['check_domicile'] }}>同裝機地址
        </td>
    </tr>
</table>

<div class="w-100">
    <p class="text-right font-s8">V:{{ date('YmdHis') }}</p>
</div>

<p class=" font-s15 line-h08">
    <input type="checkbox" class="h-15p" {{ $data['FTTH']['check_notest'] }}>未備電腦、未為供裝速率實測
    <input type="checkbox" class="h-15p" {{ $data['FTTH']['check_standalone'] }}>單機實測為
    <u> {{ $data['FTTH']['check_standalone_desc'] }} </u> ，
    在供裝速率範圍 <input type="checkbox" class="h-15p" {{ $data['FTTH']['check_notest_standalone'] }}>
    無法單機測試 <u> {{ $data['FTTH']['check_notest_standalone_desc'] }} </u>，
</p>
<p class=" font-s15 line-h08">
    <input type="checkbox" class="h-15p" {{ $data['FTTH']['check_equipmentdiscord_test'] }}>電腦設備不合標準
    ，單機實測為 <u> {{ $data['FTTH']['check_equipmentdiscord_test_desc'] }} </u>。
    加購 WIFI AP(<input type="checkbox" checked>{{ $data['FTTH']['saleap'] }})
</p>

<i class="page_break"></i>
