@extends('ewo.layouts.default')

@section('title', '拆機_訂單明細')

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

    </style>
    <main style="margin-top: 55px;">

{{--        {{  Log::channel('ewoLog')->info("Detail demolition_".$p_data['info']->CompanyNo.'_'.$p_data['info']->WorkSheet.' p_data=='.print_r($p_data,1)) }}--}}

        <input type="hidden" name="p_userCode" id="p_userCode" value="{{$p_data['user_info']['userId']}}">
        <input type="hidden" name="p_userName" id="p_userName" value="{{$p_data['info']->WorkerName}}">
        <input type="hidden" name="p_userMobile" id="p_userMobile" value="{{$p_data['user_info']['mobile']}}">
        <input type="hidden" name="p_custName" id="p_custName" value="{{$p_data['info']->CustName}}">
        <input type="hidden" name="p_id" id="p_id" value="{{$p_data['info']->Id}}">
        <input type="hidden" name="p_custId" id="p_custId" value="{{$p_data['info']->CustID}}">
        <input type="hidden" name="p_subsidStr" id="p_subsidStr" value="{{$p_data['info']->substrStr}}">
        <input type="hidden" name="p_companyNo" id="p_companyNo" value="{{$p_data['info']->CompanyNo}}">
        <input type="hidden" name="p_workSheet" id="p_workSheet" value="{{$p_data['info']->WorkSheet}}">
        <input type="hidden" name="p_BookDate" id="p_BookDate" value="{{$p_data['info']->BookDate}}">
        <input type="hidden" name="p_ServiceName" id="p_ServiceName" value="{{$p_data['info']->ServiceName}}">
        <input type="hidden" name="p_pdf_v" id="p_pdf_v" value="{{$p_data['info']->pdf_v??config('order.PDF_CODE_V')}}">
        <input type="hidden" name="p_recvAmt" id="p_recvAmt" value="{{$p_data['recvAmt']}}">
        <input type="hidden" name="p_sheetStatus" id="p_sheetStatus" value="{{$p_data['info']->SheetStatus}}">
        <input type="hidden" name="p_sign_chs" id="p_sign_chs">
        <input type="hidden" name="p_phoneNum" id="p_phoneNum" value="{{$p_data['phoneNum']}}">
        <input type="hidden" name="p_workkind" id="p_workkind" value="{{$p_data['info']->WorkKind}}">
        <input type="hidden" name="p_workkindAryStr" id="p_workkindAryStr" value="{{$p_data['info']->WorkKindAryStr}}">
        <input type="hidden" name="p_instAddr" id="p_instAddr" value="{{$p_data['instAddrName']}}">
        <input type="hidden" name="p_worksheet2" id="p_worksheet2" value="{{$p_data['info']->worksheet2}}">
        <input type="hidden" name="p_token" id="p_token" value="{{ csrf_token() }}">

        <div class="container pt-2 bg-grey">

            <div class="alert alert-info mb-1" role="alert">
                <label class="d-inline">{{$p_data['info']->WorkKind}}</label>
                <label class="d-inline text-danger">服務別</label>
                <label class="d-inline">{{$p_data['info']->ServiceName}}</label>
                <label class="d-inline float-right">應收金額${{$p_data['recvAmt']}}</label>
            </div>
            <div id="accordion">


                {{-- 到站打卡 --}}
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
                                <div class="input-group-append input-group-text p-0 col-9 bg-white d-flow-root w-100" style="white-space:normal;text-align: inherit;" id="custAddres">
                                    {{$p_data['info']->custAddress}}
                                </div>
                                <div class="input-group-prepend bg-success p-0 col-3">
                                    <span class="input-group-text w-100">用戶GPS</span>
                                </div>
                                <input type="hidden" id="p_custGps" value="{{$p_data['info']->custGps}}">
                                <div class="input-group-append input-group-text p-0 col-9 bg-white d-flow-root w-100" style="white-space:normal;text-align: inherit;" id="custGps">
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
                @endif


                {{-- 檢點表 --}}
                <div class="card border-danger bw-5p mb-3">
                    <div class="card-header pt-0 pb-0" id="laborsafety_checklist_haeder">
                        <h5 class="mb-0">
                            <button class="btn btn-primary collapsed btn_collapsed" data-toggle="collapse" data-target="#laborsafety_checklist_body">
                                檢點表
                                <svg  width="16" height="16" fill="currentColor" class="bi bi-arrows-expand" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zM7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10z"/>
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


                {{-- 區故信息 --}}
                <div class="card mb-3">
                    <div class="card-header bg-warning">
                        <div class="input-group">
                            區故信息
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="input-group ">
                            <div class="w-100">
                                <div class="alert alert-info p-0 pl-2 pr-2 mb-0 ml-3">
                                    預約日：{{ $p_data['fault'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- 用戶信息 --}}
                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0" id="userInfoHead">
                        <h5 class="mb-0">
                            <button class="btn btn-primary collapsed btn_collapsed" data-toggle="collapse" data-target="#userInfoBody">
                                用戶信息
                                <svg  width="16" height="16" fill="currentColor" class="bi bi-arrows-expand" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zM7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10z"/>
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
                                        @elseif(in_array($k,['SubsCP']))
                                            <div class="d-block w-100">
                                                @foreach($p_data['info']->AssignSheetAry as $k => $t)
                                                    <ul class="list-group pt-0 pb-0">
                                                        <li class="list-group-item active pt-0 pb-0"> {{$k}}</li>
                                                        @foreach($t as $k2 => $t2)
{{--                                                            @if(in_array($k2,['C HS','D TWMBB','2 CM','3 DSTB']))--}}
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
                            </div>
                        </div>
                    </div>
                </div>


{{--                --}}{{-- 同戶服務狀態 --}}
{{--                <div class="card mb-3">--}}
{{--                    <div class="card-header pt-0 pb-0" id="sameAccountServiceHead">--}}
{{--                        <h5 class="mb-0">--}}
{{--                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#sameAccountServiceBody">--}}
{{--                                同戶服務狀態--}}
{{--                            </button>--}}
{{--                        </h5>--}}
{{--                    </div>--}}
{{--                    <div id="sameAccountServiceBody" class="collapse show" data-parent="#sameAccountServiceHead">--}}
{{--                        <div class="card-body">--}}
{{--                            @if(!empty($p_data['sameAccountService']))--}}
{{--                                @foreach($p_data['sameAccountService'] as $k => $t)--}}
{{--                                    <ul class="list-group list-group-flush">--}}
{{--                                        <li class="list-group-item list-group-item-success"></li>--}}
{{--                                        @foreach($p_data['sameAccountServiceHead'] as $k2 => $t2)--}}
{{--                                            <li class="list-group-item bg-info pb-0 pt-0">{{$t2}}</li>--}}
{{--                                            <li class="list-group-item pt-0">{{$t->$k2}}</li>--}}
{{--                                        @endforeach--}}
{{--                                    </ul>--}}
{{--                                @endforeach--}}
{{--                            @endif--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}


                {{-- 同地址設備服務狀態 --}}
                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0" id="deviceListHead">
                        <h5 class="mb-0">
                            <button class="btn btn-primary collapsed btn_collapsed" data-toggle="collapse" data-target="#deviceListBody">
                                同地址設備服務狀態
                                <svg  width="16" height="16" fill="currentColor" class="bi bi-arrows-expand" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zM7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10z"/>
                                </svg>
                            </button>
                        </h5>
                    </div>
                    <div id="deviceListBody" class="collapse show" data-parent="#deviceListHead">
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item list-group-item-success">工單住編{{ $p_data['info']->CustID }}</li>
                                <li class="list-group-item">
                                    <div class="input-group">
                                        <div class="col-2 pl-0 pr-0">訂編</div>
                                        <div class="col-2 pl-0 pr-0">服務別</div>
                                        <div class="col-2 pl-0 pr-0">狀態</div>
                                        <div class="col-3 pl-0 pr-0">型號</div>
                                        <div class="col-3 pl-0 pr-0">序號</div>
                                    </div>
                                </li>
                            @foreach($p_data['info']->deviceList as $k => $t)
                                <li class="list-group-item">
                                    <div class="input-group">
                                        <div class="col-2 pl-0 pr-0">{{ $t->SubsID }}</div>
                                        <div class="col-2 pl-0 pr-0">{{ $t->ServiceName }}</div>
                                        <div class="col-2 pl-0 pr-0">{{ $t->CustStatus }}</div>
                                        <div class="col-3 pl-0 pr-0">{{ empty($t->SWVersion)? '空的' : $t->SWVersion }}</div>
                                        <div class="col-3 pl-0 pr-0">{{ empty($t->SingleSN)? '空的' : $t->SingleSN }}</div>
                                    </div>
                                </li>
                            @endforeach
                            @if(sizeof($p_data['info']->deviceOnAddr))
                                    <li class="list-group-item list-group-item-info">同地址，其餘戶(住編)服務清單</li>
                                    <li class="list-group-item pl-0 pr-0">
                                        <div class="input-group">
                                            <div class="col-3">住編</div>
                                            <div class="col-3">訂編</div>
                                            <div class="col-3">服務別</div>
                                            <div class="col-3">狀態</div>
                                        </div>
                                    </li>
                                @foreach($p_data['info']->deviceOnAddr as $k => $t)
                                        <li class="list-group-item pl-0 pr-0">
                                            <div class="input-group">
                                                <div class="col-3">{{ $t->CustID }}</div>
                                                <div class="col-3">{{ $t->SubsID }}</div>
                                                <div class="col-3">{{ $t->ServiceName }}</div>
                                                <div class="col-3">{{ $t->CustStatus }}</div>
                                            </div>
                                        </li>
                                @endforeach
                            @endif
                            </ul>
                        </div>
                    </div>
                </div>


                {{-- 工作清單 --}}
                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0" id="orderListHead">
                        <h5 class="mb-0">
                            <button class="btn btn-primary collapsed btn_collapsed" data-toggle="collapse" data-target="#orderListBody">
                                工作清單
                                <svg  width="16" height="16" fill="currentColor" class="bi bi-arrows-expand" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zM7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10z"/>
                                </svg>
                            </button>
                        </h5>
                    </div>
                    <div id="orderListBody" class="collapse show" data-parent="#orderListHead">
                        <div class="card-body">
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
                        </div>
                    </div>
                </div>


                {{-- 紙本工單 --}}
                <div class="card mb-3">
                    <div class="card-header" >
                        <div class="input-group">
                            <div class="btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-primary active">
                                    <input type="checkbox" autocomplete="off" id="PaperPDF">紙本工單
                                </label>
                                <label class="alert alert-info p-2 pl-2 pt-0 mb-0" id="PaperPDF_alert">紙本工單信息。</label>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- 拆機流向 --}}
                <div class="card mb-3" id="retrievelist_card">
                    <div class="card-header pt-0 pb-0">
                        <label class="btn btn-info mb-0">
                            <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">--}}
                                <path d="M6.5 7a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1h-4z"></path>
                                <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"></path>
                            </svg>
                            <input class="d-none" type="button" id="demolitionFlow_button">
                            拆機流向
                        </label>
                        <label class="alert alert-info pt-1 pl-2 pr-2 mb-0 ml-3 @if(is_null($p_data['info']->chargeback) === true) d-none @endif" id="demolitionFlow_label">
                            拆機流向：
                        </label>
                    </div>
                    <div class="collapse show">
                        <div class="card-body">
                            <select class="custom-select" id="demolitionFlow_select">
                                <option value="0">請選擇[拆機流向]...</option>
                                @foreach($p_data['demolitionFlow'] as $k => $t)
                                    <option value="{{$t->dataName}}">{{$t->dataName}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>


                {{-- 取回單 --}}
                <div class="card mb-3" id="retrievelist_card">
                    <div class="card-header pt-0 pb-0" id="retrievelist_head">
                        <h5 class="mb-0">
                            <button class="btn btn-primary collapsed btn_collapsed" data-toggle="collapse" data-target="#retrievelist_body">
                                取回單
                                <svg  width="16" height="16" fill="currentColor" class="bi bi-arrows-collapse" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 8zm7-8a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L7.5 4.293V.5A.5.5 0 0 1 8 0zm-.5 11.707-1.146 1.147a.5.5 0 0 1-.708-.708l2-2a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 11.707V15.5a.5.5 0 0 1-1 0v-3.793z"/>
                                </svg>
                            </button>
                        </h5>
                    </div>
                    <div id="retrievelist_body" class="collapse" data-parent="#retrievelist_head">
                        <div class="card-body">
                            <form id="retrievelist_form" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="{{$p_data['info']->Id}}">
                                <input type="hidden" name="type" value="RetrieveList">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item list-group-item-warning">
                                        取回設備(取回數量)
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">纜線數據機</span>
                                            <span class="input-group-text">單埠</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Cable_modem_port"
                                                   value="@if(isset($p_data['retrieveList']->get_Cable_modem_port) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Cable_modem_port)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                            <span class="input-group-text">wifi</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Cable_modem_two_way"
                                                   value="@if(isset($p_data['retrieveList']->get_Cable_modem_two_way) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Cable_modem_two_way)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">數位機上盒</span>
                                            <span class="input-group-text">基本型</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Basic_digital_set_top_box"
                                                   value="@if(isset($p_data['retrieveList']->get_Basic_digital_set_top_box) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Basic_digital_set_top_box)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                            <span class="input-group-text">雙向型</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Digital_set_top_box_two_way_type"
                                                   value="@if(isset($p_data['retrieveList']->get_Digital_set_top_box_two_way_type) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Digital_set_top_box_two_way_type)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">光纖數據機</span>
                                            <span class="input-group-text">家計用ONT</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Fiber_Modem_HomeOnt"
                                                   value="@if(isset($p_data['retrieveList']->get_Fiber_Modem_HomeOnt) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Fiber_Modem_HomeOnt)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">智能家電</span>
                                            <span class="input-group-text">攝影機</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_camera"
                                                   value="@if(isset($p_data['retrieveList']->get_camera) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_camera)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                            <span class="input-group-text">門窗感應</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Door_and_window_sensor"
                                                   value="@if(isset($p_data['retrieveList']->get_Door_and_window_sensor) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Door_and_window_sensor)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">智能家電</span>
                                            <span class="input-group-text">煙霧偵測</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Smoke_detector"
                                                   value="@if(isset($p_data['retrieveList']->get_Smoke_detector) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Smoke_detector)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>
                                    <li class="list-group-item list-group-item-warning">
                                        纜線數據機配件(取回數量)
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">無線抗頻分享器</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Cable_accessories_wireless_anti_frequency_sharing_device"
                                                   value="@if(isset($p_data['retrieveList']->get_Cable_accessories_wireless_anti_frequency_sharing_device) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Cable_accessories_wireless_anti_frequency_sharing_device)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                            <span class="input-group-text">變壓器電源線</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Cable_accessories_transformer_power_cord"
                                                   value="@if(isset($p_data['retrieveList']->get_Cable_accessories_transformer_power_cord) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Cable_accessories_transformer_power_cord)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">乙太網路線</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Cable_accessories_Ethernet_cable"
                                                   value="@if(isset($p_data['retrieveList']->get_Cable_accessories_Ethernet_cable) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Cable_accessories_Ethernet_cable)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                            <span class="input-group-text">USB無線抗頻網卡</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Cable_accessories_USB_wireless_anti_frequency_network_card"
                                                   value="@if(isset($p_data['retrieveList']->get_Cable_accessories_USB_wireless_anti_frequency_network_card) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Cable_accessories_USB_wireless_anti_frequency_network_card)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>

                                    <li class="list-group-item list-group-item-warning">
                                        數位機上盒配件(取回數量)
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">遙控器</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Set_top_box_accessories_remote_control"
                                                   value="@if(isset($p_data['retrieveList']->get_Set_top_box_accessories_remote_control) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Set_top_box_accessories_remote_control)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                            <span class="input-group-text">HDMI</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Set_top_box_accessories_HDI"
                                                   value="@if(isset($p_data['retrieveList']->get_Set_top_box_accessories_HDI) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Set_top_box_accessories_HDI)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">AV線(1.5M)</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Set_top_box_accessories_AV_cable"
                                                   value="@if(isset($p_data['retrieveList']->get_Set_top_box_accessories_AV_cable) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Set_top_box_accessories_AV_cable)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                            <span class="input-group-text">色差線(1.5M)</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Set_top_box_accessories_Chromatic_aberration_line"
                                                   value="@if(isset($p_data['retrieveList']->get_Set_top_box_accessories_Chromatic_aberration_line) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Set_top_box_accessories_Chromatic_aberration_line)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">變壓器電源線</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Set_top_box_accessories_transformer_power_cord"
                                                   value="@if(isset($p_data['retrieveList']->get_Set_top_box_accessories_transformer_power_cord) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Set_top_box_accessories_transformer_power_cord)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                            <span class="input-group-text">智慧卡</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Set_top_box_accessories_smart_card"
                                                   value="@if(isset($p_data['retrieveList']->get_Set_top_box_accessories_smart_card) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Set_top_box_accessories_smart_card)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">外接式硬碟</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Set_top_box_accessories_external_hard_disk"
                                                   value="@if(isset($p_data['retrieveList']->get_Set_top_box_accessories_external_hard_disk) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Set_top_box_accessories_external_hard_disk)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                            <span class="input-group-text">USB無線抗頻網卡</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Set_top_box_accessories_USB_wireless_anti_frequency_network_card"
                                                   value="@if(isset($p_data['retrieveList']->get_Set_top_box_accessories_USB_wireless_anti_frequency_network_card) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Set_top_box_accessories_USB_wireless_anti_frequency_network_card)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">ATV機上盒</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Set_top_box_accessories_ATV_set_top_box"
                                                   value="@if(isset($p_data['retrieveList']->get_Set_top_box_accessories_ATV_set_top_box) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Set_top_box_accessories_ATV_set_top_box)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                            <span class="input-group-text">藍芽遙控器</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Set_top_box_accessories_Bluetooth_remote_control"
                                                   value="@if(isset($p_data['retrieveList']->get_Set_top_box_accessories_Bluetooth_remote_control) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Set_top_box_accessories_Bluetooth_remote_control)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>

                                    <li class="list-group-item list-group-item-warning">
                                        智能家庭配件(取回數量)
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">變壓器電源線</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="get_Smart_home_accessories_transformer_power_cord"
                                                   value="@if(isset($p_data['retrieveList']->get_Smart_home_accessories_transformer_power_cord) === false){{intval(0)}}@else{{intval($p_data['retrieveList']->get_Smart_home_accessories_transformer_power_cord)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>
                                </ul>
                            </form>
                        </div>
                    </div>
                </div>


                {{-- dialog，通知訊息 --}}
                <div id="msg_Dialog" title="通知訊息" class="d-none">
                    <div class="card w-80" id="card_msg">
                        <div class="card-header text-right pt-0 pb-0">
                            查詢時間:...
                        </div>
                        <button class="btn btn-info w-100" onclick="setMsgRead()">讀取</button>
                        <div class="card-body">
                            <ul class="list-group list-group-flush" id="msg_dialog_ul">
                                <li class="list-group-item">通知訊息</li>
                            </ul>
                        </div>
                    </div>
                </div>


                {{-- dialog，上傳圖片檢查 --}}
                <div id="idphoto_dialog" title="上傳圖片檢查" class="d-none">
                    <div class="card w-80">
                        <div class="card-header">
                            上傳圖片檢查
                        </div>
                        <div class="card-body">
                            <img src="">
                            掃描內容:<label class="alert alert-info w-100" id="idphoto_dialog_alert"></label>
                        </div>
                    </div>
                    <button class="button" onclick="document.body.style.overflow = 'scroll';$('#idphoto_dialog').dialog('close');">確定</button>
                </div>


                {{-- dialog，危險地點 --}}
                <div id="laborsafetyDialog" title="勞工安全-危險地點" class="d-none">
                    <div class="card w-80" id="card_wifitest">
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


                {{-- 簽名欄位 --}}
                <div class="card border-danger bw-5p mb-3">
                    <div class="card-header pt-0 pb-0" id="uploSignHead">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#uploSignBody">
                                簽名欄位
                            </button>
                        </h5>
                    </div>
                    <div id="uploSignBody" class="collapse show" data-parent="#uploSignHead">
                        <div class="card-body">

                            {{--sign--}}
                            <div class="d-none" id="signDiv_dstb">
                                <div class="input-group-prepend p-0" id="signButton_dstb">
                                    <button class="btn btn-success mr-3" id="signRestBtn_dstb" onclick="resetSignButton('open','dstb')">重新簽名</button>
                                    <button class="btn btn-info mr-3" id="signUpBtn_dstb" onclick="signUpload('dstb');resetSignButton('close','dstb')">上傳</button>
                                    <button class="btn btn-secondary" id="signCloseBtn_dstb" onclick="resetSignButton('close','dstb')">取消</button>
                                    <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3 " id="signAlert_dstb">
                                        DSTB 簽名
                                    </label>
                                </div>
                                <img src="/upload/{{$p_data['uploaddir']}}/sign_dstb_{{$p_data['info']->WorkSheet}}.jpg?i={{date('His')}}" width="500" id="signShow_dstb">
                                <div id="signaturePad_dstb" class="signature-pad">
                                    <div class="signature-pad--body" style="border: 3px #000 solid;">
                                        <canvas id="upSignImg_dstb"></canvas>
                                    </div>
                                    <div class="signature-pad--footer">
                                        <div class="signature-pad--actions">
                                            <div>
                                                <button type="button" id="signClear_dstb" class="button clear" data-action="clear">重寫</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{--sign--}}
                            <div class="d-none" id="signDiv_cm">
                                <div class="input-group-prepend p-0" id="signButton_cm">
                                    <button class="btn btn-success mr-3" id="signRestBtn_cm" onclick="resetSignButton('open','cm')">重新簽名</button>
                                    <button class="btn btn-info mr-3" id="signUpBtn_cm" onclick="signUpload('cm');resetSignButton('close','cm')">上傳</button>
                                    <button class="btn btn-secondary" id="signCloseBtn_cm" onclick="resetSignButton('close','cm')">取消</button>
                                    <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3 " id="signAlert_cm">
                                        CM 簽名
                                    </label>
                                </div>
                                <img src="/upload/{{$p_data['uploaddir']}}/sign_cm_{{$p_data['info']->WorkSheet}}.jpg?i={{date('His')}}" width="500" id="signShow_cm">
                                <div id="signaturePad_cm" class="signature-pad">
                                    <div class="signature-pad--body" style="border: 3px #000 solid;">
                                        <canvas id="upSignImg_cm"></canvas>
                                    </div>
                                    <div class="signature-pad--footer">
                                        <div class="signature-pad--actions">
                                            <div>
                                                <button type="button" id="signClear_cm" class="button clear" data-action="clear">重寫</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{--sign--}}
                            <div class="d-none" id="signDiv_twmbb">
                                <div class="input-group-prepend p-0" id="signButton_twmbb">
                                    <button class="btn btn-success mr-3" id="signRestBtn_twmbb" onclick="resetSignButton('open','twmbb')">重新簽名</button>
                                    <button class="btn btn-info mr-3" id="signUpBtn_twmbb" onclick="signUpload('twmbb');resetSignButton('close','twmbb')">上傳</button>
                                    <button class="btn btn-secondary" id="signCloseBtn_twmbb" onclick="resetSignButton('close','twmbb')">取消</button>
                                    <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3 " id="signAlert_twmbb">
                                        TWMBB 簽名
                                    </label>
                                </div>
                                <img src="/upload/{{$p_data['uploaddir']}}/sign_twmbb_{{$p_data['info']->WorkSheet}}.jpg?i={{date('His')}}" width="500" id="signShow_twmbb">
                                <div id="signaturePad_twmbb" class="signature-pad">
                                    <div class="signature-pad--body" style="border: 3px #000 solid;">
                                        <canvas id="upSignImg_twmbb"></canvas>
                                    </div>
                                    <div class="signature-pad--footer">
                                        <div class="signature-pad--actions">
                                            <div>
                                                <button type="button" id="signClear_twmbb" class="button clear" data-action="clear">重寫</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{--sign--}}
                            <div class="d-none" id="signDiv_mengineer">
                                <div class="input-group-prepend p-0" id="signButton_mengineer">
                                    <button class="btn btn-success mr-3" id="signRestBtn_mengineer" onclick="resetSignButton('open','mengineer')">重新簽名</button>
                                    <button class="btn btn-info mr-3" id="signUpBtn_mengineer" onclick="signUpload('mengineer');resetSignButton('close','mengineer')">上傳</button>
                                    <button class="btn btn-secondary" id="signCloseBtn_mengineer" onclick="resetSignButton('close','mengineer')">取消</button>
                                    <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3 " id="signAlert_mengineer">
                                        工程人員 簽名
                                    </label>
                                </div>
                                <img src="/upload/{{$p_data['uploaddir']}}/sign_mengineer_{{$p_data['info']->WorkSheet}}.jpg?i={{date('His')}}" width="500" id="signShow_mengineer">
                                <div id="signaturePad_mengineer" class="signature-pad">
                                    <div class="signature-pad--body" style="border: 3px #000 solid;">
                                        <canvas id="upSignImg_mengineer"></canvas>
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


                {{-- PDF --}}
                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0" id="pdfHead">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#pdfBody">
                                PDF
                            </button>
                        </h5>
                    </div>
                    <div id="pdfBody" class="collapse show" data-parent="#pdfHead">
                        <div id="pdf_show" style="width: 100%; height: 500px;"></div>
                    </div>
                </div>


                {{-- 寄送Mail --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="input-group">
                            <label class="btn btn-success mb-0">
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


                {{-- 同戶欠費 --}}
                <div class="card mb-3">
                    <div class="card-header" >
                        <div class="input-group">
                            <div class="btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-primary active">
                                    <input type="checkbox" autocomplete="off" id="arrears_btn">查詢同戶欠費
                                </label>
                                <label class="alert alert-info p-2 pl-2 pt-0 mb-0" id="arrears_alert">...</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped" id="arrears_table">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">公司別</th>
                                <th scope="col">訂編</th>
                                <th scope="col">工單號</th>
                                <th scope="col">服務別</th>
                                <th scope="col">服務內容</th>
                                <th scope="col">金額</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>


                {{-- 完工 --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="input-group" id="receivemoneyDiv">
                            款項收取：
                            <select class="custom-select bg-success" name="receivemoney" id="receivemoney">
                                <option class="bg-info" value="3">完工(未收)</option>
                            </select>
                        </div>
                        <div class="input-group" id="creditcardInputGroup">
                            <div id="creditcardInputGroup">
                                <input type="tel" id="creditcardCode" required="" maxlength="19" onkeydown="this.value=this.value.replace(/\D/g,'').replace(/....(?!$)/g,'$&amp;-')" class="form-control text-center" placeholder="信用卡號 xxxx-xxxx-xxxx-xxxx" title="信用卡號 xxxx-xxxx-xxxx-xxxx" />
                                <input type="tel" id="creditcardMMYY" required="" maxlength="5" class="form-control text-center" onkeydown="this.value=this.value.replace(/\D/g,'').replace(/..(?!$)/g,'$&amp;/')" placeholder="有效期限(月/年) mm/yy" title="有效期限(月/年) mm/yy" />
                                <div class="col-12 alert alert-warning mb-0" role="alert" id="creditcardAlert">信用卡刷卡結果</div>
                            </div>
                        </div>
                        <div class="input-group" id="finishBtnDiv">
                            <label class="btn btn-warning mb-0">
                                <input class="d-none" type="button" id="finshBtn" onclick="stbApi('{{str_replace(' ','_',$p_data['info']->WorkKind)}}')">
                                完工
                            </label>
                            <div class="alert alert-info mb-0 ml-3" role="alert" id="finshtimeAlert">完工API:OK；時間{{date('Y-m-d H:i:s',strtotime($p_data['info']->finsh)).'；'}}款項收取:{{($p_data['info']->receiveType === '1')? '刷卡': '現金'}}{{'$'.$p_data['info']->receiveMoney}}</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning" role="alert" id="installFinshalert">完工API訊息</div>
                    </div>
                </div>


                {{-- 五金耗料存檔 --}}
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


            </div>
        </div>
    </main>
@endsection

@section('script')

    <script>

        $(document).ready(function () {
            console.log('拆機工單');
            // DSTB 表格資料確認
            $('#dstb_check_data_body').change(function(){
                var params = new FormData(document.getElementById('dstb_check_form'));
                apiCheckData(params);
            });

            // cm 表格資料確認
            $('#cm_check_data_body').change(function(){
                var params = new FormData(document.getElementById('cm_check_form'));
                apiCheckData(params);
            });

            // twmbb 表格資料確認
            $('#twmbb_check_data_body').change(function(){
                var params = new FormData(document.getElementById('twmbb_check_form'));
                apiCheckData(params);
            });

            // 借用單
            $('#borrowminglist_body').click(function(){
                var params = new FormData(document.getElementById('borrowminglist_form'));
                apiCheckData(params);
            });

            // 取回單
            $('#retrievelist_body').click(function(){
                var params = new FormData(document.getElementById('retrievelist_form'));
                apiCheckData(params);
            });

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

            // 簽名板
            if(true) {
                var servName = '{{$p_data['info']->ServiceName}}';
                if(servName.search('DSTB') > 0 || servName === '1 CATV') {
                    $('#signDiv_dstb').removeClass('d-none');
                    createSign('dstb');
                    // 簽名，Label
                    var chk_title = "@if(!empty($p_data['dstbcheck']) && $p_data['dstbcheck']->dstb_check_title){{$p_data['dstbcheck']->dstb_check_title}}@else{{$p_data['info']->CustName}}@endif";
                    var legalStr = "@if(!empty($p_data['dstbcheck']) && $p_data['dstbcheck']->dstb_check_legal){{$p_data['dstbcheck']->dstb_check_legal}}@else本人@endif";
                    var labelStr = 'DSTB 簽名：';
                    labelStr += legalStr + '('+chk_title+')';
                    $('#signAlert_dstb').text(labelStr);
                    $('#signSpan_dstb').text(labelStr);
                    // signDialog('dstb');
                }

                if(servName.search('CM') > 0 || servName.search('HS') > 0) {
                    $('#signDiv_cm').removeClass('d-none');
                    createSign('cm');
                    // 簽名，Label
                    var chk_title = "@if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_title){{$p_data['cmcheck']->cm_check_title}}@else{{$p_data['info']->CustName}}@endif";
                    var legalStr = "@if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_legal){{$p_data['cmcheck']->cm_check_legal}}@else本人@endif";
                    var labelStr = 'CM 簽名：';
                    if(servName.search('CM') < 0 && servName.search('HS') > 0) {
                        $('#p_sign_chs').val('Y');
                        labelStr = 'HS 簽名:';
                    }
                    labelStr += legalStr + '('+chk_title+')';
                    $('#signAlert_cm').text(labelStr);
                    $('#signSpan_cm').text(labelStr);
                    // signDialog('cm');
                }

                if(servName.search('TWMBB') > 0) {
                    $('#signDiv_twmbb').removeClass('d-none');
                    createSign('twmbb');
                    // 簽名，Label
                    var chk_title = "@if(!empty($p_data['twmbbcheck']) && $p_data['twmbbcheck']->twmbb_check_title){{$p_data['twmbbcheck']->twmbb_check_title}}@else{{$p_data['info']->CustName}}@endif";
                    var legalStr = "@if(!empty($p_data['twmbbcheck']) && $p_data['twmbbcheck']->twmbb_check_legal){{$p_data['twmbbcheck']->twmbb_check_legal}}@else本人@endif";
                    var labelStr = 'TWMBB 簽名：';
                    labelStr += legalStr + '('+chk_title+')';
                    $('#signAlert_twmbb').text(labelStr);
                    $('#signSpan_twmbb').text(labelStr);
                    // signDialog('twmbb');
                }

                if(true) {
                    $('#signDiv_mengineer').removeClass('d-none');
                    createSign('mengineer');
                    // 簽名，Label
                    var labelStr = '工程 簽名：' + $('#p_userName').val();
                    $('#signAlert_mengineer').text(labelStr);

                }

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
            if(0)
            $("input[type='file']").change(function() {
                var compressRatio = 1, // 圖片壓縮比例
                    imgNewWidth = 1000, // 圖片新寬度
                    img = new Image(),
                    canvas = document.createElement("canvas"),
                    context = canvas.getContext("2d"),
                    file, fileReader, dataUrl;

                var p_id = $(this).attr('id');
                var idAry = ['file_id_01','file_id_02','file_id03Photo','file_certificate_01','file_certificate_02','file_constructionPhoto','file_checkin']
                    ,fNameAry = ['identity_01.jpg','identity_02.jpg','file_id03Photo','certificate_01.jpg','certificate_02.jpg','file_constructionPhoto','checkIn.jpg']
                    ,p_id_index = idAry.indexOf(p_id);

                // 清單內的ID才處理，其他的挑過
                if(p_id_index < 0) {
                    return;
                }

                if(p_id === 'file_checkin') {
                    getLocalGPS()
                }

                file = document.getElementById(p_id).files[0];
                // 圖片才處理
                if (file && file.type.indexOf("image") == 0) {
                    fileReader = new FileReader();
                    fileReader.onload = getFileInfo;
                    fileReader.readAsDataURL(file);
                }

                // 測試圖片上傳[001265 & A2021060010579]
                if(10)
                if($('#p_userCode').val() === '001265') {
                    if($('#p_workSheet').val() == 'A2021060010579') {
                        console.log('A2021060010579')
                        var uploadImg = new UploadImg();
                        uploadImg.addFileAndSend(this);
                        function UploadImg() {
                            var xhr = new XMLHttpRequest();
                            var form_data = new FormData();
                            const LENGTH = 1024 * 1024 * 0.3; //0.5M
                            var start = 0;
                            var end = start + LENGTH;
                            var blob;
                            var blob_num = 1;
                            var is_stop = 0;

                            this.addFileAndSend = function (that) {
                                var file = that.files[0];
                                blob = cutFile(file);
                                sendFile(blob, file);
                                blob_num += 1;
                            };

                            // this.stop = function () {
                            //     xhr.abort();
                            //     is_stop = 1;
                            // };

                            function cutFile(file) {
                                var file_blob = file.slice(start, end);
                                start = end;
                                end = start + LENGTH;
                                return file_blob;
                            }

                            function sendFile(blob, file) {
                                var form_data = new FormData();
                                var total_blob_num = Math.ceil(file.size / LENGTH);
                                form_data.append("image", blob);
                                form_data.append("blob_num", blob_num);
                                form_data.append("total_blob_num", total_blob_num);

                                var fName = fNameAry[p_id_index];
                                if(p_id === 'file_constructionPhoto')
                                    fName = constructionPhotoUpload(p_id);
                                if(p_id === 'file_id03Photo')
                                    fName = id03PhotoUpload(p_id);

                                form_data.append("file_name", file.name);
                                form_data.append('_token','{{csrf_token()}}');
                                form_data.append('fileName',fName);
                                form_data.append('id',$('#p_id').val());
                                form_data.append('p_userCode',$('#p_userCode').val());
                                form_data.append('p_userName',$('#p_userName').val());
                                form_data.append('CustID',$('#p_custId').val());
                                form_data.append('BookDate',$('#p_BookDate').val());
                                form_data.append('companyNo',$('#p_companyNo').val());
                                form_data.append('workSheet',$('#p_workSheet').val());
                                form_data.append('p_columnName','constructionPhoto');
                                xhr.open("POST", "/ewo/order_info/uploadimg", false);
                                xhr.onreadystatechange = function () {
                                    if (xhr.readyState == 4 && xhr.status == 200) {
                                        console.log(xhr.responseText);
                                        var json = JSON.parse(xhr.responseText);
                                        console.log(json);
                                        if (p_id === 'file_constructionPhoto') { // 施工照片
                                            if(json['data'] === 'uploading'){
                                                console.log(json['meg'])
                                            }else {
                                                $('#file_constructionPhoto').data('names', json['data']['names']);
                                                var html_img = '<img class=" constructionPhoto-img pb-1 ml-1" width="500" name="' + fName.split('.')[0] + '" src="' + json['data']['img'] + '" ondblclick="constructionPhotoDBClick($(this))" />';
                                                var img_length = $('#constructionPhoto_img').find('img').length;
                                                if (img_length >= 5) {
                                                    $('#constructionPhoto_img').find('img[name=' + fName.split('.')[0] + ']').before(html_img);
                                                    $('#constructionPhoto_img').find('img[name=' + fName.split('.')[0] + ']').eq(1).remove();
                                                } else if (img_length < 1) {
                                                    $('#constructionPhoto_img').html(html_img);
                                                } else {
                                                    $('#constructionPhoto_img').find('img').eq(img_length - 1).after(html_img);
                                                }
                                            }
                                        }
                                    }
                                    // var progress;
                                    // // var progressObj = document.getElementById("finish");
                                    // if (total_blob_num == 1) {
                                    //     progress = "100%";
                                    // } else {
                                    //     progress =
                                    //         Math.min(
                                    //             100,
                                    //             (blob_num / total_blob_num) * 100
                                    //         ) + "%";
                                    // }
                                    // // progressObj.style.width = progress;
                                    var t = setTimeout(function () {
                                        if (start < file.size && is_stop === 0) {
                                            blob = cutFile(file);
                                            sendFile(blob, file);
                                            blob_num += 1;
                                        } else {
                                            setTimeout(t);
                                        }
                                    }, 1000);
                                };
                                xhr.send(form_data);
                            }
                        } //end functoin

                        return false;
                    }
                }

                // 取得圖片信息
                function getFileInfo(evt) {
                    console.log('chk getFileInfo();');
                    dataUrl = evt.target.result;
                    img.src = dataUrl;

                    if(0)
                    if($('#p_userCode').val() === '001265') {
                        if($('#p_workSheet').val() == 'A2021060010579')
                            alert('chk worksheet==A2021060010579')
                        console.log('segmentationImg=='+$('#p_userCode').val())
                        segmentationImg(dataUrl);
                    }
                }

                // 切割圖片
                function segmentationImg(base64Str) {
                    console.log('chk segmentationImg();');
                    console.log('chk base62 len=='+base64Str.length)
                    console.log(base64Str)
                    var strLength = base64Str.length,
                        limit = 700000,
                        arrangement = 0,
                        strStart = 0,
                        strEnd = 0;

                    var params = {
                        p_companyNo : $('#p_companyNo').val(),
                        p_workSheet : $('#p_workSheet').val(),
                        p_columnName : "segmentationImg",
                        p_imgId : p_id,
                        p_custid : $('#p_custId').val(),
                        p_bookdate : $('#p_BookDate').val(),
                    };
                    while(strLength > 0) {
                        params['arrangement'] = (arrangement += 1);
                        params['p_value'] = base64Str.substr(strStart,limit);
                        strStart += limit;

                        apiEvent('segmentationImg',params)
                        strLength -= limit;
                    }

                    params['p_columnName'] = "mergeImg_0";
                    params['arrangement'] = arrangement;
                    params['p_value'] = "";
                    //apiEvent('segmentationImg',params)
                    setTimeout(apiEvent('segmentationImg',params),3000)

                }

                // 圖片載入後
                img.onload = function() {
                    var width = this.width, // 圖片原始寬度
                        height = this.height, // 圖片原始高度
                        imgNewHeight = imgNewWidth * height / width, // 圖片新高度
                        html = "",
                        newImg;

                    // 顯示預覽圖片
                    // html += "<img src='" + dataUrl + "'/>";
                    // html += "<p>這裡是原始圖片尺寸 " + width + "x" + height + "</p>";
                    // html += "<p>檔案大小約 " + Math.round(file.size / 1000) + "k</p>";
                    // $("#oldDiv").html(html);

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
                        upload(idAry[p_id_index],fName,blob)
                    }, "image/jpeg", compressRatio);
                };
            });
*/



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

            // 資料確認，欄位開關判斷
            if(true) {
                var chk_servName = $('#p_ServiceName').val();
                if(chk_servName.search('DSTB') > 0) {
                    $('#checkDataDiv_dstb').removeClass('d-none');
                }
                if(chk_servName.search('CM') > 0) {
                    $('#checkDataDiv_cm').removeClass('d-none');
                }
                if(chk_servName.search('TWMBB') > 0) {
                    $('#checkDataDiv_twmbb').removeClass('d-none');
                }
            }

            // 個人資料確認，代簽欄位切換
            $('input[type="text"]').change(function () {
                var chk_id = $(this).attr('id');
                var idAry = ['dstb_check_legal', 'cm_check_legal'];
                // console.log('chk1791')
                if (idAry.indexOf(chk_id) >= 0) {
                    var typVal = chk_id.split('_')[0]
                    var editVal = $(this).val();
                    var titleVal = $('#dstb_check_title').val()
                    var newText = typVal.toUpperCase() + ' 簽名：';
                    // console.log('newText=='+newText+'  typVal=='+typVal+'  editVal==='+editVal+'  titleVal==='+titleVal)
                    if (titleVal === '本人') {
                        newText += $('#p_custName').val() + '(本人)';
                    } else {
                        newText += editVal + ' 代簽' + ' 關係：' + titleVal;
                    }
                    $('#signAlert_' + typVal).text(newText);
                }
            });

            // PDF 資料確認
            $('select').change(function(){
                var chk_id = $(this).attr('id');
                var idAry = ['dstb_check_title','cm_check_title'];
                if(idAry.indexOf(chk_id) >= 0) {
                    var typVal = chk_id.split('_')[0]
                    var editVal = $('#'+typVal+'_check_legal').val();
                    var titleVal = $('#'+typVal+'_check_title').val()
                    var newText = typVal.toUpperCase() + ' 簽名：';
                    // console.log('newText=='+newText+'  typVal=='+typVal+'  editVal==='+editVal+'  titleVal==='+titleVal)
                    if (titleVal === '本人') {
                        newText += $('#p_custName').val() + '(本人)';
                    } else {
                        newText += editVal + ' 代簽' + ' 關係：' + titleVal;
                    }
                    $('#signAlert_' + typVal).text(newText);
                }
            });


            // 網路品質查詢
            $('input[name="cmqualityforkg_btn"]').click(function(){
                console.log($(this).attr('id'))

                stbApi('cmqualityforkg',$(this).data('subsid'));
                $('#cmqualityforkg_'+$(this).data('subsid')+'_label').text('網路品質查找中')
                alert('網路品質查找中')
            });

            if(true) {
                var options = {
                    forcePDFJS: true,
                    fallbackLink: "<p>使用的瀏覽器或視窗格式不支援PDF預覽，請直接<a href=\"https://sms.hmps.cc/hr/HomeplusHR.pdf\">下載</a>觀看</p>",
                    PDFJS_URL: "{{asset('/PDF_JS/web/viewer.html')}}"
                };
                var myPDF = PDFObject.embed("{{asset('/upload/'.$p_data['uploaddir'].'/'.$p_data['info']->WorkSheet.'.pdf?'.date('Ymd'))}}", "#pdf_show", options);
            }

            // 設備取回單
            if(true) {
                var retrieveListShow_chk = '{{$p_data['retrieveListShow']}}';
                if(retrieveListShow_chk === 'N') {
                    $('#retrievelist_card').remove();
                }
            }

            // 打卡提醒
            if(10) {
                var p_id = $(this).attr('id');
                $('button').click(function(){
                    if(parseInt($('#img_checkin').data('chk')) > 1)
                        return ;
                    if(p_id !== undefined && p_id.search('open') >= 0) //開通按鈕
                        alert('請先完成打卡')
                });
                $('input').click(function(){
                    if(parseInt($('#img_checkin').data('chk')) > 1)
                        return ;
                    if(p_id == 'file_checkin')
                        return;
                    if(p_id == 'finshBtn') //完工按鈕
                        alert('請先完成打卡')
                });
            }

            // 拆機流向
            $('#demolitionFlow_button').click(function() {
                var p_value = $('#demolitionFlow_select').val();
                if(p_value == '0') {
                    alert('請選擇[拆機流向]')
                } else {
                    if(confirm('請確認選擇：'+p_value)) {
                        var params = {
                            p_id: $('#p_id').val(),
                            p_custId: $('#p_custId').val(),
                            p_companyNo: $('#p_companyNo').val(),
                            p_workSheet: $('#p_workSheet').val(),
                            p_userCode: $('#p_userCode').val(),
                            p_userName: $('#p_userName').val(),
                            p_value: p_value,
                            p_columnName: 'demolitionflow' }
                        console.log('demolitionFlow_button API Params==');
                        console.log(params);
                        apiEvent('demolitionflow', params);
                    }
                }
            });

        }); // endready



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

        // 更換設備
        function changeEquipment()
        {
            var val_siginsn = $("input[name='chg_siginsn']:checked").data('sn');
            //console.log(val_siginsn);
            if(typeof(val_siginsn) === "undefined")
            {
                alert( "請選取切換的設備!");
                return false;
            }
            stbApi('chgEquipment')
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
            if(scanval.length < 1)
            {
                alert('掃描失敗');
                return false;
            }
            $('#open'+scanWorkSheet+'_scanstr').val(scanval)
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

/*
        // 開通，API Response
        function openApiResponse(jsonData,worksheet)
        {
            //openalert
            var strVal = "" +
                "meg:" + jsonData['meg'] + "; " +
                "code:" + jsonData['code'] + "; " +
                "status:" + jsonData['status'] + "; " +
                "date:" + jsonData['date'] + "; "
            ;
            console.log(jsonData)
            console.log(worksheet)
            $('#open'+worksheet+'_alert').text(strVal);
        }
*/
/*
        // 完工，API Response
        function installFinshApiResponse(jsonData)
        {

            console.log('installFinshApiResponse ===')
            console.log(jsonData)
            //installFinshalert
            var strVal = "" +
                "meg:" + jsonData['meg'] + "; " +
                "code:" + jsonData['code'] + "; " +
                "status:" + jsonData['status'] + "; " +
                "date:" + jsonData['date'] + "; "
            ;

            $('#installFinshalert').text(strVal);
            $('#finshAlert').text('完工API：OK'+jsonData['date']);
            //$('#finshButton').remove();

            var chkVal = $('input[name=receivemoney]:checked').val()
            if(chkVal === '1') //刷卡=1
                $('#creditcardInputGroup').show();
        }
*/

        /*
        // 簽名檔上傳，Button
        function signUpload(servName)
        {
            const canvas = document.getElementById("upSignImg"+servName);
            const dataURL = canvas.toDataURL('image/jpg')
            const blobBin = atob(dataURL.split(',')[1])
            const array = []
            for (let i = 0; i < blobBin.length; i++) {
                array.push(blobBin.charCodeAt(i))
            }
            const file = new Blob([new Uint8Array(array)], { type: 'image/jpg' })

            upload('upSignImg'+servName,'sign'+servName+'.jpg', file);
        }
        */

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
