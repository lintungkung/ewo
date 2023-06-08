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
            @if(!empty($data['CATV']['head_logo_img']))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($data['CATV']['head_logo_img'])) }}" width="150">
            @endif
        </td>
        <td class="col-6 text-center">
            <p class="m-0 font-s30">{{ $data['CATV']['head_title01'] }}</p>
            <p class="m-0">{{ $data['CATV']['head_title02'] }}</p>
        </td>
        <td class="col-3">
            @if(!empty($data['CATV']['lineQrCode']))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($data['CATV']['lineQrCode'])) }}" width="100" height="100">
            @endif
        </td>
    </tr>
</table>


<table class="w-100">
    <tr>
        <td class="col-6">
            <p class="m-0 line-h08 font-s15">服務電話：{{ $data['CATV']['head_tel'] }}</p>
            <p class="m-0 line-h08 font-s15">地址：{{ $data['CATV']['head_addres'] }}</p>
            <p class="m-0 line-h08 font-s15">工單單號：{{ $data['CATV']['head_worksheet'] }}</p>

        </td>
        <td class="col-6">
            <p class="m-0 line-h08 font-s15 text-right">網址：{{ $data['CATV']['head_homeURL'] }}</p>
            <p class="m-0 line-h08 font-s15 text-right"> </p>
            <p class="m-0 line-h08 font-s15">NO:{{ $data['CATV']['head_no'] }}</p>
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
            <p class="m-0 line-h08 font-s10">客戶編號：{{ $data['CATV']['CustID'] }}</p>
            <p class="m-0 line-h08 font-s10">姓名：{{ $data['CATV']['CustName'] }}</p>
            <p class="m-0 line-h08 font-s10">電話(家)：{{ $data['CATV']['hometel'] }}</p>
            <p class="m-0 line-h08 font-s10">行動電話:{{ $data['CATV']['phonetel'] }}</p>
            <p class="m-0 line-h08 font-s10">裝機地址:{{ $data['CATV']['InstAddrName'] }}</p>
            <p class="m-0 line-h08 font-s10">收費地址:{{ $data['CATV']['InstAddrName'] }}</p>
            <p class="m-0 line-h08 font-s10">大樓(社區)名稱：</p>
            <p class="m-0 line-h08 font-s10">移機舊址：</p>
        </td>
        <td class="h-30p line-h10 text-center">{{ explode(' ',$data['CATV']['WorkKind'])[1] }}</td>
        <td class="h-30p line-h10 text-center wordBreakAll">
            @for($i=0; $i < count($data['CATV']['dstbIVR']);$i++)
                @if($i%3 === 0)
                    <p class="m-0 line-h08 font-s15">
                @endif
                    {{ $data['CATV']['dstbIVR'][$i] }}
                @if($i%3 === 2)
                    </p>
                @endif
            @endfor
        </td>
        <td class="h-30p line-h10 text-center">{{ $data['CATV']['WorkSheet'] }}</td>
    </tr>
    <tr>
        <td colspan="2">
            <p class="m-0 line-h08 font-s10">工程組別:{{ $data['CATV']['WorkTeam'] }}</p>
            <p class="m-0 line-h08 font-s10">網路編號:{{ $data['CATV']['NetID'] }}</p>
            <p class="m-0 line-h08 font-s10">下次收費日:</p>
            <p class="m-0 line-h08 font-s10">方案別(合約起迄日):{{ $data['CATV']['SaleCampaign'] }}</p>

        </td>
        <td>
            <p class="m-0 line-h08 font-s10">受理人:{{ $data['CATV']['CreateName'] }}</p>
            <p class="m-0 line-h08 font-s10">受理日期時間:{{ $data['CATV']['CreateTime'] }}</p>
            <p class="m-0 line-h08 font-s10">預約日期時間:{{ $data['CATV']['BookDate'] }}</p>
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
        <td class="align-top " rowspan="8">
            @foreach($data['CATV']['chargeNameAry'] as $t1)
                <p class="m-0 mb-1 font-s10 line-h08">{{ $t1 }}</p>
            @endforeach
        </td>
        <td class="align-top text-center" rowspan="8">
            @foreach($data['CATV']['chargeDateAry'] as $t2)
                <p class="m-0 mb-1 font-s10 line-h08">{{ $t2 }}</p>
            @endforeach
        </td>
        <td class="align-top text-right" rowspan="8">
            @foreach($data['CATV']['billAmtAry'] as $t3)
                <p class="m-0 mb-1 font-s10 line-h08 mr-2">{{ $t3 }}</p>
            @endforeach
        </td>
        <td class="text-center" colspan="2">${{ $data['CATV']['recvAmt'] }}</td>
    </tr>
    <tr>
        <td class="text-center line-h10 font-s15" colspan="2">總實收金額</td>
    </tr>
    <tr>
        <td class="text-center line-h08 font-s15">本票</td>
        <td class="text-center line-h08 font-s15">金額</td>
    </tr>
    <tr>
        <td class="text-center">$0</td>
        <td class="text-center">${{ $data['CATV']['recvAmt'] }}</td>
    </tr>
    <tr>
        <td class="text-center line-h10 font-s15" colspan="2">工程人員</td>
    </tr>
    <tr>
        <td class="text-center line-h10" colspan="2">{{ $data['CATV']['WorkerName'] }}</td>
    </tr>
    <tr>
        <td class="text-center font-s10 line-h08" colspan="2"></td>
    </tr>
    <tr>
        <td class="text-center font-s10 line-h08" colspan="2"></td>
    </tr>
</table>


<table id="table101">
    <tr>
        <td class="col-5 wordWrap align-top" rowspan="2">
            <p class="m-0 font-s10 line-h08">
                備註二：{{ $data['CATV']['MSComment1'] }}
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
            <p class="m-0 line-h08"><input type="checkbox" class="h-15p" {{ $data['CATV']['checkId'] }}>身分證正反面影本</p>
            <p class="m-0 line-h08"><input type="checkbox" class="h-15p" {{ $data['CATV']['checkHealth'] }}>健保卡</p>
            <p class="m-0 line-h08"><input type="checkbox" class="h-15p" {{ $data['CATV']['checkDriver'] }}>駕照影本({{ $data['CATV']['checkDriverRem'] }})</p>
            <p class="m-0 line-h08"><input type="checkbox" class="h-15p" {{ $data['CATV']['checkCompany'] }}>公司變更登記事項表</p>
            <p class="m-0 line-h08"><input type="checkbox" class="h-15p" {{ $data['CATV']['checkOther'] }}>其他{{ $data['CATV']['checkOtherRem'] }}</p>
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
                {{ $data['CATV']['MSContract'] }}
            </p>
        </td>
    </tr>
</table>

<table id="table101">
    <tr>
        <td class="wordWrap">
            <p class="m-0 mr-3 mb-3 text-top font-s8 line-h08">
                1.申裝設備內含(1)數位機上盒乙台(2)智慧卡乙張(3)遙控器乙個(4)變壓器乙組(5)AV端子線乙組(6)色差端子線乙組。2.申裝設備/月繳現付用戶須附上身分證正反面影本。3.請選擇「有線電視收視費」次期繳費週期：口月繳現付、口雙月繳、口季繳、口半年繳、口全年繳。4.用戶若選擇借用本設備，其所有權為本公司所有，用戶限於上述裝機地址使用。當雙方有線電視收視合約終止時，用戶應立即將本設備全數及完整歸還予本公司。借用人應善盡保管義務使用本設備，歸還時如發生部分設備或整組設備遺失、毀損之情事，借用人應按設備市價賠償予本公司。5.使用DTV數位加值服務之用戶必須同時為本公司有效之有線電視基本頻道收視戶。若與本公司之有線電視基本頻道收視契約終止時，本公司得不經預告立即終止本服務。6.已申裝銀行自動扣款者同意因訂購本公司之服務所生之一切費用，均由原自動扣繳方式支付。本人茲確認及同意1.本申裝方案及特約條款內容2.申請書背面之有線電視定型化契約3.貴公司保護及使用用戶資料權益聲明:為提供服務我們將保存及使用您所提供之「有線電視及加值服務」用戶資料，包括您與我們聯絡所提供之個人資料(例：姓名、電話、地址、聯絡人姓名、信用卡資料、付款帳務、資訊流服務調查等)。除了個人資料外，我們也將收集您使用機上盒、機上盒回傳本公司系統之資料(例：機上盒開關待機資訊、家戶收視頻道及時間、家戶收視習慣等)。用戶資料之保存與使用主要是用於提供我們的服務(包括基本及加值服務)、提昇產品服務品質(如：了解家戶收視習慣及偏好，以提供用戶更好的內容及服務)、加強個人化服務(例：提供個人化內容之推薦、個人化廣告之提供)、及停止服務後之服務產品訊息告知(包括以電郵、簡訊、語音及視訊等方式提供適合您的服務及行銷訊息，例：有線電視匯流相關服務產品、視訊服務之產品銷售訊息通知、節目及服務數位資訊流匯整等)，未經您同意，不會另外將您的用戶資料揭露於與本公司無關之第三人非上述目的以外之用途、或非在必要之範圍內。若您不想再收到我們的訊息或用戶資訊需要更新等，請與客服聯絡，我們將由專人為您服務。4.申裝數位電視服務，本人了解需負責保管貴公司數位機上盒及終止服務時至營業櫃檯返還之義務及法律責任。
            </p>
        </td>
        <td colspan="2" class="text-center font-s15 line-h10" style="width: 250px">
            數位機上盒<br>
            &<br>
            智慧卡條碼黏貼欄
        </td>
    </tr>
    <tr>
        <td class="font-s10">
            <p class="m-0">
                申裝人(簽名):
                @if(!empty($data['CATV']['signImage']))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($data['CATV']['signImage'])) }}" height="50px">
                @endif
                ，法定代理人/代表人:{{ $data['CATV']['checkLegal'] }}(關係或稱謂:{{ $data['CATV']['checkTitle'] }})
            </p>
            <p class="m-0">
                [請用戶務必勾選]本人
                <input type="checkbox" class="h-15p" {{ $data['CATV']['checkPersonalon'] }}>同意，
                <input type="checkbox" class="h-15p" {{ $data['CATV']['checkPersonaloff'] }}>不同意
                ，貴公司進行機上行頻道節目收視之資訊蒐集分析及個人化內容之推薦等。
            </p>
        </td>
        <td class="text-center" style="width: 15px">
            <p class="m-0 font-s15">裝</p>
            <p class="m-0 font-s15">置</p>
            <p class="m-0 font-s15">點</p>
        </td>
        <td></td>
    </tr>
</table>
<div class="w-100">
    <p class="text-right font-s8">V:{{ date('YmdHis') }}</p>
</div>

<i class="page_break"></i>
