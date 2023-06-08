@extends('ewo.layouts.default')

@section('title', '裝機_訂單明細')

@section('content')

    <?php
    $ordInfoAry = array(
        'ServiceName' => '服務別',
        'WorkSheet' => '派工單號',
        'CompanyNoName' => '公司別',
        'WorkKind' => '派工類別',
        'WorkTeam' => '工程組別',
        'WorkerName' => '工程人員',
        'NetID' => '網路編號',
        'NetPoint' => '網點',
        'SubsCP' => '明細',
        'SaleCampaign' => '業務活動',
        'CustBroker' => '推薦人',
    );
    ?>

    <style>
        .Hardware-inpt-text {
            width: 40px;
            text-align: center;
        }
        .card-body {
            padding: .5rem!important;
        }
        .gallery img {
            width: 100%;
            max-width: 15em;
            margin: 1em
        }
        .constructionPhoto-img {
            /*height: 100px;*/
            /*width: 120px;*/
        }
        .alert-file-01
        {
            padding: .375rem .75rem;
        }

        /* 完工，現金/刷卡 */
        .btn-check:active+.btn-outline-success, .btn-check:checked+.btn-outline-success, .btn-outline-success.active, .btn-outline-success.dropdown-toggle.show, .btn-outline-success:active {
            color: #fff;
            background-color: #198754;
            border-color: #198754;
        }
        .btn-check {
            position: absolute;
            clip: rect(0,0,0,0);
            pointer-events: none;
        }
        button, input, optgroup, select, textarea {
            margin: 0;
            font-family: inherit;
            font-size: inherit;
            line-height: inherit;
        }

        /* 完工，現金/刷卡 */
        .btn-check:active+.btn-outline-danger, .btn-check:checked+.btn-outline-danger, .btn-outline-danger.active, .btn-outline-danger.dropdown-toggle.show, .btn-outline-danger:active {
            color: #fff;
            background-color: #dc3545;
            border-color: #dc3545;
        }

        /* 紙本工單 */
        .btn-primary {
            color: #fff;
            background-color: #8bbff6;
            border-color: #8bbff6;
        }

        .imgWatemark { /* 浮水印 */
            -webkit-transform: rotate(-45deg);
            -moz-transform: rotate(-45deg);
            position: relative;
            text-align: center;
            /* border: none; */
            width: 500px;
            height: 0;
            /*bottom: 90px;*/
            left: 40px;
            font-size: 24px;
            letter-spacing: 20px;
            color: red;
        }
        .divWatemark {
            width: 500px;
            position: relative;
            overflow: hidden;
        }
        .bw-5p {
            border-width: 5px;
        }
        .cursor-pointer{
            cursor: pointer;
        }

    </style>
    <main style="margin-top: 55px;">

    {{  Log::channel('ewoLog')->info("Detail installed_".$p_data['info']->CompanyNo.'_'.$p_data['info']->WorkSheet.' p_data=='.print_r($p_data,1)) }}

        <input type="hidden" name="p_userCode" id="p_userCode" value="{{$p_data['user_info']['userId']}}">
        <input type="hidden" name="p_userName" id="p_userName" value="{{$p_data['info']->WorkerName}}">
        <input type="hidden" name="p_userMobile" id="p_userMobile" value="{{$p_data['user_info']['mobile']}}">
        <input type="hidden" name="p_custName" id="p_custName" value="{{$p_data['info']->CustName}}">
        <input type="hidden" name="p_id" id="p_id" value="{{$p_data['info']->Id}}">
        <input type="hidden" name="p_custId" id="p_custId" value="{{$p_data['info']->CustID}}">
        <input type="hidden" name="p_subsidStr" id="p_subsidStr" value="{{$p_data['info']->substrStr}}">
        <input type="hidden" name="p_companyNo" id="p_companyNo" value="{{$p_data['info']->CompanyNo}}">
        <input type="hidden" name="p_workSheet" id="p_workSheet" value="{{$p_data['info']->WorkSheet}}">
{{--        <input type="hidden" name="p_finshSheet" id="p_finshSheet" value="{{$p_data['info']->finshSheet}}">--}}
        <input type="hidden" name="p_BookDate" id="p_BookDate" value="{{$p_data['info']->BookDate}}">
        <input type="hidden" name="p_ServiceName" id="p_ServiceName" value="{{$p_data['info']->ServiceName}}">
        <input type="hidden" name="p_serviceNameAry2" id="p_serviceNameAry2" value="{{$p_data['serviceNameAry2']}}">
        <input type="hidden" name="p_pdf_v" id="p_pdf_v" value="{{$p_data['info']->pdf_v??config('order.PDF_CODE_V')}}">
        <input type="hidden" name="p_recvAmt" id="p_recvAmt" value="{{$p_data['recvAmt']}}">
        <input type="hidden" name="p_sheetStatus" id="p_sheetStatus" value="{{$p_data['info']->SheetStatus}}">
        <input type="hidden" name="p_sign_chs" id="p_sign_chs">
        <input type="hidden" name="p_sign_bftth" id="p_sign_bftth">
        <input type="hidden" name="p_phoneNum" id="p_phoneNum" value="{{$p_data['phoneNum']}}">
        <input type="hidden" name="p_workkind" id="p_workkind" value="{{$p_data['info']->WorkKind}}">
        <input type="hidden" name="p_workkindAryStr" id="p_workkindAryStr" value="{{$p_data['info']->WorkKindAryStr}}">
        <input type="hidden" name="p_istest" id="p_istest" value="{{$p_data['IsTest']}}">
        <input type="hidden" name="p_instAddr" id="p_instAddr" value="{{$p_data['instAddrName']}}">
        <input type="hidden" name="p_ccadaf" id="p_ccadaf" value="{{$p_data['info']->ccadaf}}">
        <input type="hidden" name="p_ach" id="p_ach" value="{{$p_data['info']->ach}}">
        <input type="hidden" name="p_etf_ach" id="p_etf_ach" value="{{$p_data['info']->etf_ach}}">
        <input type="hidden" name="p_worksheet2" id="p_worksheet2" value="{{$p_data['info']->worksheet2}}">
        <input type="hidden" name="p_BorrowmingList" id="p_BorrowmingList" value="{{ $p_data['info']->BorrowmingList }}">
        <input type="hidden" name="p_RetrieveList" id="p_RetrieveList" value="{{ $p_data['info']->RetrieveList }}">
        <input type="hidden" name="p_nodeNo" id="p_nodeNo" value="{{ $p_data['info']->NodeNo }}">
        <input type="hidden" name="p_invUnifyNo" id="p_invUnifyNo" value="{{ $p_data['info']->InvUnifyNo }}">
        <input type="hidden" name="p_linkId" id="p_linkId" value="{{ $p_data['info']->LinkID }}">
        <input type="hidden" name="p_token" id="p_token" value="{{ csrf_token() }}">

        <div class="container pt-2 bg-grey">

            <div class="alert alert-info mb-1" role="alert">
                <label class="d-inline">{{$p_data['info']->WorkKind}}</label>
                <label class="d-inline text-danger">服務別</label>
                <label class="d-inline">{{$p_data['info']->ServiceName}}</label>
                <label class="d-inline float-right">應收金額${{$p_data['recvAmt']}}</label>
            </div>
            <div id="accordion">


                {{-- 區故信息 --}}
                <div class="card w-100 mb-3">
                    <div class="card-header bg-warning">
                        <div class="input-group">
                            區故信息
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="input-group ">
                            <div class="w-100">
                                <div class="alert alert-danger p-0 pl-2 pr-2 mb-0 ml-3">
                                    預約日：{{ $p_data['fault'] }}
                                </div>
{{--                                @if(is_array($p_data['fault']['list']) && count($p_data['fault']['list']))--}}
{{--                                    <ul class="list-group pt-0 pb-0" id="faultlist" data-chk="Y">--}}
{{--                                        @foreach($p_data['fault']['list'] as $k => $t)--}}
{{--                                            <li class="list-group-item bg-danger pt-0 pb-0">--}}
{{--                                                {{ $t['CompanyNo'].'；住編:'.$t['CustID'].';網點:'.$t['linkid'] }}--}}
{{--                                            </li>--}}
{{--                                            <li class="list-group-item pt-0 pb-0">--}}
{{--                                                {{ '開始:'.substr($t['EventTime'],0,16) }}--}}
{{--                                            </li>--}}
{{--                                            <li class="list-group-item pt-0 pb-0">--}}
{{--                                                {{ '預計完成時間:'.substr($t['WishTime'],0,16)}}--}}
{{--                                            </li>--}}
{{--                                            <li class="list-group-item pt-0 pb-0">--}}
{{--                                                {{ '編號:'.$t['EventNo'].'；'.$t['EventReason'].'；'.$t['EventKind'] }}--}}
{{--                                            </li>--}}
{{--                                        @endforeach--}}
{{--                                    </ul>--}}
{{--                                @elseif(is_array($p_data['fault']['list']))--}}
{{--                                    此[{{ $p_data['info']->CompanyNo.';住編:'.$p_data['info']->CustID }}]查無區障事件--}}
{{--                                @else--}}
{{--                                    {{ $p_data['fault']['list'] }}--}}
{{--                                @endif--}}
                            </div>
                        </div>
                    </div>
                </div>


                {{-- 打卡 --}}
                <div class="card border-danger bw-5p w-100 mb-3">
                    <div class="card-header" >
                        <div class="input-group">
                            <label class="btn btn-info mb-0">
                                <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">
                                    <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                                </svg>
                                <input class="d-none" type="file" accept="image/*" id="file_checkin" >
                                到站打卡
                                <input type="hidden" class="form-control" id="localLat">
                                <input type="hidden" class="form-control" id="localLng">
                            </label>
                            <label class="alert alert-info p-0 pl-2 pr-2 mb-0 ml-3 @if(is_null($p_data['info']->checkin) === true) d-none @endif" id="label_checkin">
                                上傳時間：{{date('Y-m-d H:i:s',strtotime($p_data['info']->checkin))}}
                            </label>
                            <label class="alert alert-danger pl-2 pr-2 mb-0 ml-3">打卡請拍(門牌)照片</label>
                        </div>
                    </div>
                    <div class="collapse show">
                        <div class="card-body">
                            <div class="input-group input-group-sm mb-1">
                                <div class="input-group-prepend bg-success p-0 col-3">
                                    <span class="input-group-text w-100">用戶地址</span>
                                </div>
                                <div class="input-group-append input-group-text p-0 col-9 d-flow-root bg-white w-100" style="white-space:normal;text-align: inherit;" id="custAddres">
                                    {{$p_data['info']->custAddress}}
                                </div>
                                <div class="input-group-prepend bg-success p-0 col-3">
                                    <span class="input-group-text w-100">用戶GPS</span>
                                </div>
                                <input type="hidden" id="p_custGps" value="{{$p_data['info']->custGps}}">
                                <div class="input-group-append input-group-text p-0 col-9 d-flow-root bg-white w-100" style="white-space:normal;text-align: inherit;" id="custGps">
                                    {{$p_data['info']->custGps}}
                                </div>
                                <div class="input-group-prepend bg-primary p-0 col-3">
                                    <span class="input-group-text w-100">工程打卡地址</span>
                                </div>
                                <div class="input-group-append input-group-text p-0 col-9 bg-warning d-flow-root w-100" style="white-space:normal;text-align: inherit;" id="checkInAddres">
                                    {{$p_data['info']->gpsRefAddres}}
                                </div>
                                <div class="input-group-prepend bg-primary p-0 col-3">
                                    <span class="input-group-text w-100">工程打卡GPS</span>
                                </div>
                                <div class="input-group-append input-group-text p-0 col-9 bg-warning d-flow-root w-100" style="white-space:normal;text-align: inherit;" id="checkInGPS">
                                    {{$p_data['info']->gps}}
                                </div>
                                <div class="input-group-prepend bg-info p-0 col-3">
                                    <span class="input-group-text w-100">GPS距離</span>
                                </div>
                                <div class="input-group-append input-group-text p-0 col-9 bg-white d-flow-root w-100" style="white-space:normal;text-align: inherit;" id="gpsDistance">
                                    {{$p_data['info']->gpsDistance}}
                                </div>
                            </div>
                            <img class=" @if(is_null($p_data['info']->checkin) === true) d-none w-0 @endif" width="500"
                                 data-chk="@if(is_null($p_data['info']->checkin) === true){{0}}@else{{2}}@endif"
                                 id="img_checkin" src="/upload/{{$p_data['uploaddir']}}/checkIn.jpg?i={{date('His')}}" onerror="this.src='/img/error_02.png'">
                        </div>
                    </div>
                </div>


                {{-- 入戶確認 --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="input-group">
                            <label class="btn btn-info mb-0">
                                <svg width="16" height="16" fill="currentColor" class="bi bi-pin-map" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M3.1 11.2a.5.5 0 0 1 .4-.2H6a.5.5 0 0 1 0 1H3.75L1.5 15h13l-2.25-3H10a.5.5 0 0 1 0-1h2.5a.5.5 0 0 1 .4.2l3 4a.5.5 0 0 1-.4.8H.5a.5.5 0 0 1-.4-.8l3-4z"/>
                                    <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999z"/>
                                </svg>
                                <input class="d-none" type="button" id="btnDorIn" onclick="saveDorTime('In')">
                                入戶確認
                            </label>
                            <label class="alert alert-info pt-0 pb-0 pl-1 pr-1 ml-3 mb-0" id="labelDorIn">...</label>
                        </div>
                    </div>
                </div>


                {{-- 檢點表 --}}
                <div class="card border-danger bw-5p mb-3">
                    <div class="card-header pt-0 pb-0" id="laborsafety_checklist_haeder">
                        <h5 class="mb-0">
                            <button class="btn btn-primary collapsed btn_collapsed" data-toggle="collapse" data-target="#laborsafety_checklist_body">
                                檢點表
                                <svg  width="16" height="16" fill="currentColor" class="bi bi-arrows-collapse" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zm7-8a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 4.293V.5A.5.5 0 0 1 8 0zm-.5 11.707-1.146 1.147a.5.5 0 0 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 11.707V15.5a.5.5 0 0 1-1 0v-3.793z"/>
                                </svg>
                            </button>
                            <label class="alert alert-infoo m-0 ml-3" id="laborsafety_checklist_alert"></label>
                        </h5>
                    </div>
                    <div id="laborsafety_checklist_body" class="collapse" data-parent="#laborsafety_checklist_haeder">
                        <div class="card-body">
                            @foreach($p_data['laborsafety_checklist']['head'] as $k => $t)
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item active m-0">
                                        <div class="input-group">
                                            <p class="m-0">{{ $t }}</p>
                                            <p class="m-0 ml-3" onclick="btnChecklistAll($(this))" data-checked="false">(全選)</p>
                                        </div>
                                    </li>
                                    @foreach($p_data['laborsafety_checklist']['list'][$t] as $k2 => $t2)
                                        <li class="list-group-item pt-0 pb-0">
                                            <div class="input-group pt-1 pb-1">
                                                <input class="form-check-input" type="checkbox" id="laborsafetyList_{{ $k2 }}" data-id="{{ $k2 }}" @if($t2['reply'] === 'true') checked @endif>
                                                <label class="form-check-label" for="laborsafetyList_{{ $k2 }}">{{ $t2['desc'] }}</label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endforeach
                            <button class="btn btn-info w-100" id="laborsafety_checklist_save_btn" onclick="laborsafetyCheckListSave()">存檔</button>
                        </div>
                    </div>
                </div>


                {{-- 危險地點 --}}
                @if($p_data['laborsafety_dangerplace'])
                <div class="card mb-3">
                    <div class="card-header bg-danger">
                        <div class="input-group">
                            危險地點
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item list-group-item-warning">本次安裝地址:{{ $p_data['instAddrName'] }}</li>
                            @foreach($p_data['laborsafety_dangerplace'] as $k => $t)
                                @if($k <= 2)
                                    <li class="list-group-item">注意{{ intval($k+1) }}：{{ $t }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>

                    {{-- dialog 危險地點 --}}
                    <div id="laborsafetyDialog" title="勞工安全-危險地點" class="d-none">
                        <div class="card w-80">
                            <div class="card-header bg-danger">
                                <p>本次安裝地址:{{ $p_data['instAddrName'] }}</p>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @foreach($p_data['laborsafety_dangerplace'] as $k => $t)
                                        @if($k <= 2)
                                            <li class="list-group-item">注意{{ intval($k+1) }}：{{ $t }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-info w-100" onclick="laborsafety_dangerplaceDialogClose({{ data_get($p_data['laborsafety_dangerplace'],3) }})">確定</button>
                            </div>
                        </div>
                    </div>
                @endif


                {{-- 用戶信息 --}}
                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0" id="userInfoHead">
                        <h5 class="mb-0">
                            <button class="btn btn-primary collapsed btn_collapsed" data-toggle="collapse" data-target="#userInfoBody">
                                用戶信息
                                <svg  width="16" height="16" fill="currentColor" class="bi bi-arrows-collapse" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zm7-8a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 4.293V.5A.5.5 0 0 1 8 0zm-.5 11.707-1.146 1.147a.5.5 0 0 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 11.707V15.5a.5.5 0 0 1-1 0v-3.793z"/>
                                </svg>
                            </button>
                            @if(is_null($p_data['info']->MailTitle) === false)
                                <button class="btn btn-danger float-right" onclick="serviceMsg()" >客服信息</button>
                            @endif
                        </h5>
                    </div>
                    <div class="alert alert-danger hide show d-none" id="alert_MailTitle" role="alert">
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        客服信息：<br>{{$p_data['info']->MailTitle}}
                    </div>
                    <div id="userInfoBody" class="collapse show" data-parent="#userInfoHead">
                        <div class="card-body">
                            <div class="input-group ">
                                @foreach($ordInfoAry as $k => $t)
                                    <div class="input-group-prepend p-0 col-3">
                                        <span class="input-group-text w-100 pl-1">{{$t}}</span>
                                    </div>
                                    <div class="input-group-append p-0 col-9">
                                        @if(in_array($k,['Worker2']))
                                            <span class="input-group-text bg-white w-100">
                                            @if(empty($p_data['info']->$k) === false || is_null($p_data['info']->$k) === false)
                                                    /{{explode(' ',$p_data['info']->$k)[1]}}
                                                @endif
                                            </span>
                                        @elseif(in_array($k,['NetPoint']))
                                            <span class="input-group-text bg-white w-100">
                                            {{$p_data['info']->$k}}
                                                @if(substr($p_data['info']->$k,0,1) == 'F')
                                                    <span class="text-danger">(FTTH大樓)</span>
                                                @endif
                                            </span>
                                            <span class="input-group-text bg-white w-100">
                                            GPON:{{$p_data['info']->InvUnifyNo}}
                                            </span>
                                        @elseif(in_array($k,['SubsCP']))
                                            <div class="d-block w-100">
                                                @foreach($p_data['info']->AssignSheetAry as $k => $t)
                                                    <ul class="list-group pt-0 pb-0">
                                                        <li class="list-group-item active pt-0 pb-0"> {{$k}}</li>
                                                        @foreach($t as $k2 => $t2)
{{--                                                            @if(in_array($k2,['C HS','D TWMBB','2 CM','3 DSTB','B FTTH']))--}}
                                                                <li class="list-group-item pt-0 pb-0">
                                                                    <label class="float-left">服務別</label>
                                                                    <label class="float-right">{{$k2}}</label>
                                                                </li>
                                                                <li class="list-group-item pt-0 pb-0">
                                                                    <label class="float-left">訂編</label>
                                                                    <label class="float-right">{{$t2['subsid']}}</label>
                                                                </li>
                                                                <li class="list-group-item pt-0 pb-0">
                                                                    <label class="float-left">IVR檢碼</label>
                                                                    <label class="float-right">{{$t2['subscp2']}}</label>
                                                                </li>
{{--                                                            @endif--}}
                                                        @endforeach
                                                    </ul>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="input-group-text bg-white w-100">
                                            {{$p_data['info']->$k}}
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                                <div class="input-group-prepend p-0 col-3">
                                    <span class="input-group-text w-100 pl-1">固定IP</span>
                                </div>
                                <div class="input-group-append p-0 col-9">
                                    <ul class="list-group list-group-flush">
                                        @foreach($p_data['info']->fixedIP as $k2 => $t2)
                                            <li class="list-group-item">{{$k2.'：'.$t2}}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @if($p_data['info']->chkAlertMSG20221215 == 'Y')
                                    <div class="input-group-prepend p-0 col-12">
                                        <label class="alert alert-danger w-100">
                                            注意：<br>
                                            目前鋐寶CH8679因設備有問題，故暫停使用此設備供裝，請改用其他設備供裝(如CODA5310)。
                                        </label>
                                    </div>
                                @endif
                                @if(!empty($p_data['info']->getOETS))
                                    <div class="input-group-prepend p-0 col-12">
                                        <label class="alert alert-danger w-100">
                                            注意：<br>
                                            {{$p_data['info']->getOETS}}
                                        </label>
                                    </div>
                                @endif
                                @if(!empty($p_data['info']->qryDissatisfied))
                                    <div class="input-group-prepend p-0 col-12">
                                        <label class="alert alert-danger w-100">
                                            注意：<br>
                                            {{$p_data['info']->qryDissatisfied}}
                                        </label>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>


                {{-- 工作清單 --}}
                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0" id="orderListHead">
                        <h5 class="mb-0">
                            <button class="btn btn-primary collapsed btn_collapsed" data-toggle="collapse" data-target="#orderListBody">
                                工作清單
                                <svg  width="16" height="16" fill="currentColor" class="bi bi-arrows-collapse" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zm7-8a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 4.293V.5A.5.5 0 0 1 8 0zm-.5 11.707-1.146 1.147a.5.5 0 0 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 11.707V15.5a.5.5 0 0 1-1 0v-3.793z"/>
                                </svg>
                            </button>
                        </h5>
                    </div>
                    <div id="orderListBody" class="collapse show" data-parent="#orderListHead">
                        <div class="card-body">
                            <input type="hidden" id="chkChargeNameAlert0701" value="{{ $p_data['info']->chkChargeNameAlert0701 }}">
                            @if($p_data['info']->chkChargeNameAlert0701 == 'Y')
                                <label class="alert alert-danger w-100">注意提示：<br>智慧遙控器和ATV 6010機種仍有部份不相容狀況，請僅搭配ATV 6252或9642</label>
                            @endif
                            @if(!empty($p_data['chargeInfo']))
                                @foreach($p_data['chargeInfo'] as $k => $t)
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item list-group-item-success">{{ explode(' ', $k)[1] }}</li>
                                        @foreach($t as $k2 => $t2)
                                            <li class="list-group-item">{{$t2->ChargeName}}</li>
                                        @endforeach
                                    </ul>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                {{-- DSTB 清單(請綁定遙控器) --}}
                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0" id="DSTBListHead">
                        <h5 class="mb-0">
                            <button class="btn btn-primary collapsed btn_collapsed" data-toggle="collapse" data-target="#DSTBListBody">
                                DSTB 清單(請綁定遙控器)
                                <svg  width="16" height="16" fill="currentColor" class="bi bi-arrows-expand" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zM7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10z"/>
                                </svg>
                            </button>
                        </h5>
                    </div>
                    <div id="DSTBListBody" class="collapse show" data-parent="#DSTBListHead">
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                            @foreach ($p_data['custDives']  as $index1 => $device)
                                @if($device->ServiceName == '3 DSTB')
                                        <li class="list-group-item DSTBScanLi @if( !is_null($device->remoteQrCode)) list-group-item-success  @else list-group-item-danger  @endif">
                                            <label class="btn btn-danger mb-0">
                                                <input class="d-none" type="button" id="openButton"
                                                       onclick="openscan('dstb_remote_mapping_{{$device->SubsID}}')">
                                                <svg width="24" height="24" fill="currentColor" class="bi bi-camera"
                                                     viewBox="0 0 16 16">
                                                    <path
                                                        d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"></path>
                                                    <path
                                                        d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"></path>
                                                </svg>
                                                掃描
                                            </label>
                                            {{-- TODO 正式卡控時在加回 判斷是否為此工單的訂編                                           --}}
{{--                                            @if( in_array($device->SubsID,$p_data['info']->SubsID ?? []))--}}
{{--                                                <span style="color:red">*此為此工單的對應 DSTB 訂單為必填項目</span>--}}
{{--                                            @endif--}}
                                            @if( !is_null($device->remoteQrCode))
                                                綁定 DSTB 訂編({{$device->SubsID}})遙控器 :已綁定(可以再次掃描更新)
                                                <input type="text" class="form-control bg-white dstb_remote_mapping_info" id="opendstb_remote_mapping_{{$device->SubsID}}_scanstr" data-subs_id="{{$device->SubsID}}" data-single_sn="{{$device->SingleSN}}" placeholder="{{$device->remoteQrCode}}" onfocus="$(this).val('{{$device->remoteQrCode}}')" />
                                            @else
                                                綁定 DSTB 訂編({{$device->SubsID}})遙控器 :尚未綁定
                                                <input type="text" class="form-control bg-white dstb_remote_mapping_info" id="opendstb_remote_mapping_{{$device->SubsID}}_scanstr" data-subs_id="{{$device->SubsID}}" data-single_sn="{{$device->SingleSN}}" placeholder="請掃描遙控器或輸入送出" onfocus="$(this).val('請掃描遙控器或輸入送出')" />
                                            @endif
                                            <button type="button" class="btn btn-info dstb_remote_mapping_send_button">送出</button>
                                        </li>
                                @endif
                            @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- 設備清單 --}}
                @if(strpos($p_data['info']->WorkKindAryStr,'換機') >= 1 && 0)
                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0" id="equipmentListHead"
                         data-devchk="{{ $p_data['info']->deviceChk }}"
                         data-count="{{ $p_data['deviceDSTBCount'] }}">
                        <div class="input-group">
                        <h5 class="mb-0 mr-3">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#equipmentListBody">
                                設備清單
                            </button>
                        </h5>
                        <div class="input-group-append">
                            <label class="btn btn-info mb-0">
                                <input class="d-none" type="button" onclick="changeEquipment()">
                                切換維修設備
                            </label>
                            <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3 d-none" id="label_chgSiginsn"></label>
                        </div>
                        </div>
                    </div>
                    <div id="equipmentListBody" class="collapse" data-parent="#equipmentListHead">
                        <div class="card-body">
                            <div class="input-group ">
                                @foreach($p_data['custDives'] as $k => $t)
                                    @if(empty($t->SingleSN) === false)
                                        @if($t->ServiceName === '3 DSTB')
                                            <div class="card w-100">
                                                <div class="card-body">
                                                    <span class="input-group-text bg-info"><input type="radio" name="chg_siginsn" data-sn="{{$t->SingleSN}}" data-sc="{{$t->SmartCard}}" data-si="{{$t->SubsID}}">訂編</span>
                                                    {{$t->SubsID}}
                                                    <span class="input-group-text">設備型號</span>
                                                    {{$t->SWVersion}}
                                                    <span class="input-group-text">設備序號</span>
                                                    {{$t->SingleSN}}
                                                    <span class="input-group-text w-100">SmartCard</span>
                                                    {{$t->SmartCard}}
                                                    @if($t->ChargeName2 == '02301 外接硬碟(借)')
                                                        <span class="input-group-text w-100 text-danger">外接硬碟[型號]</span>
                                                        {{$t->SWVersion2}}
                                                        <span class="input-group-text w-100 text-danger">外接硬碟[序號]</span>
                                                        {{$t->SingleSN2}}
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="card w-100">
                                                <div class="card-body">
                                                    <span class="input-group-text bg-success">訂編 (CM設備不可以切換)</span>
                                                    {{$t->SubsID}}
                                                    <span class="input-group-text">設備型號</span>
                                                    {{$t->SWVersion}}
                                                    <span class="input-group-text">設備序號</span>
                                                    {{$t->SingleSN}}
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif


                {{-- 設備清單[輸入序號] --}}
                @if(count($p_data['info']->inpDevSin))
                    <div class="card mb-3">
                        <div class="card-header pt-0 pb-0" id="inpDevSinBtnHead" data-parent="#inpDevSinBtnBody">
                            <div class="input-group">
                                <h5 class="mb-0 mr-3">
                                    輸入設備序號
                                    <label class="alert alert-info pt-2 pl-2 mb-0" id="inpDevSinLab">...</label>
                                </h5>
                            </div>
                        </div>
                        <div id="inpDevSinBtnBody" class="collapse show" data-parent="#inpDevSinBtnHead">
                            <div class="card-body">
                                    @foreach($p_data['info']->inpDevSin as $k => $t)
                                        <ul class="list-group list-group-flush mb-2">
                                            <li class="list-group-item list-group-item-success pt-0 pb-0" name="subsId_chargeName_{{$t['subsId'].'#'.$t['chargeName']}}">{{$t['subsId'].'#'.$t['chargeName']}}</li>
                                            <li class="list-group-item list-group-item-Secondary pt-0 pb-0" name="singleSn_{{$t['subsId']}}">序號:{{$t['singleSn']}}</li>
                                            <li class="singlesn list-group-item list-group-item-info pt-0 pb-0">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">輸入序號</div>
                                                    </div>
                                                    <input type="text" class="form-control bg-white" maxlength="40" name="newSingleSn"
                                                           data-subsid="{{$t['subsId']}}" data-chargename="{{$t['chargeName']}}"
                                                    value="{{$t['singleSn']}}"/>
                                                    <select class="custom-select pl-0" name="newSWVersion">
                                                        <option value="">請選擇(型號)</option>
                                                        @foreach(config('order.MESHAPModelList') as $k2 => $t2)
                                                        <option value="{{ $t2 }}" @if($t['swVersion'] == $t2) selected @endif>{{ $t2 }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </li>
                                        </ul>
                                    @endforeach
                                <div class="input-group float-right">
                                    <label class="btn btn-primary mb-0 w-100">
                                        <input class="d-none" id="inpDevSinBtn" onclick="setDevicSelign()" >
                                        送出設備序號
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif


                {{-- HDCP開關 --}}
                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0">
                        <div class="input-group">
                            HDCP開關
                        </div>
                    </div>
                    <div class="card-body">
                        <label class="btn btn-info mb-0">
                            <input class="d-none" type="button" onclick="if(confirm('確認開啟HDCP'))enableHDCP()">
                            開啟HDCP
                        </label>
                        <label class="btn btn-warning mb-0">
                            <input class="d-none" type="button" onclick="if(confirm('確認關閉HDCP'))disableHDCP()">
                            關閉HDCP
                        </label>
                    </div>
                </div>


                {{-- 品質查詢 --}}
                @if(!empty($p_data['chargeInfo']) && strpos($p_data['info']->ServiceName,'FTTH') < 1)
                    @foreach($p_data['chargeInfo'] as $k => $t)
                        @if($p_data['serviceNameAry2'] == $k && !empty($p_data['serviceNameAry2']))
                            <div class="card mb-3">
                                <div class="card-header pt-0 pb-0" id="cmqualityforkg_{{$t[0]->SubsID}}_Head">
                                    <h5 class="mb-0">
                                        <button class="btn btn-primary collapsed btn_collapsed" data-toggle="collapse" data-target="#cmqualityforkg_{{$t[0]->SubsID}}_Body">
                                            網路品質查詢(SubsID:{{ $t[0]->SubsID }})
                                            <svg  width="16" height="16" fill="currentColor" class="bi bi-arrows-collapse" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zm7-8a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 4.293V.5A.5.5 0 0 1 8 0zm-.5 11.707-1.146 1.147a.5.5 0 0 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 11.707V15.5a.5.5 0 0 1-1 0v-3.793z"/>
                                            </svg>
                                        </button>
                                        <label class="btn btn-info mb-0">
                                            <svg width="16" height="16" fill="currentColor" class="bi bi-bar-chart" viewBox="0 0 16 16">
                                                <path d="M4 11H2v3h2v-3zm5-4H7v7h2V7zm5-5v12h-2V2h2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1h-2zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zm-5 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3z"/>
                                            </svg>
                                            <input class="d-none" type="button" data-subsid="{{$t[0]->SubsID}}" name="cmqualityforkg_btn" id="cmqualityforkg_{{$t[0]->SubsID}}_btn" >
                                            網路品質查詢
                                        </label>
                                        <label class="btn btn-success mb-0">
                                            <svg width="16" height="24" fill="currentColor" class="bi bi-save" viewBox="0 0 16 16">
                                                <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z"/>
                                            </svg>
                                            <input class="d-none" type="button" data-cmqualityforkg="" data-subsid="{{$t[0]->SubsID}}" name="cmqualityforkg_save_btn" id="cmqualityforkg_{{$t[0]->SubsID}}_save_btn" >
                                            存檔
                                        </label>
                                        <label class="alert alert-info pt-2 pl-2 mb-0" id="cmqualityforkg_{{$t[0]->SubsID}}_label">網路品質查詢信息</label>
                                        <input type="hidden" id="cmqualityforkg_{{$t[0]->SubsID}}_value">
                                    </h5>
                                </div>
                                <div id="cmqualityforkg_{{$t[0]->SubsID}}_Body" class="collapse show" data-parent="#cmqualityforkg_{{$t[0]->SubsID}}_Head">
                                    <div class="card-body">
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif


                {{-- CM 新增[幹線]維修工單 --}}
                @if($p_data['IsTest'] > 0 && 0)
                @if(!empty($p_data['info']->serviceNameForI))
                    <div class="card d-none mb-3" id="addWorkerMain">
                        <div class="card-header">
                            <div class="input-group">
                                [I]新增(幹線)維修工單
                            </div>
                        </div>
                        <div class="card-body">
                            <label class="btn btn-info mb-0">
                                <input class="d-none" type="button" onclick="if(confirm('確認新增(幹線)維修工單'))addWorkerMain('{{$t[0]->SubsID}}')">
                                新增(幹線)維修工單
                            </label>
                            <label class="alert alert-info pt-2 pl-2 mb-0" id="addWorkerMain_label">CM 新增(維修)維修工單Yes/No</label>
                            <br>
                            <label class="alert alert-danger pt-2 pl-2 mb-0 w-100">維修原因</label>
                            <select class="custom-select" id="select_srviceReasonFirst"></select>
                            <select class="custom-select" id="select_srviceReasonLast"></select>
                        </div>
                    </div>
                @endif
                @endif


                {{-- FTTH 狀態查測 --}}
                @if(strpos($p_data['info']->ServiceName,'FTTH'))
                    <div class="card mb-3">
                        <div class="card-header pt-0 pb-0" id="ftthDeviceInfo_Head">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#ftthDeviceInfo_Body">
                                    FTTH狀態查測
                                </button>
                                <label class="btn btn-info mb-0">
                                    <svg width="16" height="16" fill="currentColor" class="bi bi-bar-chart" viewBox="0 0 16 16">
                                        <path d="M4 11H2v3h2v-3zm5-4H7v7h2V7zm5-5v12h-2V2h2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1h-2zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zm-5 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3z"/>
                                    </svg>
                                    <input class="d-none" type="button" name="ftthDeviceInfo_btn" id="ftthDeviceInfo_btn" >
                                    FTTH狀態查測
                                </label>
                                <label class="btn btn-success mb-0 d-none">
                                    <svg width="16" height="24" fill="currentColor" class="bi bi-save" viewBox="0 0 16 16">
                                        <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z"/>
                                    </svg>
                                    <input class="d-none" type="button" data-ftthDeviceInfo="" name="ftthDeviceInfo_save_btn" id="ftthDeviceInfo_save_btn" >
                                    存檔
                                </label>
                                <label class="alert alert-info pt-2 pl-2 mb-0" id="ftthDeviceInfo_label">FTTH狀態查測</label>
                            </h5>
                        </div>
                        <div id="ftthDeviceInfo_Body" class="collapse show" data-parent="#ftthDeviceInfo_Head">
                            <div class="card-body">
                            </div>
                        </div>
                    </div>
                @endif


                @if(!empty($p_data['serviceNameAry2']))
                    {{-- CMNS測速查詢 --}}
                    <div class="card border-danger bw-5p mb-3">
                        <div class="card-header">
                            <div class="input-group">
                                <label class="btn btn-info mb-0">
                                    <svg width="16" height="16" fill="currentColor" class="bi bi-bar-chart" viewBox="0 0 16 16">
                                        <path d="M4 11H2v3h2v-3zm5-4H7v7h2V7zm5-5v12h-2V2h2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1h-2zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zm-5 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3z"/>
                                    </svg>
                                    <input class="d-none" type="button" id="cmnsQueryBtn" onclick="cmnsQuery()">
                                    CMNS測速查詢
                                </label>
                                <label class="alert alert-info pt-0 pb-0 pl-1 pr-1 ml-3 mb-0" id="cmnsQuery_label">...</label>
                            </div>

                        </div>
                        <div class="card-body" id="cmnsQueryBody" data-json="">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th style="width: 145px;">欄位</th>
                                    <th>數值</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <div class="input-group float-right d-none" id="cmnsQuerySaveDiv">
                                <label class="btn btn-success w-100 mb-0 float-right">
                                    <input class="d-none" id="cmnsQuerySaveBtn" data-json="" onclick="cmnsQuerySave()">
                                    存檔
                                </label>
                            </div>
                        </div>
                    </div>


                    {{-- wifi環境檢測 --}}
                    <div class="card border-danger bw-5p mb-3">
                        <div class="card-header">
                            <div class="input-group">
                                wifi環境檢測
                                <label class="alert alert-info pt-0 pb-0 pl-1 pr-1 ml-3 mb-0" id="WifiTestValue_label"> wifi 環 境 檢 測 數 據</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="m-0">經常上網位置</p>

                            <div class="input-group mb-0">
                                <div class="input-group-prepend">
                                    <label class="btn btn-info mb-0 pl-0 pr-0">
                                        <input class="d-none" type="button" onclick="getSSID()">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-patch-question" viewBox="0 0 16 16">
                                            <path d="M8.05 9.6c.336 0 .504-.24.554-.627.04-.534.198-.815.847-1.26.673-.475 1.049-1.09 1.049-1.986 0-1.325-.92-2.227-2.262-2.227-1.02 0-1.792.492-2.1 1.29A1.71 1.71 0 0 0 6 5.48c0 .393.203.64.545.64.272 0 .455-.147.564-.51.158-.592.525-.915 1.074-.915.61 0 1.03.446 1.03 1.084 0 .563-.208.885-.822 1.325-.619.433-.926.914-.926 1.64v.111c0 .428.208.745.585.745z"/>
                                            <path d="m10.273 2.513-.921-.944.715-.698.622.637.89-.011a2.89 2.89 0 0 1 2.924 2.924l-.01.89.636.622a2.89 2.89 0 0 1 0 4.134l-.637.622.011.89a2.89 2.89 0 0 1-2.924 2.924l-.89-.01-.622.636a2.89 2.89 0 0 1-4.134 0l-.622-.637-.89.011a2.89 2.89 0 0 1-2.924-2.924l.01-.89-.636-.622a2.89 2.89 0 0 1 0-4.134l.637-.622-.011-.89a2.89 2.89 0 0 1 2.924-2.924l.89.01.622-.636a2.89 2.89 0 0 1 4.134 0l-.715.698a1.89 1.89 0 0 0-2.704 0l-.92.944-1.32-.016a1.89 1.89 0 0 0-1.911 1.912l.016 1.318-.944.921a1.89 1.89 0 0 0 0 2.704l.944.92-.016 1.32a1.89 1.89 0 0 0 1.912 1.911l1.318-.016.921.944a1.89 1.89 0 0 0 2.704 0l.92-.944 1.32.016a1.89 1.89 0 0 0 1.911-1.912l-.016-1.318.944-.921a1.89 1.89 0 0 0 0-2.704l-.944-.92.016-1.32a1.89 1.89 0 0 0-1.912-1.911l-1.318.016z"/>
                                            <path d="M7.001 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0z"/>
                                        </svg>
                                        SSID:
                                    </label>
                                </div>
                                <input type="text" class="form-control" placeholder="SSID" id="ssid_val" readonly >
                            </div>

                            @for($i = 0; $i < 5; $i++)
                                <div class="input-group mb-0">
                                    <div class="input-group-prepend">
                                        {{--                                    <span class="input-group-text pl-0">第{{$i}}常上網</span>--}}
                                        <label class="btn btn-primary mb-0 pl-0 pr-0">
                                            <input class="d-none" type="button" onclick="wifiTestFunc({{$i}})">
                                            <svg width="24" height="24" fill="currentColor" class="bi bi-wifi" viewBox="0 0 16 16">
                                                <path d="M15.384 6.115a.485.485 0 0 0-.047-.736A12.444 12.444 0 0 0 8 3C5.259 3 2.723 3.882.663 5.379a.485.485 0 0 0-.048.736.518.518 0 0 0 .668.05A11.448 11.448 0 0 1 8 4c2.507 0 4.827.802 6.716 2.164.205.148.49.13.668-.049z"></path>
                                                <path d="M13.229 8.271a.482.482 0 0 0-.063-.745A9.455 9.455 0 0 0 8 6c-1.905 0-3.68.56-5.166 1.526a.48.48 0 0 0-.063.745.525.525 0 0 0 .652.065A8.46 8.46 0 0 1 8 7a8.46 8.46 0 0 1 4.576 1.336c.206.132.48.108.653-.065zm-2.183 2.183c.226-.226.185-.605-.1-.75A6.473 6.473 0 0 0 8 9c-1.06 0-2.062.254-2.946.704-.285.145-.326.524-.1.75l.015.015c.16.16.407.19.611.09A5.478 5.478 0 0 1 8 10c.868 0 1.69.201 2.42.56.203.1.45.07.61-.091l.016-.015zM9.06 12.44c.196-.196.198-.52-.04-.66A1.99 1.99 0 0 0 8 11.5a1.99 1.99 0 0 0-1.02.28c-.238.14-.236.464-.04.66l.706.706a.5.5 0 0 0 .707 0l.707-.707z"></path>
                                            </svg>
                                            第{{$i + 1}}:
                                        </label>
                                    </div>
                                    <div class="input-group-prepend">
                                        <select class="custom-select pl-0" id="wifiTestFloor_{{$i}}_select" name="wifiTestFloor_select[]">
                                            @foreach($p_data['wifiTestFloorAry'] as $t)
                                                <option value="{{$t}}">{{$t}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="input-group-prepend">
                                        <select class="custom-select pl-0" id="wifiTestPoint_{{$i}}_select" name="wifiTestPoint_select[]">
                                            @foreach($p_data['wifiTestPointAry'] as $k => $t)
                                                <option value="{{$t}}" @if($i == $k) selected @else disabled @endif>{{$t}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <input type="text" class="form-control" placeholder="wifi檢測數據" id="wifiTest_{{$i}}_value" name="wifiTest_value[]" maxlength="7" readonly >
                                    <div class="input-group-append ">
                                        <input type="hidden" id="wifiTest_{{$i}}_grade" name="wifiTest_grade[]">
                                        <span class="input-group-text p-0 grade_{{$i}}">待測</span>
                                    </div>
                                </div>
                            @endfor
                            <div class="input-group float-right">
                                <label class="btn btn-info  mb-0 float-right">
                                    <input class="d-none" id="WifiTestValue_btn">
                                    送出
                                </label>
                            </div>
                        </div>
                    </div>
                @endif


                {{-- 順推 順推  --}}
                <div class="card border-danger bw-5p mb-3">
                    <div class="card-header">
                        <div class="input-group">
                            順推-加購WifiAP
                            <label class="alert alert-info pt-0 pb-0 pl-1 pr-1 ml-3 mb-0" id="saleap_label">[{{ $p_data['saleAP'] }}]</label>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="input-group mb-0 w-100">
                            <div class="input-group-prepend w-100">
                                <ul class="list-group w-100">
                                    <label for="saleap_good" class="cursor-pointer"><li class="list-group-item"><input type="radio" id="saleap_good" name="saleap" value="訊號良好，無須加購">訊號良好，無須加購</li></label>
                                    <label for="saleap_bad" class="cursor-pointer"><li class="list-group-item"><input type="radio" id="saleap_bad" name="saleap" value="訊號不良，順推單購">訊號不良，順推單購</li></label>
                                </ul>
                            </div>
                        </div>
                        <div class="input-group float-right">
                            <label class="btn btn-info  mb-0 w-100">
                                <input class="d-none" id="saleap_btn">
                                送出
                            </label>
                        </div>
                    </div>
                </div>


                {{-- 順推產品 --}}
                <div class="card mb-3 d-none">
                    <div class="card-header pt-0 pb-0" id="chargeProductHead">
                        <div class="input-group">
                            <h5 class="mb-0 mr-3">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#chargeProductBody">
                                    順推產品
                                </button>
                            </h5>
                            <div class="input-group-append">
                                <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3 " id="label_chargeProduct">
                                    順推產品：
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="chargeProductBody" class="collapse show" data-parent="#chargeProductHead">
                        <div class="card-body">
                            <select class="custom-select" id="select_chargeProduct" onchange="">
                                <option>請選擇 產品</option>
                                @foreach($p_data['chargeProduct'] as $k => $t)
                                    <option data-chargeName="{{$t['chargeName']}}" data-amt="{{$t['baseAmt']}}">{{$t['chargeName'].' ($'.$t['baseAmt'].')'}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>


                {{-- 家戶側寫 --}}
                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0" id="surveyHead">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#surveyBody" id="button_survey">
                                家戶側寫
                            </button>
                            <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3" id="label_survey">
                                點選[家戶側寫]開始側寫。
                            </label>
                        </h5>
                    </div>
                    <div id="surveyBody" class="collapse" data-parent="#surveyHead">
                        <div class="card-body" onclick="alert('iframe div click')">
                            <iframe width="100%" height="500" id="iframe_survey"
                                src="{{config('order.R1_URL')}}/chp/survey/{{$p_data['info']->WorkerNum}}/{{$p_data['info']->CompanyNo}}/{{$p_data['info']->SubsID[0]}}">
                            </iframe>
                        </div>
                    </div>
                </div>


                {{-- 身分證-正面 --}}
                <div class="card mb-3" id="div_id_01">
                    <div class="card-header" >
                        <div class="input-group">
                            <label class="btn btn-info mb-0">
                                <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">
                                    <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                                </svg>
                                <input class="d-none" type="file" accept="image/*" id="file_id_01">
                                上傳身分證正面_圖片
                            </label>
                            <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3 @if(is_null($p_data['info']->id01) === true) d-none @endif" id="label_id_01">
                                上傳時間：{{date('Y-m-d H:i:s',strtotime($p_data['info']->id01))}}
                            </label>
                        </div>
                    </div>
                    <div class="collapse show" data-parent="#uploIdHead">
                        <div class="card-body">
                            <div class="divWatemark">
                            <img class=" @if(is_null($p_data['info']->id01) === true) d-none w-0 @endif" width="500"
                                 id="img_id_01" name="img_id_01" src="/upload/{{$p_data['uploaddir']}}/identity_01.jpg?i={{date('His')}}" onerror="this.src='/img/error_02.png'" />
                            </div>
                        </div>
                    </div>
                    <div class="collapse" id="alert_id_01">
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <strong class="h1">請確認上傳身分證正面圖片是否清楚?</strong>
                            <button type="button" class="close" onclick="$('#alert_id_01').collapse('hide')">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>


                {{-- 身分證-反面 --}}
                <div class="card mb-3">
                    <div class="card-header" >
                        <div class="input-group">
                            <label class="btn btn-info mb-0">
                                <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">
                                    <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                                </svg>
                                <input class="d-none" type="file" accept="image/*" id="file_id_02" >
                                上傳身分證反面_圖片
                            </label>
                            <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3 @if(is_null($p_data['info']->id02) === true) d-none @endif" id="label_id_02">
                                上傳時間：{{date('Y-m-d H:i:s',strtotime($p_data['info']->id02))}}
                            </label>
                        </div>
                    </div>
                    <div class="collapse show" data-parent="#uploIdHead">
                        <div class="card-body">
                            <div class="divWatemark">
                            <img class=" @if(is_null($p_data['info']->id02) === true) d-none w-0 @endif" width="500"
                                 id="img_id_02" name="img_id_02" src="/upload/{{$p_data['uploaddir']}}/identity_02.jpg?i={{date('His')}}" onerror="this.src='/img/error_02.png'" />
                            </div>
                        </div>
                    </div>
                    <div class="collapse" id="alert_id_02">
                        <div class="alert alert-warning alert-dismissible" role="alert">
                            <strong class="h1">請確認上傳身分證反面圖片是否清楚?</strong>
                            <button type="button" class="close" onclick="$('#alert_id_02').collapse('hide')">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>


                {{-- 證件照 dialog --}}
                <div id="idphoto_dialog" title="上傳圖片檢查" class="d-none">

                    <div class="card w-80">
                        <div class="card-header">
                            證件照2
                        </div>
                        <div class="card-body">
                            <img src="">
                            掃描內容:<label class="alert alert-info w-100" id="idphoto_dialog_alert"></label>
                        </div>
                    </div>

                    <button class="button" onclick="document.body.style.overflow = 'scroll';$('#idphoto_dialog').dialog('close');">確定</button>
                </div>


                {{-- 證件照2 --}}
                <div class="card mb-3">
                    <div class="card-header" >
                        <div class="input-group">
                            <label class="btn btn-info mb-0">
                                <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">
                                    <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                                </svg>
                                <input class="d-none" type="file" accept="image/*" data-names="{{$p_data['info']->id03Photo}}" id="file_id03Photo" >
                                第二證件[Max 3]
                            </label>
                            <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3 d-none" id="label_id03Photo"></label>
                        </div>
                    </div>
                    <div class="collapse show" data-parent="#uploIdHead">
                        <div class="card-body" id="id03Photo_img">
                            @if(strlen($p_data['info']->id03Photo ) > 0)
                                @foreach(json_decode($p_data['info']->id03Photo,1) as $k => $t)
                                    <div class="divWatemark mb-1">
                                        <img class="constructionPhoto-img" id="{{explode('.',$t)[0]}}" width="500" name="img_id_03"
                                             src="/upload/{{$p_data['uploaddir'].'/'.$t}}?i={{date('His')}}" onerror="this.src='/img/error_02.png'"
                                             ondblclick="id03PhotDBClick($(this))" >
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>


                {{-- 上傳憑證 --}}
                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0" id="certificateHead">
                        <label class="btn btn-light mb-0">
                            <input class="d-none" data-toggle="collapse" data-target="#certificateBody" id="certificate_btn">
                            上傳憑證 (1G & 低收)
                        </label>
                    </div>
                    <div id="certificateBody" class="collapse" data-parent="#certificateHead">
                        <div class="card-body">
                            <iframe width="100%" height="500" id="iframe_survey"
                                src="{{config('order.R1_URL')}}/tm/customerupload/{{$p_data['user_info']['userId']}}/{{$p_data['info']->CompanyNo}}/{{$p_data['cmSubsid']}}">
                            </iframe>
                        </div>
                    </div>
                </div>


                {{-- 上傳附件圖片 --}}
                <div class="card mb-3" id="div_certificate_01">
                    <div class="card-header" >
                        <div class="input-group">
                            <label class="btn btn-info mb-0">
                                <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">
                                    <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                                </svg>
                                <input class="d-none" type="file" accept="image/*" id="file_certificate_01" >
                                上傳附件圖片01
                            </label>
                            <label class="alert alert-primary p-0 pt-1 pl-2 pr-2 mb-0 ml-3 @if(is_null($p_data['info']->cert01) === true) d-none @endif" id="label_certificate_01">
                                上傳時間：{{date('Y-m-d H:i:s',strtotime($p_data['info']->cert01))}}
                            </label>
                        </div>
                    </div>

                    <div class="collapse show" data-parent="#uploIdHead">
                        <div class="card-body">
                            <div class="divWatemark">
                            <img class=" @if(is_null($p_data['info']->cert01) === true) d-none w-0 @endif" width="500"
                                 id="img_certificate_01" name="img_certificate_01" src="/upload/{{$p_data['uploaddir']}}/certificate_01.jpg?i={{date('His')}}" onerror="this.src='/img/error_02.png'" />
                            </div>
                        </div>
                    </div>
                </div>


                {{-- 上傳附件圖片 --}}
                <div class="card mb-3" id="div_certificate_02">
                    <div class="card-header" >
                        <div class="input-group">
                            <label class="btn btn-info mb-0">
                                <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">
                                    <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                                </svg>
                                <input class="d-none" type="file" accept="image/*" id="file_certificate_02" >
                                上傳附件圖片02
                            </label>
                            <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3 @if(is_null($p_data['info']->cert02) === true) d-none @endif" id="label_certificate_02">
                                上傳時間：{{date('Y-m-d H:i:s',strtotime($p_data['info']->cert02))}}
                            </label>
                        </div>
                    </div>
                    <div class="collapse show" data-parent="#uploIdHead">
                        <div class="card-body divWatemark">
                            <div class="divWatemark">
                            <img class=" @if(is_null($p_data['info']->cert02) === true) d-none w-0 @endif" width="500"
                                 id="img_certificate_02" name="img_certificate_02" src="/upload/{{$p_data['uploaddir']}}/certificate_02.jpg?i={{date('His')}}" onerror="this.src='/img/error_02.png'" />
                            </div>
                        </div>
                    </div>
                </div>


                {{-- 施工照片 --}}
                <div class="card mb-3">
                    <div class="card-header" >
                        <div class="input-group">
                            <label class="btn btn-info mb-0">
                                <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">
                                    <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                                </svg>
                                <input class="d-none" type="file" accept="image/*" data-names="{{$p_data['info']->constructionPhoto}}" id="file_constructionPhoto" >
                                施工照片
                            </label>
                            <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3 d-none" id="label_constructionPhoto"></label>
                        </div>
                    </div>
                    <div class="collapse show" data-parent="#uploIdHead">
                        <div class="card-body" id="constructionPhoto_img">
                            @if(strlen($p_data['info']->constructionPhoto ) > 0)
                            @foreach(json_decode($p_data['info']->constructionPhoto,1) as $k => $t)
                                <img class=" pb-1 constructionPhoto-img" name="{{explode('.',$t)[0]}}" width="500" src="/upload/{{$p_data['uploaddir'].'/'.$t}}?i={{date('His')}}"
                                     onerror="this.src='/img/error_02.png'" ondblclick="constructionPhotoDBClick($(this))" />
                            @endforeach
                            @endif
{{--                            <img class=" @if(is_null($p_data['info']->cert02) === true) d-none w-0 @endif"--}}
{{--                                 id="img_constructionPhoto" src="/upload/{{$p_data['uploaddir']}}/constructionPhoto.jpg?i={{date('His')}}" onerror="this.src='/img/error_02.png'">--}}
                        </div>
                    </div>
                </div>


{{--                <div class="card mb-3">--}}
{{--                    <div class="card-header pt-0 pb-0" id="uploCertHead">--}}
{{--                        <h5 class="mb-0">--}}
{{--                            <button class="btn btn-link" data-toggle="collapse" data-target="#uploCertBody">--}}
{{--                                憑證上傳--}}
{{--                            </button>--}}
{{--                        </h5>--}}
{{--                    </div>--}}
{{--                    <div id="uploCertBody" class="collapse show" data-parent="#uploCertHead">--}}
{{--                        <div class="card-body">--}}
{{--                            <div class="input-group-append p-0 col-10">--}}
{{--                                <input lang="zh" type="file" accept="image/*" id="uploCertPho01" onchange="upload($(this).attr('id'),'certificate_01.jpg')">--}}
{{--                            </div>--}}
{{--                            <div class="input-group-append p-0 col-10">--}}
{{--                                <input type="file" accept="image/*" id="uploCertPho02" onchange="upload($(this).attr('id'),'certificate_02.jpg')">--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}


                {{-- 開通 --}}
                @foreach($p_data['info']->AssignSheetAry as $k => $t)
                    @if(in_array('3 DSTB',array_keys($t)))
                        <div class="card mb-3" >
                            <div class="card-header">
                                <div class="input-group">
                                    <label class="btn btn-danger mb-0">
                                        <input class="d-none" type="button" id="openButton" onclick="openscan('{{$k}}')">
                                        <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">
                                            <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                                            <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                                        </svg>
                                        開通 <u>{{$k.'訂編'.$t['3 DSTB']['subsid'].$t['3 DSTB']['IncludeHD']}}</u> 掃描
                                    </label>
                                </div>
                            </div>
                            <div class="collapse show" data-parent="#uploIdHead">
                                <div class="card-body" id="openBody">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-danger" type="button" data-worksheet="{{$k}}" data-subscp="{{$t['3 DSTB']['subscp2']}}" id="open{{$k}}_btn" onclick="stbApi('authorstb','{{$k}}')">開通</button>
                                        </div>
                                        <input type="text" class="form-control bg-white" id="open{{$k}}_scanstr" data-subsid="{{$t['3 DSTB']['subsid']}}" placeholder="請先掃描電視QRCode" onfocus="$(this).val('請先掃描電視QRCode')" readonly />
                                    </div>
                                    <div class="alert alert-danger mb-0" role="alert" id="open{{$k}}_alert">OpenAlert</div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach


                {{-- STB、ATV設備參數紀錄 --}}
                <div class="card mb-3" id="stbmatvdevicvalue_card">
                    <div class="card-header pt-0 pb-0" id="stbmatvdevicvalue_head">
                        <h5 class="mb-0">
                            <button class="btn btn-primary collapsed btn_collapsed" data-toggle="collapse" data-target="#stbmatvdevicvalue_body">
                                STB、DTV設備參數紀錄
                                <svg  width="16" height="16" fill="currentColor" class="bi bi-arrows-collapse" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zm7-8a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 4.293V.5A.5.5 0 0 1 8 0zm-.5 11.707-1.146 1.147a.5.5 0 0 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 11.707V15.5a.5.5 0 0 1-1 0v-3.793z"/>
                                </svg>
                            </button>
                        </h5>
                    </div>
                    <div id="stbmatvdevicvalue_body" class="collapse" data-parent="#stbmatvdevicvalue_head">
                        <div class="card-body">
                            <div class="input-group mb-2">
                                <label class="btn btn-info mb-2">
                                    <input class="d-none" type="button" id="openButton" onclick="openscan('stbmatvdevicvalue')">
                                    <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">
                                        <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                                        <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                                    </svg>
                                    請掃描 STB or ATV 電視QrCode
                                </label>
                                <input type="text" class="form-control bg-white" id="openstbmatvdevicvalue_scanstr" data-subsid="" placeholder="請掃描 STB or ATV 電視QrCode" onfocus="$(this).val('請掃描 STB or ATV 電視QrCode')" readonly />
                            </div>
                            <div class="input-group">
                                <ul class="list-group list-group-flush w-100" id="stbmatvdevicvalue_list">
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- 設備借用單[v3] --}}
                <div class="card mb-3" id="borrowminglist_card">
                    <div class="card-header pt-0 pb-0" id="borrowminglist_head">
                        <h5 class="mb-0">
                            <button class="btn btn-primary collapsed btn_collapsed" data-toggle="collapse" data-target="#borrowminglist_body">
                                設備借用單(NEW)
                                <svg  width="16" height="16" fill="currentColor" class="bi bi-arrows-collapse" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zm7-8a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 4.293V.5A.5.5 0 0 1 8 0zm-.5 11.707-1.146 1.147a.5.5 0 0 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 11.707V15.5a.5.5 0 0 1-1 0v-3.793z"/>
                                </svg>
                            </button>
                        </h5>
                    </div>
                    <div id="borrowminglist_body" class="collapse show" data-parent="#borrowminglist_head">
                        <div class="card-body">
                            <form id="borrowminglist_form" enctype="multipart/form-data">
{{--                                @if(in_array('1 CATV',$p_data['serviceNameAry']) || in_array('3 DSTB',$p_data['serviceNameAry']))--}}
                                    @if(isset($p_data['equipmentList']['D']))
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item list-group-item-warning pb-0">
                                            {{ $p_data['equipmentList']['D'][0]['typeDesc'] }}
                                        </li>
                                        @foreach($p_data['equipmentList']['D'] as $k => $t)
                                            <li class="list-group-item pt-0 pb-0">
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ $t['deviceName'] }}</span>
                                                    <span class="input-group-text">{{ $t['amt'] }}元/{{ $t['qtyType'] }}</span>
                                                    <select class="deviceList" name="{{ $t['Id'] }}">
                                                        <option value="0">0{{ $t['qtyType'] }}</option>
                                                        @for($i = 1; $i <= 20; $i++)
                                                            <option value="{{ $i }}">{{ $i.$t['qtyType'] }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @endif
{{--                                @endif--}}
{{--                                @if(!in_array($p_data['serviceNameAry2'],['1 CATV','3 DSTB']))--}}
                                    @if(isset($p_data['equipmentList']['I']))
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item list-group-item-warning pb-0">
                                            {{ $p_data['equipmentList']['I'][0]['typeDesc'] }}
                                        </li>
                                        @foreach($p_data['equipmentList']['I'] as $k => $t)
                                            <li class="list-group-item pt-0 pb-0">
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ $t['deviceName'] }}</span>
                                                    <span class="input-group-text">{{ $t['amt'] }}元/{{ $t['qtyType'] }}</span>
                                                    <select class="deviceList" name="{{  $t['Id'] }}">
                                                        <option value="0">0{{ $t['qtyType'] }}</option>
                                                        @for($i = 1; $i <= 20; $i++)
                                                            <option value="{{ $i }}">{{ $i.$t['qtyType'] }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                   @endif
{{--                                @endif--}}
                                @if(isset($p_data['equipmentList']['HP']))
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item list-group-item-warning pb-0">
                                            {{ $p_data['equipmentList']['HP'][0]['typeDesc'] }}
                                        </li>
                                        @foreach($p_data['equipmentList']['HP'] as $k => $t)
                                            <li class="list-group-item pt-0 pb-0">
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ $t['deviceName'] }}</span>
                                                    <span class="input-group-text">{{ $t['amt'] }}元/{{ $t['qtyType'] }}</span>
                                                    <select class="deviceList" name="{{  $t['Id'] }}">
                                                        <option value="0">0{{ $t['qtyType'] }}</option>
                                                        @for($i = 1; $i <= 20; $i++)
                                                            <option value="{{ $i }}">{{ $i.$t['qtyType'] }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>


                {{-- 設備取回單[v3] --}}
                <div class="card mb-3" id="retrievelist_card">
                    <div class="card-header pt-0 pb-0" id="retrievelist_head">
                        <h5 class="mb-0">
                            <button class="btn btn-primary collapsed btn_collapsed" data-toggle="collapse" data-target="#retrievelist_body">
                                設備取回單(NEW)
                                <svg  width="16" height="16" fill="currentColor" class="bi bi-arrows-collapse" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zm7-8a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 4.293V.5A.5.5 0 0 1 8 0zm-.5 11.707-1.146 1.147a.5.5 0 0 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 11.707V15.5a.5.5 0 0 1-1 0v-3.793z"/>
                                </svg>
                            </button>
                        </h5>
                    </div>
                    <div id="retrievelist_body" class="collapse" data-parent="#retrievelist_head">
                        <div class="card-body">
                            <form id="retrievelist_form" enctype="multipart/form-data">
{{--                                @if(in_array('1 CATV',$p_data['serviceNameAry']) || in_array('3 DSTB',$p_data['serviceNameAry']))--}}
                                    @if(isset($p_data['equipmentList']['D']))
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item list-group-item-warning pb-0">
                                            {{ $p_data['equipmentList']['D'][0]['typeDesc'] }}
                                        </li>
                                        @foreach($p_data['equipmentList']['D'] as $k => $t)
                                            <li class="list-group-item pt-0 pb-0">
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ $t['deviceName'] }}</span>
                                                    <span class="input-group-text">{{ $t['amt'] }}元/{{ $t['qtyType'] }}</span>
                                                    <select class="deviceList" name="{{ $t['Id'] }}">
                                                        <option value="0">0{{ $t['qtyType'] }}</option>
                                                        @for($i = 1; $i <= 20; $i++)
                                                            <option value="{{ $i }}">{{ $i.$t['qtyType'] }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @endif
{{--                                @endif--}}
{{--                                @if(!in_array($p_data['serviceNameAry2'],['1 CATV','3 DSTB']))--}}
                                    @if(isset($p_data['equipmentList']['I']))
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item list-group-item-warning pb-0">
                                            {{ $p_data['equipmentList']['I'][0]['typeDesc'] }}
                                        </li>
                                        @foreach($p_data['equipmentList']['I'] as $k => $t)
                                            <li class="list-group-item pt-0 pb-0">
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ $t['deviceName'] }}</span>
                                                    <span class="input-group-text">{{ $t['amt'] }}元/{{ $t['qtyType'] }}</span>
                                                    <select class="deviceList" name="{{  $t['Id'] }}">
                                                        <option value="0">0{{ $t['qtyType'] }}</option>
                                                        @for($i = 1; $i <= 20; $i++)
                                                            <option value="{{ $i }}">{{ $i.$t['qtyType'] }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    @endif
{{--                                @endif--}}
                                @if(isset($p_data['equipmentList']['HP']))
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item list-group-item-warning pb-0">
                                            {{ $p_data['equipmentList']['HP'][0]['typeDesc'] }}
                                        </li>
                                        @foreach($p_data['equipmentList']['HP'] as $k => $t)
                                            <li class="list-group-item pt-0 pb-0">
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ $t['deviceName'] }}</span>
                                                    <span class="input-group-text">{{ $t['amt'] }}元/{{ $t['qtyType'] }}</span>
                                                    <select class="deviceList" name="{{  $t['Id'] }}">
                                                        <option value="0">0{{ $t['qtyType'] }}</option>
                                                        @for($i = 1; $i <= 20; $i++)
                                                            <option value="{{ $i }}">{{ $i.$t['qtyType'] }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>


                {{-- 紙本工單 --}}
                <div class="card mb-3">
                    <div class="card-header" >
                        <div class="input-group">
                            <div class="btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-info active">
                                    <input type="checkbox" autocomplete="off" id="PaperPDF">紙本工單
                                </label>
                                <label class="alert alert-info p-2 pl-2 pt-0 mb-0" id="PaperPDF_alert">紙本工單信息。</label>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- 已核個資 --}}
                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0">
                        <div class="input-group">
                            <label class="alert p-0 m-0">
                                已核個資
                            </label>
                            <label class="alert alert-info mb-0 ml-3 p-0" id="certified_label">...</label>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="個資..." id="certified" maxlength="20" value="{{ $p_data['info']->certified }}">
                            <div class="input-group-append">
                                <span class="input-group-text btn btn-info" id="certified_btn">送出</span>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- 信用卡定扣 --}}
                <div class="card @if($p_data['info']->ccadaf === 'Y') border-danger bw-5p @endif mb-3">
                    <div class="card-header pt-0 pb-0">
                        <div class="input-group">
                            <label class="btn btn-info mb-0">
                                <svg  width="24" height="24" fill="currentColor" class="bi bi-credit-card" viewBox="0 0 16 16">
                                    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1H2zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V7z"/>
                                    <path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1z"/>
                                </svg>
                                <input type="button" class="d-none" data-disabled="N" onclick="ccadaf_Submit($(this))">
                                信用卡定期扣款
                            </label>

                            {{-- 商品補貼案 --}}
                            @if($p_data['info']->ccadaf === 'Y')
                                <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3" data-chk="@if(!is_null($p_data['info']->etf_ccada)) Y @endif" id="label_ccadaf">
                                    設定：@if(empty($p_data['info']->etf_ccada))尚未設定@else{{date('Y-m-d H:i:s',strtotime($p_data['info']->etf_ccada))}}@endif
                                </label>
                            @else
                                <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3">
                                    設定：@if(empty($p_data['info']->etf_ccada))尚未設定@else{{date('Y-m-d H:i:s',strtotime($p_data['info']->etf_ccada))}}@endif
                                </label>
                            @endif
                        </div>
                    </div>
                </div>


                {{-- ACH [自動扣款/轉帳授權書] --}}
                @if($p_data['info']->ach === 'Y')
                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0">
                        <div class="input-group">
                            <label class="btn btn-primary mb-0">
                                <svg  width="24" height="24" fill="currentColor" class="bi bi-credit-card" viewBox="0 0 16 16">
                                    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1H2zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V7z"/>
                                    <path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1z"/>
                                </svg>
                                <input type="button" class="d-none" data-disabled="N" onclick="ach_Submit($(this))">
                                自動扣款/轉帳同意書
                            </label>
                            <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3">
                                設定：
                                @if(empty($p_data['info']->etf_ach))
                                    尚未設定
                                @else
                                    {{date('Y-m-d H:i:s',strtotime($p_data['info']->etf_ach))}}
                                @endif
                            </label>
                        </div>
                    </div>
                    @if(!empty($p_data['info']->etf_ach))
                    <div class="card-body">
                        <div class="card mb-3">
                            <div class="card-header pt-0 pb-0">
                                <div id="pdf_ach_show" style="width: 100%; height: 500px;"></div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @endif


                {{-- Line QrCode --}}
                <div class="card border-danger bw-5p mb-3 d-none">
                    <div class="card-header pt-0 pb-0" id="linkQrCodeHead">
                        <h5 class="mb-0">
                            <label class="btn btn-link mb-0" data-toggle="collapse" data-target="#linkQrCodeBody">
                                Line@中嘉寬頻線上客服
                            </label>
                            <label class="alert alert-info p-0 pt-1 pr-2 mb-0" id="linkWill_alert">&nbsp</label>
                        </h5>
                    </div>
                    <div class="card-body collapse show" id="linkQrCodeBody" data-parent="#linkQrCodeHead">
                        <p>
                            Line@中嘉寬頻線上客服 提供線上續約、帳務查詢/繳費、報修...等等服務。
                        </p>
                        <div class="text-center">
                            <img src="/img/LineQrCode/qrCodeM.png?0630" width="100">
                        </div>
                        <div class="text-left">
                            <div class="form-check alert alert-success mb-1">
                                &nbsp&nbsp&nbsp
                                <input class="form-check-input" type="radio" name="lineWill" id="lineWill1" value="Y" @if($p_data['info']->lineWill == 'Y') checked @endif >
                                <label class="form-check-label" for="lineWill1">
                                    客願意加入
                                </label>
                            </div>
                            <div class="form-check alert alert-danger">
                                &nbsp&nbsp&nbsp
                                <input class="form-check-input" type="radio" name="lineWill" id="lineWill2" value="N" @if($p_data['info']->lineWill == 'N') checked @endif >
                                <label class="form-check-label" for="lineWill2">
                                    客不願意加入
                                </label>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card mb-3 d-none">
                    <div class="card-header PDF_D pt-0 pb-0">
                        有線電視條款
                        <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3" data-read="" data-api="" data-rtime="{{ $p_data['info']->termsd }}" id="label_D_pdf">請閱覽條款...</label>
                    </div>
                    <div id="terms_D_pdf" style="width: 100%; height: 500px;"></div>
                    <div class="card-header PDF_D_pcl pt-0 pb-0">
                        有線電視條款(隱私權條款)
                        <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3" data-read="" id="label_D_pcl_pdf">請閱覽條款...</label>
                    </div>
                    <div id="terms_D_pcl_pdf" style="width: 100%; height: 500px;"></div>
                </div>


                <div class="card mb-3 d-none">
                    <div class="card-header PDF_I pt-0 pb-0">
                        寬頻條款
                        <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3" data-read="" data-api="" data-rtime="{{ $p_data['info']->termsi }}" id="label_I_pdf">請閱覽條款...</label>
                    </div>
                    <div id="terms_I_pdf" style="width: 100%; height: 500px;"></div>
                    <div class="card-header pt-0 pb-0">
                        寬頻條款(隱私條款)
                        <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3" data-read="" id="label_I_pcl_pdf">請閱覽條款...</label>
                    </div>
                    <div id="terms_I_pcl_pdf" style="width: 100%; height: 500px;"></div>
                </div>


                    {{-- 簽名 --}}
                <div class="card border-danger bw-5p mb-3">
                    <div class="card-header pt-0 pb-0" id="uploSignHead">
                        <h5 class="mb-0">
                            <button class="btn btn-primary collapsed btn_collapsed" data-toggle="collapse" data-target="#uploSignBody">
                                簽名欄位
                                <svg  width="16" height="16" fill="currentColor" class="bi bi-arrows-collapse" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zm7-8a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 4.293V.5A.5.5 0 0 1 8 0zm-.5 11.707-1.146 1.147a.5.5 0 0 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 11.707V15.5a.5.5 0 0 1-1 0v-3.793z"/>
                                </svg>
                            </button>
                        </h5>
                    </div>
                    <div id="uploSignBody" class="collapse show" data-parent="#uploSignHead">
                        <div class="card-body">

                            {{--sign--}}
                            <div class="d-none mb-3" id="signDiv_mcust">
                                <div class="input-group-prepend p-0" id="signButton_mcust">
                                    <button class="btn btn-success mr-3" id="signRestBtn_mcust" onclick="resetSignButton('open','mcust')">用戶重新簽名</button>
                                    <button class="btn btn-info mr-3" id="signUpBtn_mcust" onclick="signUpload('mcust');resetSignButton('close','mcust')">用戶簽名上傳</button>
                                    <button class="btn btn-secondary" id="signCloseBtn_mcust" onclick="resetSignButton('close','mcust')">取消</button>
                                    <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3 " id="signAlert_mcust">
                                        用戶 簽名
                                    </label>
                                    <div class="input-group-prepend">
                                        <select class="custom-select pl-0" id="selectMcustUser" onchange="signUserSelAPI()">
                                            <option value="本人簽名">本人簽名</option>
                                            <option value="家人簽名">家人簽名</option>
                                            <option value="親人簽名">親人簽名</option>
                                            <option value="朋友簽名">朋友簽名</option>
                                            <option value="管委會">管委會</option>
                                            <option value="公司/機關承辦人">公司/機關承辦人</option>
                                            <option value="未成年法定代理人">未成年法定代理人</option>
                                        </select>
                                    </div>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item p-0 pl-2">1. 已同意申裝服務之專案條款，如附(加值服務專案條款、有線電視、寬頻網路專案條款)。</li>
                                    <li class="list-group-item p-0 pl-2">2. 已閱有線電視、光纖網路服務定型化契約及個人資料蒐集告知聲明。</li>
                                    <li class="list-group-item p-0 pl-2">3. 已裝設借用業者之設備及數量於申裝地址，如附(有線電視機上盒、光纖網路數據機等，借用品項如上勾選)。</li>
                                    <li class="list-group-item p-0 pl-2">4. 同意業者無償使用申請人外牆附掛纜線，以提供申裝人正常使用上述服務，。</li>
                                    <li class="list-group-item p-0 pl-2">5. 同意業者進行機上行頻道節目收視之資訊蒐集分析及個人化內容之推薦等。</li>
                                    <li class="list-group-item p-0 pl-2">6. 確認業者已於您所申裝地址現場進行寬頻網路速率測試並告知實測速率結果，且經申裝人或代理人簽認並同意測試結果無誤。</li>
                                    <li class="list-group-item p-0 pl-2">7. 申請人同意申請「簡訊帳單」服務。業者得透過簡訊通知本人所申請服務之繳款資訊至本人提供之手機號碼(該手機需具上網功能)，本人可透過手機簡訊連結之網址即時連結電子帳單，並持該電子帳單前往中嘉寬頻門市或代收機構繳款。</li>
                                    <li class="list-group-item p-0 pl-2">8. 就本人所申請之服務，本人同意申請「簡訊帳單」服務。中嘉寬頻得透過簡訊通知本人所申請服務之繳款資訊至本人提供之手機號碼(該手機需具上網功能)，本人可透過手機簡訊連結之網址即時連結電子帳單，並持該電子帳單前往中嘉寬頻門市或代收機構繳款。</li>
                                    <li class="list-group-item p-0 pl-2 list-group-item-danger">上述 1～8項說明，本人/本公司或受託代辦人均已充分了解並同意。</li>
                                </ul>
                                <img src="/upload/{{$p_data['uploaddir']}}/sign_mcust_{{$p_data['info']->WorkSheet}}.jpg?i={{date('His')}}" width="500" id="signShow_mcust">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item p-0 pl-2">附註：</li>
                                    <li class="list-group-item p-0 pl-2 list-group-item-warning @if(in_array($p_data['sign_mcust_select'],['','本人簽名'])) d-none @endif" id="sign_mcust_desc01">
                                        　　本受託人確實受申請人委託代辦服務，並代為同意上述事宜</li>
                                    <li class="list-group-item p-0 pl-2 text-danger">　　個人用戶請簽名，公司/團體用戶請蓋大小章，代理人請另出示身分證正本。</li>
                                    <li class="list-group-item p-0 pl-2 text-danger">　　未滿十八歲者，需檢附法定代理人之簽章及身分證明文件。</li>
                                </ul>
                                <div class="input-group-prepend p-0" id="signButton_mengineer">
                                    <button class="btn btn-success mr-3" id="signRestBtn_mcust" onclick="resetSignButton('open','mcust')">用戶重新簽名</button>
                                    <button class="btn btn-info mr-3" id="signUpBtn_mcust" onclick="signUpload('mcust');resetSignButton('close','mcust')">用戶簽名上傳</button>
                                    <button class="btn btn-secondary" id="signCloseBtn_mcust" onclick="resetSignButton('close','mcust')">取消</button>
                                </div>
                                <div id="signaturePad_mcust" class="signature-pad">
                                    <div class="signature-pad--body" style="border: 3px #000 solid;">
                                        <canvas id="upSignImg_mcust" data-clickchk="N"></canvas>
                                    </div>
                                    <div class="signature-pad--footer">
                                        <div class="signature-pad--actions">
                                            <div>
                                                <button type="button" id="signClear_mcust" class="button clear" data-action="clear">重寫</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            {{--sign--}}
                            <div class="d-none" id="signDiv_mengineer">
                                <div class="input-group-prepend p-0" id="signButton_mengineer">
                                    <button class="btn btn-success mr-3" id="signRestBtn_mengineer" onclick="resetSignButton('open','mengineer')">工程重新簽名</button>
                                    <button class="btn btn-info mr-3" id="signUpBtn_mengineer" onclick="signUpload('mengineer');resetSignButton('close','mengineer')">工程簽名上傳</button>
                                    <button class="btn btn-secondary" id="signCloseBtn_mengineer" onclick="resetSignButton('close','mengineer')">取消</button>
                                    <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3 " id="signAlert_mengineer">
                                        工程人員 簽名
                                    </label>
                                </div>
                                <img src="/upload/{{$p_data['uploaddir']}}/sign_mengineer_{{$p_data['info']->WorkSheet}}.jpg?i={{date('His')}}" width="500" id="signShow_mengineer">
                                <div id="signaturePad_mengineer" class="signature-pad">
                                    <div class="signature-pad--body" style="border: 3px #000 solid;">
                                        <canvas id="upSignImg_mengineer" data-clickchk="N"></canvas>
                                    </div>
                                    <div class="signature-pad--footer">
                                        <div class="signature-pad--actions">
                                            <div>
                                                <button type="button" id="signClear_mengineer" class="button clear" data-action="clear">重寫</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- 完工檢核表 --}}
                <div class="card border-danger bw-5p mb-3">
                    <div class="card-header pt-0 pb-0" id="finishChkListHead">
                        <h5 class="mb-0">
                            <button class="btn btn-primary collapsed btn_collapsed" data-toggle="collapse" data-target="#finishChkListBody">
                                完工檢核表
                                <svg  width="16" height="16" fill="currentColor" class="bi bi-arrows-collapse" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zm7-8a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 4.293V.5A.5.5 0 0 1 8 0zm-.5 11.707-1.146 1.147a.5.5 0 0 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 11.707V15.5a.5.5 0 0 1-1 0v-3.793z"/>
                                </svg>
                            </button>
                            <label class="alert alert-info p-0 pt-1 pr-2 mb-0" id="finishChkList_label">@if($p_data['chkFinishCheckList'] == 'Y')已存檔 @endif</label>
                        </h5>
                    </div>
                    <div class="card-body collapse show" id="finishChkListBody" data-parent="#finishChkListHead">
                        <ul class="list-group list-group-flush">
                            @foreach($p_data['finishChkList'] as $k => $t)
                                <li class="list-group-item bg-primary text-white p-0 pl-2">
                                    <input type="checkbox" name="finishChkListCB" id="{{ 'chkList'.$k }}" @if($p_data['chkFinishCheckList'] == 'Y')checked @endif>
                                    <label class="m-0 ml-2" for="{{ 'chkList'.$k }}">{{ $k }}</label>
                                </li>
                                <li class="list-group-item p-0 pl-2">
                                    <ul class="list-group list-group-flush">
                                        @foreach($p_data['finishChkList'][$k] as $k2 => $t2)
                                            <li class="list-group-item p-0 pl-4">{{ $t2 }}</li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                        <label class="btn btn-info mb-0 w-100">
                            <input class="d-none" type="button" id="finishChkListSaveBtn" data-save="" onclick="finishCheckListSave()">
                            存檔
                        </label>

                        <hr>
                        <div class="col">
                            <div class="input-group">
{{--                                <label class="btn btn-primary">--}}
{{--                                    <input class="d-none" type="button" onclick="chkSign('chk_signShow_mcust')">用戶簽名檢查--}}
{{--                                    --}}
{{--                                </label>--}}
                                <button class="btn btn-info mr-3" onclick="chkSign('chk_signShow_mcust')">用戶簽名檢查</button>
                                <label class="alert alert-info p-0 pt-1 mb-0">用戶：{{$p_data['info']->CustName}}</label>
                            </div>
                        </div>
                        <img id="chk_signShow_mcust">
                        <div class="col">
                            <div class="input-group">
{{--                                <label class="btn btn-primary">--}}
{{--                                    <input class="d-none" type="button" onclick="chkSign('chk_signShow_mengineer')">工程簽名檢查--}}
{{--                                </label>--}}
                                <button class="btn btn-info mr-3" onclick="chkSign('chk_signShow_mengineer')">工程簽名檢查</button>
                                <label class="alert alert-info p-0 pt-1 mb-0">工程：{{$p_data['info']->WorkerName}}</label>
                            </div>
                        </div>
                        <img id="chk_signShow_mengineer">
                    </div>
                </div>


                {{-- PDF --}}
                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0">
                        PDF
                        <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3" id="label_pdf">...</label>
                    </div>
                    <div id="pdf_show" style="width: 100%; height: 500px;"></div>
                </div>


                {{-- 寄送PDF --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="input-group">
                            <label class="btn btn-info mb-0">
                                <input class="d-none" type="button" id="sentmailpdf_btn"
                                       onclick="if(confirm('確認寄送Mail(PDF)給['+$('#sentmailpdf_vlaue').val()+']。'))sentMailPdfChkMailValue($('#sentmailpdf_vlaue').val())" >
                                寄送Mail(PDF)
                            </label>
                            <label class="alert alert-info p-2 ml-3 mb-0" id="sentmailpdf_label">請輸入Mail[{{$p_data['info']->sentmail}}]</label>
                        </div>
                    </div>
                    <div class="collapse show">
                        <div class="card-body">
                            <div class="input-group">
                                <input type="email" class="form-control" placeholder="請輸入寄送的Mail" id="sentmailpdf_vlaue" maxlength="50" value="{{$p_data['info']->sentmail}}">
                                <div class="input-group-append">
                                    <span class="input-group-text">@</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- 退單 --}}
                @if(false)
                    <div class="card mb-3">
                        <div class="card-header" >
                            <div class="input-group">
                                <label class="btn btn-info mb-0">
                                    <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">--}}
                                        <path d="M6.5 7a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1h-4z"></path>
                                        <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"></path>
                                    </svg>
                                    <input class="d-none" type="button" id="button_chargeback">
                                    退單
                                </label>
                                <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3 @if(is_null($p_data['info']->chargeback) === true) d-none @endif" id="label_chargeback">
                                    退單時間:{{date('Y-m-d H:i:s',strtotime($p_data['info']->chargeback))}}
                                </label>
                            </div>
                        </div>
                        <div id="chargeBackBody" class="collapse show" data-parent="#chargeBackHead">
                            <div class="card-body">
                                <div class="input-group">
                                    <select class="custom-select" id="chargeBackDesc">
                                        @foreach($p_data['backReason'] as $k => $t)
                                            <option value="{{$t->DataName}}">{{$t->DataName}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif


                {{-- 完工 --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="input-group text-center" id="receivemoneyDiv" style="line-height: 40px;">
                            款項收取：
                            <select class="custom-select bg-success" name="receivemoney" id="receivemoney">
                                <option class="bg-success" value="2">現金 ${{intval($p_data['recvAmt'])}}  </option>
                                @if($p_data['recvAmt'] > 0)
                                    @if(!empty($p_data['swipeAuthorization']))
                                        <option class="bg-warning" value="1">信用卡 ${{$p_data['recvAmt']}} </option>
                                    @endif

                                    <option class="bg-info" value="3">完工(未收)</option>
                                @endif
                            </select>
                        </div>
                        <div id="creditcardInputGroup">
                            <div class="input-group">
                                <input type="hidden" id="creditcardCode">
                                <input type="text" class="form-control text-center" id="creditcardCode1" name="creditcardCode" maxlength="4" onchange="value=value.replace(/[^\d]/g,'')">
                                <span class="input-group-text pl-0 pr-0">-</span>
                                <input type="password" class="form-control text-center" id="creditcardCode2" name="creditcardCode" maxlength="4" onchange="value=value.replace(/[^\d]/g,'')">
                                <span class="input-group-text pl-0 pr-0">-</span>
                                <input type="password" class="form-control text-center" id="creditcardCode3" name="creditcardCode" maxlength="4" onchange="value=value.replace(/[^\d]/g,'')">
                                <span class="input-group-text pl-0 pr-0">-</span>
                                <input type="text" class="form-control text-center" id="creditcardCode4" name="creditcardCode" maxlength="4" onchange="value=value.replace(/[^\d]/g,'')">
                            </div>
                            <input type="tel" id="creditcardMMYY" required="" maxlength="5" class="form-control text-center" onkeydown="this.value=this.value.replace(/\D/g,'').replace(/..(?!$)/g,'$&amp;/')" placeholder="有效期限(月/年) mm/yy" title="有效期限(月/年) mm/yy" />
                            <div class="col-12 alert alert-warning mb-0" role="alert" id="creditcardAlert">信用卡刷卡結果</div>
                        </div>
                        <div class="input-group" id="finishBtnDiv">
                            <label class="btn btn-warning mb-0">
                                <input class="d-none" type="button" id="finshBtn">
                                {{--                                <input class="d-none" type="button" id="finshBtn" onclick="stbApi('{{str_replace(' ','_',$p_data['info']->WorkKind)}}')">--}}
                                完工
                            </label>
                            <div class="alert alert-info mb-0 ml-3" role="alert" id="finshtimeAlert">完工API:OK；時間{{date('Y-m-d H:i:s',strtotime($p_data['info']->finsh)).'；'}}款項收取:{{($p_data['info']->receiveType === '1')? '信用卡': '現金'}}{{'$'.$p_data['info']->receiveMoney}}</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning" role="alert" id="installFinshalert">完工API訊息</div>
                    </div>
                </div>


                {{-- 離戶確認 --}}
                <div class="card">
                    <div class="card-header">
                        <div class="input-group">
                            <label class="btn btn-info mb-0">
                                <svg width="16" height="16" fill="currentColor" class="bi bi-pin-map" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M3.1 11.2a.5.5 0 0 1 .4-.2H6a.5.5 0 0 1 0 1H3.75L1.5 15h13l-2.25-3H10a.5.5 0 0 1 0-1h2.5a.5.5 0 0 1 .4.2l3 4a.5.5 0 0 1-.4.8H.5a.5.5 0 0 1-.4-.8l3-4z"/>
                                    <path fill-rule="evenodd" d="M8 1a3 3 0 1 0 0 6 3 3 0 0 0 0-6zM4 4a4 4 0 1 1 4.5 3.969V13.5a.5.5 0 0 1-1 0V7.97A4 4 0 0 1 4 3.999z"/>
                                </svg>
                                <input class="d-none" type="button" id="btnDorOut" onclick="saveDorTime('Out')">
                                離戶確認
                            </label>
                            <label class="alert alert-danger pt-0 pb-0 pl-1 pr-1 ml-3 mb-0">提醒:確認離戶後,訂單管理中心將持續與用戶聯繫,並另派人員處理此工單</label>
                            <label class="alert alert-info pt-0 pb-0 pl-1 pr-1 ml-3 mb-0" id="labelDorOut">...</label>
                        </div>
                    </div>
                </div>


                {{-- 我會遲到 --}}
                <div class="card border-danger bw-5p mb-3">
                    <div class="card-header pt-0 pb-0" id="delateHead">
                        <h5 class="mb-0">
                            <button class="btn btn-primary collapsed btn_collapsed" data-toggle="collapse" data-target="#delateBody">
                                我會遲到
                                <svg  width="16" height="16" fill="currentColor" class="bi bi-arrows-collapse" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zm7-8a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 4.293V.5A.5.5 0 0 1 8 0zm-.5 11.707-1.146 1.147a.5.5 0 0 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 11.707V15.5a.5.5 0 0 1-1 0v-3.793z"/>
                                </svg>
                            </button>
                        </h5>
                    </div>
                    <div id="delateBody" class="collapse show" data-parent="#delateHead">
                        <div class="card-body">
                            <div class="input-group-append p-0 col-10">
                                <select class="" name="delatedesc" id="delatedesc" onchange="delateDesc($(this))">
                                    <option value="">我會準時</option>
                                    @foreach(config('order.delateDesc') as $k => $t)
                                        <option value="{{$t}}" @if($p_data['info']->delatedesc == $t) selected @endif>{{$t}}</option>
                                    @endforeach
                                </select>
                                @if($p_data['info']->delatedesc != '')
                                    <div class="alert alert-danger mb-0 ml-3" role="alert" id="delatealert">遲到時間:{{date('Y-m-d H:i:s',strtotime($p_data['info']->delate))}}</div>
                                @else
                                    <div class="alert alert-danger mb-0 ml-3 d-none" role="alert" id="delatealert"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>


                {{-- 五金耗料 --}}
                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0" id="hardConsHead">
                        <div class="input-group-append">
                            <label class="btn btn-info mb-0">
                                <svg width="16" height="16" fill="currentColor" class="bi bi-cloud-upload" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383z"/>
                                    <path fill-rule="evenodd" d="M7.646 4.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V14.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3z"/>
                                </svg>
                                <input class="d-none" type="button" onclick="hardConsSave()">
                                五金耗料存檔
                            </label>
                            <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3 d-none " id="hardConsLabel">
                                存檔時間：
                            </label>
                        </div>
                    </div>
                    <div id="hardConsBody" class="collapse show" data-parent="#hardConsHead">
                        <div class="card-body" id="hardCons_body">
                            <div class="col">
                                <div class="input-group">
                                    <div class="input-group-prepend col-1 p-0">
                                        <button class="btn btn-warning w-100" onclick="hardConsAdd($(this))" title="新增">＋</button>
                                    </div>
                                    <div class="input-group-append col-11 p-0">
                                        <select id="hardConsCate" class="col-md-4">
                                            <option>請選擇</option>
                                            @foreach($p_data['hardconsList'] as $k => $t)
                                                {{print_r($t,1)}}
                                                <option>{{$t->category01}}</option>
                                            @endforeach
                                        </select>
                                        <select id="hardConsPrdNam" class="col-md-4"></select>
                                        <select id="hardConsStand" class="col-md-4"></select>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            @if(sizeof($p_data['hardcons']) > 0)
                                {{--五金耗料，上次存檔--}}
                                @foreach($p_data['hardcons'] as $k => $t)
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-danger" onclick="$(this).parents('.input-group').remove();" title="刪除">Ｘ</button>
                                        </div>
                                        <input type="text" class="form-control bg-muted" disabled value="{{$t->category01}}_{{$t->materialsName}}_{{$t->standard}}">
                                        <div class="input-group-append">
                                            <button class="btn btn-success" onclick="materialsSetInt(-1,$(this))">-</button>
                                            <input type="number" class="form-control Hardware-inpt-text p-0" name="hardCons" data-code="{{$t->materialsCode}}" ondblclick="$(this).val(0)" onchange="value=value.replace(/[^\d]/g,'')" value="{{$t->count}}">
                                            <button class="btn btn-info" onclick="materialsSetInt(10,$(this))">10</button>
                                            <button class="btn btn-warning" onclick="materialsSetInt(1,$(this))">+</button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                {{--五金耗料，預設--}}
                                @foreach($p_data['hardconsList'] as $k => $t)
                                    @if($t->often === '1')
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-danger" onclick="$(this).parents('.input-group').remove();" title="刪除">Ｘ</button>
                                        </div>
                                        <input type="text" class="form-control bg-muted" disabled value="{{$t->category01}}_{{$t->materialsName}}_{{$t->standard}}">
                                        <div class="input-group-append">
                                            <button class="btn btn-success" onclick="materialsSetInt(-1,$(this))">-</button>
                                            <input type="number" class="form-control Hardware-inpt-text p-0" name="hardCons" data-code="{{$t->materialsCode}}" onchange="value=value.replace(/[^\d]/g,'')" ondblclick="$(this).val(0)" value="0">
                                            <button class="btn btn-info" onclick="materialsSetInt(10,$(this))">10</button>
                                            <button class="btn btn-warning" onclick="materialsSetInt(1,$(this))">+</button>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            @endif

                        </div>
                    </div>
                </div>


                {{-- Dialog wifi測試數據 --}}
                <div id="alertDialog" title="安裝說明提示" class="d-none">
                    <div class="card w-80" id="card_wifitest">
                        <div class="card-header">
                            本次安裝_wifi測試數據
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">尚未完成測</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card w-80" id="card_saleap">
                        <div class="card-header">
                            順推-加購wifiAP
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">加購wifiAP</li>
                            </ul>
                        </div>
                    </div>

                    <button class="button " id="alertDialog_close_btn" onclick="$('#alertDialog').dialog('close');">確定</button>
                </div>


                {{-- Dialog 通知訊息 --}}
                <div id="msg_Dialog" title="通知訊息" class="d-none">
                    <div class="card w-80" id="card_msg">
                        <div class="card-header text-right pt-0 pb-0">
                            查詢時間:...
                        </div>
                        <button class="btn btn-info w-100" onclick="setMsgRead()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hand-index-thumb" viewBox="0 0 16 16">
                                <path d="M6.75 1a.75.75 0 0 1 .75.75V8a.5.5 0 0 0 1 0V5.467l.086-.004c.317-.012.637-.008.816.027.134.027.294.096.448.182.077.042.15.147.15.314V8a.5.5 0 0 0 1 0V6.435l.106-.01c.316-.024.584-.01.708.04.118.046.3.207.486.43.081.096.15.19.2.259V8.5a.5.5 0 1 0 1 0v-1h.342a1 1 0 0 1 .995 1.1l-.271 2.715a2.5 2.5 0 0 1-.317.991l-1.395 2.442a.5.5 0 0 1-.434.252H6.118a.5.5 0 0 1-.447-.276l-1.232-2.465-2.512-4.185a.517.517 0 0 1 .809-.631l2.41 2.41A.5.5 0 0 0 6 9.5V1.75A.75.75 0 0 1 6.75 1zM8.5 4.466V1.75a1.75 1.75 0 1 0-3.5 0v6.543L3.443 6.736A1.517 1.517 0 0 0 1.07 8.588l2.491 4.153 1.215 2.43A1.5 1.5 0 0 0 6.118 16h6.302a1.5 1.5 0 0 0 1.302-.756l1.395-2.441a3.5 3.5 0 0 0 .444-1.389l.271-2.715a2 2 0 0 0-1.99-2.199h-.581a5.114 5.114 0 0 0-.195-.248c-.191-.229-.51-.568-.88-.716-.364-.146-.846-.132-1.158-.108l-.132.012a1.26 1.26 0 0 0-.56-.642 2.632 2.632 0 0 0-.738-.288c-.31-.062-.739-.058-1.05-.046l-.048.002zm2.094 2.025z"/>
                            </svg>
                            讀取
                        </button>
                        <div class="card-body">
                            <ul class="list-group list-group-flush" id="msg_dialog_ul">
                                <li class="list-group-item">尚未完成測</li>
                            </ul>
                        </div>
                    </div>
                </div>


            </div>

        </div>

    </main>
@endsection

@section('script')

    <script>

        $(document).ready(function () {
            console.log('裝機工單2');


            // workername check
            var chk_workername = $('#p_userName').val();
            var p_companyno = $('#p_companyNo').val();
            var p_worksheet = $('#p_workSheet').val();
            if(chk_workername === '') {
                alert('提示：\n代號[EWO-D1820]!\n麻煩請截圖回報!\n謝謝。\n['+p_companyno+'_'+p_worksheet+']');
            }
            // 借用單[v3]
            $('#borrowminglist_form select ').change(function(){
                equipmentAPI('borrowminglist_form','BorrowmingList')
            });

            // 取回單[v3]
            $('#retrievelist_form select ').change(function(){
                equipmentAPI('retrievelist_form','RetrieveList')
            });

            // 綁定遙控器手動送出
            $('.dstb_remote_mapping_send_button').click(function (e) {
                e.stopPropagation();
                e.preventDefault();
                dstb_remote_mapping_info = $(this).siblings('.dstb_remote_mapping_info');
                console.log(dstb_remote_mapping_info);
                console.log(dstb_remote_mapping_info.attr('id'));
                // addDstbRemoteMapping
                addDstbRemoteMapping(dstb_remote_mapping_info.attr('id'));
            })

            // 借用單 DB to HTML
            borrowminglistSetOption()
            // 取回單 DB to HTML
            // retrieveListSetOption()


            // PDF 寄送mail
            $('#sentmail').change(function(){
                var params = {
                    p_companyNo : "{{$p_data['info']->CompanyNo}}",
                    p_workSheet : "{{$p_data['info']->WorkSheet}}",
                    p_columnName : "sentmail",
                    p_value : $(this).val()
                };

                apiEvent('sentmail',params)
            });

            // 五金耗料，第一層，清除重複的
            var chkoptoinAry = [];
            $('#hardConsCate option').each(function(){
                //console.log($(this).text())
                var fstr = $(this).text();
                if(chkoptoinAry.indexOf(fstr) > 0)
                    $(this).remove();
                else
                    chkoptoinAry.push(fstr);
            });

            // 五金耗料-種類 hardConsPrdNam hardConsStand
            $('#hardConsCate').change(function (){
                var category = $(this).find('option:selected').text();
                // 清除下層 option
                $('#hardConsPrdNam option').remove();
                $('#hardConsStand option').remove();

                $("#hardConsPrdNam").append("<option value=''>品項選擇</option>");
                @foreach($p_data['hardconsList'] as $k => $t)
                    if(category === '{{$t->category01}}')
                    {
                        $("#hardConsPrdNam").append("<option>{{$t->materialsName}}</option>");
                    }
                @endforeach

                // 刪除重複的option
                var chkoptoinAry = [];
                $('#hardConsPrdNam option').each(function(){
                    //console.log($(this).text())
                    var fstr = $(this).text();
                    if(chkoptoinAry.indexOf(fstr) > 0)
                        $(this).remove();
                    else
                        chkoptoinAry.push(fstr);
                });
            });

            // 五金耗料-品項
            $('#hardConsPrdNam').change(function (){
                var category = $('#hardConsCate').find('option:selected').text();
                var standard = $(this).find('option:selected').text();
                // 刪除第一階
                $(this).find('option[value=""]').remove()
                // 刪除下階 option
                $('#hardConsStand option').remove();
                @foreach($p_data['hardconsList'] as $k => $t)
                if(category === '{{$t->category01}}' && standard === '{{$t->materialsName}}')
                {
                    $("#hardConsStand").append("<option value='{{$t->materialsCode}}'>{{$t->standard}}</option>");
                }
                @endforeach

            });

            // 家戶側寫
            $('#button_survey').click(function(){
                var params = {
                    p_companyNo : $('#p_companyNo').val(),
                    p_workSheet : $('#p_workSheet').val(),
                    p_columnName : "survey",
                    p_value : ''
                };
                apiEvent('survey',params);
            });

            // 憑證上傳[上傳憑證 (1G & 低收)]
            $('#certificate_btn').click(function(){
                var params = {
                    p_companyNo : $('#p_companyNo').val(),
                    p_workSheet : $('#p_workSheet').val(),
                    p_custId : $('#p_custId').val(),
                    p_columnName : "certificatejz",
                    p_value : ''
                };
                apiEvent('certificatejz',params);
            });

            // 創建，簽名板
            if(true) {

                var servName = '{{$p_data['info']->ServiceName}}';

                {{--if(0)--}}
                {{--if(servName.search('DSTB') > 0 || servName === '1 CATV') {--}}
                {{--    $('#signDiv_dstb').removeClass('d-none');--}}
                {{--    createSign('dstb');--}}
                {{--    // 簽名，Label--}}
                {{--    var chk_title = "@if(!empty($p_data['dstbcheck']) && $p_data['dstbcheck']->dstb_check_title) {{$p_data['dstbcheck']->dstb_check_title}} @else {{$p_data['info']->CustName}} @endif";--}}
                {{--    var legalStr = "@if(!empty($p_data['dstbcheck']) && $p_data['dstbcheck']->dstb_check_legal) {{$p_data['dstbcheck']->dstb_check_legal}} @else 本人 @endif";--}}
                {{--    var labelStr = 'DSTB 簽名：';--}}
                {{--    labelStr += legalStr + '('+chk_title+')';--}}
                {{--    $('#signAlert_dstb').text(labelStr);--}}
                {{--    $('#signSpan_dstb').text(labelStr);--}}
                {{--    $("#dstb_check_title option[value='"+chk_title+"']").attr('selected',true);--}}
                {{--    signDialog('dstb');--}}
                {{--    // test--}}
                {{--    if('{{ $p_data['IsTest'] }}' > 0) {--}}
                {{--        alertDialog('dstb');--}}
                {{--    }--}}
                {{--}--}}
                {{--if(0)--}}
                {{--if(servName.search('CM') > 0 || servName.search('HS') > 0 || servName.search('FTTH') > 0 || servName.search('FTTB') > 0) {--}}
                {{--    $('#signDiv_cm').removeClass('d-none');--}}
                {{--    createSign('cm');--}}
                {{--    var chk_sign = '{{ $p_data['info']->sign_cm }}';--}}
                {{--    // 簽名，Label--}}
                {{--    var chk_title = "@if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_title){{$p_data['cmcheck']->cm_check_title}}@else{{$p_data['info']->CustName}}@endif";--}}
                {{--    var legalStr = "@if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_legal){{$p_data['cmcheck']->cm_check_legal}}@else本人@endif";--}}
                {{--    var labelStr = 'CM 簽名：';--}}
                {{--    if(servName.search('CM') < 0 && servName.search('HS') > 0) {--}}
                {{--        $('#p_sign_chs').val('Y');--}}
                {{--        labelStr = 'HS 簽名:';--}}
                {{--    }--}}
                {{--    if(servName.search('CM') < 0 && servName.search('FTTH') > 0) {--}}
                {{--        $('#p_sign_bftth').val('Y');--}}
                {{--        labelStr = 'FTTH 簽名:';--}}
                {{--    }--}}
                {{--    labelStr += legalStr + '('+chk_title+')';--}}
                {{--    $('#signAlert_cm').text(labelStr);--}}
                {{--    $('#signSpan_cm').text(labelStr);--}}
                {{--    $("#cm_check_title option[value='"+chk_title+"']").attr('selected',true);--}}
                {{--    // signDialog('cm');--}}
                {{--    --}}{{--// test--}}
                {{--    --}}{{--if('{{ $p_data['IsTest'] }}' > 0) {--}}
                {{--    --}}{{--    alertDialog('cm');--}}
                {{--    --}}{{--}--}}
                {{--}--}}
                {{--if(servName.search('TWMBB') > 0) {--}}
                {{--    $('#signDiv_twmbb').removeClass('d-none');--}}
                {{--    createSign('twmbb');--}}
                {{--    // 簽名，Label--}}
                {{--    var chk_title = "@if(!empty($p_data['twmbbcheck']) && $p_data['twmbbcheck']->twmbb_check_title){{$p_data['twmbbcheck']->twmbb_check_title}}@else{{$p_data['info']->CustName}}@endif";--}}
                {{--    var legalStr = "@if(!empty($p_data['twmbbcheck']) && $p_data['twmbbcheck']->twmbb_check_legal){{$p_data['twmbbcheck']->twmbb_check_legal}}@else本人@endif";--}}
                {{--    var labelStr = 'TWMBB 簽名：';--}}
                {{--    labelStr += legalStr + '('+chk_title+')';--}}
                {{--    $('#signAlert_twmbb').text(labelStr);--}}
                {{--    $('#signSpan_twmbb').text(labelStr);--}}
                {{--    $("#twmbb_check_title option[value='"+chk_title+"']").attr('selected',true);--}}
                {{--    // signDialog('twmbb');--}}
                {{--    --}}{{--// test--}}
                {{--    --}}{{--if('{{ $p_data['IsTest'] }}' > 0) {--}}
                {{--    --}}{{--    alertDialog('twmbb');--}}
                {{--    --}}{{--}--}}
                {{--}--}}

                if(true) {
                    $('#signDiv_mengineer').removeClass('d-none');
                    createSign('mengineer');
                    let labelStr = '工程簽名：' + $('#p_userName').val();
                    $('#signAlert_mengineer').text(labelStr);
                }
                if(true) {
                    $('#signDiv_mcust').removeClass('d-none');
                    createSign('mcust');
                    let labelStrmcust = '用戶簽名：' + $('#p_custName').val();
                    $('#signAlert_mcust').text(labelStrmcust);
                }
                chkPDFTreamRead(); // 判斷PDF閱讀，隱藏簽名欄位
            }

            // 收款>>刷卡>>切換刷卡輸入欄位
            $('#receivemoney').change(function(){
                var chkVal = $(this).val()
                $(this).removeClass();
                if(chkVal === '1') { // 1=刷卡，2=現金，3=完工未收
                    $(this).toggleClass('custom-select bg-warning');
                    $('#creditcardInputGroup').show();
                } else if (chkVal === '3') {
                    $(this).toggleClass('custom-select bg-info');
                    $('#creditcardInputGroup').hide();
                } else {
                    $(this).toggleClass('custom-select bg-success');
                    $('#creditcardInputGroup').hide();
                }
            });


/*
            //上傳檔案[限制清單]
            // 測試 壓縮檔案
            $("input[type='file']").change(function() {
                var compressRatio = 1, // 圖片壓縮比例
                    imgNewWidth = 1000, // 圖片新寬度
                    img = new Image(),
                    urlBase64,
                    canvas = document.createElement("canvas"),
                    context = canvas.getContext("2d"),
                    file, fileReader, dataUrl;

                var p_id = $(this).attr('id');
                var idAry = ['file_id_01','file_id_02','file_id03Photo','file_certificate_01','file_certificate_02','file_constructionPhoto','file_checkin']
                    ,fNameAry = ['identity_01.jpg','identity_02.jpg','file_id03Photo','certificate_01.jpg','certificate_02.jpg','file_constructionPhoto','checkIn.jpg']
                    ,p_id_index = idAry.indexOf(p_id)
                ;

// console.log('1597')
//                 if(true) {
//                     console.log('1599');
//                     var fName = fNameAry[p_id_index];
//                     if(p_id === 'file_constructionPhoto')
//                         fName = constructionPhotoUpload(p_id);
//                     if(p_id === 'file_id03Photo')
//                         fName = id03PhotoUpload(p_id);
// // 輸入上傳程式碼
//                     console.log(p_id+'__'+fName)
//                     file = document.getElementById(p_id).files[0];
//                     upload(idAry[p_id_index],fName,file)
//                     return;
//                 }

                if(p_id_index < 0) {
                    return;
                } else {
                    // if(p_id === 'file_id_01') {
                    //     upload_test(p_id,fNameAry[p_id_index],$('#'+p_id)[0].files[0]);
                    //     return;
                    // }
                }


                if(p_id === 'file_checkin')
                    getLocalGPS()

                file = document.getElementById(p_id).files[0];

// 圖片才處理
                if (file && file.type.indexOf("image") == 0) {
                    fileReader = new FileReader();
                    fileReader.onload = getFileInfo;
                    fileReader.readAsDataURL(file);
                }

                function getFileInfo(evt)
                {
                    dataUrl = evt.target.result,

// 取得圖片
                        img.src = dataUrl;
                }

// 圖片載入後
                img.onload = function() {
                    var width = this.width, // 圖片原始寬度
                        height = this.height, // 圖片原始高度
                        imgNewHeight = imgNewWidth * height / width, // 圖片新高度
                        html = "",
                        newImg;

// 顯示預覽圖片
//                 html += "<img src='" + dataUrl + "'/>";
//                 html += "<p>這裡是原始圖片尺寸 " + width + "x" + height + "</p>";
//                 html += "<p>檔案大小約 " + Math.round(file.size / 1000) + "k</p>";
//                 $("#oldDiv").html(html);

// 使用 canvas 調整圖片寬高
                    canvas.width = imgNewWidth;
                    canvas.height = imgNewHeight;
                    context.clearRect(0, 0, imgNewWidth, imgNewHeight);

                    // console.log('chk dataUrl=='+dataUrl)
// 調整圖片尺寸
                    context.drawImage(img, 0, 0, imgNewWidth, imgNewHeight);

// 顯示新圖片
                    newImg = canvas.toDataURL("image/jpeg", compressRatio);

                    // console.log('chk newImg=='+newImg)
                    // html = "";
                    // html += "<img src='" + newImg + "'/>";
                    // html += "<p>這裡是新圖片尺寸 " + imgNewWidth + "x" + imgNewHeight + "</p>";
                    // html += "<p>檔案大小約 " + Math.round(0.75 * newImg.length / 1000) + "k</p>"; // 出處 https://stackoverflow.com/questions/18557497/how-to-get-html5-canvas-todataurl-file-size-in-javascript
                    // $("#newDiv").html(html);

// canvas 轉換為 blob 格式、上傳
                    canvas.toBlob(function(blob) {
                        var fName = fNameAry[p_id_index];
                        if(p_id === 'file_constructionPhoto')
                            fName = constructionPhotoUpload(p_id);
                        if(p_id === 'file_id03Photo')
                            fName = id03PhotoUpload(p_id);
// 輸入上傳程式碼
                        //console.log(p_id+'__'+fName+'___'+p_id)
                        upload(idAry[p_id_index],fName,blob)
                    }, "image/jpeg", compressRatio);
                };

            });
*/

            // 開通，檢查[開通]時間紀錄[關閉開通功能]
            if(true) {
                var opentimeCheck = '{{$p_data['info']->openApi}}{{$p_data['info']->finsh}}';
                // if(opentimeCheck === '')
                // {   // 沒做
                //     $('#openTimsAlert').hide();
                // } else { // 已經完成
                //     console.log('opentimeCheck=='+opentimeCheck);
                //     $('#openBody').hide();
                //     $('#openButton').remove();
                // }
            }

            // 判斷圖片，增加[浮水印]
            $('img').each(function(){
                //console.log('img each id>>'+$(this).attr('id'))
                var imgNameAry = ['img_id_01','img_id_02','img_id_03','img_certificate_01','img_certificate_02'];
                var p_name = $(this).attr('name');
                if(imgNameAry.indexOf(p_name) >= 0) {
                    var p_id = $(this).attr('id');
                    createImgWatemark(p_id);
                }
            });

            // 紙本工單信息[初始]
            if(true) {
                var chk_paperPDF = "{{$p_data['info']->PaperPDF}}";
                var paperPdfObj = $('#PaperPDF_alert');
                if (chk_paperPDF.length < 1) {
                    paperPdfObj.addClass('d-none');
                } else if (chk_paperPDF.length > 0) {
                    paperPdfObj.removeClass('d-none');
                    paperPdfObj.text('申請紙本工單；時間:'+chk_paperPDF);
                    $('#PaperPDF').prop('checked','true');
                }
            }

            // // 資料確認，欄位開關判斷
            // if(true) {
            //     var chk_servName = $('#p_ServiceName').val();
            //     if(chk_servName.search('DSTB') > 0) {
            //         $('#checkDataDiv_dstb').removeClass('d-none');
            //     }
            //     if(chk_servName.search('CM') > 0 || chk_servName.search('HS') > 0 || chk_servName.search('FTTH') > 0) {
            //         $('#checkDataDiv_cm').removeClass('d-none');
            //     }
            //     if(chk_servName.search('TWMBB') > 0) {
            //         $('#checkDataDiv_twmbb').removeClass('d-none');
            //     }
            // }
            //
            // // 個人資料確認，代簽欄位切換
            // $('input[type="text"]').change(function () {
            //     var chk_id = $(this).attr('id');
            //     var idAry = ['dstb_check_legal', 'cm_check_legal'];
            //     // console.log('chk1791')
            //     if (idAry.indexOf(chk_id) >= 0) {
            //         var typVal = chk_id.split('_')[0]
            //         var editVal = $(this).val();
            //         var titleVal = $('#dstb_check_title').val()
            //         var newText = typVal.toUpperCase() + ' 簽名：';
            //         // console.log('newText=='+newText+'  typVal=='+typVal+'  editVal==='+editVal+'  titleVal==='+titleVal)
            //         if (titleVal === '本人') {
            //             newText += $('#p_custName').val() + '(本人)';
            //         } else {
            //             newText += editVal + ' 代簽' + ' 關係：' + titleVal;
            //         }
            //         $('#signAlert_' + typVal).text(newText);
            //     }
            // });
            //
            // // PDF 資料確認
            // $('select').change(function(){
            //     var chk_id = $(this).attr('id');
            //     var idAry = ['dstb_check_title','cm_check_title'];
            //     if(idAry.indexOf(chk_id) >= 0) {
            //         var typVal = chk_id.split('_')[0]
            //         var editVal = $('#'+typVal+'_check_legal').val();
            //         var titleVal = $('#'+typVal+'_check_title').val()
            //         var newText = typVal.toUpperCase() + ' 簽名：';
            //         // console.log('newText=='+newText+'  typVal=='+typVal+'  editVal==='+editVal+'  titleVal==='+titleVal)
            //         if (titleVal === '本人') {
            //             newText += $('#p_custName').val() + '(本人)';
            //         } else {
            //             newText += editVal + ' 代簽' + ' 關係：' + titleVal;
            //         }
            //         $('#signAlert_' + typVal).text(newText);
            //     }
            // });

            // 服務申請書，PDF
            if(true) {
                var options = {
                    forcePDFJS: true,
                    fallbackLink: "<p>使用的瀏覽器或視窗格式不支援PDF預覽，請直接<a href=\"https://sms.hmps.cc/hr/HomeplusHR.pdf\">下載</a>觀看</p>",
                    PDFJS_URL: "{{asset('/PDF_JS/web/viewer.html')}}"
                };
                var myPDF = PDFObject.embed("{{asset('/upload/'.$p_data['uploaddir'].'/'.$p_data['info']->WorkSheet.'.pdf?'.date('Ymd'))}}", "#pdf_show", options);
            }

            // [D]條款，PDF
            if($('#p_ServiceName').val().search('DSTB') > 0 || $('#p_ServiceName').val().search('CATV') > 0) {
                let id_d = 'terms_D_pdf';
                // 移除[d-none]Class
                $('#'+id_d).parent().removeClass('d-none');
                var options = {
                    forcePDFJS: true,
                    fallbackLink: "<p>使用的瀏覽器或視窗格式不支援PDF預覽，請直接<a href=\"https://sms.hmps.cc/hr/HomeplusHR.pdf\">下載</a>觀看</p>",
                    PDFJS_URL: "{{asset('/PDF_JS/web/viewer.html')}}",
                    id: `${id_d}_iframe`
                };
                // 有線電視
                {{--console.log('pdf='+'{{ asset('/pdfTerms/dtv'.$p_data['info']->CompanyNo.'.pdf#page=1') }}');--}}
                PDFObject.embed("{{ asset('/pdfTerms/dtv'.$p_data['info']->CompanyNo.'.pdf#page=1') }}", `#${id_d}`, options);
                // 讀取監控
                $(`#${id_d}_iframe`).on('load',function(){ addIframeEvent(`${id_d}_iframe`); });

                let id_d_pcl = 'terms_D_pcl_pdf';
                options.id = `${id_d_pcl}_iframe`
                // 有線電視_隱私權
                if($('#p_companyNo').val() == '209')
                    PDFObject.embed("{{ asset('/pdfTerms/dtv_pcl_209.pdf#page=1') }}", `#${id_d_pcl}`, options);
                else
                    PDFObject.embed("{{ asset('/pdfTerms/dtv_pcl.pdf#page=1') }}", `#${id_d_pcl}`, options);
                // 讀取監控
                $(`#${id_d_pcl}_iframe`).on('load',function(){ addIframeEvent(`${id_d_pcl}_iframe`); });
            }

            // [I]條款，PDF
            if($('#p_serviceNameAry2').val() > 0
                || $('#p_ServiceName').val().search('CM') > 0
                || $('#p_ServiceName').val().search('FTTH') > 0
                || $('#p_ServiceName').val().search('TWMBB') > 0
                || $('#p_ServiceName').val() == 'C HS'
                || $('#p_ServiceName').val() == 'F CML'
            ) {
                let id_i = 'terms_I_pdf';
                // 移除[d-none]Class
                $('#'+id_i).parent().removeClass('d-none');
                var options = {
                    forcePDFJS: true,
                    fallbackLink: "<p>使用的瀏覽器或視窗格式不支援PDF預覽，請直接<a href=\"https://sms.hmps.cc/hr/HomeplusHR.pdf\">下載</a>觀看</p>",
                    PDFJS_URL: "{{asset('/PDF_JS/web/viewer.html')}}",
                    id:`${id_i}_iframe`
                };
                // 寬頻條款
                if($('#p_companyNo').val() == '209')
                    PDFObject.embed("{{ asset('/pdfTerms/cm209.pdf#page=1') }}", `#${id_i}`, options);
                else
                    PDFObject.embed("{{ asset('/pdfTerms/cmdef.pdf#page=1') }}", `#${id_i}`, options);
                // 讀取監控
                $(`#${id_i}_iframe`).on('load',function(){ addIframeEvent(`${id_i}_iframe`); });

                let id_i_pcl = 'terms_I_pcl_pdf';
                options.id = `${id_i_pcl}_iframe`
                // 寬頻條款_隱私權
                if($('#p_companyNo').val() == '209')
                    PDFObject.embed("{{ asset('/pdfTerms/cm_pcl_209.pdf#page=1') }}", `#${id_i_pcl}`, options);
                else
                    PDFObject.embed("{{ asset('/pdfTerms/cm_pcl.pdf#page=1') }}", `#${id_i_pcl}`, options);
                // 讀取監控
                $(`#${id_i_pcl}_iframe`).on('load',function(){ addIframeEvent(`${id_i_pcl}_iframe`); });
            }

            // ETF ACH PDF
            @if(!empty($p_data['info']->etf_ach))
                var options_ach = {
                    forcePDFJS: true,
                    fallbackLink: "<p>使用的瀏覽器或視窗格式不支援PDF預覽，請直接<a href=\"https://sms.hmps.cc/hr/HomeplusHR.pdf\">下載</a>觀看</p>",
                    PDFJS_URL: "{{asset('/PDF_JS/web/viewer.html')}}"
                };
                var myAchPDF = PDFObject.embed("{{asset('/upload/'.$p_data['uploaddir'].'/ACH_'.$p_data['info']->WorkSheet.'.pdf#page=1?'.date('Ymd'))}}", "#pdf_ach_show", options_ach);
            @endif

            // 設備取回單
            if(true) {
                var retrieveListShow_chk = '{{$p_data['retrieveListShow']}}';
                console.log('retrieveListShow_chk=='+retrieveListShow_chk)
                if(retrieveListShow_chk === 'N') {
                    // $('#retrievelist_card').remove();
                }
            }

            // 打卡提醒
            if(true) {
                $('button').click(function(){
                    var p_id = $(this).attr('id');
                    if(parseInt($('#img_checkin').data('chk')) > 1)
                        return ;
                    //開通按鈕
                    if(p_id !== undefined && p_id.search('open') >= 0) {
                        alert('請先完成打卡01');
                    }
                });

            }

            // 完工檢查
            if(true) {
                $('#finshBtn').click(function(){
                    //打卡
                    if($('#img_checkin').data('chk') < 1) {
                        alert('請先完成打卡02');
                        return;
                    }

                    if('{{date('Ymd')}}' >= '20221028')
                        if(deviceListChk() == 'Y') { // 設備清單選取
                            return;
                        }

                    // 完工，檢核表
                    if($('#finishChkListSaveBtn').data('save') != 'Y') {
                        alert('請先確認(完工檢核表)是否全部勾選並(存檔)');
                        return;
                    }

                    // 加入Line 確認
                    // if($("input[name='lineWill']:checked").length < 1) {
                    //     alert('請先完成[加入Line]確認');
                    //     // return;
                    // }

                    // 信用卡定期扣款檢查
                    if($('#p_ccadaf').val() === 'Y') {
                        if($('#label_ccadaf').data('chk') === '') {
                            alert('請先完成[信用卡定期扣款]');
                            return;
                        }
                    }

                    // 檢點表檢查
                    // 在API才檢查

                    // DSTB，資料確認[本公司進行機上盒頻道節目收視之資訊蒐集分析及個人化內容之推薦等]有選擇才能完工
                    // var servicename = $('#p_ServiceName').val();
                    // if(servicename.search('DSTB') >= 0) {
                    //     // 不是本人簽名，不檢查
                    //     let chkTitle = $('#dstb_check_title').prop('selectedIndex');
                    //     if(chkTitle < 1) {
                    //         if(chkDSTB_CHECK_PERSONAL() === 'N') {
                    //             // console.log('finsh error');
                    //             return;
                    //         }
                    //     }
                    // }

                    // 借用單，檢查

                    if(true) {
                        var p_chkVal = 'N';
                        $('input[type="number"]').each(function(){
                           var p_name = $(this).attr('name');
                           switch(p_name) {
                           case 'Cable_modem_port':
                           case 'Basic_digital_set_top_box':
                           case 'Digital_set_top_box_two_way_type':
                               var p_val = $(this).val();
                               if(p_val > 0 && p_chkVal === 'N') {
                                   p_chkVal = 'Y';
                               }
                               break;
                           }
                        });
                        if(p_chkVal === 'N') {
                            alert('請檢查[借用單]設備數量是否正確!');
                        }
                    }

                    // if(true) {
                    //     var p_chkVal = 'N';
                    //     $('input[type="number"]').each(function(){
                    //        var p_name = $(this).attr('name');
                    //        switch(p_name) {
                    //            case 'Cable_modem_port':
                    //            case 'Basic_digital_set_top_box':
                    //            case 'Digital_set_top_box_two_way_type':
                    //                var p_val = $(this).val();
                    //                if(p_val > 0 && p_chkVal === 'N') {
                    //                    p_chkVal = 'Y';
                    //                }
                    //                break;
                    //        }
                    //     });
                    //     if(p_chkVal === 'N') {
                    //         alert('請檢查[借用單]設備數量是否正確!');
                    //     }
                    // }

// >>>>>>> Stashed changes
                    stbApi('{{str_replace(' ','_',$p_data['info']->WorkKind)}}');
                })
            }


            // line 同意/不同意，意願調查
            $("input[name='lineWill']").change(function(){
                let p_val = $(this).val();
                let params = {
                    p_companyNo : $('#p_companyNo').val(),
                    p_workSheet : $('#p_workSheet').val(),
                    p_custid : $('#p_custId').val(),
                    p_columnName : "lineWill",
                    p_value : p_val,
                };
                apiEvent('lineWill',params);
            });


            // 2022-07-01 符合條件，需要出現alert & 訊息
            if($('#chkChargeNameAlert0701').val() == 'Y') {
                alert('注意事項：\n 智慧遙控器和ATV 6010機種仍有部份不相容狀況，請僅搭配ATV 6252或9642');
            }

            @if(!empty($p_data['serviceNameAry2']))
            // 維修原因JSON.load
            $.getJSON( "/json/ReasonRepair.json?_{{ time() }}", function( json ) {
                var servicename = $('#p_serviceNameAry2').val();
                servicename = servicename.substr(2,10);
                ReasonRepairFirst = json.first;
                ReasonRepairSecond = json.second;
                // 維修原因[First]
                var obj = $('#select_srviceReasonFirst');
                obj.find('option').remove();
                obj.append('<option>請選擇原因</option>');
                ReasonRepairFirst = ReasonRepairFirst[servicename];
                ReasonRepairFirst.forEach(function(t){
                    var htmlStr = '<option data-mscode="'+t.mscode+'" value="'+t.dataname+'">'+t.dataname+'</option>';
                    obj.append(htmlStr);
                });
                var firstVal = obj.data('val');
                if(firstVal !== '') {
                    $('#select_srviceReasonFirst').find('option[value="'+firstVal+'"]').prop('selected',true);
                }
            });

            // 維修原因[Second]
            $('#select_srviceReasonFirst').change(function(){
                var obj = $("#select_srviceReasonLast");
                obj.find('option').remove();
                var mscode = $(this).find('option:selected').data('mscode');
                var servicename = $('#p_serviceNameAry2').val();
                servicename = servicename.substr(2,10);
                var secondAry = ReasonRepairSecond[servicename][mscode];
                obj.append('<option>請選擇</option>');
                secondAry.forEach(function(t){
                    var htmlStr = '<option data-mscode="'+t.mscode+'">'+t.dataname+'</option>';
                    obj.append(htmlStr);
                });
            });
            @endif

        }); // endready

        // chkSign
        function chkSign(id) {
            let src = '/upload';
            let custid = $('#p_custId').val();
            let bookdate = $('#p_BookDate').val();
            let bookdateStr = bookdate.replaceAll('-','').substring(0,8);
            let workSheet = $('#p_workSheet').val();
            switch(id) {
                case 'chk_signShow_mcust':
                    src += '/'+custid+'_'+bookdateStr+'/'+'sign_mcust_'+workSheet+'.jpg?'+parseInt(Math.random()*10000);
                    break;
                case 'chk_signShow_mengineer':
                    src += '/'+custid+'_'+bookdateStr+'/'+'sign_mengineer_'+workSheet+'.jpg?'+parseInt(Math.random()*10000);
                    break;
            }
            $('#'+id).attr('src',src);
        }

        // ach[授權轉帳/扣款同意書][submit]
        function ach_Submit(obj) {
            var disabled = obj.data('disabled');
            if(disabled === 'Y') {
                return;
            }
            obj.data('disabled','Y');
            $('#label_ccadaf').text('跳轉中...');

            var url = '{{ config('order.R1_URL') }}/etf/ach_form';
            var temp_form = document.createElement("form");
            temp_form.action = url;
            temp_form.target = "_self";
            temp_form.method = "post";
            temp_form.style.display = "none";
            var p_ach = $('#p_ach').val();

            var params = {
                p_CompanyNo:$('#p_companyNo').val(),
                p_WorkSheet:$('#p_workSheet').val(),
                p_ServiceName:$('#p_ServiceName').val(),
                p_id:$('#p_id').val(),
                p_source:'ewo_app',
                backurl:window.location.href,
            };

            // 處理需要傳遞的參數
            for (var x in params) {
                var opt = document.createElement("textarea");
                opt.name = x;
                opt.value = params[x];
                temp_form.appendChild(opt);
            }
            document.body.appendChild(temp_form);
            // 提交表單
            temp_form.submit();
        }


        // 信用卡自動扣款[submit]
        function ccadaf_Submit(obj) {
            var disabled = obj.data('disabled');
            if(disabled === 'Y') {
                return;
            }
            obj.data('disabled','Y');
            $('#label_ccadaf').text('跳轉中...');

            var url = '{{ config('order.R1_URL') }}/etf/ccada_form';
            var temp_form = document.createElement("form");
            temp_form.action = url;
            temp_form.target = "_self";
            temp_form.method = "post";
            temp_form.style.display = "none";
            var p_def_type = 'def';
            var p_ccadaf = $('#p_ccadaf').val();
            if(p_ccadaf === 'Y') {
                p_def_type = 'fet';
            }

            var params = {
                p_CompanyNo:$('#p_companyNo').val(),
                p_WorkSheet:$('#p_workSheet').val(),
                p_source:'ewo_app',
                p_def_type:p_def_type,
                backurl:window.location.href,
            };

            // 處理需要傳遞的參數
            for (var x in params) {
                var opt = document.createElement("textarea");
                opt.name = x;
                opt.value = params[x];
                temp_form.appendChild(opt);
            }
            document.body.appendChild(temp_form);
            // 提交表單
            temp_form.submit();
        }


        // function chkDSTB_CHECK_PERSONAL() {
        //     var chkVal = 'N';
        //     // console.log('chkVal01='+chkVal);
        //     $('input[id="dstb_check_personal"]').each(function(){
        //         // console.log($(this).prop('checked'))
        //         var checked = $(this).prop('checked');
        //         if(chkVal === 'N' && checked === true) {
        //             // console.log('chkVal02='+chkVal);
        //             chkVal = 'Y';
        //         }
        //     });
        //     if(chkVal === 'N') {
        //         alert('請先確認 \nDSTB 資料確認\n[本公司進行機上盒頻道節目收視之資訊蒐集分析及個人化內容之推薦等]的選擇!!!');
        //     }
        //     // console.log('chkVal03='+chkVal);
        //     return chkVal;
        // }



        // API CheckData
        function apiCheckData(params)
        {
            $.ajax({
                url: '/ewo/checkData',
                type: 'POST',
                data: params,
                async: false,
                processData: false,
                contentType: false,
                success: function (json) {
                    console.log(json);
                }, error: function (data) {
                    console.log(data);
                    alert('API Error[Check Data];');
                }
            });
        }


        // 客服信息
        function serviceMsg()
        {
            var chkEvent = confirm('有客服貼標信息，是否讀取!!!');
            if(chkEvent)
            {
                $('#alert_MailTitle').attr('class','alert alert-danger hide show ');
            }

        }

        // 五金耗料，存檔
        function hardConsSave() {
            var chk_length = 0;
            var p_data = [];
            var p_str = '';
            $('#hardConsBody input[name="hardCons"]').each(function(){
                var obj = $(this)
                var code = obj.data('code');
                var count = obj.val();
                if(count > 0)
                {
                    chk_length = 1;
                    p_data['code_'+code] = count;

                    p_str = (p_str.length < 1)? '{' : p_str;
                    p_str += (p_str.search(':') > 2)? ',' : '';
                    p_str += '"'+code+'":'+count;

                }
            });

            p_str += (chk_length > 0)? '}' : p_str;
            var params = {
                p_id: $('#p_id').val(),
                p_companyNo: $('#p_companyNo').val(),
                p_custId: $('#p_custId').val(),
                p_workSheet: $('#p_workSheet').val(),
                p_userCode: $('#p_userCode').val(),
                p_value: p_str,
                p_columnName: 'hardConsSave' }
            if(chk_length < 1)
            {
                alert('請確認[五金耗料]使用數量。')
                return;
            } else {
                apiEvent('hardConsSave',params)
            }

        }

        // 五金耗料 增加 品項
        function hardConsAdd(obj) {
            var category = $('#hardConsCate').find('option:selected').text();
            var prdname = $('#hardConsPrdNam').find('option:selected').text();
            var stand = $('#hardConsStand').find('option:selected').text();
            var code = $('#hardConsStand').find('option:selected').val();

            if(code === undefined)
            {
                alert('請確認[五金耗料]的種類/規格/型號')
                return;
            }
            var htmlStr = "";
            htmlStr += " " +
                " <div class=\"input-group mb-3\">\n" +
                "     <div class=\"input-group-prepend\">\n" +
                "         <button class=\"btn btn-danger\" onclick=\"$(this).parents('.input-group').remove();\" title=\"刪除\">Ｘ</button>\n" +
                "     </div>\n" +
                "     <input type=\"text\" class=\"form-control bg-muted\" disabled value=\""+category+"_"+prdname+"_"+stand+"\">\n" +
                "     <div class=\"input-group-append\">\n" +
                "         <button class=\"btn btn-success\" onclick=\"materialsSetInt(-1,$(this))\">-</button>\n" +
                "         <input type=\"number\" class=\"form-control Hardware-inpt-text p-0\" data-code=\""+code+"\" name=\"hardCons\" onchange=\"value=value.replace(/[^\\d]/g,'')\" ondblclick=\"$(this).val(0)\" value=\"0\">\n" +
                "         <button class=\"btn btn-info\" onclick=\"materialsSetInt(10,$(this))\">10</button>\n" +
                "         <button class=\"btn btn-warning\" onclick=\"materialsSetInt(1,$(this))\">+</button>\n" +
                "     </div>\n" +
                " </div>";
            $(obj).parents('.card-body').find('hr').after(htmlStr);
            $("#hardConsPrdNam").prepend("<option value=''>品項選擇</option>");
            $('#hardConsPrdNam option').eq(0).attr('selected',true);
            $('#hardConsStand option').remove();
        }


        // 物料/數量
        function materialsSetInt(plusVal,obj) {
            var b1 = obj.parents('.input-group');
            var b2 = b1.find('input[type=number]');
            var retVal = (parseInt(b2.val()) + parseInt(plusVal)) < 0? 0 : parseInt(b2.val()) + parseInt(plusVal);
            var retVal = retVal > 99? 99 : retVal;
            b2.val(retVal);
        }

        // 兩數，加減計算
        function calculationInt(val1,val2,min,max)
        {
            val2 = (val2.length < 1)? parseInt(0) : val2;
            var ret = parseInt(val1) + parseInt(val2);
            ret = (ret < min)? parseInt(0) : ret;
            ret = (ret > max)? max : ret;
            return ret;
        }



        // 掃描，value
        var scanval = '';
        var scanWorkSheet = '';

        // 相機掃描 APP => QrCode，回饋
        function getScanValue(value) {
            scanval=value;
            //alert('chk scanval length=='+scanval.length)
            if(scanval.length < 1) {
                alert('掃描失敗');
                return false;
            }


            $('#open'+scanWorkSheet+'_scanstr').val(scanval);

            // 掃描後執行 function 字串完全相符判斷
            switch (scanWorkSheet) {
                case 'stbmatvdevicvalue':
                    addStbMAtvTestValueLog('openstbmatvdevicvalue_scanstr','stb_atvqrcode');
                    break;
            }

            // 掃描後執行 function 字串 includes 判斷
            switch (true) {
                case (scanWorkSheet.includes('dstb_remote_mapping')):
                    addDstbRemoteMapping('open'+scanWorkSheet+'_scanstr');
                    break;
            }

        }

        // 開通，掃描
        function openscan(Id)
        {
            console.log(Id)
            // scanval = "SO: 720, Facisno: 01500013E500024D06, CM Mode: (),Data: (I1.32 , 0, 44dB, 56dBuV)";
            scanWorkSheet = Id;
            app.scan();
        }


        // 退單，API
        function Chargeback()
        {
            var p_type = "{{$p_data['info']->WorkKind}}";
            switch(p_type)
            {
                case "1 裝機":
                case "3 拆機":
                case "5 維修":
                case "C 換機":
                    break;
            }
        }



        // 施工照片刪除
        function constructionPhotoDBClick(obj)
        {
            var chkDel = confirm('確認刪除圖片?');
            // var chkDel = '';
            if(chkDel === false)
            {
                return;
            }

            var formData = {
                p_id:$('#p_id').val(),
                CustID:$('#p_custId').val(),
                BookDate:$('#p_BookDate').val(),
                fname:obj.attr('name')+'.jpg',
                names:$('#file_constructionPhoto').data('names'),
                p_columnName:'constructionPhotoDel'
            };
            apiEvent('constructionPhotoDel',formData);
        }

        // 第二證件照片刪除
        function id03PhotDBClick(obj)
        {
            var chkDel = confirm('確認刪除圖片?');
            // var chkDel = '';
            if(chkDel === false)
            {
                return;
            }

            var formData = {
                p_id:$('#p_id').val(),
                CustID:$('#p_custId').val(),
                BookDate:$('#p_BookDate').val(),
                fname:obj.attr('id')+'.jpg',
                names:$('#file_id03Photo').data('names'),
                p_columnName:'id03PhotoDel'
            };
            apiEvent('id03PhotoDel',formData);
        }

/*
        // 施工照片上傳
        // 施工照片，檔案名稱確認
        function constructionPhotoUpload(p_id)
        {
            var names = $('#'+p_id).data('names');
            var worksheet = '{{$p_data['info']->WorkSheet}}';
            var file_name = worksheet + '_construction_' ;
            if(names.indexOf(worksheet + '_construction_1.jpg') < 0)
                file_name += '1.jpg';
            else if(names.indexOf(worksheet + '_construction_2.jpg') < 0)
                file_name += '2.jpg';
            else if(names.indexOf(worksheet + '_construction_3.jpg') < 0)
                file_name += '3.jpg';
            else if(names.indexOf(worksheet + '_construction_4.jpg') < 0)
                file_name += '4.jpg';
            else if(names.indexOf(worksheet + '_construction_5.jpg') < 0)
                file_name += '5.jpg';
            else
                file_name += '1.jpg';

            // console.log('chk1850 file_name =='+file_name);
            //upload(p_id,file_name)
            return file_name;
        }
*/

/*
        // ID03[第二證件]，檔案名稱確認
        function id03PhotoUpload(p_id)
        {
            var names = $('#'+p_id).data('names');
            var worksheet = '{{$p_data['info']->WorkSheet}}';
            var file_name = worksheet + '_id03_' ;
            if(names.indexOf(worksheet + '_id03_1.jpg') < 0)
                file_name += '1.jpg';
            else if(names.indexOf(worksheet + '_id03_2.jpg') < 0)
                file_name += '2.jpg';
            else if(names.indexOf(worksheet + '_id03_3.jpg') < 0)
                file_name += '3.jpg';
            // else if(names.indexOf(worksheet + '_id03_4.jpg') < 0)
            //     file_name += '4.jpg';
            // else if(names.indexOf(worksheet + '_id03_5.jpg') < 0)
            //     file_name += '5.jpg';
            else
                file_name += '1.jpg';

            // console.log('chk1850 file_name =='+file_name);
            //upload(p_id,file_name)
            return file_name;
        }
*/
/*
        // test upload
        function upload_test(imgId, fileName, obj='') {
            var formData = new FormData();
            formData.append('image',obj);
            formData.append('_token',$('#p_token').val());
            formData.append('id',$('#p_id').val());
            formData.append('BookDate',$('#p_BookDate').val());
            formData.append('CustID',$('#p_custId').val());
            formData.append('companyNo',$('#p_companyNo').val());
            formData.append('workSheet',$('#p_workSheet').val());
            formData.append('p_userCode',$('#p_userCode').val());
            formData.append('p_userName',$('#p_userName').val());
            formData.append('fileName',fileName);

            var params = {
                image:obj,
                _token:$('#p_token').val(),
                id:$('#p_id').val(),
                BookDate:$('#p_BookDate').val(),
                CustID:$('#p_custId').val(),
                companyNo:$('#p_companyNo').val(),
                workSheet:$('#p_workSheet').val(),
                p_userCode:$('#p_userCode').val(),
                p_userName:$('#p_userName').val(),
                p_columnName:'id01',
                fileName:fileName
            };

            const XHR = new XMLHttpRequest();
            let urlEncodedData = "",
                urlEncodedDataPairs = [],
                name;
            for(var key in params) {
                urlEncodedDataPairs.push( encodeURIComponent( key ) + '=' + encodeURIComponent( params[key] ) );
            }
            urlEncodedData = urlEncodedDataPairs.join( '&' ).replace( /%20/g, '+' );

            var method = "post";
            var path = "/ewo/order_info/uploadimg";
            XHR.addEventListener( 'load', function(event) {
                alert( 'Yeah! Data sent and response loaded.' );
            } );
            XHR.addEventListener( 'error', function(event) {
                alert( 'Oops! Something went wrong.' );
            } );
            XHR.open(method, path);
            XHR.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
            XHR.send(urlEncodedData);
            XHR.onreadystatechange = (e) => {
                console.log('XHR request To reponse');
                var json = JSON.parse(XHR.responseText);
                console.log(json)
            }

        }
*/
/*
        // 取得GPS經緯
        function getLocalGPS() {
            var locationValue;
            try {
                locationValue= app.getLocation();
            } catch (error) {
                locationValue = '111,222';
            }
            var location= locationValue.split(",");
            var latitude = location[0];
            var longitude= location[1];
            //alert(`${latitude},${longitude}`);
            $('#localLat').val(latitude);
            $('#localLng').val(longitude);
        }
*/

        // 遲到，原因
        function delateDesc(obj)
        {
            var p_val = obj.val();
            var params = {
                p_companyNo : $('#p_companyNo').val(),
                p_workSheet : $('#p_workSheet').val(),
                p_columnName : "delate",
                p_value : p_val
            };
            apiEvent('delatedesc',params);
        }

/*
        // api Event
        function apiEvent(type, params)
        {
            params['_token'] = $('#p_token').val();
            params['p_userCode'] = $('#p_userCode').val();
            params['p_userName'] = $('#p_userName').val();

            console.log('apiEvent==');
            console.log(params);
            // if(0)
            $.ajax({
                method: 'POST',
                url: '/ewo/event',
                data: params,
                success: function (json) {
                    console.log(json)
                    if(json.code === "0000") {
                        switch(type)
                        {
                            case 'delatedesc': // 遲到原因
                                responseDelateDesc(json);
                                break;
                            case 'SrviceReason': // 維修原因
                                responseSrviceReason(json);
                                break;
                            case 'constructionPhotoDel': // 施工照片，刪除
                                // console.log('constructionPhotoDel api success==');
                                // console.log(json)
                                // console.log(params)
                                var img_name = (params.fname).split('.')[0];
                                $('#constructionPhoto_img img[name="'+img_name+'"]').remove();
                                $('#file_constructionPhoto').data('names',json.data)
                                break;
                            case 'id03PhotoDel': // 第二證件，刪除
                                // console.log('constructionPhotoDel api success==');
                                // console.log(json)
                                // console.log(params)
                                var img_name = (params.fname).split('.')[0];
                                $('#id03Photo_img img[id="'+img_name+'"]').closest('div').remove();
                                $('#file_id03Photo').data('names',json.data)
                                break;
                            case 'hardConsSave': // 五金耗料，存檔
                                $('#hardConsLabel').removeClass('d-none')
                                $('#hardConsLabel').text('存檔時間:'+json.date);
                                break;
                            case 'sentmail': // PDF 寄送 mail
                                var obj = $('#sentmail').parents('.input-group');
                                obj.find('.input-group-append span').text(json.date);
                                break;
                            case 'PaperPDF': // 紙本工單
                                $('#'+type+'_alert').removeClass('d-none');
                                $('#'+type+'_alert').text(json.data);
                                break;
                            case 'checkin': // 打卡
                                alert('打卡紀錄已經送出');
                                break;
                            default:
                                console.log('default');
                                // console.log(json);
                                break;
                        }
                    }
                }
            });
        }
*/


        // 維修原因[第二個]
        function chgSrviceReason()
        {
            var p_val = $('#select_srviceReasonLast :selected').val()
            if(p_val === "")
            {
                alert('請選擇第二個維修原因');
                return false;
            }
            var params = {
                p_columnName : "serviceReson",
                p_serviceResonFirst : $('#select_srviceReasonFirst :selected').text(),
                p_serviceResonLast : $('#select_srviceReasonLast :selected').text(),
                p_id : $('#p_id').val()
            };
            apiEvent('SrviceReason',params)

        }

        // 遲到原因[API response]
        function responseDelateDesc(json)
        {
            $('#delatealert').text('遲到時間:'+json['date']);
            $('#delatealert').removeClass('d-none');
            if(json['data'] === null)
            {
                $('#delatealert').addClass('d-none');
            }
        }

        // 維修原因[API response]
        function responseSrviceReason(json)
        {
            console.log('chk responseSrviceReason;;')
            console.log(json)
            //label_chgSiginsn
            $('#label_srviceReason').text('維修原因：'+json['date']);
            $('#label_srviceReason').removeClass('d-none');
        }

/*
        // 檢查信用卡卡號
        function creditcardNumCheck()
        {
            var chkVal = 0;
            var toDay = new Date(), chkDay;
            var creditcardCode = $('#creditcardCode').val();
            var creditcardMMYY = $('#creditcardMMYY').val();

            // 檢查卡號
            re = /^\d{4}-\d{4}-\d{4}-\d{4}$/;
            if (re.test(creditcardCode))
                chkVal += 1;

            // 檢查有效期
            re = /^\d{2}\/\d{2}$/;
            if (re.test(creditcardMMYY)) {
                chkVal += 2;

                var mm = creditcardMMYY.substr(0,2);
                var YY = creditcardMMYY.substr(3,2);
                var afterDay = new Date('20'+YY,mm-1,1)

                if(mm < 1 || mm > 12) {
                    chkVal = 4;
                } else if (afterDay < toDay) {
                    chkVal = 4;
                }

            }

            var error_msg = '';
            switch(chkVal) {
                case 0: // 資料都錯
                    error_msg = '';
                    break;
                case 1: // 有效期錯誤
                    error_msg = '有效期';
                    break;
                case 2: // 信用卡號錯
                    error_msg = '信用卡號';
                    break;
                case 4:
                    error_msg = '有效期不正確；';
                    break;
                case 3: // 資料正確
                    stbApi('creditcard');
                    return;
                    break;
            }

            alert(error_msg+ '資料錯誤');
            if(chkVal == 2)
                $('#creditcardCode').focus();
            else
                $('#creditcardMMYY').focus();

        }
*/

/*
        // 生成浮水印[DIV]
        function  createImgWatemark(p_id) {
            var htmlStr = "<div class=\"imgWatemark\">限用申請中嘉服務限用申請中嘉服務限用申請中嘉服務</div>";
            var imgObj = $('#'+p_id)
                ,p_src = imgObj.attr('src')
                ,imgHeight = imgObj.height()
                ,imgWidth = imgObj.width()
                ,chkStr = imgObj.next().text()
                ,wmHeight = imgHeight / 2 + 10
                ,wmWidth = imgWidth / 2 - (179 / 2)
            ;

            if(p_src.search('error') < 1) {
                if (chkStr === '限用申請中嘉服務') {
                    imgObj.next().remove();
                } else if (wmHeight > 0) {
                    imgObj.after(htmlStr);
                    imgObj.next().css('bottom', wmHeight)
                    imgObj.next().css('left', 0)
                }
            }
        }
*/
        // PDF reload
        function pdf_reload()
        {
            var iframeobj = $('#pdf_show').find('iframe')
            //iframeobj.attr('src')
            var timeSec = new Date().getSeconds() + new Date().getMilliseconds()
            var url = iframeobj.attr('src') + timeSec;
            iframeobj.attr('src',url)
            console.log(iframeobj.attr('src'));
        }

        // ======================= PDF =======================
        // ======================= PDF =======================
        // ======================= PDF =======================
        if(false) {
            // If absolute URL from the remote server is provided, configure the CORS
            // header on that server.
            //var url = 'https://
            // raw.githubusercontent.com/mozilla/pdf.js/ba2edeae/web/compressed.tracemonkey-pldi-09.pdf';
            // var url = 'https://raw.githubusercontent.com/mozilla/pdf.js/ba2edeae/web/compressed.tracemonkey-pldi-09.pdf';
            // var pdf_url = 'https://ewo-s.hmps.cc/upload/{{$p_data['uploaddir']}}/wm.pdf';
            var pdf_url = '/upload/{{$p_data['uploaddir']}}/{{$p_data['info']->WorkSheet}}.pdf{{'?i='.date('His')}}';

            // Loaded via <script> tag, create shortcut to access PDF.js exports.
            var pdfjsLib = window['pdfjs-dist/build/pdf'];

            // The workerSrc property shall be specified.
            pdfjsLib.GlobalWorkerOptions.workerSrc = '//mozilla.github.io/pdf.js/build/pdf.worker.js';

            var pdfDoc = null,
                pageNum = 1,
                pageRendering = false,
                pageNumPending = null,
                scale = 1,
                canvas = document.getElementById('the-canvas'),
                ctx = canvas.getContext('2d');

            /**
             * Get page info from document, resize canvas accordingly, and render page.
             * @param  num Page number.
             */
            function renderPage(num) {
                pageRendering = true;
                // Using promise to fetch the page
                pdfDoc.getPage(num).then(function (page) {
                    var viewport = page.getViewport({scale: scale});
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    // Render PDF page into canvas context
                    var renderContext = {
                        canvasContext: ctx,
                        viewport: viewport
                    };
                    var renderTask = page.render(renderContext);

                    // Wait for rendering to finish
                    renderTask.promise.then(function () {
                        pageRendering = false;
                        if (pageNumPending !== null) {
                            // New page rendering is pending
                            renderPage(pageNumPending);
                            pageNumPending = null;
                        }
                    });
                });

                // Update page counters
                document.getElementById('page_num').textContent = num;
            }

            /**
             * If another page rendering in progress, waits until the rendering is
             * finised. Otherwise, executes rendering immediately.
             */
            function queueRenderPage(num) {
                if (pageRendering) {
                    pageNumPending = num;
                } else {
                    renderPage(num);
                }
            }

            /**
             * Displays previous page.
             */
            function onPrevPage() {
                if (pageNum <= 1) {
                    return;
                }
                pageNum--;
                queueRenderPage(pageNum);
            }

            document.getElementById('prev').addEventListener('click', onPrevPage);

            /**
             * Displays previous zoom. 縮放
             */
            function onPrevZoom() {
                var s = document.getElementById('pdfZoom').value;
                scale = s;
                queueRenderPage(pageNum);
            }

            document.getElementById('pdfZoom').addEventListener('change', onPrevZoom);

            /**
             * Displays next page.
             */
            function onNextPage() {
                if (pageNum >= pdfDoc.numPages) {
                    return;
                }
                pageNum++;
                queueRenderPage(pageNum);
            }

            document.getElementById('next').addEventListener('click', onNextPage);

            /**
             * Asynchronously downloads PDF.
             */
             //{ url: url, password: '0000' }
            pdfjsLib.getDocument({ url: pdf_url, password: '0000' }).promise.then(function (pdfDoc_) {
                //console.log('ulr==' + url);
                pdfDoc = pdfDoc_;
                document.getElementById('page_count').textContent = pdfDoc.numPages;

                // Initial/first page rendering
                renderPage(pageNum);
            });

            function pdf_reload0(pdf_url2,pwd_val='0000')
            {
                pdfjsLib.getDocument({ url: pdf_url2, password: pwd_val }).promise.then(function (pdfDoc_) {
                    //console.log('ulr==' + url);
                    pdfDoc = pdfDoc_;
                    document.getElementById('page_count').textContent = pdfDoc.numPages;

                    // Initial/first page rendering
                    renderPage(pageNum);
                });
            }
        }
    </script>

@endsection
