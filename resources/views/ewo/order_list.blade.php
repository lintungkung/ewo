@extends('ewo.layouts.default')

@section('title', '訂單清單')

@section('content')
    <style>
        main {
            padding-top: 70px;
        }

        .input-group-append + .tooltip > .tooltip-inner {background-color: #f00;}
        input[type="datetime-local"]::-webkit-clear-button {
            -webkit-appearance: none;
            display: none;
        }

        .d-flow-root {
            display: flow-root!important;
        }

    </style>
    <main style="">

        {{-- alertDanger --}}
        <div class="container alert alert-danger text-center" style="display: none;" id="alertDanger">
            查詢資料有錯[Err-Code:E005]
        </div>

        {{-- alertTopLoad --}}
        <div class="container alert alert-info text-center" style="display: none;" id="alertTopLoad">
            資料查詢中
            <div class="d-flex justify-content-center">
                <div class="spinner-border">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>

        <?php
        $ordListAry = array(
            'CustName' => '客戶名稱',
            'CustID' => '住編',
            'subsidAry' => '訂編清單',
            'worksheet_workkindAry' => '工單號清單',
            'CustPhoneSummary' => '客戶電話',
            'CMValue' => '速率別',
            'TotalAmt' => '應收金額',
            'InstAddrName' => '安裝地址',
            'servicenameAry' => '服務別',
            'SaleCampaign' => '方案別',
            'CallCause' => '備註二',
        );
        ?>

{{--    {{  Log::channel('ewoLog')->info('order list p_data==='.print_r($p_data,1)) }}--}}

        <input type="hidden" id="p_token" value="{{ csrf_token() }}">
        <input type="hidden" id="p_lsImg" value="{{ $p_data['lsImg'] }}">

        <div class="container bg-grey collapse" id="unFinshOrderList" name="divpage">
            @if(sizeof($p_data['unFin_list']) > 0)
                @foreach($p_data['unFin_list'] as $k=>$t)
                    <div class="col order" title="order" id="order{{$t['AssignSheet']}}" data-workkind="{{ $t['WorkKind'] }}">
                        <div class="card border-primary mb-2">
                            <div class="card-header">
                                <div class="row">
                                    <div class="input-group ">
                                        @if(!empty($t['faultDetail']))
                                            <a class="btn btn-danger ml-3 mr-3"
                                               href="/ewo/order_info/{{$t['CompanyNo'].'-'.$t['AssignSheet']}}/{{$p_data['tt']}}">
                                                {{$t['WorkKind'].'-'.$t['CompanyNo'].'-'.$t['AssignSheet']}}({{ $t['faultDetail'] }})
                                            </a>
                                        @else
                                            <a class="btn btn-outline-primary ml-3 mr-3"
                                               href="/ewo/order_info/{{$t['CompanyNo'].'-'.$t['AssignSheet']}}/{{$p_data['tt']}}">
                                                {{$t['WorkKind'].'-'.$t['CompanyNo'].'-'.$t['AssignSheet']}}
                                            </a>
                                        @endif
                                        <div class="input-group-append">
                                            <span class="input-group-text text-sm">約工到府時間</span>
                                        </div>
                                        <div class="input-group-append">
                                            <select id="expected_select_hour_{{$t['Id']}}" data-id="{{$t['Id']}}" data-date="{{date('Y-m-d',strtotime($t['BookDate']))}}" onchange="ExpectedDate($(this))">
                                                <option value="x">時</option>
                                                @for($i=$t['BookDateHS'];$i<=$t['BookDateHE'];$i++)
                                                    <option value="{{$i}}">{{substr('0'.$i,-2)}}</option>
                                                @endfor
                                            </select>
                                            <select id="expected_select_minute_{{$t['Id']}}" data-id="{{$t['Id']}}" data-date="{{date('Y-m-d',strtotime($t['BookDate']))}}" onchange="ExpectedDate($(this))">
                                                <option value="x">分</option>
                                                @for($i=0;$i<=50;$i+=10)
                                                    <option value="{{$i}}">{{substr('0'.$i,-2)}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body text-primary">
                                <div class="row">
                                    <div class="col-12 p-0 d-inline-block">
                                        <h5 class="card-title text-primary d-inline">
                                            {{date('Y-m-d H:i',strtotime($t['BookDate']))}}
                                        </h5>
                                        @if($t['delayStatus'] === "Y")
                                            <img src="{{asset('img/alert_red.gif')}}" width="20" height="20">
                                        @endif
                                        <h6 class="card-title text-danger d-inline float-right text-warning pr-3 mb-0">
                                            @if(!empty($t['expected']))
                                                約工到府時間:{{date('Y-m-d H:i',strtotime($t['expected']))}}
                                            @endif
                                        </h6>
                                        @foreach($ordListAry as $k2 => $t2)
                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend p-0 col-3">
                                                    <span class="input-group-text w-100">{{$t2}}</span>
                                                </div>
                                                <div class="input-group-append input-group-text p-0 col-9 bg-white d-flow-root w-100" style="white-space:normal;text-align: inherit;">

                                                    @if($k2 === "CustPhoneSummary")
                                                        <ul class="list-group pt-0 pb-0">
                                                            @foreach($t['CustPhoneSummary'] as $phone_type => $phone_number )
                                                                <li class="list-group-item pt-0 pb-0">
                                                                    {{$phone_type}}:{{$phone_number}}
                                                                    <button type="button" class="btn btn-info btn-sm ml-3" onclick="addEventLog('{{$t['CompanyNo']}}','{{$t['CustID']}}','{{$t['AssignSheet']}}','appCallPhone','app撥打電話[{{$phone_number}}]','###','{{$phone_number}}');app.call('tel:{{$phone_number}}')">
                                                                        <svg width="16" height="16" fill="currentColor" class="bi bi-telephone-outbound" viewBox="0 0 16 16">
                                                                            <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511zM11 .5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V1.707l-4.146 4.147a.5.5 0 0 1-.708-.708L14.293 1H11.5a.5.5 0 0 1-.5-.5z"/>
                                                                        </svg>
                                                                    </button>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @elseif($k2 === "subsidAry")
                                                        <ul class="list-group pt-0 pb-0">
                                                            @foreach($t[$k2] as $k3 => $t3)
                                                                <li class="list-group-item pt-0 pb-0">
                                                                    {{ $t3 }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @elseif($k2 === "CMValue")
                                                        <div class="alert @if(isset($t['CMValue']) && explode('M',$t['CMValue'])[0] >= $p_data['BandwidthH']) alert-danger @else alert-primary @endif pt-0 pb-0 mb-0" role="alert">
                                                            {{ $t['CMValue'] }}
                                                        </div>
                                                        <ul class="list-group pt-0 pb-0">
                                                            @foreach(data_get($t,'CMValueBox') as $k3 => $t3)
                                                                <li class="list-group-item pt-0 pb-0">
                                                                    {{ $t3 }}
                                                                </li>
                                                            @endforeach
                                                            @if(isset($t['SaleCampaignAry']['MESH']) && $t['SaleCampaignAry']['MESH'])
                                                                <li class="list-group-item bg-warning pt-0 pb-0">
                                                                    準備[MESH]:{{ $t['SaleCampaignAry']['MESH'] }}台。
                                                                </li>
                                                            @endif
                                                            @if(isset($t['SaleCampaignAry']['AP']) && $t['SaleCampaignAry']['AP'])
                                                                <li class="list-group-item bg-warning pt-0 pb-0">
                                                                    準備[AP]:{{ $t['SaleCampaignAry']['AP'] }}台。
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    @elseif($k2 === "servicenameAry")
                                                        <ul class="list-group pt-0 pb-0">
                                                            @foreach($t[$k2] as $k3 => $t3)
                                                                <li class="list-group-item pt-0 pb-0">
                                                                    {{ $t3 }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @elseif($k2 === "worksheet_workkindAry")
                                                        <ul class="list-group pt-0 pb-0">
                                                            @foreach($t[$k2] as $k3 => $t3)
                                                                <li class="list-group-item pt-0 pb-0 @if(strpos($t3,$t['AssignSheet'].'_') !== false) list-group-item-primary @endif">
                                                                    {{ $t3 }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @elseif($k2 === "InstAddrName")
                                                        {{$t['InstAddrName']}}
                                                        <button type="button" class="btn btn-success btn-sm ml-3" onclick="app.map(encodeURI('{{$t['InstAddrName']}}'))">
                                                            <svg width="16" height="16" fill="currentColor" class="bi bi-map" viewBox="0 0 16 16">
                                                                <path fill-rule="evenodd" d="M15.817.113A.5.5 0 0 1 16 .5v14a.5.5 0 0 1-.402.49l-5 1a.502.502 0 0 1-.196 0L5.5 15.01l-4.902.98A.5.5 0 0 1 0 15.5v-14a.5.5 0 0 1 .402-.49l5-1a.5.5 0 0 1 .196 0L10.5.99l4.902-.98a.5.5 0 0 1 .415.103zM10 1.91l-4-.8v12.98l4 .8V1.91zm1 12.98l4-.8V1.11l-4 .8v12.98zm-6-.8V1.11l-4 .8v12.98l4-.8z"></path>
                                                            </svg>
                                                            地圖
                                                        </button>
                                                    @elseif($k2 === "TotalAmt")
                                                        ${{ $t[$k2] }}
{{--                                                        <button type="button" class="btn btn-success btn-sm ml-3" onclick="getSumAMT('{{ $t['CompanyNo'] }}','{{ $t['WorkSheet'] }}',$(this))">--}}
{{--                                                            查詢--}}
{{--                                                            <svg width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">--}}
{{--                                                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>--}}
{{--                                                            </svg>--}}
{{--                                                        </button>--}}
{{--                                                        <label id="totalamt_{{ $t['CompanyNo'].'_'.$t['WorkSheet'] }}">$???</label>--}}
                                                    @elseif($k2 === "SaleCampaign")
                                                        {{ $t[$k2] }}
                                                        @if(empty($t['PackageName']) === false)
                                                            <hr class="m-0">
                                                            {{ $t['PackageName'] }}
                                                        @endif
                                                    @else
                                                        {{ $t[$k2] }}
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="alert alert-info mb-0" role="alert">工單備註：{{$t['MSComment1']}}</div>
                                        @if($t['WorkKind'] == '5 維修')
                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend p-0 col-3">
                                                    <span class="input-group-text w-100">維修次數</span>
                                                </div>
                                                <div class="input-group-append input-group-text p-0 col-9 bg-white d-flow-root w-100" style="white-space:normal;text-align: inherit;">
                                                    <ul class="list-group pt-0 pb-0">
                                                        <li class="list-group-item pt-0 pb-0">
                                                            @if($t['COUNT07'])
                                                                <button type="button" class="btn btn-info btn-sm" name="maintainCount" data-count="7" data-so="{{$t['CompanyNo']}}" data-custid="{{$t['CustID']}}">
                                                                    7天內維修次數{{ $t['COUNT07'] }}
                                                                </button>
                                                            @else
                                                                7天內維修次數0
                                                            @endif
                                                        </li>
                                                        <li class="list-group-item pt-0 pb-0">
                                                            @if($t['COUNT30'])
                                                                <button type="button" class="btn btn-success btn-sm" name="maintainCount" data-count="30" data-so="{{$t['CompanyNo']}}" data-custid="{{$t['CustID']}}">
                                                                    30天內維修次數{{ $t['COUNT30'] }}
                                                                </button>
                                                            @else
                                                                30天內維修次數0
                                                            @endif
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
											@foreach($t['CustTagContents'] as $cust_tag_content)
                                                <div class="alert alert-danger mb-0" role="alert">提醒事項：<br>{{$cust_tag_content}}</div>
                                            @endforeach
                                        @endif
                                        @if($t['alert_C000003'] === 'Y')
                                            <div class="alert alert-danger mb-0" role="alert">提醒事項：<br>此用戶為尊榮用戶，若用戶欲使用WiFi單購案，請推尊榮方案。</div>
                                        @endif
                                        @if($t['chkChargeNameAlert0701'] === 'Y')
                                            <div class="alert alert-danger mb-0" role="alert">提醒事項：<br>智慧遙控器和ATV 6010機種仍有部份不相容狀況，請僅搭配ATV 6252或9642。</div>
                                        @endif
                                        @if(empty($t['alert135']) === false)
                                            <div class="alert alert-danger mb-0" role="alert">提醒事項：<br>{{ $t['alert135'] }}</div>
                                        @endif
                                        @if(!empty($t['fault']))
                                            <div class="alert alert-danger mb-0" role="alert">
                                                區故：{{ $t['fault'] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--      @endfor--}}
                @endforeach
            @else
                查無[未完工]資料
            @endif
        </div>

        <div class="container bg-grey collapse" id="unPlanList" name="divpage">
            @if(sizeof($p_data['unplan_list']) > 0)
                @foreach($p_data['unplan_list'] as $k=>$t)
                    <div class="col order" title="order" id="order{{$t['AssignSheet']}}" data-workkind="{{ $t['WorkKind'] }}">
                        <div class="card border-info mb-2">
                            <div class="card-header ">
                                <div class="row">
                                    <div class="input-group ">
                                        @if(!empty($t['faultDetail']))
                                            <a class="btn btn-danger ml-3 mr-3"
                                               href="/ewo/order_info/{{$t['CompanyNo'].'-'.$t['AssignSheet']}}/{{$p_data['tt']}}">
                                                {{$t['WorkKind'].'-'.$t['CompanyNo'].'-'.$t['AssignSheet']}}({{ $t['faultDetail'] }})
                                            </a>
                                        @else
                                            <a class="btn btn-outline-info ml-3 mr-3"
                                               href="/ewo/order_info/{{$t['CompanyNo'].'-'.$t['AssignSheet']}}/{{$p_data['tt']}}">
                                                {{$t['WorkKind'].'-'.$t['CompanyNo'].'-'.$t['AssignSheet']}}
                                            </a>
                                        @endif
                                        <div class="input-group-append">
                                            <span class="input-group-text text-sm">約工到府時間</span>
                                        </div>
                                        <div class="input-group-append">
                                            <select id="expected_select_hour_{{$t['Id']}}" data-id="{{$t['Id']}}" data-date="{{date('Y-m-d',strtotime($t['BookDate']))}}" onchange="ExpectedDate($(this))">
                                                <option value="x">時</option>
{{--                                                @for($i=0;$i<=23;$i++)--}}
                                                @for($i=$t['BookDateHS'];$i<=$t['BookDateHE'];$i++)
                                                    <option value="{{$i}}">{{substr('0'.$i,-2)}}</option>
                                                @endfor
                                            </select>
                                            <select id="expected_select_minute_{{$t['Id']}}" data-id="{{$t['Id']}}" data-date="{{date('Y-m-d',strtotime($t['BookDate']))}}" onchange="ExpectedDate($(this))">
                                                <option value="x">分</option>
                                                @for($i=0;$i<=50;$i+=10)
                                                    <option value="{{$i}}">{{substr('0'.$i,-2)}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="card-body text-info">
                                <div class="row">
                                    <div class="col-12 p-0 d-inline-block">
                                        <h5 class="card-title text-info d-inline">
                                            {{date('Y-m-d H:i',strtotime($t['BookDate']))}}
                                        </h5>
                                        @if($t['delayStatus'] === "Y")
                                            <img  src="{{asset('img/alert_red.gif')}}" width="20" height="20">
                                        @endif
                                        <h6 class="card-title text-danger d-inline float-right text-warning pr-3 mb-0">
                                            @if(!empty($t['expected']))
                                                約工到府時間:{{date('Y-m-d H:i',strtotime($t['expected']))}}
                                            @endif
                                        </h6>
                                        @foreach($ordListAry as $k2 => $t2)
                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend p-0 col-3">
                                                    <span class="input-group-text w-100">{{$t2}}</span>
                                                </div>
                                                <div class="input-group-append input-group-text p-0 col-9 bg-white d-flow-root w-100" style="white-space:normal;text-align: inherit;">

                                                    @if($k2 === "CustPhoneSummary")
                                                        <ul class="list-group pt-0 pb-0">
                                                            @foreach($t['CustPhoneSummary'] as $phone_type => $phone_number )
                                                                <li class="list-group-item pt-0 pb-0">
                                                                    {{$phone_type}}:{{$phone_number}}
                                                                    <button type="button" class="btn btn-info btn-sm ml-3" onclick="addEventLog('{{$t['CompanyNo']}}','{{$t['CustID']}}','{{$t['AssignSheet']}}','appCallPhone','app撥打電話[{{$phone_number}}]','###','{{$phone_number}}');app.call('tel:{{$phone_number}}')">
                                                                        <svg width="16" height="16" fill="currentColor" class="bi bi-telephone-outbound" viewBox="0 0 16 16">
                                                                            <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511zM11 .5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V1.707l-4.146 4.147a.5.5 0 0 1-.708-.708L14.293 1H11.5a.5.5 0 0 1-.5-.5z"/>
                                                                        </svg>
                                                                    </button>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @elseif($k2 === "subsidAry")
                                                        <ul class="list-group pt-0 pb-0">
                                                            @foreach($t[$k2] as $k3 => $t3)
                                                                <li class="list-group-item pt-0 pb-0">
                                                                    {{ $t3 }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @elseif($k2 === "CMValue")
                                                        <div class="alert @if(isset($t['CMValue']) && explode('M',$t['CMValue'])[0] >= $p_data['BandwidthH']) alert-danger @else alert-primary @endif pt-0 pb-0 mb-0" role="alert">
                                                            {{ $t['CMValue'] }}
                                                        </div>
                                                        <ul class="list-group pt-0 pb-0">
                                                            @foreach(data_get($t,'CMValueBox') as $k3 => $t3)
                                                                <li class="list-group-item pt-0 pb-0">
                                                                    {{ $t3 }}
                                                                </li>
                                                            @endforeach
                                                            @if(isset($t['SaleCampaignAry']['MESH']) && $t['SaleCampaignAry']['MESH'])
                                                                <li class="list-group-item bg-warning pt-0 pb-0">
                                                                    準備[MESH]:{{ $t['SaleCampaignAry']['MESH'] }}台。
                                                                </li>
                                                            @endif
                                                            @if(isset($t['SaleCampaignAry']['AP']) && $t['SaleCampaignAry']['AP'])
                                                                <li class="list-group-item bg-warning pt-0 pb-0">
                                                                    準備[AP]:{{ $t['SaleCampaignAry']['AP'] }}台。
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    @elseif($k2 === "servicenameAry")
                                                        <ul class="list-group pt-0 pb-0">
                                                            @foreach($t[$k2] as $k3 => $t3)
                                                                <li class="list-group-item pt-0 pb-0">
                                                                    {{ $t3 }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @elseif($k2 === "worksheet_workkindAry")
                                                        <ul class="list-group pt-0 pb-0">
                                                            @foreach($t[$k2] as $k3 => $t3)
                                                                <li class="list-group-item pt-0 pb-0 @if(strpos($t3,$t['AssignSheet'].'_') !== false) list-group-item-primary @endif">
                                                                    {{ $t3 }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @elseif($k2 === "InstAddrName")
                                                        {{$t['InstAddrName']}}
                                                        <button type="button" class="btn btn-success btn-sm ml-3" onclick="app.map(encodeURI('{{$t['InstAddrName']}}'))">
                                                            <svg width="16" height="16" fill="currentColor" class="bi bi-map" viewBox="0 0 16 16">
                                                                <path fill-rule="evenodd" d="M15.817.113A.5.5 0 0 1 16 .5v14a.5.5 0 0 1-.402.49l-5 1a.502.502 0 0 1-.196 0L5.5 15.01l-4.902.98A.5.5 0 0 1 0 15.5v-14a.5.5 0 0 1 .402-.49l5-1a.5.5 0 0 1 .196 0L10.5.99l4.902-.98a.5.5 0 0 1 .415.103zM10 1.91l-4-.8v12.98l4 .8V1.91zm1 12.98l4-.8V1.11l-4 .8v12.98zm-6-.8V1.11l-4 .8v12.98l4-.8z"></path>
                                                            </svg>
                                                            地圖
                                                        </button>

                                                    @elseif($k2 === "TotalAmt")
                                                        ${{ $t[$k2] }}
{{--                                                        <button type="button" class="btn btn-success btn-sm ml-3" onclick="getSumAMT('{{ $t['CompanyNo'] }}','{{ $t['WorkSheet'] }}',$(this))">--}}
{{--                                                            查詢--}}
{{--                                                            <svg width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">--}}
{{--                                                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>--}}
{{--                                                            </svg>--}}
{{--                                                        </button>--}}
{{--                                                        <label id="totalamt_{{ $t['CompanyNo'].'_'.$t['WorkSheet'] }}">$???</label>--}}
                                                    @elseif($k2 === "SaleCampaign")
                                                        {{ $t[$k2] }}
                                                        @if(empty($t['PackageName']) === false)
                                                            <hr class="m-0">
                                                            {{ $t['PackageName'] }}
                                                        @endif
                                                    @else
                                                        {{ $t[$k2] }}
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="alert alert-info mb-0" role="alert">工單備註：{{$t['MSComment1']}}</div>
                                        @if($t['WorkKind'] == '5 維修')
                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend p-0 col-3">
                                                    <span class="input-group-text w-100">維修次數</span>
                                                </div>
                                                <div class="input-group-append input-group-text p-0 col-9 bg-white d-flow-root w-100" style="white-space:normal;text-align: inherit;">
                                                    <ul class="list-group pt-0 pb-0">
                                                        <li class="list-group-item pt-0 pb-0">
                                                            @if($t['COUNT07'])
                                                            <button type="button" class="btn btn-info btn-sm" name="maintainCount" data-count="7" data-so="{{$t['CompanyNo']}}" data-custid="{{$t['CustID']}}">
                                                                7天內維修次數{{ $t['COUNT07'] }}
                                                            </button>
                                                            @else
                                                                7天內維修次數0
                                                            @endif
                                                        </li>
                                                        <li class="list-group-item pt-0 pb-0">
                                                            @if($t['COUNT30'])
                                                                <button type="button" class="btn btn-success btn-sm" name="maintainCount" data-count="30" data-so="{{$t['CompanyNo']}}" data-custid="{{$t['CustID']}}">
                                                                    30天內維修次數{{ $t['COUNT30'] }}
                                                                </button>
                                                            @else
                                                                30天內維修次數0
                                                            @endif
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
											@foreach($t['CustTagContents'] as $cust_tag_content)
                                                <div class="alert alert-danger mb-0" role="alert">提醒事項：<br>{{$cust_tag_content}}</div>
                                            @endforeach
                                        @endif
                                        @if($t['alert_C000003'] === 'Y')
                                            <div class="alert alert-danger mb-0" role="alert">提醒事項：<br>此用戶為尊榮用戶，若用戶欲使用WiFi單購案，請推尊榮方案。</div>
                                        @endif
                                        @if($t['chkChargeNameAlert0701'] === 'Y')
                                            <div class="alert alert-danger mb-0" role="alert">提醒事項：<br>智慧遙控器和ATV 6010機種仍有部份不相容狀況，請僅搭配ATV 6252或9642。</div>
                                        @endif
                                        @if(empty($t['alert135']) === false)
                                            <div class="alert alert-danger mb-0" role="alert">提醒事項：<br>{{ $t['alert135'] }}</div>
                                        @endif
                                        @if(!empty($t['fault']))
                                            <div class="alert alert-danger mb-0" role="alert">
                                                區故:{{ $t['fault'] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--      @endfor--}}
                @endforeach
            @else
                查無[未完工的未約件]資料
            @endif
        </div>

        <div class="container bg-grey collapse" id="finshOrderList" name="divpage">
            @if(sizeof($p_data['fin_list']) > 0)
                @foreach($p_data['fin_list'] as $k=>$t)
                    <div class="col order" title="order" id="order{{$t['AssignSheet']}}" data-workkind="{{ $t['WorkKind'] }}">
                        <div class="card border-secondary mb-2">
                            <div class="card-header ">
                                <div class="row">
                                    <div class="input-group ">
                                        @if($t['SheetStatus'] === 'A.取消')
                                            {{$t['WorkKind'].'-'.$t['CompanyNo'].'-'.$t['WorkSheet']}}[退單]
                                        @else
                                            <a class="btn btn-outline-secondary ml-3 mr-3"
                                               href="/ewo/order_info/{{$t['CompanyNo'].'-'.$t['AssignSheet']}}/{{$p_data['tt']}}">
                                                {{$t['WorkKind'].'-'.$t['CompanyNo'].'-'.$t['AssignSheet']}}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-body text-secondary">
                                <div class="row">
                                    <div class="col-12 p-0 d-inline-block">
                                        <h5 class="card-title text-secondary d-inline">
                                            {{date('Y-m-d H:i',strtotime($t['BookDate']))}}
                                        </h5>
                                        <h6 class="card-title text-danger d-inline float-right text-warning pr-3 mb-0">
                                            @if(!empty($t['expected']))
                                                約工到府時間:{{date('Y-m-d H:i',strtotime($t['expected']))}}
                                            @endif
                                        </h6>
                                        @foreach($ordListAry as $k2 => $t2)
                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend p-0 col-3">
                                                    <span class="input-group-text w-100">{{$t2}}</span>
                                                </div>
                                                <div class="input-group-append input-group-text p-0 col-9 bg-white d-flow-root w-100" style="white-space:normal;text-align: inherit;">
                                                    @if($k2 === "TotalAmt")
                                                        $
                                                    @endif

                                                    @if($k2 === "CustPhoneSummary")
                                                        <ul class="list-group pt-0 pb-0">
                                                            @foreach($t['CustPhoneSummary'] as $phone_type => $phone_number )
                                                                <li class="list-group-item pt-0 pb-0">
                                                                    {{$phone_type}}:{{$phone_number}}
                                                                    <button type="button" class="btn btn-info btn-sm ml-3" onclick="addEventLog('{{$t['CompanyNo']}}','{{$t['CustID']}}','{{$t['AssignSheet']}}','appCallPhone','app撥打電話[{{$phone_number}}]','###','{{$phone_number}}');app.call('tel:{{$phone_number}}')">
                                                                        <svg width="16" height="16" fill="currentColor" class="bi bi-telephone-outbound" viewBox="0 0 16 16">
                                                                            <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511zM11 .5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V1.707l-4.146 4.147a.5.5 0 0 1-.708-.708L14.293 1H11.5a.5.5 0 0 1-.5-.5z"/>
                                                                        </svg>
                                                                    </button>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @elseif($k2 === "subsidAry")
                                                        <ul class="list-group pt-0 pb-0">
                                                            @foreach($t[$k2] as $k3 => $t3)
                                                                <li class="list-group-item pt-0 pb-0">
                                                                    {{ $t3 }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @elseif($k2 === "CMValue")
                                                            <div class="alert @if(isset($t['CMValue']) && explode('M',$t['CMValue'])[0] >= $p_data['BandwidthH']) alert-danger @else alert-primary @endif pt-0 pb-0 mb-0" role="alert">
                                                                {{ $t['CMValue'] }}
                                                            </div>
                                                        <ul class="list-group pt-0 pb-0">
                                                            @foreach(data_get($t,'CMValueBox') as $k3 => $t3)
                                                                <li class="list-group-item pt-0 pb-0">
                                                                    {{ $t3 }}
                                                                </li>
                                                            @endforeach
                                                            @if(isset($t['SaleCampaignAry']['MESH']) && $t['SaleCampaignAry']['MESH'])
                                                                <li class="list-group-item bg-warning pt-0 pb-0">
                                                                    準備[MESH]:{{ $t['SaleCampaignAry']['MESH'] }}台。
                                                                </li>
                                                            @endif
                                                            @if(isset($t['SaleCampaignAry']['AP']) && $t['SaleCampaignAry']['AP'])
                                                                <li class="list-group-item bg-warning pt-0 pb-0">
                                                                    準備[AP]:{{ $t['SaleCampaignAry']['AP'] }}台。
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    @elseif($k2 === "servicenameAry")
                                                        <ul class="list-group pt-0 pb-0">
                                                            @foreach($t[$k2] as $k3 => $t3)
                                                                <li class="list-group-item pt-0 pb-0">
                                                                    {{ $t3 }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @elseif($k2 === "worksheet_workkindAry")
                                                        <ul class="list-group pt-0 pb-0">
                                                            @foreach($t[$k2] as $k3 => $t3)
                                                                <li class="list-group-item pt-0 pb-0 @if(strpos($t3,$t['AssignSheet'].'_') !== false) list-group-item-primary @endif">
                                                                    {{ $t3 }}
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @elseif($k2 === "InstAddrName")
                                                        {{$t['InstAddrName']}}
                                                        <button type="button" class="btn btn-success btn-sm ml-3" onclick="app.map(encodeURI('{{$t['InstAddrName']}}'))">
                                                            <svg width="16" height="16" fill="currentColor" class="bi bi-map" viewBox="0 0 16 16">
                                                                <path fill-rule="evenodd" d="M15.817.113A.5.5 0 0 1 16 .5v14a.5.5 0 0 1-.402.49l-5 1a.502.502 0 0 1-.196 0L5.5 15.01l-4.902.98A.5.5 0 0 1 0 15.5v-14a.5.5 0 0 1 .402-.49l5-1a.5.5 0 0 1 .196 0L10.5.99l4.902-.98a.5.5 0 0 1 .415.103zM10 1.91l-4-.8v12.98l4 .8V1.91zm1 12.98l4-.8V1.11l-4 .8v12.98zm-6-.8V1.11l-4 .8v12.98l4-.8z"></path>
                                                            </svg>
                                                            地圖
                                                        </button>
                                                    @elseif($k2 === "TotalAmt")
                                                        {{ $t[$k2] }}{{ $t['receiveType']== '1'? '[刷卡]' : '[現金]'}}
                                                    @elseif($k2 === "SaleCampaign")
                                                        {{ $t[$k2] }}
                                                        @if(empty($t['PackageName']) === false)
                                                            <hr class="m-0">
                                                            {{ $t['PackageName'] }}
                                                        @endif
                                                    @else
                                                        {{ $t[$k2] }}
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="alert alert-secondary mb-0" role="alert">工單備註：{{$t['MSComment1']}}</div>
                                        @if($t['WorkKind'] == '5 維修')
                                            <div class="input-group input-group-sm mb-1">
                                                <div class="input-group-prepend p-0 col-3">
                                                    <span class="input-group-text w-100">維修次數</span>
                                                </div>
                                                <div class="input-group-append input-group-text p-0 col-9 bg-white d-flow-root w-100" style="white-space:normal;text-align: inherit;">
                                                    <ul class="list-group pt-0 pb-0">
                                                        <li class="list-group-item pt-0 pb-0">
                                                            @if($t['COUNT07'])
                                                                <button type="button" class="btn btn-info btn-sm" name="maintainCount" data-count="7" data-so="{{$t['CompanyNo']}}" data-custid="{{$t['CustID']}}">
                                                                    7天內維修次數{{ $t['COUNT07'] }}
                                                                </button>
                                                            @else
                                                                7天內維修次數0
                                                            @endif
                                                        </li>
                                                        <li class="list-group-item pt-0 pb-0">
                                                            @if($t['COUNT30'])
                                                                <button type="button" class="btn btn-success btn-sm" name="maintainCount" data-count="30" data-so="{{$t['CompanyNo']}}" data-custid="{{$t['CustID']}}">
                                                                    30天內維修次數{{ $t['COUNT30'] }}
                                                                </button>
                                                            @else
                                                                30天內維修次數0
                                                            @endif
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
											@foreach($t['CustTagContents'] as $cust_tag_content)
                                                <div class="alert alert-danger mb-0" role="alert">提醒事項：<br>{{$cust_tag_content}}</div>
                                            @endforeach
                                        @endif
                                        @if($t['alert_C000003'] === 'Y')
                                            <div class="alert alert-danger mb-0" role="alert">提醒事項：<br>此用戶為尊榮用戶，若用戶欲使用WiFi單購案，請推尊榮方案。</div>
                                        @endif
                                        @if($t['chkChargeNameAlert0701'] === 'Y')
                                            <div class="alert alert-danger mb-0" role="alert">提醒事項：<br>智慧遙控器和ATV 6010機種仍有部份不相容狀況，請僅搭配ATV 6252或9642。</div>
                                        @endif
                                        @if(empty($t['alert135']) === false)
                                            <div class="alert alert-danger mb-0" role="alert">提醒事項：<br>{{ $t['alert135'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--      @endfor--}}
                @endforeach
            @else
                查無[完工]資料
            @endif
        </div>

        <div class="container bg-grey collapse" id="STATISTICS" name="divpage">
            <div class="card">
                <div class="card-header h4">
                    <label class="mb-0 pr-3">收取款項</label>
                    <span class="badge badge-warning badge-pill">$2500</span>
                </div>
                <div class="card-body m-0 p-0">
                    <ul class="list-group">
                        <li class="h4 list-group-item d-flex justify-content-between align-items-center">
                            港都=>A2021010006266=>1 CATV=>王再添
                            <span class="badge badge-warning badge-pill">$2500</span>
                        </li>
                        <li class="h4 list-group-item d-flex justify-content-between align-items-center">
                            雙子星=>A2021010005002=>1 CATV=>李邱淨娥
                            <span class="badge badge-warning badge-pill">$2500</span>
                        </li>
                        <li class="h4 list-group-item d-flex justify-content-between align-items-center">
                            慶聯	=>A2021010024649=>1 CATV, 3 DSTB=>黃柏勳
                            <span class="badge badge-warning badge-pill">$4500</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card m-0 p-0">
                <div class="card-header h4">
                    <label class="mb-0">五金耗料</label>
                </div>
                <div class="card-body m-0 p-0">
                    <ul class="list-group">
                        <li class="h4 list-group-item d-flex justify-content-between align-items-center">
                            返向衰減器(普京)(6DB)	31-RSA258-06
                            <span class="badge badge-primary badge-pill">14</span>
                        </li>
                        <li class="h4 list-group-item d-flex justify-content-between align-items-center">
                            數位機上盒遙控器(HIYE)	RCH01B
                            <span class="badge badge-primary badge-pill">2</span>
                        </li>
                        <li class="h4 list-group-item d-flex justify-content-between align-items-center">
                            5C無Y單網雙鋁同軸電纜100%(DK)	RG6-T77F PVC
                            <span class="badge badge-primary badge-pill">1</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card m-0 p-0">
                <div class="card-header h4">
                    <label class="mb-0">字形大小</label>
                </div>
                <div class="card-body m-0 p-0">
                    <ul class="list-group">
                        @for($i=20; $i<62; $i+=2)
                            <li class="list-group-item d-flex justify-content-between align-items-center" style="font-size: {{$i}}px">
                                {{$i}}
                            </li>
                        @endfor
                    </ul>
                </div>
            </div>

        </div>


        {{-- alertBodLoad --}}
        <div class="container alert alert-info text-center" style="display: none;" id="alertBodLoad">
            資料查詢中
            <div class="d-flex justify-content-center">
                <div class="spinner-border">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>

        <div class="modal fade" id="lodIng" role="dialog" data-backdrop="static">
            <div class="modal-dialog2 modal-sm modal-dialog-centered" role="document">
                <div class="modal-content2">
                    <div class="modal-body">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <div class="showStr">
                                資料查詢中...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- 清單 -->
        <div class="modal fade" id="list_modal" role="dialog">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">...</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        查詢中...
                    </div>
                    <div class="modal-footer">
                        <label>...</label>
                    </div>
                </div>
            </div>
        </div>


    </main>
@endsection

@section('script')
    <script>
        $(document).ready(function () {

            // 重整，切換selected
            if(10) {
                var sel = '{{$p_data['select']}}';

                if(sel.length > 0) {
                    $("#soSelect option[value='"+sel+"']").attr('selected',true);
                    soSelectChange(sel)
                }
            }


            // 清單列表，重整，抓取下拉選單
            $('#reload').click(function(){
                var select_val = $('#soSelect').val();
                var pathname = window.location.pathname;
                var origin   = window.location.origin;
                window.location = origin + pathname + '?sel='+select_val;
            });

            // 功能列，自動調整高度
            if(true) {
                $('main').css('padding-top',$('#header_list_div').height() + parseInt('20')+'px')
                $(window).on("orientationchange",function(event){
                    $('main').css('padding-top',$('#header_list_div').height() + parseInt('20')+'px')
                });
            }

            ajaxStat = 0; // ajax 開關
            pageType = "UNFIN"; //分頁標籤

            fpage = {{$p_data['finPage']}};
            fpageAll = {{$p_data['finPageAll']}};

            ufpage = {{$p_data['unFinPage']}};
            ufpageAll = {{$p_data['unFinPageAll']}};

            unPlanPage = {{$p_data['unplanPage']}};
            unPlanPageAll = {{$p_data['unplanPageAll']}};

            ordListAry = [
                ['CustName' , '客戶名稱'],
                ['CustID' , '住編'],
                ['subsidAry' , '訂編清單'],
                ['worksheet_workkindAry' , '工單號清單'],
                ['CustPhoneSummary' , '客戶電話'],
                ['CMValue' , '速率別'],
                ['TotalAmt' , '應收金額'],
                ['InstAddrName' , '安裝地址'],
                ['servicenameAry' , '服務別'],
                ['SaleCampaign' , '方案別']
            ];

            // 卷軸到底
            $(window).scroll(function () {
                var last = $("body").height() - $(window).height() - 5;
                // console.log('chk scroll scrollTop=='+$(window).scrollTop()+'; last=='+last+';ajaxStat=='+ajaxStat);
                if ($(window).scrollTop() >= last && ajaxStat < 1) {
                    // console.log('chk scroll ajaxStat=='+ajaxStat+"; pageType=="+pageType+";last=="+last+';scrollTop=='+$(window).scrollTop()+';pageType=='+pageType);
                    // console.log('chk unPlanPage=='+unPlanPage+"; unPlanPageAll=="+unPlanPageAll);
                    if (pageType === "FIN" && fpage < fpageAll) {
                        fpage += 1;
                        ajaxStat = 1;
                        searchAjax('ADD', pageType);
                    } else if (pageType === "UNFIN" && ufpage < ufpageAll) {
                        ufpage += 1;
                        ajaxStat = 1;
                        searchAjax('ADD', pageType);
                    } else if (pageType === "UNPLAN" && unPlanPage < unPlanPageAll) {
                        unPlanPage += 1;
                        ajaxStat = 1;
                        searchAjax('ADD', pageType);
                    } else return;

                    $('#alertBodLoad').show();
                }
            });

            // 查詢類別
            $('#searchType').change(function(){
                var strText = $(this).find('option:selected').text() + '...', strVal = $(this).find('option:selected').val();
                $('#searchVal').attr('placeholder',strText).attr('maxlength',strVal);
            })

            // 鬧鈴提示
            @foreach($p_data['alertList'] as $k => $t)
            Alarm('{{ $t['title'] }}','{{ $t['body'] }}',{{ $t['sec'] }},{{ $t['id'] }});

                {{--Alarm('{{ $t['title'].';02' }}', '{{ $t['body'].';02' }}', {{ ($t['sec']+4) }}, {{ $t['id'].'2' }});--}}
                {{--Alarm('{{ $t['title'].';03' }}', '{{ $t['body'].';03' }}', {{ ($t['sec']+8) }}, {{ $t['id'].'3' }});--}}
                {{--Alarm('{{ $t['title'].';04' }}', '{{ $t['body'].';04' }}', {{ ($t['sec']+12) }}, {{ $t['id'].'4' }});--}}
                {{--Alarm('{{ $t['title'].';05' }}', '{{ $t['body'].';05' }}', {{ ($t['sec']+16) }}, {{ $t['id'].'5' }});--}}

            @endforeach


            // 勞安照片
            @if($p_data['lsImg'] === 'N')
                $('#soSelect').val('laborsafety');

                // // 沒上傳勞安照片，不能操作
                // $('#soSelect').prop('disabled',true);
                // $('button[name="headerBtn"]').prop('disabled',true);

                searchBtn('laborsafety');
            @endif


            // 維修7-30天內維修清單
            $("button[name='maintainCount']").click(function(){
                let countType = $(this).data('count');
                let custId = $(this).data('custid');
                let url = '/api/EWO/getMaintainList';
                let data = {
                    'companyNo' : $(this).data('so'),
                    'countType' : countType,
                    'custId' : custId,
                };
                let params = JSON.stringify(Object.assign({}, data));

                $.ajax({
                    url: url,
                    data: params,
                    dataType: 'json',
                    contentType: 'application/json',
                    type: "POST",
                    headers: {'X-CSRF-TOKEN': $('#p_token').val()},
                    success: function (result) {

                        if(result.code != '0000') {
                            alert('查詢失敗#855;');
                            return;
                        }
                        editMaintainListHtml(result,countType);

                    },
                    error: function () {
                        alert('查詢失敗#860;');
                    }
                });
            });


        }); // end ready


        // 維修清單HTML
        function editMaintainListHtml(data,countType) {
            let time = data.date;
            $('#list_modal .modal-footer label').html(time);
            let title = '前'+countType+'天維修紀錄清單';
            $('#list_modal .modal-header h4').html(title);

            let listStr = ``;
            listStr += `
                <table class="table table-sm table-hover table-striped table-bordered" id="dataList">
                    <thead>
                        <tr>
                            <th scope="col">#<\/th>
                            <th scope="col">公司別<\/th>
                            <th scope="col">住編<\/th>
                            <th scope="col">訂編<\/th>
                            <th scope="col">服務別<\/th>
                            <th scope="col">工程<\/th>
                            <th scope="col">來電原因<\/th>
                            <th scope="col">完工原因1<\/th>
                            <th scope="col">完工原因2<\/th>
                            <th scope="col">預約時間<\/th>
                        <\/tr>
                    <\/thead>
                    <tbody>`;

            Object.entries(data.data).forEach(entry => {
                let [k, t] = entry;

                listStr += `
                    <tr>
                        <td>${ parseInt(k)+parseInt(1)}<\/td>
                        <td>${t['CompanyNo']}<\/td>
                        <td>${t['CustId']}<\/td>
                        <td>${t['SubsId']}<\/td>
                        <td>${t['ServiceName']}<\/td>
                        <td>${t['Worker1']}<\/td>
                        <td>${t['WorkCause']}<\/td>
                        <td>${t['BackCause']}<\/td>
                        <td>${t['CleanCause']}<\/td>
                        <td>${t['BookDate']}<\/td>
                    <\/tr>`;

            });

            listStr += `
                    <\/tbody>
                <\/table> `;

            $('#list_modal .modal-body').html(listStr);
            $('#list_modal').modal('show');

        }


        // 工單篩選
        function orderFilter(id) {
            // let id = $(this).attr('id');
            let type = $('#'+id).prop('checked');
            let workKindAry = [];
            workKindAry['btnIns'] = '{{ implode(',',config('order.workKindIns')) }}';
            workKindAry['btnDel'] = '{{ implode(',',config('order.workKindDel')) }}';
            workKindAry['btnMai'] = '{{ implode(',',config('order.workKindMai')) }}';
            console.log(workKindAry[id]);
            console.log('type='+type);

            $('.col.order').each(function(){

                let workkind = $(this).data('workkind')
                console.log(workkind)

                if(workKindAry[id].search(workkind) >= 0) {
                    if(type == true)
                        $(this).hide();
                    else
                        $(this).show();
                }
            })
        }


        // Expected 切換[到府時間]功能
        function changExpectedDate(objId) {
            var idAry = objId.split('_');
            console.log('changExpectedDate=='+idAry[0])
            if(idAry[0] === "h6") {
                $('#h6_'+idAry[1]).addClass('d-none');
                $('#div_'+idAry[1]).removeClass('d-none');
            } else if(idAry[0] === 'select') {
                $('#h6_'+idAry[1]).removeClass('d-none');
                $('#select_'+idAry[1]).addClass('d-none');
            }
        }

        // 鬧鈴提示
        function Alarm(title, msg, sec, channel) {
            try {
                app.Alarm(title, msg, sec, channel);
            } catch (e) {
                //
            }
        }

        // ajax to search
        function searchAjax(delStat = '', pageTypeVal = '') {
            //console.log('chk func searchAjax;; delStat=='+delStat+';; ajaxStat=='+ajaxStat)
            if(ajaxStat < 1) return;
            var url = '/ewo/order_list/search/' + "{{ $p_data['tt'] }}",
                workKind = $('#soSelect').val(),
                workSheet = $('#workSheet').val(),
                data = JSON.stringify({
                    "workKind": workKind,
                    "finPage": fpage,
                    "unFinPage": ufpage,
                    "unPlanPage": unPlanPage,
                    "worksheet": 'null',
                    "finish": 0,
                });
            // console.log('chk func searchAjax data==' + data+';unPlanPage=='+unPlanPage);

            $.ajax({
                url: url, //請求的url地址
                dataType: "json", //返回格式為json
                async: true, //請求是否非同步，預設為非同步，這也是ajax重要特性
                contentType: 'application/json; charset=utf-8', // 要送到server的資料型態
                data: data, //引數值
                type: "POST", //請求方式
                {{--headers: {'X-CSRF-TOKEN': "{{ $p_data['tt'] }}"},--}}
                headers: {'X-CSRF-TOKEN': $('#p_token').val()},
                beforeSend: function () {
                    //請求前的處理
                },
                success: function (req) {
                    ajaxStat = 0;
                    console.log('===success ajaxStat===')
                    // console.log(req);
                    if (req['code'] === "0000" && req['status'] === "OK") {
                        fpage = req['data']['finPage'];
                        fpageAll = req['data']['finPageAll'];
                        ufpage = req['data']['unFinPage'];
                        ufpageAll = req['data']['unFinPageAll'];
                        unPlanPage = req['data']['unplanPage'];
                        unPlanPageAll = req['data']['unplanPageAll'];
                        //console.log('chk status === ok;' + delStat)
                        editHtml(req['data'], delStat, pageTypeVal);
                    }
                },
                error: function () {
                    //請求出錯處理
                    ajaxStat = 0;
                    console.log('ajax error;');
                    $('.alert').hide();
                    $('#alertDanger').show();
                }
            });

        }

        // search btn [select change、search text]
        function searchBtn(delStr) {
            var p_searchbtn = $('#soSelect').val();

            var p_stop = soSelectChange(p_searchbtn);
            if(p_stop) return 0;

            $('.alert').hide();
            $('#alertTopLoad').show();
            ajaxStat = 1; // ajax 動作代號
            fpage = 1;
            ufpage = 1;
            unPlanPage = 1;
            searchAjax(delStr);
        }


        // 切換頁面[show/hide]
        function soSelectChange(p_type) {
            TOP();
            $("div[name='divpage']").collapse('hide');

            switch(p_type) {
                case 'all':
                case '1 裝機':
                case '3 拆除':
                case '5 維修':
                    $("#finshOrderList").collapse('show');
                    return 0;
                    break;

                case 'logout':
                    document.location = '/ewo/login';
                    return 1;
                    break;

                case 'appmsg':
                    $("#appMSG").collapse('show');
                    getPushMSG();
                    return 1;
                    break;

                case 'addsign':
                    $("#addSign").collapse('show');
                    return 1;
                    break;

                case 'laborsafety':
                    $("#laborsafety").collapse('show');
                    return 1;
                    break;

                case 'appstatistics':
                    $("#appStatistics").collapse('show');
                    getStatistics();
                    return 1;
                    break;

                case 'appinfo':
                    $("#appInfo").collapse('show');
                    return 1;
                    break;

                case 'plandevice':
                    $("#plandevice").collapse('show');
                    return 1;
                    break;

            }

        }


        // 勞安照片檢查
        function chkLsImg() {
            var chkLS = $('#p_lsImg').val();
            if(chkLS === 'Y') {
                return true;
            }
        }


        // edit html [刪除清單?/寫上新的]
        function editHtml(data, delStat = '', pageTypeVal = '') {
            var finHtmlStr = '',unFinHtmlStr = '',unFinPlanHtmlStr = '';
            let yourDate = new Date();
            let p_ymd = yourDate.toISOString().split('T')[0];
            //console.log('func editHtml delStat==' + delStat +';pageTypeVal=='+pageTypeVal)
            //console.log(data);
            //清除內容
            if (delStat === 'DEL') {
                $('#finshOrderList').children('div').remove();
                $('#unFinshOrderList').children('div').remove();
                $('#unPlanList').children('div').remove();
            }

            // 未完工清單================================================
            if (pageTypeVal === '' || pageTypeVal === 'UNFIN')
                if (data['unFin_list'].length > 0 && data['unFinPage'] > 0) {
                    data['unFin_list'].forEach(function (t) {
                        //console.log('foreach chk unFin_list');
                        unFinHtmlStr +=
                            "<div class=\"col\" title=\"order\">\n" +
                            "  <div class=\"card border-primary mb-2\">\n" +
                            "    <div class=\"card-header order\" data-workkind=\""+ t['WorkKind'] +"\">\n" +
                            "      <div class=\"row\">\n" +
                            "          <div class=\"input-group \">\n" +
                            "            <a class=\"btn btn-outline-primary ml-3 mr-3\"\n" +
                            "               href=\"/ewo/order_info/" + t['CompanyNo'] + "-" + t['AssignSheet'] + "/{{$p_data['tt']}}\">\n" +
                            "                " + t['WorkKind'] + "-" + t['CompanyNo'] + "-" + t['AssignSheet'] + "\n" +
                            "            </a>\n" +
                            "            <div class=\"input-group-append\">\n" +
                            "                <span class=\"input-group-text text-sm\">約工到府時間</span>\n" +
                            "            </div>\n" +
                            "                <select id=\"expected_select_hour_" + t['Id'] + "\" data-id=\"" + t['Id'] + "\" data-date=\"" + t['BookDate'].substr(0,10) + "\" onchange=\"ExpectedDate($(this))\">\n" +
                            "                    <option value=\"x\">時</option>\n";
                        for(var i=t.BookDateHS;i <= t.BookDateHE;i++) {
                            unFinHtmlStr += "<option value=\""+i+"\">"+i+"</option>\n";
                        }
                        unFinHtmlStr +=
                            "                </select>\n" +
                            "                <select id=\"expected_select_minute_" + t['Id'] + "\" data-id=\"" + t['Id'] + "\" data-date=\"" + t['BookDate'].substr(0,10) + "\" onchange=\"ExpectedDate($(this))\">\n" +
                            "                    <option value=\"x\">分</option>\n";
                        for(var i=0;i <= 50;i+=10) {
                            unFinHtmlStr += "<option value=\""+i+"\">"+i+"</option>\n";
                        }
                        unFinHtmlStr +=
                            "                </select>" +
                            "          </div>\n" +
                            "      </div>" +
                            "    </div>\n" +
                            "    <div class=\"card-body text-primary\">\n" +
                            "      <div class=\"row\">\n" +
                            "        <div class=\"col-12 p-0 d-inline-block\">" +
                            "          <h5 class=\"card-title text-primary d-inline\">" + t['BookDate'].substr(0, 16) + "</h5>";
                        if(t.delayStatus === "Y")
                            unFinHtmlStr += "<img src=\"{{asset('img/alert_red.gif')}}\" width=\"20\" height=\"20\">";
                        unFinHtmlStr +=
                            "          <h6 class=\"card-title text-danger d-inline float-right text-warning pr-3 mb-0\">約工到府時間:"+t['expected'].substr(0,16)+"</h6>";
                        ordListAry.forEach(function (t2){
                            unFinHtmlStr +=
                                "<div class=\"input-group input-group-sm mb-1\">\n" +
                                "  <div class=\"input-group-prepend p-0 col-3\">\n" +
                                "    <span class=\"input-group-text w-100\">" + t2[1] + "</span>\n" +
                                "  </div>\n" +
                                "  <div class=\"input-group-append input-group-text p-0 col-9 bg-white d-flow-root w-100\" style=\"white-space:normal;text-align: inherit;\">\n" ;

                            if(t2[0] === 'CustPhoneSummary') {
                                unFinHtmlStr += "<ul class='list-group pt-0 pb-0'>\n";
                                    t[t2[0]]['MS0300Phone'].forEach(function(ms_0300_phone){
                                        unFinHtmlStr += "<li class='list-group-item pt-0 pb-0'>";
                                        unFinHtmlStr += ms_0300_phone;
                                        unFinHtmlStr +=
                                            "<button type=\"button\" class=\"btn btn-info btn-sm ml-3\" onclick=\"addEventLog('"+t['CompanyNo']+"','"+t['CustID']+"','"+t['AssignSheet']+"','appCallPhone','app撥打電話['+ms_0300_phone+']','###','"+ms_0300_phone+"');app.call('tel:" +ms_0300_phone+ "')\">\n" +
                                            "    <svg width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-telephone-outbound\" viewBox=\"0 0 16 16\">\n" +
                                            "        <path d=\"M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511zM11 .5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V1.707l-4.146 4.147a.5.5 0 0 1-.708-.708L14.293 1H11.5a.5.5 0 0 1-.5-.5z\"/>\n" +
                                            "    </svg>\n" +
                                            "</button>";
                                        unFinHtmlStr += "</li>";
                                    });
                                    t[t2[0]]['MS0200Phone'].forEach(function(ms_0200_phone){
                                        unFinHtmlStr += "<li class='list-group-item pt-0 pb-0'>";
                                        unFinHtmlStr += ms_0200_phone;
                                        unFinHtmlStr +=
                                            "<button type=\"button\" class=\"btn btn-info btn-sm ml-3\" onclick=\"addEventLog('"+t['CompanyNo']+"','"+t['CustID']+"','"+t['AssignSheet']+"','appCallPhone','app撥打電話['+ms_0200_phone+']','###','"+ms_0200_phone+"');app.call('tel:" +ms_0200_phone+ "')\">\n" +
                                            "    <svg width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-telephone-outbound\" viewBox=\"0 0 16 16\">\n" +
                                            "        <path d=\"M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511zM11 .5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V1.707l-4.146 4.147a.5.5 0 0 1-.708-.708L14.293 1H11.5a.5.5 0 0 1-.5-.5z\"/>\n" +
                                            "    </svg>\n" +
                                            "</button>";
                                        unFinHtmlStr += "</li>";
                                    });
                                unFinHtmlStr += "</ul>\n";
                            } else if(t2[0] === 'InstAddrName') {
                                unFinHtmlStr += t[t2[0]];
                                unFinHtmlStr +=
                                    "<button type=\"button\" class=\"btn btn-success btn-sm ml-3\" onclick=\"app.map(encodeURI('" + t['InstAddrName'] + "'))\">\n" +
                                    "    <svg width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-map\" viewBox=\"0 0 16 16\">\n" +
                                    "        <path fill-rule=\"evenodd\" d=\"M15.817.113A.5.5 0 0 1 16 .5v14a.5.5 0 0 1-.402.49l-5 1a.502.502 0 0 1-.196 0L5.5 15.01l-4.902.98A.5.5 0 0 1 0 15.5v-14a.5.5 0 0 1 .402-.49l5-1a.5.5 0 0 1 .196 0L10.5.99l4.902-.98a.5.5 0 0 1 .415.103zM10 1.91l-4-.8v12.98l4 .8V1.91zm1 12.98l4-.8V1.11l-4 .8v12.98zm-6-.8V1.11l-4 .8v12.98l4-.8z\"></path>\n" +
                                    "    </svg>\n" +
                                    "    地圖\n" +
                                    "</button>";
                            } else if(t2[0] === 'CMValue') {

                                unFinHtmlStr += '<div class=\"alert ';
                                if(t['CMValue'].length > 0 && (t['CMValue'].split('M'))[0] >= {{ $p_data['BandwidthH'] }}) {
                                    unFinHtmlStr += 'alert-danger ';
                                } else {
                                    unFinHtmlStr += 'alert-primary ';
                                }
                                unFinHtmlStr += 'pt-0 pb-0 mb-0\" role=\"alert\">';
                                unFinHtmlStr += t['CMValue'];
                                unFinHtmlStr += '</div>';

                                unFinHtmlStr += "<ul class=\"list-group pt-0 pb-0\">\n";
                                t['CMValueBox'].forEach(function(t3){
                                    unFinHtmlStr += "<li class=\"list-group-item pt-0 pb-0\">\n";
                                    unFinHtmlStr += t3;
                                    unFinHtmlStr += '</li>';
                                })
                                unFinHtmlStr += '</ul>';
                            } else if(t2[0] === 'servicenameAry' || t2[0] === 'subsidAry') {
                                unFinHtmlStr += '<ul class="list-group pt-0 pb-0">';
                                t[t2[0]].forEach(function(t3){
                                    unFinHtmlStr += '<li class="list-group-item pt-0 pb-0">\n';
                                    unFinHtmlStr += t3;
                                    unFinHtmlStr += '</li>';
                                })
                                unFinHtmlStr += '</ul>';
                            } else if(t2[0] === 'worksheet_workkindAry') {
                                unFinHtmlStr += '' +
                                    '<ul class="list-group pt-0 pb-0">';
                                t[t2[0]].forEach(function(t3){
                                    unFinHtmlStr += '<li class="list-group-item pt-0 pb-0';
                                    if(t3.search((t['AssignSheet']+'_')) >= 0) {
                                        unFinHtmlStr += ' list-group-item-primary ';
                                    }
                                    unFinHtmlStr += '">\n';
                                    unFinHtmlStr += t3;
                                    unFinHtmlStr += '</li>';
                                })
                                unFinHtmlStr += '</ul>';
                            } else if(t2[0] === 'TotalAmt') {
                                unFinHtmlStr += '$'+t[t2[0]];
                                // unFinHtmlStr += '' +
                                //     '<button type="button" class="btn btn-success btn-sm ml-3" onclick="getSumAMT(\''+t['CompanyNo']+'\',\''+t['AssignSheet']+'\',$(this))">\n' +
                                //     '        查詢\n' +
                                //     '        <svg width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">\n' +
                                //     '        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>\n' +
                                //     '        </svg>\n' +
                                //     '</button>';
                                // unFinHtmlStr += '<label id="totalamt_'+t['CompanyNo']+'_'+t['AssignSheet']+'">$???</label>';
                            } else if(t2[0] === 'SaleCampaign') {
                                unFinHtmlStr += t[t2[0]];
                                if(t['PackageName'].length > 0) {
                                    unFinHtmlStr += '<hr class="m-0">';
                                    unFinHtmlStr += t['PackageName'];
                                }
                            } else {
                                unFinHtmlStr += t[t2[0]];
                            }

                            unFinHtmlStr +=
                                "  </div>\n" +
                                "</div>\n";
                        });
                        unFinHtmlStr += "<div class=\"alert alert-info mb-0\" role=\"alert\">工單備註："+t['MSComment1']+"</div>";
                        if(t['alert_C000003'] === 'Y') {
                            unFinHtmlStr += "<div class=\"alert alert-danger mb-0\" role=\"alert\">提醒事項：<br>此用戶為尊榮用戶，若用戶欲使用WiFi單購案，請推尊榮方案。</div>";
                        }
                        if(t['chkChargeNameAlert0701'] === 'Y') {
                            unFinHtmlStr += "<div class=\"alert alert-danger mb-0\" role=\"alert\">提醒事項：<br>智慧遙控器和ATV 6010機種仍有部份不相容狀況，請僅搭配ATV 6252或9642</div>";
                        }
                        if(t['alert135'].length > 0) {
                            unFinHtmlStr += "<div class=\"alert alert-danger mb-0\" role=\"alert\">提醒事項：<br>"+t['alert135']+"</div>";
                        }
                        unFinHtmlStr +=
                            "        </div>\n" +
                            "      </div>\n" +
                            "    </div>\n" +
                            "  </div>\n" +
                            "</div>";
                    });
                    // console.log('html count=='+htmlStr.length);
                } else {
                    unFinHtmlStr +=
                        "<div class=\"container alert alert-warning text-center\">\n" +
                        "  查無[未完工清單]資料\n" +
                        "</div>";
                }

            // 完工清單==================================================
            //console.log('chk editHTML fin_list length=='+data['fin_list'].length+';finPage=='+data['finPage']);
            if (pageTypeVal === '' || pageTypeVal === 'FIN')
                if (data['fin_list'].length > 0 && data['finPage'] > 0) {
                    data['fin_list'].forEach(function (t) {
                        //console.log('foreach chk fin_list');
                        finHtmlStr +=
                            "<div class=\"col\" title=\"order\">\n" +
                            "  <div class=\"card border-secondary mb-2\">\n" +
                            "    <div class=\"card-header order\" data-workkind=\""+ t['WorkKind'] +"\">\n" +
                            "      <div class=\"row\">\n" +
                            "          <div class=\"input-group \">\n";

                        if(t['SheetStatus'] == 'A.取消')
                        	finHtmlStr += t['WorkKind'] + "-" + t['CompanyNo'] + "-" + t['WorkSheet'] + "(退單)\n" ;
                        else
                        	finHtmlStr +=
                                "<a class=\"btn btn-outline-secondary ml-3 mr-3\"\n" +
                                "   href=\"/ewo/order_info/" + t['CompanyNo'] + "-" + t['AssignSheet'] + "/{{$p_data['tt']}}\">\n" +
                                "    " + t['WorkKind'] + "-" + t['CompanyNo'] + "-" + t['AssignSheet'] + "\n" +
                                "</a>\n";

                        finHtmlStr +=
                            "      </div>\n" +
                            "  </div>" +
                            "</div>\n" +
                            "<div class=\"card-body text-secondary\">\n" +
                            "  <div class=\"row\">\n" +
                            "    <div class=\"col-12 p-0 d-inline-block\">" +
                            "      <h5 class=\"card-title text-secondary d-inline mb-0\">" + t['BookDate'].substr(0, 16) + "</h5>\n";
                        ordListAry.forEach(function (t2){
                            finHtmlStr +=
                                "<div class=\"input-group input-group-sm mb-1\">\n" +
                                "  <div class=\"input-group-prepend p-0 col-3\">\n";
                            if(t2[0] === 'TotalAmt')
                                finHtmlStr +=
                                    "    <span class=\"input-group-text w-100\">" + '已收金額' + "</span>\n";
                            else
                                finHtmlStr +=
                                    "    <span class=\"input-group-text w-100\">" + t2[1] + "</span>\n";
                            finHtmlStr +=
                                "  </div>\n" +
                                "  <div class=\"input-group-append input-group-text p-0 col-9 bg-white d-flow-root w-100\" style=\"white-space:normal;text-align: inherit;\">\n" ;
                            if(t2[0] === 'TotalAmt')
                                finHtmlStr += "$";

                            if(t2[0] === 'CustPhoneSummary') {
                                finHtmlStr += "<ul class='list-group pt-0 pb-0'>\n";
                                    t[t2[0]]['MS0300Phone'].forEach(function(ms_0300_phone){
                                        finHtmlStr += "<li class='list-group-item pt-0 pb-0'>";
                                        finHtmlStr += ms_0300_phone;
                                        finHtmlStr +=
                                            "<button type=\"button\" class=\"btn btn-info btn-sm ml-3\" onclick=\"addEventLog('"+t['CompanyNo']+"','"+t['CustID']+"','"+t['AssignSheet']+"','appCallPhone','app撥打電話['+ms_0300_phone+']','###','"+ms_0300_phone+"');app.call('tel:" +ms_0300_phone+ "')\">\n" +
                                            "    <svg width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-telephone-outbound\" viewBox=\"0 0 16 16\">\n" +
                                            "        <path d=\"M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511zM11 .5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V1.707l-4.146 4.147a.5.5 0 0 1-.708-.708L14.293 1H11.5a.5.5 0 0 1-.5-.5z\"/>\n" +
                                            "    </svg>\n" +
                                            "</button>";
                                        finHtmlStr += "</li>";
                                    });
                                    t[t2[0]]['MS0200Phone'].forEach(function(ms_0200_phone){
                                        finHtmlStr += "<li class='list-group-item pt-0 pb-0'>";
                                        finHtmlStr += ms_0200_phone;
                                        finHtmlStr +=
                                            "<button type=\"button\" class=\"btn btn-info btn-sm ml-3\" onclick=\"addEventLog('"+t['CompanyNo']+"','"+t['CustID']+"','"+t['AssignSheet']+"','appCallPhone','app撥打電話['+ms_0200_phone+']','###','"+ms_0200_phone+"');app.call('tel:" +ms_0200_phone+ "')\">\n" +
                                            "    <svg width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-telephone-outbound\" viewBox=\"0 0 16 16\">\n" +
                                            "        <path d=\"M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511zM11 .5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V1.707l-4.146 4.147a.5.5 0 0 1-.708-.708L14.293 1H11.5a.5.5 0 0 1-.5-.5z\"/>\n" +
                                            "    </svg>\n" +
                                            "</button>";
                                        finHtmlStr += "</li>";
                                    });
                                finHtmlStr += "</ul>\n";
                            } else if(t2[0] === 'InstAddrName') {
                                finHtmlStr += t['InstAddrName'];
                                finHtmlStr +=
                                    "<button type=\"button\" class=\"btn btn-success btn-sm ml-3\" onclick=\"app.map(encodeURI('" + t['InstAddrName'] + "'))\">\n" +
                                    "    <svg width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-map\" viewBox=\"0 0 16 16\">\n" +
                                    "        <path fill-rule=\"evenodd\" d=\"M15.817.113A.5.5 0 0 1 16 .5v14a.5.5 0 0 1-.402.49l-5 1a.502.502 0 0 1-.196 0L5.5 15.01l-4.902.98A.5.5 0 0 1 0 15.5v-14a.5.5 0 0 1 .402-.49l5-1a.5.5 0 0 1 .196 0L10.5.99l4.902-.98a.5.5 0 0 1 .415.103zM10 1.91l-4-.8v12.98l4 .8V1.91zm1 12.98l4-.8V1.11l-4 .8v12.98zm-6-.8V1.11l-4 .8v12.98l4-.8z\"></path>\n" +
                                    "    </svg>\n" +
                                    "    地圖\n" +
                                    "</button>";
                            } else if(t2[0] === 'CMValue') {

                                finHtmlStr += '<div class=\"alert ';
                                if(t['CMValue'].length > 0 && (t['CMValue'].split('M'))[0] >= {{ $p_data['BandwidthH'] }}) {
                                    finHtmlStr += 'alert-danger ';
                                } else {
                                    finHtmlStr += 'alert-primary ';
                                }
                                finHtmlStr += 'pt-0 pb-0 mb-0\" role=\"alert\">';
                                finHtmlStr += t['CMValue'];
                                finHtmlStr += '</div>';

                                finHtmlStr += "<ul class=\"list-group pt-0 pb-0\">\n";
                                t['CMValueBox'].forEach(function(t3){
                                    finHtmlStr += "<li class=\"list-group-item pt-0 pb-0\">\n";
                                    finHtmlStr += t3;
                                    finHtmlStr += '</li>';
                                })
                                finHtmlStr += '</ul>';
                            } else if(t2[0] === 'servicenameAry' || t2[0] === 'subsidAry') {
                                finHtmlStr += '<ul class="list-group pt-0 pb-0">';
                                t[t2[0]].forEach(function(t3){
                                    finHtmlStr += '<li class="list-group-item pt-0 pb-0">\n';
                                    finHtmlStr += t3;
                                    finHtmlStr += '</li>';
                                })
                                finHtmlStr += '</ul>';
                            } else if(t2[0] === 'worksheet_workkindAry') {
                                finHtmlStr += '<ul class="list-group pt-0 pb-0">';
                                t[t2[0]].forEach(function(t3){
                                    finHtmlStr += '<li class="list-group-item pt-0 pb-0';
                                    if(t3.search((t['AssignSheet']+'_')) >= 0) {
                                        finHtmlStr += ' list-group-item-primary ';
                                    }
                                    finHtmlStr += '">\n';
                                    finHtmlStr += t3;
                                    finHtmlStr += '</li>';
                                })
                                finHtmlStr += '</ul>';
                            } else if(t2[0] === 'SaleCampaign') {
                                finHtmlStr += t[t2[0]];
                                if(t['PackageName'].length > 0) {
                                    finHtmlStr += '<hr class="m-0">';
                                    finHtmlStr += t['PackageName'];
                                }
                            } else {
                                finHtmlStr += t[t2[0]];
                            }

                            finHtmlStr +=
                                "  </div>\n" +
                                "</div>\n";

                        });
                        finHtmlStr += "<div class=\"alert alert-secondary mb-0\" role=\"alert\">工單備註："+t['MSComment1']+"</div>";
                        if(t['alert_C000003'] === 'Y') {
                            finHtmlStr += "<div class=\"alert alert-danger mb-0\" role=\"alert\">提醒事項：<br>此用戶為尊榮用戶，若用戶欲使用WiFi單購案，請推尊榮方案。</div>";
                        }
                        if(t['chkChargeNameAlert0701'] === 'Y') {
                            finHtmlStr += "<div class=\"alert alert-danger mb-0\" role=\"alert\">提醒事項：<br>智慧遙控器和ATV 6010機種仍有部份不相容狀況，請僅搭配ATV 6252或9642</div>";
                        }
                        if(t['alert135'].length > 0) {
                            finHtmlStr += "<div class=\"alert alert-danger mb-0\" role=\"alert\">提醒事項：<br>"+t['alert135']+"</div>";
                        }
                        finHtmlStr +=
                            "        </div>\n" +
                            "      </div>\n" +
                            "    </div>\n" +
                            "  </div>\n" +
                            "</div>";
                    });
                } else {
                    finHtmlStr += "" +
                        "   <div class=\"container alert alert-warning text-center\">\n" +
                        "      查無[完工訂單]資料\n" +
                        "    </div>";
                }

            // 未約件==================================================
            //console.log('editHtom data unplan_list=='+data['unplan_list'].length+';unplanPage=='+data['unplanPage'])
            if (pageTypeVal === '' || pageTypeVal === 'UNPLAN')
                if (data['unplan_list'].length > 0 && data['unplanPage'] > 0) {
                    data['unplan_list'].forEach(function (t) {
                        console.log('foreach chk unplan_list');
                        // console.log(t)
                        unFinPlanHtmlStr +=
                            "<div class=\"col\" title=\"order\">\n" +
                            "  <div class=\"card border-info mb-2\">\n" +
                            "    <div class=\"card-header order\" data-workkind=\""+ t['WorkKind'] +"\">\n" +
                            "      <div class=\"row\">\n" +
                            "          <div class=\"input-group \">\n" +
                            "              <a class=\"btn btn-outline-info ml-3 mr-3\"\n" +
                            "                 href=\"/ewo/order_info/" + t['CompanyNo'] + "-" + t['AssignSheet'] + "/{{$p_data['tt']}}\">\n" +
                            "                " + t['WorkKind'] + "-" + t['CompanyNo'] + "-" + t['AssignSheet'] + "\n" +
                            "              </a>\n" +
                            "              <div class=\"input-group-append\">\n" +
                            "                  <span class=\"input-group-text text-sm\">約工到府時間</span>\n" +
                            "              </div>\n" +
                            "              <select id=\"expected_select_hour_" + t['Id'] + "\" data-id=\"" + t['Id'] + "\" data-date=\"" + t['BookDate'].substr(0,10) + "\" onchange=\"ExpectedDate($(this))\">\n" +
                            "                  <option value=\"x\">時</option>\n";

                        for(var i=t.BookDateHS;i <= t.BookDateHE;i++) {
                            unFinPlanHtmlStr += "<option value=\""+i+"\">"+i+"</option>\n";
                        }
                        unFinPlanHtmlStr +=
                            "              </select>\n" +
                            "              <select id=\"expected_select_minute_" + t['Id'] + "\" data-id=\"" + t['Id'] + "\" data-date=\"" + t['BookDate'].substr(0,10) + "\" onchange=\"ExpectedDate($(this))\">\n" +
                            "                  <option value=\"x\">分</option>\n";
                        for(var i=0;i <= 50;i+=10) {
                            unFinPlanHtmlStr += "<option value=\""+i+"\">"+i+"</option>\n";
                        }
                        unFinPlanHtmlStr +=
                            "               </select>" +

                            "          </div>\n" +
                            "      </div>" +
                            "    </div>\n" +
                            "    <div class=\"card-body text-info\">\n" +
                            "      <div class=\"row\">\n" +
                            "        <div class=\"col-12 p-0 d-inline-block\">" +
                            "          <h5 class=\"card-title text-info d-inline\">" + t['BookDate'].substr(0, 16) + "</h5>\n";
                        if(t.delayStatus === "Y")
                            unFinPlanHtmlStr += "<img src=\"{{asset('img/alert_red.gif')}}\" width=\"20\" height=\"20\">";
                        unFinPlanHtmlStr +=
                            "          <h6 class=\"card-title text-danger d-inline float-right text-warning pr-3 mb-0\"></h6>";
                        ordListAry.forEach(function (t2){
                            unFinPlanHtmlStr +=
                                "<div class=\"input-group input-group-sm mb-1\">\n" +
                                "  <div class=\"input-group-prepend p-0 col-3\">\n" +
                                "    <span class=\"input-group-text w-100\">" + t2[1] + "</span>\n" +
                                "  </div>\n" +
                                "  <div class=\"input-group-append input-group-text p-0 col-9 bg-white d-flow-root w-100\" style=\"white-space:normal;text-align: inherit;\">\n" ;

                            if(t2[0] === 'CustPhoneSummary') {
                                unFinPlanHtmlStr += "<ul class='list-group pt-0 pb-0'>\n";
                                    t[t2[0]]['MS0300Phone'].forEach(function(ms_0300_phone){
                                        unFinPlanHtmlStr += "<li class='list-group-item pt-0 pb-0'>";
                                        unFinPlanHtmlStr += ms_0300_phone;
                                        unFinPlanHtmlStr +=
                                            "<button type=\"button\" class=\"btn btn-info btn-sm ml-3\" onclick=\"addEventLog('"+t['CompanyNo']+"','"+t['CustID']+"','"+t['AssignSheet']+"','appCallPhone','app撥打電話['+ms_0300_phone+']','###','"+ms_0300_phone+"');app.call('tel:" +ms_0300_phone+ "')\">\n" +
                                            "    <svg width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-telephone-outbound\" viewBox=\"0 0 16 16\">\n" +
                                            "        <path d=\"M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511zM11 .5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V1.707l-4.146 4.147a.5.5 0 0 1-.708-.708L14.293 1H11.5a.5.5 0 0 1-.5-.5z\"/>\n" +
                                            "    </svg>\n" +
                                            "</button>";
                                        unFinPlanHtmlStr += "</li>";
                                    });
                                    t[t2[0]]['MS0200Phone'].forEach(function(ms_0200_phone){
                                        unFinPlanHtmlStr += "<li class='list-group-item pt-0 pb-0'>";
                                        unFinPlanHtmlStr += ms_0200_phone;
                                        unFinPlanHtmlStr +=
                                            "<button type=\"button\" class=\"btn btn-info btn-sm ml-3\" onclick=\"addEventLog('"+t['CompanyNo']+"','"+t['CustID']+"','"+t['AssignSheet']+"','appCallPhone','app撥打電話['+ms_0200_phone+']','###','"+ms_0200_phone+"');app.call('tel:" +ms_0200_phone+ "')\">\n" +
                                            "    <svg width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-telephone-outbound\" viewBox=\"0 0 16 16\">\n" +
                                            "        <path d=\"M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511zM11 .5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V1.707l-4.146 4.147a.5.5 0 0 1-.708-.708L14.293 1H11.5a.5.5 0 0 1-.5-.5z\"/>\n" +
                                            "    </svg>\n" +
                                            "</button>";
                                        unFinPlanHtmlStr += "</li>";
                                    });
                                unFinPlanHtmlStr += "</ul>\n";
                            } else if(t2[0] === 'InstAddrName') {
                                unFinPlanHtmlStr += t['InstAddrName'];
                                unFinPlanHtmlStr +=
                                    "<button type=\"button\" class=\"btn btn-success btn-sm ml-3\" onclick=\"app.map(encodeURI('" + t['InstAddrName'] + "'))\">\n" +
                                    "    <svg width=\"16\" height=\"16\" fill=\"currentColor\" class=\"bi bi-map\" viewBox=\"0 0 16 16\">\n" +
                                    "        <path fill-rule=\"evenodd\" d=\"M15.817.113A.5.5 0 0 1 16 .5v14a.5.5 0 0 1-.402.49l-5 1a.502.502 0 0 1-.196 0L5.5 15.01l-4.902.98A.5.5 0 0 1 0 15.5v-14a.5.5 0 0 1 .402-.49l5-1a.5.5 0 0 1 .196 0L10.5.99l4.902-.98a.5.5 0 0 1 .415.103zM10 1.91l-4-.8v12.98l4 .8V1.91zm1 12.98l4-.8V1.11l-4 .8v12.98zm-6-.8V1.11l-4 .8v12.98l4-.8z\"></path>\n" +
                                    "    </svg>\n" +
                                    "    地圖\n" +
                                    "</button>";
                            } else if(t2[0] === 'CMValue') {

                                unFinPlanHtmlStr += '<div class=\"alert ';
                                if(t['CMValue'].length > 0 && (t['CMValue'].split('M'))[0] >= {{ $p_data['BandwidthH'] }}) {
                                    unFinPlanHtmlStr += 'alert-danger ';
                                } else {
                                    unFinPlanHtmlStr += 'alert-primary ';
                                }
                                unFinPlanHtmlStr += 'pt-0 pb-0 mb-0\" role=\"alert\">';
                                unFinPlanHtmlStr += t['CMValue'];
                                unFinPlanHtmlStr += '</div>';

                                unFinPlanHtmlStr += "<ul class=\"list-group pt-0 pb-0\">\n";
                                t['CMValueBox'].forEach(function(t3){
                                    unFinPlanHtmlStr += "<li class=\"list-group-item pt-0 pb-0\">\n";
                                    unFinPlanHtmlStr += t3;
                                    unFinPlanHtmlStr += '</li>';
                                })
                                unFinPlanHtmlStr += '</ul>';
                            } else if(t2[0] === 'servicenameAry' || t2[0] === 'subsidAry') {
                                unFinPlanHtmlStr += '<ul class="list-group pt-0 pb-0">';
                                t[t2[0]].forEach(function(t3){
                                    unFinPlanHtmlStr += '<li class="list-group-item pt-0 pb-0">\n';
                                    unFinPlanHtmlStr += t3;
                                    unFinPlanHtmlStr += '</li>';
                                })
                                unFinPlanHtmlStr += '</ul>';
                            } else if(t2[0] === 'worksheet_workkindAry') {
                                unFinPlanHtmlStr += '<ul class="list-group pt-0 pb-0">';
                                t[t2[0]].forEach(function(t3){
                                    unFinPlanHtmlStr += '<li class="list-group-item pt-0 pb-0';
                                    if(t3.search((t['AssignSheet']+'_')) >= 0) {
                                        unFinPlanHtmlStr += ' list-group-item-primary ';
                                    }
                                    unFinPlanHtmlStr += '">\n';
                                    unFinPlanHtmlStr += t3;
                                    unFinPlanHtmlStr += '</li>';
                                })
                                unFinPlanHtmlStr += '</ul>';
                            } else if(t2[0] === 'TotalAmt') {
                                unFinPlanHtmlStr += '$'+t[t2[0]];
                                // unFinPlanHtmlStr += '' +
                                //     '<button type="button" class="btn btn-success btn-sm ml-3" onclick="getSumAMT(\''+t['CompanyNo']+'\',\''+t['AssignSheet']+'\',$(this))">\n' +
                                //     '        查詢\n' +
                                //     '        <svg width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">\n' +
                                //     '        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>\n' +
                                //     '        </svg>\n' +
                                //     '</button>';
                                // unFinPlanHtmlStr += '<label id="totalamt_'+t['CompanyNo']+'_'+t['AssignSheet']+'">$???</label>';
                            } else if(t2[0] === 'SaleCampaign') {
                                unFinPlanHtmlStr += t[t2[0]];
                                if(t['PackageName'].length > 0) {
                                    unFinPlanHtmlStr += '<hr class="m-0">';
                                    unFinPlanHtmlStr += t['PackageName'];
                                }
                            } else {
                                unFinPlanHtmlStr += t[t2[0]];
                            }

                            unFinPlanHtmlStr +=
                                "  </div>\n" +
                                "</div>\n";
                        });
                        unFinPlanHtmlStr += "<div class=\"alert alert-info mb-0\" role=\"alert\">工單備註："+t['MSComment1']+"</div>";
                        if(t['alert_C000003'] === 'Y') {
                            unFinPlanHtmlStr += "<div class=\"alert alert-danger mb-0\" role=\"alert\">提醒事項：<br>此用戶為尊榮用戶，若用戶欲使用WiFi單購案，請推尊榮方案。</div>";
                        }
                        if(t['chkChargeNameAlert0701'] === 'Y') {
                            unFinPlanHtmlStr += "<div class=\"alert alert-danger mb-0\" role=\"alert\">提醒事項：<br>智慧遙控器和ATV 6010機種仍有部份不相容狀況，請僅搭配ATV 6252或9642</div>";
                        }
                        if(t['alert135'].length > 0) {
                            unFinPlanHtmlStr += "<div class=\"alert alert-danger mb-0\" role=\"alert\">提醒事項：<br>"+t['alert135']+"</div>";
                        }
                        unFinPlanHtmlStr +=
                            "        </div>\n" +
                            "      </div>\n" +
                            "    </div>\n" +
                            "  </div>\n" +
                            "</div>";
                    });
                } else {
                    unFinPlanHtmlStr += "" +
                        "   <div class=\"container alert alert-info text-center\">\n" +
                        "      查無[未約件]資料\n" +
                        "    </div>";
                }


            // PAGE======================================================
            fpage = data['finPage'];
            ufpage = data['unFinPage'];

            // 筆數=======================================================
            if (pageTypeVal === '' || pageTypeVal === 'UNFIN')
                $('#unfinish_count').text(data['unfinish_count']);

            if (pageTypeVal === '' || pageTypeVal === 'FIN')
                $('#finish_count').text(data['finish_count']);

            if (pageTypeVal === '' || pageTypeVal === 'UNPLAN')
                $('#unplan_count').text(data['unplan_count']);

            // Loading hide==============================================
            //console.log("editHTML() >> delStat==  " + delStat)
            if (delStat === 'DEL') {
                $('#unFinshOrderList').html(unFinHtmlStr);
                $('#finshOrderList').html(finHtmlStr);
                $('#unPlanList').html(unFinPlanHtmlStr);
                $('#alertTopLoad').hide();
            } else if (delStat === 'ADD') {
                if (pageTypeVal === '' || pageTypeVal === 'UNFIN')
                    $('#unFinshOrderList').append(unFinHtmlStr);
                if (pageTypeVal === '' || pageTypeVal === 'FIN')
                    $('#finshOrderList').append(finHtmlStr);
                if (pageTypeVal === '' || pageTypeVal === 'UNPLAN')
                    $('#unPlanList').append(unFinPlanHtmlStr);
                $('#alertBodLoad').hide();
            }

        }

        // 明細 縮放[btn]
        function detailBtn(obj) {
            var parentObj = obj.parent();
            var detailObj = parentObj.find('.detail.collapcollapse');

            detailObj.collapse('hide')
            detailObj.collapse('show')
        }

        // count btn [unfin/fin 切換分頁]
        function countBtn(type) {
            $("div[name='divpage']").collapse('hide');
            $('#soSelect option').eq(0).prop('selected',true);
            //$('#soSelect').prop('selectedIndex',0);
            switch (type) {
                case "FIN":
                    $("#finshOrderList").collapse('show');
                    break;
                case "UNFIN":
                    $("#unFinshOrderList").collapse('show');
                    break;
                case "NotAgreed":
                    $("#STATISTICS").collapse('show');
                    break;
                case "UNPLAN":
                    $("#unPlanList").collapse('show');
                    break;

            }
            pageType = type;

            // TOP
            TOP();
        }

        // to TOP
        function TOP() {
            $('html,body').animate({scrollTop: 0}, 'slow');
        }

        // 日期輸入框，外掛套件
        // $("input[name='datetimepicker4']").datetimepicker({
        //     format: "yyyy-mm-dd hh:ii"//顯示格式
        //     ,language:"zh-TW"//語言
        //     ,startView: 0
        //     ,minuteStep:10
        //     ,initialDate: new Date()//初始化當前日期
        //     ,autoclose: true//選中自動關閉
        //     ,todayBtn: true//顯示今日按鈕
        // })


        // create 約工到府時間[option]
        function createExpectedOption(sDate) {
            var retHtml = '<option vaule="0">請選擇</option>\n';
            var date = new Date(sDate);
            var i = date.getMinutes();
            var i2 = Math.round(i/10);
            i2 = (i2<1)? 1 : i2;
            var day1Ary = sDate.split(' ')
            for(var a=1; a<15; a++) {
                var m2 = a*10 + i2*10;
                var day10M = (new Date(new Date(sDate).setMinutes(m2))).toString().split(' ')
                retHtml += '<option value="'+day1Ary[0]+' '+day10M[4]+'">'+day10M[4].substr(0,5)+'</option>\n';
            }
            return retHtml;
        }


        function ExpectedDate_new(obj) {
            var method = "post";
            var path = "/ewo/event";
            var p_id = obj.data('id');
            var p_date = obj.data('date');
            var p_hour = $('#expected_select_hour_'+p_id).val();
            var p_minute = $('#expected_select_minute_'+p_id).val();
            if(p_hour == 'x' || p_minute == 'x')
                return ;

            var p_value = p_date+' '+p_hour+':'+p_minute+':00';
            console.log('p_value=='+p_value)
            var params = {
                _token : $('#p_token').val(),
                p_id : p_id,
                p_columnName : "expected",
                p_value : p_value
            };

            const XHR = new XMLHttpRequest();
            let urlEncodedData = "",
                urlEncodedDataPairs = [],
                name;
            for(var key in params) {
                urlEncodedDataPairs.push( encodeURIComponent( key ) + '=' + encodeURIComponent( params[key] ) );
            }
            urlEncodedData = urlEncodedDataPairs.join( '&' ).replace( /%20/g, '+' );
            XHR.addEventListener( 'load', function(event) {
                // alert( 'Yeah! Data sent and response loaded.' );
            } );
            XHR.addEventListener( 'error', function(event) {
                alert( 'Oops! Something went wrong.' );
            } );
            XHR.open( method, path );
            XHR.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
            XHR.send( urlEncodedData );
            XHR.onreadystatechange = (e) => {
                console.log('XHR request To reponse');
                var json = JSON.parse(XHR.responseText);
                console.log(json)
                if(json.code === "0000")
                {
                    obj.parents('.card').find('h6').text("約工到府時間:" + (json.data).replace(":00.000",""));
                }
            }
        }


            function ExpectedDate(obj) {
                var method = "post";
                var path = "/ewo/event";
                var p_id = obj.data('id');
                var p_date = obj.data('date');
                var p_hour = $('#expected_select_hour_'+p_id).val();
                var p_minute = $('#expected_select_minute_'+p_id).val();

                if(p_hour == 'x' || p_minute == 'x')
                    return ;
                var p_value = p_date+' '+p_hour+':'+p_minute+':00';
                var params = {
                    p_id : obj.data('id'),
                    p_userCode : $('#p_userCode').val(),
                    p_userName : $('#p_userName').val(),
                    p_columnName : "expected",
                    p_value : p_value
                };
                console.log(params);

                $.ajax({
                    method: method,
                    url: path,
                    headers: {'X-CSRF-TOKEN': $('#p_token').val()},
                    data: params,
                    success: function (json) {
                        console.log('---expected-----');
                        console.log(json);

                        if(json.code === "0000") {
                            var expected = json.data.expected
                            obj.parents('.card').find('h6').text("約工到府時間:" + json.data.expected);

                            Object.entries(json.data.alert).forEach(entry => {
                                const [k, t] = entry;
                                Alarm(t['title'],t['body'],t['sec'],t['id']);

                            });
                        }
                    }
                });
            }



        // 查詢，應收金額
        function getSumAMT(companyno, worksheet, obj) {
            var url = '/api/sumReceivableAMT';
            obj.prop('disabled',true);

            var objLable = $('#totalamt_'+companyno+'_'+worksheet);
            objLable.html('金額查詢中');

            var usercode = $('#p_userCode').val();
            var data = JSON.stringify({
                companyno : companyno,
                worksheet : worksheet,
                usercode : usercode
            });

            $.ajax({
                url: url,
                type: 'post',
                dataType: "json", //返回格式為json
                async: true, //請求是否非同步，預設為非同步，這也是ajax重要特性
                contentType: 'application/json; charset=utf-8', // 要送到server的資料型態
                data: data,
                success: function (json) {
                    obj.prop('disabled',false);
                    if(json.code != '0000') {
                        alert('查詢金額API錯誤!['+companyno+worksheet+']')
                    }
                    objLable.html('$'+json.data.sumamt);

                },
                error: function (xhr, ajaxOptions, thrownError) {
                    obj.prop('disabled',false);
                    console.log(xhr);
                }
            });
        }


        // 電話紀錄
        function addEventLog(companyNo,custId,workSheet,type,request='',responses='',value)
        {
            let method = "post";
            let path = "/api/EWO/addEventLog";
            let userCode = $('#p_userCode').val();
            let userName = $('#p_userName').val();
            let data = JSON.stringify({
                companyNo : companyNo,
                custId : custId,
                workSheet : workSheet,
                userCode : userCode,
                userName : userName,
                request : request,
                responses : responses,
                type : type,
                value : value
            });

            $.ajax({
                method: method,
                url: path,
                headers: {'X-CSRF-TOKEN': $('#p_token').val()},
                data: data,
                success: function (json) {
                    console.log(json);
                }
            });
        }

    </script>
@endsection


