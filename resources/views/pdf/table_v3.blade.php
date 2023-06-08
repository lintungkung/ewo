<head runat="server">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>服務申請書</title>
    <link rel="stylesheet" href="{{ asset('/cns/css/bootstrap.min.css') }}">
</head>
@include('pdf.css')

<style>
    .border-0 {border: 0px solid black;}
    .d-grid {display: grid;}
    .bg-00968f {background-color: #00968f;}
    .red {color: red;}
    .w-33 {width: 33.33%;}

    .borColR01 {
        border-top-color: black !important;
        border-right-color: red !important;
    }
    .borColR02 {
        border-bottom-color: red !important;
        border-right-color: red !important;
    }
    .borColR03r {
        border-right: 3px solid red !important;
    }
    .borColR03t {
        border-top: 3px solid red !important;
    }
    .borColR03b {
        border-bottom: 3px solid red !important;
    }

    .borColR03 {
        border: 3px solid red !important;
    }
    .borColR01 {
        border: 1px solid red !important;
    }
    .borColR01r {
        border-right: 1px solid red !important;
    }
    .borColR01t {
        border-top: 1px solid red !important;
    }
    .borColR01b {
        border-bottom: 1px solid red !important;
    }
    /* new page */
    .page_break {
        /*page-break-before: always !important;*/
        display: block;
        page-break-after: always;
        position: relative;
    }

    .line-h15 {line-height: 1.5 !important;}

</style>


<table class="text-center" id="table101">
    <tr>
        <td colspan="99">
            @if(strpos($data['info']['ServiceName'],'DSTB') > 0) ■@else □@endif 有線電視
            @if(strpos($data['info']['ServiceName'],'CATV') > 0) ■@else □@endif 數位電視加值服務
            @if(empty($data['serviceName2'])) □@else ■@endif 光纖寬頻網路
        </td>
    </tr>
    <tr>
        <td class="text-left" colspan="99">一、用戶基本資料（※客戶基本資料係營業秘密不得複製或外洩）</td>
    </tr>
    <tr>
        <td>客戶姓名</td>
        <td>{{ $data['info']['CustName'] }}</td>
        <td>住戶編號</td>
        <td>{{ $data['info']['CustID'] }}</td>
    </tr>
    <tr>
        <td>聯絡電話</td>
        <td>{{ $data['info']['hometel'] }}</td>
        <td>電話(行動)：</td>
        <td>{{ $data['info']['phonetel'] }}</td>
    </tr>
    <tr>
        <td>裝機地址</td>
        <td colspan="3">{{ $data['info']['InstAddrName'] }}</td>
    </tr>
    <tr>
        <td>收費地址</td>
        <td colspan="3">{{ $data['info']['InstAddrName'] }}</td>
    </tr>
    <tr><td colspan="99">&nbsp;</td></tr>
</table>


<table class="text-center" id="table101">
    <tr>
        <td>員工代號+受理人員姓名：</td>
        <td>{{ $data['info']['CustBroker'] }}</td>
        <td>申辦裝機通路</td>
        <td>{{ data_get(explode(' ',$data['info']['BrokerKind']),'1') }}</td>
        <td>受理日期：</td>
        <td>{{ substr($data['info']['AcceptDate'],0,19) }}</td>
    </tr>
    <tr>
        <td>工程組別：</td>
        <td>{{ $data['info']['WorkTeam'] }}</td>
        <td>派工類別</td>
        <td>{{ explode(' ',$data['info']['WorkKind'])[1] }}</td>
        <td>完工時間</td>
        <td>{{ substr($data['info']['finsh'],0,19) }}</td>
    </tr>
</table>

@if(in_array('1 CATV',$data['serviceNameAry']) || in_array('3 DSTB',$data['serviceNameAry']))
<table class="text-center" id="table101">
    <tr>
        <td class="text-left red" colspan="2">二、申辦服務內容及相關注意事項 ※申請人，請詳閱紅框內各項內容及服務契約(以紅字提醒)</td>
    </tr>
    <tr>
        <td class="w-150p">■ 有線電視/數位電視加值服務</td>
        <td class="borColR03">
            <table id="table101">
                <tr>
                    <td class="text-center">收費/設備項目</td>
                    <td class="text-center">首期服務期間</td>
                    <td class="text-center">金額</td>
                </tr>
                @if(isset($data['charges']['chargeNameAry']['C']))
                @foreach($data['charges']['chargeNameAry']['C'] as $k => $t)
                    <tr>
                        <td class="pl-2">{{ $data['charges']['chargeNameAry']['C'][$k] }}</td>
                        <td class="text-center">{{ $data['charges']['chargeDateAry']['C'][$k] }}</td>
                        <td class="text-center">{{ $data['charges']['billAmtAry']['C'][$k] }}</td>
                    </tr>
                @endforeach
                @endif
                @if(isset($data['charges']['chargeNameAry']['D']))
                @foreach($data['charges']['chargeNameAry']['D'] as $k => $t)
                    <tr>
                        <td class="pl-2">{{ $data['charges']['chargeNameAry']['D'][$k] }}</td>
                        <td class="text-center">{{ $data['charges']['chargeDateAry']['D'][$k] }}</td>
                        <td class="text-center">{{ $data['charges']['billAmtAry']['D'][$k] }}</td>
                    </tr>
                @endforeach
                @endif
                <tr>
                    <td colspan="2" class="text-right pr-3">應收總金額</td>
                    <td class="text-center">
                        @if(isset($data['charges']['recvAmt']['C']) && isset($data['charges']['recvAmt']['D']))
                            {{ intval($data['charges']['recvAmt']['C'])+intval($data['charges']['recvAmt']['D']) }}
                        @elseif(isset($data['charges']['recvAmt']['C']))
                            {{ intval($data['charges']['recvAmt']['C']) }}
                        @elseif(isset($data['charges']['recvAmt']['D']))
                            {{ intval($data['charges']['recvAmt']['D']) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="99" class="red text-left">
                        <span>※加值服務專案條款：</span>
                        <p class="m-0 mr-1 text-top font-s8">
                            {{ $data['info']['MSContract'] }}
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@endif


@if(strlen($data['serviceName2']) > 0 || in_array('C HS',$data['serviceNameAry']))
<table class="text-center" id="table101">
    <tr>
        <td class="w-150p">■ 光纖寬頻網路</td>
        <td class="borColR03">
            <table class="" id="table101">
                <tr>
                    <td class="text-center">收費/設備項目</td>
                    <td class="text-center">收費期間</td>
                    <td class="text-center">金額</td>
                </tr>
                @if(isset($data['charges']['chargeNameAry']['I']))
                @foreach($data['charges']['chargeNameAry']['I'] as $k => $t)
                    <tr>
                        <td class="pl-2">{{ $data['charges']['chargeNameAry']['I'][$k] }}</td>
                        <td class="text-center">{{ $data['charges']['chargeDateAry']['I'][$k] }}</td>
                        <td class="text-center">{{ $data['charges']['billAmtAry']['I'][$k] }}</td>
                    </tr>
                @endforeach
                @endif
                <tr>
                    <td colspan="2" class="text-right pr-3">應收總金額</td>
                    <td class="text-center">{{ intval($data['charges']['recvAmt']['I']) }}</td>
                </tr>
            </table>

            @if($data['newIns'] == 'Y' && $data['emptyListPriceORListPrd'] != 'Y')
            <table class="text-center" id="table101">
                <tr>
                    <td colspan="99" class="text-left">※資費方案：</td>
                </tr>
                <tr>
                    <td>速率</td>
                    <td>合約優惠價/每月</td>
                    <td class="red"><b>資費方案合約起迄日</b></td>
                </tr>
                <tr>
                    <td><u>&nbsp;{{ $data['info']['BillItem'] }}&nbsp;</u></td>
                    <td><u>&nbsp;{{ $data['info']['Aveamt'] }}&nbsp;</u>元</td>
                    <td class="red"><b>自 <u>&nbsp;{{ substr($data['info']['BookDate'],0,10) }}&nbsp;</u> 起 連續<u>&nbsp;{{ $data['info']['PackDuration'] }}&nbsp;</u>個月</b></td>
                </tr>
                <tr>
                    <td style="background-color: #c6c6c6"><b class="red">補繳專案費用</b></td>
                    <td colspan="2" class="red">
                        <b><u>&nbsp;{{ $data['info']['PenalAmt01'] }}&nbsp;</u>元<span class="font-s9">(約期未滿提前退拆則須依未使用完畢之月份按比例補繳專案費用)</span></b>
                    </td>
                </tr>
            </table>
            @endif

            <table class="text-center" id="table101">
                <tr>
                    <td colspan="99" class="red text-left">
                        <span>※寬頻網路資費特約條款：</span>
                        <p class="m-0 mr-1 text-top font-s8">
                            {{ $data['info']['MSContract2'] }}
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@endif


<table class="" id="table101">
    <tr>
        <td class="w-33 text-center">
            總應收金額
            <u>&nbsp;
                {{ data_get($data['charges']['recvAmt'],'totalAmt') }}
                &nbsp;</u>元
        </td>
        <td class="w-33 text-center">
            總實收金額
            <u>&nbsp;
            {{ data_get($data['charges']['recvAmt'],'totalAmt') }}
                &nbsp;</u>元
        </td>
        <td class="w-33">
            付款方式：
            @if($data['info']['receiveType'] == '2' && intval(data_get($data['charges']['recvAmt'],'totalAmt'))>0)■@else□@endif現金
            @if($data['info']['receiveType'] == '1' && intval(data_get($data['charges']['recvAmt'],'totalAmt'))>0)■@else□@endif線上刷卡
        </td>
    </tr>
    <tr>
        <td class="text-left" colspan="3">三、申請人同意事項暨簽名欄位</td>
    </tr>
    <tr>
        <td colspan="3">
            <p class="mb-2 mt-2">■ 必勾，已同意申裝服務之專案條款，如附(加值服務專案條款、有線電視、寬頻網路專案條款)。</p>
            <p class="mb-2">■ 必勾，已閱有線電視、光纖網路服務定型化契約及個人資料蒐集告知聲明。</p>
            <p class="mb-2">■ 必勾，已裝設借用業者之設備及數量於申裝地址，如附(有線電視機上盒、光纖網路數據機等，借用品項如上勾選)。</p>
            <p class="mb-2">■ 必勾，同意業者無償使用申請人外牆附掛纜線，以提供申裝人正常使用上述服務，。</p>
            <p class="mb-2">■ 必勾，同意業者進行機上行頻道節目收視之資訊蒐集分析及個人化內容之推薦等。</p>
            <p class="mb-2">■ 必勾，確認業者已於您所申裝地址現場進行寬頻網路速率測試並告知實測速率結果，且經申裝人或代理人簽認並同意測試結果無誤。</p>
            <p class="mb-2">■ 必勾，申請人同意申請「簡訊帳單」服務。業者得透過簡訊通知本人所申請服務之繳款資訊至本人提供之手機號碼(該手機需具上網功能)，本人可透過手機簡訊連結之網址即時連結電子帳單，並持該電子帳單前往中嘉寬頻門市或代收機構繳款。</p>
            <p class="mb-2">■ 必勾，就本人所申請之服務，本人同意申請「簡訊帳單」服務。中嘉寬頻得透過簡訊通知本人所申請服務之繳款資訊至本人提供之手機號碼(該手機需具上網功能)，本人可透過手機簡訊連結之網址即時連結電子帳單，並持該電子帳單前往中嘉寬頻門市或代收機構繳款。</p>
            <p class="mb-5 mt-5">■ 上述 1～8項說明，本人/本公司或受託代辦人均已充分了解並同意。</p>
            <p class="mb-0">
                @if(in_array($data['sign_mcust_select'],['','本人簽名'])) 客戶 @else 受託代辦人 @endif簽名：
                    <img src="{{ asset($data['sign_mcust_url']) }}" height="100" onerror="$(this).style.display=none">
                @if(!in_array($data['sign_mcust_select'],['','本人簽名'])) ({{ $data['sign_mcust_select'] }}) @endif
            </p>
            <p class="mb-0">工程簽名：<img src="{{ asset($data['sign_mengineer_url']) }}" height="100" onerror="$(this).style.display=none"></p>
            <p class="mb-0">附註：</p>
            @if(!in_array($data['sign_mcust_select'],['','本人簽名']))
                <p class="mb-0 red">　　本受託人確實受申請人委託代辦***服務，並代為同意上述事宜</p>
            @endif
            <p class="mb-0 red">　　個人用戶請簽名，公司/團體用戶請蓋大小章，代理人請另出示身分證正本。</p>
            <p class="mb-0 red">　　未滿十八歲者，需檢附法定代理人之簽章及身分證明文件。</p>
            <br>
            <br>
            <br>
            <span class="text-right d-grid font-s5">{{ 'C'.date('YmdHis').'I'.$data['info']['Id'] }}</span>
        </td>
    </tr>
</table>

<i class="page_break"></i>

<div style="" class="w-100 wordWrap line-h08 font-s10">
    <p class="m-0 mr-1 mb-3 text-top font-s20 line-h08">
        條款[提示]
    </p>
</div>


<table class="" id="table101">
@if(in_array('1 CATV',$data['serviceNameAry']) === true || in_array('3 DSTB',$data['serviceNameAry']) === true)
    <tr>
        <td class="wordWrap line-h08 font-s10 border-0">
{{--        <div style="" class="w-100 wordWrap line-h08 font-s10">--}}
            <p class="m-0 mr-1 text-top font-s18 line-h08">
                加值服務專案條款：
            </p>
            <p class="m-0 mr-1 mb-3 text-top font-s18 line-h15">
                {{ $data['info']['MSContract'] }}
            </p>
{{--        </div>--}}
        </td>
    </tr>
@endif

@if(strlen($data['serviceName2']) > 0)
    <tr>
        <td class="wordWrap line-h08 font-s10 border-0">
    {{--    <div class="w-100 wordWrap line-h08 font-s10">--}}
            <p class="m-0 mr-1 text-top font-s18 line-h08">
                寬頻網路資費特約條款：
            </p>
            <p class="m-0 mr-1 mb-3 text-top font-s18 line-h15">
                {{ $data['info']['MSContract2'] }}
            </p>
    {{--    </div>--}}
        </td>
    </tr>
@endif
</table>
