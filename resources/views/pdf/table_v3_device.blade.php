<head runat="server">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>服務申請書</title>
    <link rel="stylesheet" href="{{ asset('/cns/css/bootstrap.min.css') }}">
</head>

@include('pdf.css')

<style>
    .wp-01 { width: 100px; }
    .bg-d9e2f3 { background-color: #d9e2f3; }
    .bg-fbe4d5 { background-color: #fbe4d5; }
    .bg-00968f { background-color: #00968f; }
</style>

<body>
    <input type="hidden" name="p_BorrowmingList" id="p_BorrowmingList" value="{{ $data['info']['BorrowmingList'] }}">
    <input type="hidden" name="p_RetrieveList" id="p_RetrieveList" value="{{ $data['info']['RetrieveList'] }}">

    <div class="row">
        <div class="col-12 pr-0 mb-1">
            <img class="float-left" src="{{ asset('/img/logo'.$data['info']['CompanyNo'].'.png') }}" height="20" onerror='this.style.display="none"'>
        </div>
    </div>
    <div class="row">
        <div class="col-12 bg-00968f pr-0">
            <h4 class="text-white m-0 pt-3 pb-3 pl-1">設備借用/取回保管單</h4>
        </div>
    </div>
    <table class="w-100" id="table101">
        <tr>
            <td>客戶姓名：</td>
            <td>{{ $data['info']['CustName'] }}</td>
            <td>住戶編號：</td>
            <td>{{ $data['info']['CustID'] }}</td>
        </tr>
        <tr>
            <td>聯絡電話：</td>
            <td>{{ $data['info']['hometel'] }}</td>
            <td>電話(行動)：</td>
            <td>{{ $data['info']['phonetel'] }}</td>
        </tr>
        <tr>
            <td>裝機地址：</td>
            <td colspan="99">{{ $data['info']['InstAddrName'] }}</td>
        </tr>
    </table>

    <table class="w-100 text-center" id="table101">
        <tr>
            <td rowspan="99" class="wp-01">{{ $data['equipment']['D'][0]['typeDesc'] }}</td>
            <td>
                <table class="w-100 text-center" id="table101">
                    <tr class="bg-d9e2f3">
                        <td>主機功能</td>
                        <td>價格</td>
                        <td>型號</td>
                        <td>序號</td>
                        <td>借用數量</td>
                        <td>取回數量</td>
                        <td>備註</td>
                    </tr>
                    @foreach($data['equipment']['D'] as $k => $t)
                        @if($t['selectType'] == '0')
                            <tr>
                                <td class="text-left">{{ $t['deviceName'] }}</td>
                                <td>{{ $t['amt'].'元/'.$t['qtyType'] }}</td>
                                <td>
                                    @if(isset($data['BorrowmingListDev'][$t['Id']]))
                                        @foreach($data['BorrowmingListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">{{ $t2['model'] }}</p>
                                        @endforeach
                                    @endif


                                     @if(isset($data['RetrieveListDev'][$t['Id']]))
                                        @foreach($data['RetrieveListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">{{ $t2['model'] }}</p>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    @if(isset($data['BorrowmingListDev'][$t['Id']]))
                                        @foreach($data['BorrowmingListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">{{ $t2['singlesn'] }}</p>
                                        @endforeach
                                    @endif


                                     @if(isset($data['RetrieveListDev'][$t['Id']]))
                                        @foreach($data['RetrieveListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">{{ $t2['singlesn'] }}</p>
                                        @endforeach
                                    @endif
                                </td>
                                <td id="{{ 'bor_'.$t['Id'] }}">
                                    @if(isset($data['BorrowmingListDev'][$t['Id']]))
                                        @foreach($data['BorrowmingListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">1</p>
                                        @endforeach
                                    @endif


                                     @if(isset($data['RetrieveListDev'][$t['Id']]))
                                        @foreach($data['RetrieveListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">0</p>
                                        @endforeach
                                    @endif
                                </td>
                                <td id="{{ 'ret_'.$t['Id'] }}">
                                    @if(isset($data['BorrowmingListDev'][$t['Id']]))
                                        @foreach($data['BorrowmingListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">0</p>
                                        @endforeach
                                    @endif


                                     @if(isset($data['RetrieveListDev'][$t['Id']]))
                                        @foreach($data['RetrieveListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">1</p>
                                        @endforeach
                                    @endif
                                </td>
                                <td class="text-left">
                                    @if(isset($data['BorrowmingListDev'][$t['Id']]))
                                        @foreach($data['BorrowmingListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">{{ $t2['desc'] }}</p>
                                        @endforeach
                                    @endif

                                    @if(isset($data['RetrieveListDev'][$t['Id']]))
                                        @foreach($data['RetrieveListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">{{ $t2['desc'] }}</p>
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    <tr class="bg-fbe4d5">
                        <td colspan="4">合計</td>
                        <td>
                            @if(isset($data['BorrowmingListDevCount']['D']))
                                {{ $data['BorrowmingListDevCount']['D'] }}
                            @else
                                0
                            @endif
                        </td>
                        <td>
                            @if(isset($data['RetrieveListDevCount']['D']))
                                {{ $data['RetrieveListDevCount']['D'] }}
                            @else
                                0
                            @endif
                        </td>
                        <td>*</td>
                    </tr>
                </table>
            </td>
        <tr>
        </tr>
            <td>
                <table class="w-100 text-center" id="table101">
                    <tr>
                        <td>配件</td>
                        <td>價格</td>
                        <td>借用數量</td>
                        <td>取回數量</td>
                        <td>配件</td>
                        <td>價格</td>
                        <td>借用數量</td>
                        <td>取回數量</td>
                    </tr>
                    @php $i=0; @endphp
                    @foreach($data['equipment']['D'] as $k => $t)
                        @if($t['selectType'] == '1')
                            <input type="hidden" value="{{ $i++ }}">
                            @if($i == '0' || $i%2 == 1)<tr>@endif
                                <td class="text-left">{{ $t['deviceName'] }}</td>
                                <td>{{ $t['amt'].'元/'.$t['qtyType'] }}</td>
                                <td id="{{ 'bor_'.$t['Id'] }}">@if(isset($data['BorrowmingListSel'][$t['Id']]['qty'])){{ $data['BorrowmingListSel'][$t['Id']]['qty'] }}@else - @endif</td>
                                <td id="{{ 'ret_'.$t['Id'] }}">@if(isset($data['RetrieveListSel'][$t['Id']]['qty'])){{ $data['RetrieveListSel'][$t['Id']]['qty'] }}@else - @endif</td>
                            @if($i%2 < 1)</tr>@endif
                        @endif
                    @endforeach
                    @if($i%2 == 1)<td></td><td></td><td></td><td></td>@endif
                </table>
            </td>
        </tr>
    </table>

    <table class="w-100 text-center" id="table101">
        <tr>
            <td rowspan="99" class="wp-01">{{ $data['equipment']['I'][0]['typeDesc'] }}</td>
            <td>
                <table class="w-100 text-center" id="table101">
                    <tr class="bg-d9e2f3">
                        <td>主機功能</td>
                        <td>價格</td>
                        <td>型號</td>
                        <td>序號</td>
                        <td>借用數量</td>
                        <td>取回數量</td>
                        <td>備註</td>
                    </tr>
                    @foreach($data['equipment']['I'] as $k => $t)
                        @if($t['selectType'] == '0')
                            <tr>
                                <td class="text-left">{{ $t['deviceName'] }}</td>
                                <td>{{ $t['amt'].'元/'.$t['qtyType'] }}</td>
                                <td>
                                    @if(isset($data['BorrowmingListDev'][$t['Id']]))
                                        @foreach($data['BorrowmingListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">{{ $t2['model'] }}</p>
                                        @endforeach
                                    @endif


                                    @if(isset($data['RetrieveListDev'][$t['Id']]))
                                        @foreach($data['RetrieveListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">{{ $t2['model'] }}</p>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    @if(isset($data['BorrowmingListDev'][$t['Id']]))
                                        @foreach($data['BorrowmingListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">{{ $t2['singlesn'] }}</p>
                                        @endforeach
                                    @endif


                                    @if(isset($data['RetrieveListDev'][$t['Id']]))
                                        @foreach($data['RetrieveListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">{{ $t2['singlesn'] }}</p>
                                        @endforeach
                                    @endif
                                </td>
                                <td id="{{ 'bor_'.$t['Id'] }}">
                                    @if(isset($data['BorrowmingListDev'][$t['Id']]))
                                        @foreach($data['BorrowmingListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">1</p>
                                        @endforeach
                                    @endif


                                    @if(isset($data['RetrieveListDev'][$t['Id']]))
                                        @foreach($data['RetrieveListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">0</p>
                                        @endforeach
                                    @endif
                                </td>
                                <td id="{{ 'ret_'.$t['Id'] }}">
                                    @if(isset($data['BorrowmingListDev'][$t['Id']]))
                                        @foreach($data['BorrowmingListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">0</p>
                                        @endforeach
                                    @endif


                                    @if(isset($data['RetrieveListDev'][$t['Id']]))
                                        @foreach($data['RetrieveListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">1</p>
                                        @endforeach
                                    @endif
                                </td>
                                <td class="text-left">
                                    @if(isset($data['BorrowmingListDev'][$t['Id']]))
                                        @foreach($data['BorrowmingListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">{{ $t2['desc'] }}</p>
                                        @endforeach
                                    @endif

                                    @if(isset($data['RetrieveListDev'][$t['Id']]))
                                        @foreach($data['RetrieveListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">{{ $t2['desc'] }}</p>
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    <tr class="bg-fbe4d5">
                        <td colspan="4">合計</td>
                        <td>
                            @if(isset($data['BorrowmingListDevCount']['I']))
                                {{ $data['BorrowmingListDevCount']['I'] }}
                            @else
                                0
                            @endif
                        </td>
                        <td>
                            @if(isset($data['RetrieveListDevCount']['I']))
                                {{ $data['RetrieveListDevCount']['I'] }}
                            @else
                                0
                            @endif
                        </td>
                        <td>*</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table class="w-100 text-center" id="table101">
                    <tr class="bg-d9e2f3">
                        <td>配件</td>
                        <td>價格</td>
                        <td>借用數量</td>
                        <td>取回數量</td>
                        <td>配件</td>
                        <td>價格</td>
                        <td>借用數量</td>
                        <td>取回數量</td>
                    </tr>
                    @php $i=0; @endphp
                    @foreach($data['equipment']['I'] as $k => $t)
                        @if($t['selectType'] == '1')
                        <input type="hidden" value="{{ $i++ }}">
                        @if($i == '0' || $i%2 == 1)<tr>@endif
                            <td class="text-left">{{ $t['deviceName'] }}</td>
                            <td>{{ $t['amt'].'元/'.$t['qtyType'] }}</td>
                            <td id="{{ 'bor_'.$t['Id'] }}">@if(isset($data['BorrowmingListSel'][$t['Id']]['qty'])){{ $data['BorrowmingListSel'][$t['Id']]['qty'] }}@else - @endif</td>
                            <td id="{{ 'ret_'.$t['Id'] }}">@if(isset($data['RetrieveListSel'][$t['Id']]['qty'])){{ $data['RetrieveListSel'][$t['Id']]['qty'] }}@else - @endif</td>
                        @if($i%2 < 1)</tr>@endif
                        @endif
                    @endforeach
                    @if($i%2 == 1)<td></td><td></td><td></td><td></td>@endif
                </table>
            </td>
        </tr>
    </table>

    <table class="w-100 text-center" id="table101">
        <tr>
            <td rowspan="99" class="wp-01">{{ $data['equipment']['HP'][0]['typeDesc'] }}</td>
            <td>
                <table class="w-100 text-center" id="table101">
                    <tr class="bg-d9e2f3">
                        <td>主機功能</td>
                        <td>價格</td>
                        <td>型號</td>
                        <td>序號</td>
                        <td>借用數量</td>
                        <td>取回數量</td>
                        <td>備註</td>
                    </tr>
                    @foreach($data['equipment']['HP'] as $k => $t)
                        @if($t['selectType'] == '0')
                            <tr>
                                <td class="text-left">{{ $t['deviceName'] }}</td>
                                <td>{{ $t['amt'].'元/'.$t['qtyType'] }}</td>
                                <td>
                                    @if(isset($data['BorrowmingListDev'][$t['Id']]))
                                        @foreach($data['BorrowmingListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">{{ $t2['model'] }}</p>
                                        @endforeach
                                    @endif

                                    @if(isset($data['RetrieveListDev'][$t['Id']]))
                                        @foreach($data['RetrieveListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">{{ $t2['model'] }}</p>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    @if(isset($data['BorrowmingListDev'][$t['Id']]))
                                        @foreach($data['BorrowmingListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">{{ $t2['singlesn'] }}</p>
                                        @endforeach
                                    @endif

                                    @if(isset($data['RetrieveListDev'][$t['Id']]))
                                        @foreach($data['RetrieveListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">{{ $t2['singlesn'] }}</p>
                                        @endforeach
                                    @endif
                                </td>
                                <td id="{{ 'bor_'.$t['Id'] }}">
                                    @if(isset($data['BorrowmingListDev'][$t['Id']]))
                                        @foreach($data['BorrowmingListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">1</p>
                                        @endforeach
                                    @endif

                                    @if(isset($data['RetrieveListDev'][$t['Id']]))
                                        @foreach($data['RetrieveListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">0</p>
                                        @endforeach
                                    @endif
                                </td>
                                <td id="{{ 'ret_'.$t['Id'] }}">
                                    @if(isset($data['BorrowmingListDev'][$t['Id']]))
                                        @foreach($data['BorrowmingListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">0</p>
                                        @endforeach
                                    @endif

                                    @if(isset($data['RetrieveListDev'][$t['Id']]))
                                        @foreach($data['RetrieveListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">1</p>
                                        @endforeach
                                    @endif
                                </td>
                                <td class="text-left">
                                    @if(isset($data['BorrowmingListDev'][$t['Id']]))
                                        @foreach($data['BorrowmingListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">{{ $t2['desc'] }}</p>
                                        @endforeach
                                    @endif

                                    @if(isset($data['RetrieveListDev'][$t['Id']]))
                                        @foreach($data['RetrieveListDev'][$t['Id']] as $k2 => $t2)
                                            <p class="m-0">{{ $t2['desc'] }}</p>
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    <tr class="bg-fbe4d5">
                        <td colspan="4">合計</td>
                        <td>
                            @if(isset($data['BorrowmingListDevCount']['HP']))
                                {{ $data['BorrowmingListDevCount']['HP'] }}
                            @else
                                0
                            @endif
                        </td>
                        <td>
                            @if(isset($data['RetrieveListDevCount']['HP']))
                                {{ $data['RetrieveListDevCount']['HP'] }}
                            @else
                                0
                            @endif
                        </td>
                        <td>*</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table class="w-100 text-center" id="table101">
                    <tr class="bg-d9e2f3">
                        <td>配件</td>
                        <td>價格</td>
                        <td>借用數量</td>
                        <td>取回數量</td>
                        <td>配件</td>
                        <td>價格</td>
                        <td>借用數量</td>
                        <td>取回數量</td>
                    </tr>
                    @php $i=0; @endphp
                    @foreach($data['equipment']['HP'] as $k => $t)
                        @if($t['selectType'] == '1')
                            <input type="hidden" value="{{ $i++ }}">
                            @if($i == '0' || $i%2 == 1)<tr>@endif
                                <td class="text-left">{{ $t['deviceName'] }}</td>
                                <td>{{ $t['amt'].'元/'.$t['qtyType'] }}</td>
                                <td id="{{ 'bor_'.$t['Id'] }}">
                                    @if(isset($data['BorrowmingListSel'][$t['Id']]['qty'])){{ $data['BorrowmingListSel'][$t['Id']]['qty'] }}@else - @endif
                                    <br>
                                    @if(isset($data['BorrowmingSelListCount']['HP'][$t['Id']])){{ $data['BorrowmingSelListCount']['HP'][$t['Id']] }}@else - @endif
                                </td>
                                <td id="{{ 'ret_'.$t['Id'] }}">
                                    @if(isset($data['RetrieveListSel'][$t['Id']]['qty'])){{ $data['RetrieveListSel'][$t['Id']]['qty'] }}@else - @endif
                                    <br>
                                    @if(isset($data['RetrieveSelListCount']['HP'][$t['Id']])){{ $data['RetrieveSelListCount']['HP'][$t['Id']] }}@else - @endif
                                </td>
                            @if($i%2 < 1)</tr>@endif
                        @endif
                    @endforeach
                    @if($i%2 == 1)<td></td><td></td><td></td><td></td>@endif
                </table>
            </td>
        </tr>
    </table>

    <table class="w-100" id="table101">
        <tr>
            <td class="wp-01 text-center" id="t254">設備借用/取回保管單事項</td>
            <td class="pt-2">
                <ul style="list-style: decimal;">
                    <li class="red">本公司就用戶所提供之租押(借)用設備，係依本公司當時可提供之型號供裝，恕不保證提供特定型號或全新品之設備。</li>
                    <li>乙方借用之設備限申裝同址同人使用，不得移至他處使用。乙方使用期間應善盡保管之義務，如有不當處 置、使用、致本借用設備毀損、減失及減少正常功能，遭扣押、或為第三人佔有時，乙方應負完全賠償之 責，乙方並同意賠償設備主機及配件價值如上。</li>
                    <li>乙方使用期間，設備因可歸責乙方事由發生故障時，致需送修時，甲方得酌收維修材料及工資。</li>
                    <li>乙方停用／終止甲方有線電視 ■數位電視加值服務 ■光纖寬頻網路服務後，退訂用戶須憑【設設備借用/取回保管單】、七日內聯繫本公司派員或委託第3人至府上取回相關借用之設備及配件，用戶亦可持申裝人身分證件正本，自行將相關借用之設備及配件至本公司門市返還。（如有委託他人代辦者，須提供檢附用戶本人及代辦人身分 證/印章及委託書）提醒您，如未歸還恐將構成 刑法侵占之虞，請務必注意。</li>
                    <li>乙方退訂設備之主機及配件需完好無缺損，如發生損害或遺失時，本公司得自保證金及可退還費用中酌扣材料費用。</li>
                </ul>
            </td>
        </tr>
    </table>
</body>

<script type="text/javascript" src="{{asset('/js/jquery-3.5.1.min.js')}}"></script>
<script>
    // $(document).ready(function () {
    //     borrowmingListToHtml();
    //
    //     //retrieveListListToHtml();
    //
    // });
    // document.getElementById('t254').innerHTML = '277';
    // $('#t254').html('d');

    // 借用單 設備序號
    function borrowmingListDeviceToHtml(vJson)
    {
        let devList = vJson['device'];
        let qtyTotal = [];
        qtyTotal['I'] = 0;
        qtyTotal['D'] = 0;
        qtyTotal['HP'] = 0;
        let vId = '';
        devList.forEach(function(t,k){
            vId = t.id;
            if($('#model_'+vId).html() == '-') $('#model_'+vId).html('');
            if($('#singlesn_'+vId).html() == '-') $('#singlesn_'+vId).html('');
            if($('#bor_'+vId).html() == '-') $('#bor_'+vId).html('');

            $('#model_'+vId).append(`<p class="m-0">${t.model}</p>`);
            $('#singlesn_'+vId).append(`<p class="m-0">${t.singlesn}</p>`);
            $('#bor_'+vId).append(`<p class="m-0">1</p>`);

            qtyTotal[t.equType] += 1;
        });
        for (var k in qtyTotal) {
            $('#bor_'+k+'_total').html(qtyTotal[k]);
        }
    }

    // 借用單 檢查
    function borrowmingListToHtml()
    {
        $('#test').html('307');
        let vStr = $('#p_BorrowmingList').val();
        if(vStr.length < 1) return;
        let vJson = JSON.parse(vStr);
        let vJsonKeys = Object.keys(vJson);
        if($.inArray('select', vJsonKeys) >= parseInt('0')) {
            borrowmingListSelectToHtml(vJson);
        }
        if($.inArray('device', vJsonKeys) >= parseInt('0')) {
            borrowmingListDeviceToHtml(vJson);
        }
    }

    // 借用單 人工select
    function borrowmingListSelectToHtml(vJson)
    {
        $('#test').html('323');
        let selList = vJson['select'];
        selList.forEach(function(t,k){
            $('#bor_'+t.id).html(t.qty);
        });
    }

    // borrowmingListToHtml();

////////////////////////////////////////////////////////////

    // 取回單 人工select
    function retrieveListListSelectToHtml(vJson)
    {
        let selList = vJson['select'];
        selList.forEach(function(t,k){
            $('#ret_'+t.id).html(t.qty);
        });
    }

    // 取回單 設備序號
    function retrieveListListDeviceToHtml(vJson)
    {
        let devList = vJson['device'];
        let qtyTotal = [];
        qtyTotal['I'] = 0;
        qtyTotal['D'] = 0;
        qtyTotal['HP'] = 0;
        let vId = '';
        devList.forEach(function(t,k){
            vId = t.id;
            if($('#model_'+vId).html() == '-') $('#model_'+vId).html('');
            if($('#singlesn_'+vId).html() == '-') $('#singlesn_'+vId).html('');
            if($('#ret_'+vId).html() == '-') $('#ret_'+vId).html('');

            $('#model_'+vId).append(`<p class="m-0">${t.model}</p>`);
            $('#singlesn_'+vId).append(`<p class="m-0">${t.singlesn}</p>`);
            $('#ret_'+vId).append(`<p class="m-0">1</p>`);

            qtyTotal[t.equType] += 1;
        });
        for (var k in qtyTotal) {
            $('#ret_'+k+'_total').html(qtyTotal[k]);
        }
    }

    // 取回單 檢查
    function retrieveListListChk()
    {
        let vStr = $('#p_RetrieveList').val();
        if(vStr.length < 1) return;
        let vJson = JSON.parse(vStr);
        let vJsonKeys = Object.keys(vJson);
        console.log($.inArray('select', vJsonKeys))
        if($.inArray('select', vJsonKeys) >= parseInt('0')) {
            console.log('select')
            retrieveListListSelectToHtml(vJson);
        }
        if($.inArray('device', vJsonKeys) >= parseInt('0')) {
            console.log('device')
            retrieveListListDeviceToHtml(vJson);
        }

    }
    retrieveListListChk()

</script>
