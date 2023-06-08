@extends('ewo.layouts.default')

@section('title', '訂單明細')

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
        'SubsCP' => 'IVR檢碼',
        'SaleCampaign' => '方案別',
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

    </style>
    <main style="margin-top: 55px;">

{{--    {{  Log::channel('ewoLog')->info('p_data[dstbcheck]=='.print_r($p_data,1)) }}--}}

        <input type="hidden" name="p_userCode" id="p_userCode" value="{{$p_data['user_info']['userId']}}">
        <input type="hidden" name="p_userName" id="p_userName" value="{{$p_data['info']->WorkerName}}">
        <input type="hidden" name="p_userCode" id="p_userMobile" value="{{$p_data['user_info']['mobile']}}">
        <input type="hidden" name="p_custName" id="p_custName" value="{{$p_data['info']->CustName}}">
        <input type="hidden" name="p_id" id="p_id" value="{{$p_data['info']->Id}}">
        <input type="hidden" name="p_custId" id="p_custId" value="{{$p_data['info']->CustID}}">
        <input type="hidden" name="p_companyNo" id="p_companyNo" value="{{$p_data['info']->CompanyNo}}">
        <input type="hidden" name="p_workSheet" id="p_workSheet" value="{{$p_data['info']->WorkSheet}}">
        <input type="hidden" name="p_BookDate" id="p_BookDate" value="{{$p_data['info']->BookDate}}">
        <input type="hidden" name="p_ServiceName" id="p_ServiceName" value="{{$p_data['info']->ServiceName}}">
        <input type="hidden" name="p_pdf_v" id="p_pdf_v" value="{{$p_data['info']->pdf_v??config('order.PDF_CODE_V')}}">
        <input type="hidden" name="p_totalAmt" id="p_totalAmt" value="{{$p_data['totalAmt']}}">
        <input type="hidden" name="p_token" id="p_token" value="{{ csrf_token() }}">

        <div class="container pt-2 bg-grey">

            <div class="alert alert-info mb-1" role="alert">
                <label class="d-inline">{{$p_data['info']->WorkKind}}</label>
                <label class="d-inline text-danger">服務別</label>
                <label class="d-inline">{{$p_data['info']->ServiceName}}</label>
                <label class="d-inline float-right">應收金額${{$p_data['totalAmt']}}</label>
            </div>
            <div id="accordion">

                <div class="card mb-3">
                    <div class="card-header" >
                        <div class="input-group">
                            <label class="btn btn-info mb-0">
                                <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">
                                    <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                                </svg>
                                <input class="d-none" type="file" accept="image/*" id="file_checkIn" >
                                到站打卡
                                <input type="hidden" class="form-control" id="localLat">
                                <input type="hidden" class="form-control" id="localLng">
                            </label>

                            <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3 @if(is_null($p_data['info']->checkin) === true) d-none @endif" id="label_checkIn">
                                上傳時間：{{date('Y-m-d H:i:s',strtotime($p_data['info']->checkin))}}
                            </label>
                        </div>
                    </div>

                    <div class="collapse show">
                        <div class="card-body col-3">
                            <img class=" @if(is_null($p_data['info']->checkin) === true) d-none w-0 @endif" width="500"
                                 id="img_checkIn" src="/upload/{{$p_data['uploaddir']}}/checkIn.jpg?i={{date('His')}}" onerror="this.src='/img/error_02.png'">
                        </div>
                    </div>
                </div>


                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0" id="delateHead">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#delateBody">
                                我會遲到
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


                <div class="card mb-3">
                    <div class="card-header" >
                        <div class="input-group">
                            <label class="btn btn-info mb-0">
                                <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">--}}
                                    <path d="M6.5 7a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1h-4z"></path>
                                    <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"></path>
                                </svg>
                                <input class="d-none" type="button" id="button_chargeback" onclick="stbApi('{{config('order.CahrgeBackType')[$p_data['info']->WorkKind]}}_退單')">
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

                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0" id="userInfoHead">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#userInfoBody">
                                用戶信息
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
                                    <div class="input-group-prepend p-0 col-2">
                                        <span class="input-group-text w-100">{{$t}}</span>
                                    </div>
                                    <div class="input-group-append p-0 col-10">
                                        <span class="input-group-text bg-white w-100">
                                            @if(in_array($k,['Worker2']))
                                                @if(empty($p_data['info']->$k) === false || is_null($p_data['info']->$k) === false)
                                                    /{{explode(' ',$p_data['info']->$k)[1]}}
                                                @endif
                                            @elseif(in_array($k,['SubsCP']))
                                                {{preg_replace('/:/','=',preg_replace('/,/'," ； ",preg_replace('/"/','',substr($p_data['info']->$k,1,strlen($p_data['info']->$k)-2))))}}
                                            @else
                                                {{$p_data['info']->$k}}
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3 @if(empty($p_data['custDives']) === true) d-none @endif">
                    <div class="card-header pt-0 pb-0" id="equipmentListHead">
{{--                        display: -webkit-inline-box;--}}
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
                    <div id="equipmentListBody" class="collapse show" data-parent="#equipmentListHead">
                        <div class="card-body">
                            <div class="input-group ">
                                @foreach($p_data['custDives'] as $k => $t)
                                    @if(empty($t->SingleSN) === false)
                                        <div class="input-group-prepend p-0 col-2">
                                            <span class="input-group-text w-100"><input type="radio" name="chg_siginsn" data-sn="{{$t->SingleSN}}" data-sc="{{$t->SmartCard}}">設備序號</span>
                                        </div>
                                        <div class="input-group-append p-0 col-4">
                                            <span class="input-group-text bg-white w-100">
                                               {{$t->SingleSN}}
                                            </span>
                                        </div>
                                        <div class="input-group-append p-0 col-2">
                                            <span class="input-group-text w-100">SmartCard</span>
                                        </div>
                                        <div class="input-group-append p-0 col-4">
                                            <span class="input-group-text bg-white w-100">
                                               {{$t->SmartCard}}
                                            </span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0" id="orderListHead">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#orderListBody">
                                工作清單
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
                </div>

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
                </div>

{{--                <div class="card mb-3" id="div_id_03">--}}
{{--                    <div class="card-header" >--}}
{{--                        <div class="input-group">--}}
{{--                            <label class="btn btn-warning mb-0">--}}
{{--                                <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">--}}
{{--                                    <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>--}}
{{--                                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>--}}
{{--                                </svg>--}}
{{--                                <input class="d-none" type="file" accept="image/*" id="file_id_03" >--}}
{{--                                上傳第二證件_圖片--}}
{{--                            </label>--}}

{{--                            <label class="alert alert-warning p-0 pt-1 pl-2 pr-2 mb-0 ml-3 @if(is_null($p_data['info']->id03) === true) d-none @endif" id="label_id_03">--}}
{{--                                上傳時間：{{date('Y-m-d H:i:s',strtotime($p_data['info']->id03))}}--}}
{{--                            </label>--}}

{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="collapse show" data-parent="#uploIdHead">--}}
{{--                        <div class="card-body col-3">--}}
{{--                            <img class=" @if(is_null($p_data['info']->id03) === true) d-none w-0 @endif"--}}
{{--                                 id="img_id_03" src="/upload/{{$p_data['uploaddir']}}/identity_03.jpg?i={{date('His')}}" onerror="this.src='/img/error_02.png'"--}}
{{--                                 onload="createImgWatemark($(this).attr('id'))" >--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}

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

                <div class="cawebrd mb-3" id="div_certificate_01">
                    <div class="card-header" >
                        <div class="input-group">
                            <label class="btn btn-primary mb-0">
                                <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">
                                    <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                                </svg>
                                <input class="d-none" type="file" accept="image/*" id="file_certificate_01" >
                                上傳憑證01_圖片
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


                <div class="card mb-3" id="div_certificate_02">
                    <div class="card-header" >
                        <div class="input-group">
                            <label class="btn btn-info mb-0">
                                <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">
                                    <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                                </svg>
                                <input class="d-none" type="file" accept="image/*" id="file_certificate_02" >
                                上傳憑證02_圖片
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



                <div class="card mb-3 @if($p_data['info']->WorkKind !== "3 維修") d-none @endif ">
                    <div class="card-header pt-0 pb-0" id="srviceReasonHead">
                        <div class="input-group">
                            <h5 class="mb-0 mr-3">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#srviceReasonBody">
                                    維修原因
                                </button>
                            </h5>
                            <div class="input-group-append">
                                <label class="alert alert-info p-0 pt-1 pl-2 pr-2 mb-0 ml-3 @if($p_data['info']->serviceResonTime === '') d-none @endif " id="label_srviceReason">
                                    維修原因：{{date('Y-m-d H:i:s', strtotime($p_data['info']->serviceResonTime))}}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="srviceReasonBody" class="collapse show" data-parent="#srviceReasonHead">
                        <div class="card-body">
                            @if($p_data['srviceReasonFirst'] != '')
                            <select class="custom-select" id="select_srviceReasonFirst" onchange="stbApi('serviceResonLast')">
                                <option>請選擇維修原因</option>
                                @foreach($p_data['srviceReasonFirst'] as $k => $t)
                                    <option data-mscode="{{$t->mscode}}" data-servicecode="{{$t->servicecode}}" @if($p_data['info']->serviceResonFirst != '') selected @endif>{{$t->dataname}}</option>
                                @endforeach
                            </select>
                            <select class="custom-select" id="select_srviceReasonLast" onchange="chgSrviceReason()">
                                @if($p_data['info']->serviceResonLast != '')
                                    <option>{{$p_data['info']->serviceResonLast}}</option>
                                @else
                                    <option>請選擇</option>
                                @endif
                            </select>
                                @endif
                        </div>
                    </div>
                </div>

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
                                    <option data-chargeName="{{$t['chargeName']}}" data-amt="{{$t['BaseAmt']}}">{{$t['chargeName'].' ($'.$t['BaseAmt'].')'}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                @if(strpos($p_data['info']->ServiceName,'3 DSTB') !== '')
                    @foreach($p_data['info']->dstbOpenAry as $k => $t)
                        <div class="card mb-3" >
                            <div class="card-header">
                                <div class="input-group">
                                    <label class="btn btn-danger mb-0">
                                        <input class="d-none" type="button" id="openButton" onclick="openscan('{{$k}}')">
                                        <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">
                                            <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                                            <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                                        </svg>
                                        開通<u>{{$k}}訂編{{$t['SubsId']}}</u> 掃描
                                    </label>
                                </div>
                            </div>
                            <div class="collapse show" data-parent="#uploIdHead">
                                <div class="card-body" id="openBody">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-danger" type="button" data-worksheet="{{$k}}" data-subscp="{{$t['SubsCP2']}}" id="open{{$k}}_btn" onclick="stbApi('authorstb','{{$k}}')">開通</button>
                                        </div>
                                        <input type="text" class="form-control bg-white" id="open{{$k}}_scanstr" placeholder="請先掃描電視QRCode" onfocus="$(this).val('請先掃描電視QRCode')" readonly value=""/>
                                    </div>
                                    <div class="alert alert-danger mb-0" role="alert" id="open{{$k}}_alert">OpenAlert</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if(!empty($p_data['chargeInfo']))
                    @foreach($p_data['chargeInfo'] as $k => $t)
                        @if($k == "2 CM" || $k == 'D TWMBB')
                            <div class="card mb-3">
                                <div class="card-header pt-0 pb-0" id="cmqualityforkg_{{$t[0]->SubsID}}_Head">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#cmqualityforkg_{{$t[0]->SubsID}}_Body">
                                            網路品質查詢(SubsID:{{ $t[0]->SubsID }})
                                        </button>
                                        <label class="btn btn-info mb-0">
                                            <svg width="16" height="16" fill="currentColor" class="bi bi-bar-chart" viewBox="0 0 16 16">
                                                <path d="M4 11H2v3h2v-3zm5-4H7v7h2V7zm5-5v12h-2V2h2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1h-2zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zm-5 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3z"/>
                                            </svg>
                                            <input class="d-none" type="button" data-subsid="{{$t[0]->SubsID}}" name="cmqualityforkg_btn" id="cmqualityforkg_{{$t[0]->SubsID}}_btn" >
                                            網路品質查詢
                                        </label>
                                        <label class="alert alert-info pt-2 pl-2 mb-0" id="cmqualityforkg_{{$t[0]->SubsID}}_label">網路品質查詢信息</label>
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

                <div class="card mb-3">
                    <div class="card-header">

                        @if($p_data['info']->WorkKind === '3 拆機')
                        <div class="input-group">
                            <select class="custom-select" >
                                <option>故障原因一</option>
                            </select>
                            <select class="custom-select" >
                                <option>故障原因2</option>
                            </select>
                        </div>
                        @endif

                        <div class="input-group" id="receivemoneyDiv">
                            款項收取：
                            <input type="radio" class="btn-check" name="receivemoney" id="receivemoney-cash" value="2" checked />
                            <label class="btn btn-outline-success mr-3" for="receivemoney-cash">現金 ${{intval($p_data['totalAmt'])}}  </label>
                            <input type="radio" class="btn-check" name="receivemoney" id="receivemoney-card" value="1" />
                            <label class="btn btn-outline-danger" for="receivemoney-card">信用卡 ${{$p_data['totalAmt']}} </label>
                        </div>
                        <div class="input-group" id="finishBtnDiv">
                            <label class="btn btn-warning mb-0">
                                <input class="d-none" type="button" id="finshBtn" onclick="stbApi('{{str_replace(' ','_',$p_data['info']->WorkKind)}}')">
                                完工
                            </label>
                            <div class="alert alert-info mb-0 ml-3" role="alert" id="finshtimeAlert">完工API:OK；時間{{date('Y-m-d H:i:s',strtotime($p_data['info']->finsh)).'；'}}{{($p_data['info']->receiveType === '1')? '刷卡': '現金'}}{{'$'.$p_data['info']->receiveMoney}}</div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="alert alert-warning" role="alert" id="installFinshalert">完工API訊息</div>
                        <div class="input-group" id="creditcardInputGroup">
.                            <div class="d-inline-flex">
                                <input type="tel" id="creditcardCode" required="" maxlength="19" onkeydown="this.value=this.value.replace(/\D/g,'').replace(/....(?!$)/g,'$&amp;-')" class="form-control text-center" placeholder="信用卡號 xxxx-xxxx-xxxx-xxxx" title="信用卡號 xxxx-xxxx-xxxx-xxxx" />
                                <input type="tel" id="creditcardMMYY" required="" maxlength="5" class="form-control text-center" onkeydown="this.value=this.value.replace(/\D/g,'').replace(/..(?!$)/g,'$&amp;/')" placeholder="有效期限(月/年) mm/yy" title="有效期限(月/年) mm/yy" />
                                <label class="btn btn-danger w-100 mb-0">
                                    <input class="d-none" type="button" onclick="creditcardNumCheck()">
                                    {{--                                <input class="d-none" type="button" onclick="stbApi('receivemoneyCard')">--}}
                                    刷卡
                                </label>
                            </div>
                            <div class="col-12 alert alert-warning mb-0" role="alert" id="creditcardAlert">信用卡刷卡結果</div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0" id="borrowminglist_head">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#borrowminglist_body">
                                借用單
                            </button>
                        </h5>
                    </div>
                    <div id="borrowminglist_body" class="collapse show" data-parent="#borrowminglist_head">
                        <div class="card-body">
                            <form id="borrowminglist_form" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="{{$p_data['info']->Id}}">
                                <input type="hidden" name="type" value="BorrowmingList">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <ul class="list-group list-group-flush">

                                    <li class="list-group-item list-group-item-info">
                                        借用設備(借用數量)
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">纜線數據機</span>
                                            <span class="input-group-text">單埠</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="Cable_modem_port"
                                                   value="@if(isset($p_data['borrowmingList']->Cable_modem_port) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->Cable_modem_port)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                            <span class="input-group-text">wifi</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="Cable_modem_two_way"
                                                   value="@if(isset($p_data['borrowmingList']->Cable_modem_two_way) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->Cable_modem_two_way)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">數位機上盒</span>
                                            <span class="input-group-text">基本型</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="Basic_digital_set_top_box"
                                                   value="@if(isset($p_data['borrowmingList']->Basic_digital_set_top_box) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->Basic_digital_set_top_box)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                            <span class="input-group-text">雙向型</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="Digital_set_top_box_two_way_type"
                                                   value="@if(isset($p_data['borrowmingList']->Digital_set_top_box_two_way_type) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->Digital_set_top_box_two_way_type)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>

                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">智能家電</span>
                                            <span class="input-group-text">攝影機</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="camera"
                                                   value="@if(isset($p_data['borrowmingList']->camera) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->camera)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                            <span class="input-group-text">門窗感應</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="Door_and_window_sensor"
                                                   value="@if(isset($p_data['borrowmingList']->Door_and_window_sensor) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->Door_and_window_sensor)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">智能家電</span>
                                            <span class="input-group-text">煙霧偵測</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="Smoke_detector"
                                                   value="@if(isset($p_data['borrowmingList']->Smoke_detector) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->Smoke_detector)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>

                                    <li class="list-group-item list-group-item-info">
                                        纜線數據機配件(借用數量)
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">無線抗頻分享器</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="Cable_accessories_wireless_anti_frequency_sharing_device"
                                                   value="@if(isset($p_data['borrowmingList']->Cable_accessories_wireless_anti_frequency_sharing_device) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->Cable_accessories_wireless_anti_frequency_sharing_device)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                            <span class="input-group-text">變壓器電源線</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="Cable_accessories_transformer_power_cord"
                                                   value="@if(isset($p_data['borrowmingList']->Cable_accessories_transformer_power_cord) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->Cable_accessories_transformer_power_cord)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">乙太網路線</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="Cable_accessories_Ethernet_cable"
                                                   value="@if(isset($p_data['borrowmingList']->Cable_accessories_Ethernet_cable) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->Cable_accessories_Ethernet_cable)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                            <span class="input-group-text">USB無線抗頻網卡</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="Cable_accessories_USB_wireless_anti_frequency_network_card"
                                                   value="@if(isset($p_data['borrowmingList']->Cable_accessories_USB_wireless_anti_frequency_network_card) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->Cable_accessories_USB_wireless_anti_frequency_network_card)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>

                                    <li class="list-group-item list-group-item-info">
                                        數位機上盒配件(借用數量)
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">遙控器</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="Set_top_box_accessories_remote_control"
                                                   value="@if(isset($p_data['borrowmingList']->Set_top_box_accessories_remote_control) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->Set_top_box_accessories_remote_control)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                            <span class="input-group-text">HDMI</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="Set_top_box_accessories_HDI"
                                                   value="@if(isset($p_data['borrowmingList']->Set_top_box_accessories_HDI) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->Set_top_box_accessories_HDI)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">AV線(1.5M)</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="Set_top_box_accessories_AV_cable"
                                                   value="@if(isset($p_data['borrowmingList']->Set_top_box_accessories_AV_cable) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->Set_top_box_accessories_AV_cable)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                            <span class="input-group-text">色差線(1.5M)</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="Set_top_box_accessories_Chromatic_aberration_line"
                                                   value="@if(isset($p_data['borrowmingList']->Set_top_box_accessories_Chromatic_aberration_line) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->Set_top_box_accessories_Chromatic_aberration_line)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">變壓器電源線</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="Set_top_box_accessories_transformer_power_cord"
                                                   value="@if(isset($p_data['borrowmingList']->Set_top_box_accessories_transformer_power_cord) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->Set_top_box_accessories_transformer_power_cord)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                            <span class="input-group-text">智慧卡</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="Set_top_box_accessories_smart_card"
                                                   value="@if(isset($p_data['borrowmingList']->Set_top_box_accessories_smart_card) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->Set_top_box_accessories_smart_card)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">外接式硬碟</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="Set_top_box_accessories_external_hard_disk"
                                                   value="@if(isset($p_data['borrowmingList']->Set_top_box_accessories_external_hard_disk) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->Set_top_box_accessories_external_hard_disk)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                            <span class="input-group-text">USB無線抗頻網卡</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="Set_top_box_accessories_USB_wireless_anti_frequency_network_card"
                                                   value="@if(isset($p_data['borrowmingList']->Set_top_box_accessories_USB_wireless_anti_frequency_network_card) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->Set_top_box_accessories_USB_wireless_anti_frequency_network_card)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">ATV機上盒</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="Set_top_box_accessories_ATV_set_top_box"
                                                   value="@if(isset($p_data['borrowmingList']->Set_top_box_accessories_ATV_set_top_box) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->Set_top_box_accessories_ATV_set_top_box)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                            <span class="input-group-text">藍芽遙控器</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="Set_top_box_accessories_Bluetooth_remote_control"
                                                   value="@if(isset($p_data['borrowmingList']->Set_top_box_accessories_Bluetooth_remote_control) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->Set_top_box_accessories_Bluetooth_remote_control)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>

                                    <li class="list-group-item list-group-item-info">
                                        智能家庭配件(借用數量)
                                    </li>
                                    <li class="list-group-item pt-0 pb-0">
                                        <div class="input-group">
                                            <span class="input-group-text">變壓器電源線</span>
                                            <input type="number" class="form-control text-center" min="0"  max="9" maxlength="1" name="Smart_home_accessories_transformer_power_cord"
                                                   value="@if(isset($p_data['borrowmingList']->Smart_home_accessories_transformer_power_cord) === false){{intval(0)}}@else{{intval($p_data['borrowmingList']->Smart_home_accessories_transformer_power_cord)}}@endif"
                                                   ondblclick="$(this).val(0)" onclick="$(this).val(parseInt($(this).val()) + parseInt('1'))" />
                                        </div>
                                    </li>
                                </ul>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card mb-3" id="retrievelist_card">
                    <div class="card-header pt-0 pb-0" id="retrievelist_head">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#retrievelist_body">
                                取回單
                            </button>
                        </h5>
                    </div>
                    <div id="retrievelist_body" class="collapse show" data-parent="#retrievelist_head">
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

                <div class="card mb-3 d-none" id="checkDataDiv_dstb">
                    <div class="card-header pt-0 pb-0" id="dstb_check_data_head">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#dstb_check_data_body">
                                DSTB 資料確認
                            </button>
                        </h5>
                    </div>
                    <div id="dstb_check_data_body" class="collapse show" data-parent="#dstb_check_data_head">
                        <div class="card-body">
                            <form id="dstb_check_form" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="{{$p_data['info']->Id}}">
                                <input type="hidden" name="type" value="dstbcheck">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <ul class="list-group list-group-flush">

                                    <li class="list-group-item list-group-item-success">
                                        設備／贈品／證件繳交確認
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="dstb_check_id" name="dstb_check_id"
                                               @if(!empty($p_data['dstbcheck']) && $p_data['dstbcheck']->dstb_check_id === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">身分證正反面影本</label>
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="dstb_check_health" name="dstb_check_health"
                                               @if(!empty($p_data['dstbcheck']) &&  $p_data['dstbcheck']->dstb_check_health === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">健保卡</label>
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="dstb_check_driver" name="dstb_check_driver"
                                               @if(!empty($p_data['dstbcheck']) &&  $p_data['dstbcheck']->dstb_check_driver === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">駕照影本</label>
                                        <input type="email" class="form-control" id="dstb_check_driver_desc" name="dstb_check_driver_desc" placeholder="(其他)"
                                               value="@if(!empty($p_data['dstbcheck']) && $p_data['dstbcheck']->dstb_check_driver_desc){{$p_data['dstbcheck']->dstb_check_driver_desc}}@endif" />
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="dstb_check_company" name="dstb_check_company"
                                               @if(!empty($p_data['dstbcheck']) &&  $p_data['dstbcheck']->dstb_check_company === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">公司變更證記事項表</label>
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="dstb_check_other" name="dstb_check_other"
                                               @if(!empty($p_data['dstbcheck']) &&  $p_data['dstbcheck']->dstb_check_other === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">其他</label>
                                        <input type="email" class="form-control" id="dstb_check_other_desc" name="dstb_check_other_desc" placeholder="(其他)"
                                               value="@if(!empty($p_data['dstbcheck']) && $p_data['dstbcheck']->dstb_check_other_desc){{$p_data['dstbcheck']->dstb_check_other_desc}}@endif" />
                                    </li>

                                    <li class="list-group-item list-group-item-info">
                                        個人資料確認
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="dstb_check_personal" name="dstb_check_personal"
                                               @if(!empty($p_data['dstbcheck']) &&  $p_data['dstbcheck']->dstb_check_personal === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">同意本公司進行機上盒頻道節目收視之資訊蒐集分析及個人化內容之推薦等</label>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="input-group">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text" for="inputGroupSelect01">關係或稱謂：</label>
                                                </div>
                                                <select class="custom-select" id="dstb_check_title" name="dstb_check_title">
                                                    <option value="本人">本人</option>
                                                    <option value="代表人">代表人</option>
                                                    <option value="家人">家人</option>
                                                    <option value="朋友">朋友</option>
                                                    <option value="同事">同事</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">法定代理人/代表人/代簽：</span>
                                            </div>
                                            <input type="text" class="form-control" id="dstb_check_legal" name="dstb_check_legal" placeholder="輸入 法定代理人/代表人/代簽"
                                                   value="@if(!empty($p_data['dstbcheck']) && $p_data['dstbcheck']->dstb_check_legal){{$p_data['dstbcheck']->dstb_check_legal}}@endif" />
                                        </div>
                                    </li>
                                </ul>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card mb-3 d-none" id="checkDataDiv_cm" >
                    <div class="card-header pt-0 pb-0" id="cm_check_data_head">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#cm_check_data_body">
                                CM 資料確認
                            </button>
                        </h5>
                    </div>
                    <div id="cm_check_data_body" class="collapse show" data-parent="#cm_check_data_head">
                        <div class="card-body">
                            <form id="cm_check_form" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="{{$p_data['info']->Id}}">
                                <input type="hidden" name="type" value="cmcheck">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <ul class="list-group list-group-flush">

                                    <li class="list-group-item list-group-item-success">
                                        設備／贈品／證件繳交確認
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="cm_check_id" name="cm_check_id"
                                               @if(!empty($p_data['cmcheck']) && !empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_id === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">身分證正反面影本</label>
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="cm_check_health" name="cm_check_health"
                                               @if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_health === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">健保卡</label>
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="cm_check_driver" name="cm_check_driver"
                                               @if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_driver === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">駕照影本</label>
                                        <input type="text" class="form-control" id="cm_check_driver_desc" name="cmcheck" placeholder="(其他)"
                                               value="@if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_driver_desc){{$p_data['cmcheck']->cm_check_driver_desc}}@endif" />
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="cm_check_company" name="cm_check_company"
                                               @if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_company === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">公司變更證記事項表</label>
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="cm_check_other" name="cm_check_other"
                                               @if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_other === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">其他</label>
                                        <input type="text" class="form-control" id="cm_check_other_desc" name="cm_check_other_desc" placeholder="(其他)"
                                               value="@if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_other_desc){{$p_data['cmcheck']->cm_check_other_desc}}@endif" />
                                    </li>

                                    <li class="list-group-item list-group-item-info">
                                        個人資料確認
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="cm_check_domicile" name="cm_check_domicile"
                                               @if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_domicile === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">戶籍地址：同裝機地址</label>
                                    </li>
                                    <li class="list-group-item">
                                        <div class="input-group">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text" for="inputGroupSelect01">關係或稱謂：</label>
                                                </div>
                                                <select class="custom-select" id="cm_check_title" name="cm_check_title">
                                                    <option value="本人">本人</option>
                                                    <option value="代表人">代表人</option>
                                                    <option value="家人">家人</option>
                                                    <option value="朋友">朋友</option>
                                                    <option value="同事">同事</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">法定代理人/代表人/代簽：</span>
                                            </div>
                                            <input type="text" class="form-control hide" id="cm_check_legal" name="cm_check_legal" placeholder="輸入 法定代理人/代表人/代簽"
                                                   value="@if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_legal){{$p_data['cmcheck']->cm_check_legal}}@endif" />
                                        </div>
                                    </li>
                                    <li class="list-group-item list-group-item-warning">
                                        設備確認
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="cm_check_equipment" name="cm_check_equipment"
                                               @if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_equipment === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">CM一台、乙太網路線一條、USB連接線一條、電源線一條、說明書及驅動程式光碟</label>
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="cm_check_notest" name="cm_check_notest"
                                               @if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_notest === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">未備電腦、未為供裝速率實測</label>
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="cm_check_standalone" name="cm_check_standalone"
                                               @if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_standalone === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">單機實測為</label>
                                        <input type="text" class="form-control" id="cm_check_standalone_desc" name="cm_check_standalone_desc" placeholder="請輸入..."
                                               value="@if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_standalone_desc){{$p_data['cmcheck']->cm_check_standalone_desc}}@endif" />
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="cm_check_notest_standalone" name="cm_check_notest_standalone"
                                               @if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_notest_standalone === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">無法單機測試</label>
                                        <input type="text" class="form-control" id="cm_check_notest_standalone_desc" name="cm_check_notest_standalone_desc" placeholder="請輸入..."
                                               value="@if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_notest_standalone_desc){{$p_data['cmcheck']->cm_check_notest_standalone_desc}}@endif" />
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="cm_check_equipmentdiscord_test" name="cm_check_equipmentdiscord_test"
                                               @if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_equipmentdiscord_test === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">電腦設備不合標準，單機實測為</label>
                                        <input type="text" class="form-control" id="cm_check_equipmentdiscord_test_desc" name="cm_check_equipmentdiscord_test_desc" placeholder="請輸入..."
                                               value="@if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_equipmentdiscord_test_desc){{$p_data['cmcheck']->cm_check_equipmentdiscord_test_desc}}@endif" />
                                    </li>
                                </ul>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card mb-3 d-none" id="checkDataDiv_twmbb">
                    <div class="card-header pt-0 pb-0" id="twmbb_check_data_head">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#twmbb_check_data_body">
                                TWMBB 資料確認
                            </button>
                        </h5>
                    </div>
                    <div id="twmbb_check_data_body" class="collapse show" data-parent="#twmbb_check_data_head">
                        <div class="card-body">
                            <form id="twmbb_check_form" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="{{$p_data['info']->Id}}">
                                <input type="hidden" name="type" value="twmbbcheck">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <ul class="list-group list-group-flush">

                                    <li class="list-group-item list-group-item-info">
                                        個人資料確認
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="twmbb_check_domicile" name="twmbb_check_domicile"
                                               @if(!empty($p_data['twmbbcheck']) && $p_data['twmbbcheck']->twmbb_check_domicile === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">戶籍地址：同裝機地址</label>
                                    </li>
                                    <li class="list-group-item list-group-item-warning">
                                        設備確認
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="twmbb_check_equipment" name="twmbb_check_equipment"
                                               @if(!empty($p_data['twmbbcheck']) && $p_data['twmbbcheck']->twmbb_check_equipment === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">CM一台、乙太網路線一條、USB連接線一條、電源線一條、說明書及驅動程式光碟</label>
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="twmbb_check_notest" name="twmbb_check_notest"
                                               @if(!empty($p_data['twmbbcheck']) && $p_data['twmbbcheck']->twmbb_check_notest === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">未備電腦、未為供裝速率實測</label>
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="twmbb_check_standalone" name="twmbb_check_standalone"
                                               @if(!empty($p_data['twmbbcheck']) && $p_data['twmbbcheck']->twmbb_check_standalone === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">單機實測為</label>
                                        <input type="text" class="form-control" id="twmbb_check_standalone_desc" name="twmbb_check_standalone_desc" placeholder="請輸入..."
                                               value="@if(!empty($p_data['twmbbcheck']) && $p_data['twmbbcheck']->twmbb_check_standalone_desc){{$p_data['twmbbcheck']->twmbb_check_standalone_desc}}@endif" />
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="twmbb_check_notest_standalone" name="twmbb_check_notest_standalone"
                                               @if(!empty($p_data['twmbbcheck']) && $p_data['twmbbcheck']->twmbb_check_notest_standalone === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">無法單機測試</label>
                                        <input type="text" class="form-control" id="twmbb_check_notest_standalone_desc" name="twmbb_check_notest_standalone_desc" placeholder="請輸入..."
                                               value="@if(!empty($p_data['twmbbcheck']) && $p_data['twmbbcheck']->twmbb_check_notest_standalone_desc){{$p_data['twmbbcheck']->twmbb_check_notest_standalone_desc}}@endif" />
                                    </li>
                                    <li class="list-group-item">
                                        <input type="checkbox" class="form-check-input" id="twmbb_check_equipmentdiscord_test" name="twmbb_check_equipmentdiscord_test"
                                               @if(!empty($p_data['twmbbcheck']) && $p_data['twmbbcheck']->twmbb_check_equipmentdiscord_test === 'on') checked @endif />
                                        <label class="form-check-label" for="exampleCheck1">電腦設備不合標準，單機實測為</label>
                                        <input type="text" class="form-control" id="twmbb_check_equipmentdiscord_test_desc" name="twmbb_check_equipmentdiscord_test_desc" placeholder="請輸入..."
                                               value="@if(!empty($p_data['twmbbcheck']) && $p_data['twmbbcheck']->twmbb_check_equipmentdiscord_test_desc){{$p_data['twmbbcheck']->twmbb_check_equipmentdiscord_test_desc}}@endif" />
                                    </li>
                                    <li class="list-group-item">
                                        <div class="input-group">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <label class="input-group-text" for="inputGroupSelect01">關係或稱謂：</label>
                                                </div>
                                                <select class="custom-select" id="twmbb_check_title" name="twmbb_check_title">
                                                    <option value="本人">本人</option>
                                                    <option value="代表人">代表人</option>
                                                    <option value="家人">家人</option>
                                                    <option value="朋友">朋友</option>
                                                    <option value="同事">同事</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">法定代理人/代表人/代簽：</span>
                                            </div>
                                            <input type="text" class="form-control hide" id="twmbb_check_legal" name="twmbb_check_legal" placeholder="輸入 法定代理人/代表人/代簽"
                                                   value="@if(!empty($p_data['twmbbcheck']) && $p_data['twmbbcheck']->twmbb_check_legal){{$p_data['twmbbcheck']->twmbb_check_legal}}@endif" />
                                        </div>
                                    </li>
                                </ul>
                            </form>
                        </div>
                    </div>
                </div>

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

                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0" id="uploSignHead">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#uploSignBody">
                                簽名欄位
                            </button>
                        </h5>
                    </div>
                    <div id="uploSignBody" class="collapse show" data-parent="#uploSignHead">
                        <div class="card-body">
                            <div class="input-group-prepend">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">eMail</span>
                                    </div>
                                    <input type="email" class="form-control" id="sentmail" placeholder="請輸入eMail" value="{{$p_data['info']->sentmail}}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">@if($p_data['info']->sentmail != '')OK @else ?@endif</span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-none" id="signDiv_dstb">
                                <div class="input-group-prepend p-0" id="signButton_dstb">
                                    <button class="btn btn-success mr-3" id="signRestBtn_dstb" onclick="resetSignButton('open','_dstb')">重新簽名</button>
                                    <button class="btn btn-info mr-3" id="signUpBtn_dstb" onclick="signUpload('_dstb');resetSignButton('close','_dstb')">上傳</button>
                                    <button class="btn btn-secondary" id="signCloseBtn_dstb" onclick="resetSignButton('close','_dstb')">取消</button>
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

                            <div class="d-none" id="signDiv_cm">
                                <div class="input-group-prepend p-0" id="signButton_cm">
                                    <button class="btn btn-success mr-3" id="signRestBtn_cm" onclick="resetSignButton('open','_cm')">重新簽名</button>
                                    <button class="btn btn-info mr-3" id="signUpBtn_cm" onclick="signUpload('_cm');resetSignButton('close','_cm')">上傳</button>
                                    <button class="btn btn-secondary" id="signCloseBtn_cm" onclick="resetSignButton('close','_cm')">取消</button>
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

                            <div class="d-none" id="signDiv_twmbb">
                                <div class="input-group-prepend p-0" id="signButton_twmbb">
                                    <button class="btn btn-success mr-3" id="signRestBtn_twmbb" onclick="resetSignButton('open','_twmbb')">重新簽名</button>
                                    <button class="btn btn-info mr-3" id="signUpBtn_twmbb" onclick="signUpload('_twmbb');resetSignButton('close','_twmbb')">上傳</button>
                                    <button class="btn btn-secondary" id="signCloseBtn_twmbb" onclick="resetSignButton('close','_twmbb')">取消</button>
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

{{--
                            <div class="input-group-append p-0" id="signButton_dstb">
                                DSTB<button class="btn btn-success mr-3" id="signRestBtn_dstb" onclick="resetSignButton('dstb','open')">重新簽名</button>
                                <button class="btn btn-info" id="signUpBtn_dstb" onclick="signUpload('dstb');resetSignButton('dstb','close')">上傳</button>
                            </div>
                            <img src="/upload/{{$p_data['uploaddir']}}/sign_dstb.jpg?i={{date('His')}}" width="200" id="signShow_dstb">
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
--}}
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0" id="pdfHead">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#pdfBody">
                                PDF
                            </button>
                        </h5>
                    </div>
                    <div id="pdfBody" class="collapse show" data-parent="#pdfHead">
{{--                        <div class="card-body">--}}
{{--                            <div>--}}
{{--                                <button id="prev">前1頁</button>--}}
{{--                                <span>Page: <span id="page_num"></span> / <span id="page_count"></span></span>--}}
{{--                                <select id="pdfZoom" onchange="zoomPDF($(this).val())">--}}
{{--                                    <option value="1">放大x 1</option>--}}
{{--                                    <option value="2">放大x 2</option>--}}
{{--                                    <option value="3">放大x 3</option>--}}
{{--                                    <option value="5">放大x 5</option>--}}
{{--                                    <option value="8">放大x 8</option>--}}
{{--                                </select>--}}
{{--                                <button id="next">下1頁</button>--}}
{{--                            </div>--}}
{{--                            <div style="width: 100%;overflow: auto;">--}}
{{--                                <canvas id="the-canvas"></canvas>--}}
{{--                            </div>--}}

{{--                        </div>--}}
{{--                    </div>--}}

                    <div id="pdf_show" style="width: 100%; height: 500px;"></div>

                </div>

{{--
                <div class="card mb-3">
                    <div class="card-header pt-0 pb-0" id="singProdDepleHead">
                        <h5 class="mb-0">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#singProdDepleBody">
                                單品耗料
                            </button>
                        </h5>
                    </div>
                    <div id="singProdDepleBody" class="collapse show" data-parent="#singProdDepleHead">
                        <div class="card-body">
                            <div class="input-group col-12">
                                <div class="input-group-prepend">
                                    <button class="btn btn-warning" onclick="singProdScan()">
                                        <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">
                                            <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
                                            <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                                        </svg>
                                    </button>
                                </div>
                                <input class="input-group-text bg-white" placeholder="序號" name="singProdDeple" id="singProdDeple">
                                <div class="input-group-append">
                                    <button class="btn btn-success">確認</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
--}}

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
        </div>

    </main>
@endsection

@section('script')

    <script>

        $(document).ready(function () {

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
                    p_companyNo : $('#p_companyNo').val(),
                    p_workSheet : $('#p_workSheet').val(),
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

            // 創建，簽名板
            if(true) {

                var servName = '{{$p_data['info']->ServiceName}}';
                if(servName.search('DSTB') > 0 || servName === '1 CATV') {
                    $('#signDiv_dstb').removeClass('d-none');
                    createSign('_dstb');
                    var chk_sign = '{{ $p_data['info']->sign_dstb }}';
                    if(chk_sign === "")
                        resetSignButton('open','_dstb');
                    else
                        resetSignButton('close','_dstb');
                    // 簽名，Label
                    var chk_title = "@if(!empty($p_data['dstbcheck']) && $p_data['dstbcheck']->dstb_check_title){{$p_data['dstbcheck']->dstb_check_title}}@else{{$p_data['info']->CustName}}@endif";
                    var legalStr = "@if(!empty($p_data['dstbcheck']) && $p_data['dstbcheck']->dstb_check_legal){{$p_data['dstbcheck']->dstb_check_legal}}@else本人@endif";
                    var labelStr = 'DSTB 簽名：';
                    if(chk_title === '本人') {
                        labelStr += $('#p_custName').val()+'(本人)';
                    } else {
                        labelStr += legalStr+" 關係："+chk_title;
                    }
                    $('#signAlert_dstb').text(labelStr);
                    $("#dstb_check_title option[value='"+chk_title+"']").attr('selected',true);

                }
                if(servName.search('CM') > 0) {
                    $('#signDiv_cm').removeClass('d-none');
                    createSign('_cm');
                    var chk_sign = '{{ $p_data['info']->sign_cm }}';
                    if(chk_sign === "") {
                        resetSignButton('open','_cm');
                    } else {
                        resetSignButton('close','_cm');
                    }

                    // 簽名，Label
                    var chk_title = "@if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_title){{$p_data['cmcheck']->cm_check_title}}@else{{$p_data['info']->CustName}}@endif";
                    var legalStr = "@if(!empty($p_data['cmcheck']) && $p_data['cmcheck']->cm_check_legal){{$p_data['cmcheck']->cm_check_legal}}@else本人@endif";
                    var labelStr = 'CM 簽名：';
                    if(chk_title === '本人') {
                        labelStr += $('#p_custName').val()+'(本人)';
                    } else {
                        labelStr += legalStr+" 關係："+chk_title;
                    }
                    $('#signAlert_cm').text(labelStr);
                    $("#dstb_check_title option[value='"+chk_title+"']").attr('selected',true);
                }
                if(servName.search('TWMBB') > 0) {
                    $('#signDiv_twmbb').removeClass('d-none');
                    createSign('_twmbb');
                    var chk_sign = '{{ $p_data['info']->sign_twmbb }}';
                    if(chk_sign === "") {
                        resetSignButton('open','_twmbb');
                    } else {
                        resetSignButton('close','_twmbb');
                    }

                    // 簽名，Label
                    var chk_title = "@if(!empty($p_data['twmbbcheck']) && $p_data['twmbbcheck']->twmbb_check_title){{$p_data['twmbbcheck']->twmbb_check_title}}@else{{$p_data['info']->CustName}}@endif";
                    var legalStr = "@if(!empty($p_data['twmbbcheck']) && $p_data['twmbbcheck']->twmbb_check_legal){{$p_data['twmbbcheck']->twmbb_check_legal}}@else本人@endif";
                    var labelStr = 'TWMBB 簽名：';
                    if(chk_title === '本人') {
                        labelStr += $('#p_custName').val()+'(本人)';
                    } else {
                        labelStr += legalStr+" 關係："+chk_title;
                    }
                    $('#signAlert_twmbb').text(labelStr);
                    $("#dstb_check_title option[value='"+chk_title+"']").attr('selected',true);
                }

                {{--var chk_sign = '{{$p_data['info']->sign}}';--}}
                // createSign();
                // if(chk_sign === "")
                //     resetSignButton('open');
                // else
                //     resetSignButton('close')
            }

            // 收款>>刷卡>>切換刷卡輸入欄位
            $('input[name=receivemoney]').change(function(){
                var chkVal = $('input[name=receivemoney]:checked').val()
                // if(chkVal === '2') { // 1=刷卡，2=現金
                //     $('#finishBtnDiv').show();
                //     $('#installFinshalert').show();
                //     $('#creditcardInputGroup').hide();
                // } else {
                //     $('#finishBtnDiv').hide();
                //     $('#installFinshalert').hide();
                //     $('#creditcardInputGroup').show();
                // }
            });


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
                var idAry = ['file_id_01','file_id_02','file_id03Photo','file_certificate_01','file_certificate_02','file_constructionPhoto','file_checkIn']
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
                    // 不做壓縮
                    // file = document.getElementById(p_id).files[0];
                    // upload(p_id,fNameAry[p_id_index],file)
                }


                if(p_id === 'file_checkIn')
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

            // 完工，檢查[完工]時間紀錄[關閉完工，刷卡功能]
            if(true) {
                var finshtimeCheck = '{{$p_data['info']->finsh}}';
                if(finshtimeCheck === '') {
                    $('#finshtimeAlert').hide();
                    $('#creditcardInputGroup').hide();
                } else {
                    $('#receivemoneyDiv').hide();
                    $('#creditcardInputGroup').hide();
                    $('#creditcardInputGroup').hide();
                    $('#finshBtn').remove();
                    $('#installFinshalert').hide();
                }
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

            // 紙本工單
            $('#PaperPDF').click(function(){
                var objChk = $(this).prop('checked');
                var p_value = (objChk)? '{{date('Y-m-d H:i:s')}}' : '';
                var params = {
                    p_columnName : "PaperPDF",
                    p_value : p_value,
                    p_id : $('#p_id').val()
                }
                apiEvent('PaperPDF',params);
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

        }); // endready


//         var compressRatio = 0.8, // 圖片壓縮比例
//             imgNewWidth = 300, // 圖片新寬度
//             img = new Image(),
//             urlBase64,
//             canvas = document.createElement("canvas"),
//             context = canvas.getContext("2d"),
//             file, fileReader, dataUrl;
//
//         // 壓縮圖片
//         function compressImg()
//         {
//
//             file = document.getElementById('file_id_01').files[0];
// // 圖片才處理
//             if (file && file.type.indexOf("image") == 0) {
//                 fileReader = new FileReader();
//                 fileReader.onload = getFileInfo;
//                 fileReader.readAsDataURL(file);
//                 console.log('chk file is img;')
//             } else
//             return;
//
//         }
//
//         function getFileInfo(evt)
//         {
//             dataUrl = evt.target.result;
//
//             // 取得圖片
//             img.src = dataUrl;
//         }
//
//         // 圖片載入後
//         img.onload = function() {
//             var width = this.width, // 圖片原始寬度
//                 height = this.height, // 圖片原始高度
//                 imgNewHeight = imgNewWidth * height / width, // 圖片新高度
//                 html = "",
//                 newImg;
//             console.log('chkk img onload;')
//
// // 顯示預覽圖片
//             console.log('chk dataUrl=='+dataUrl)
//             html += "<img src='" + dataUrl + "'/>";
//             html += "<p>這裡是原始圖片尺寸 " + width + "x" + height + "</p>";
//             html += "<p>檔案大小約 " + Math.round(file.size / 1000) + "k</p>";
//             $("#oldDiv").html(html);
//
// // 使用 canvas 調整圖片寬高
//             canvas.width = imgNewWidth;
//             canvas.height = imgNewHeight;
//             context.clearRect(0, 0, imgNewWidth, imgNewHeight);
//
// // 調整圖片尺寸
//             context.drawImage(img, 0, 0, imgNewWidth, imgNewHeight);
//             console.log(imgNewHeight+'__'+imgNewWidth+'__'+compressRatio)
//
// // 顯示新圖片
//             newImg = canvas.toDataURL("image/jpeg", compressRatio);
//
//             console.log('chk newImg=='+newImg)
//             html = "";
//             html += "<img src='" + newImg + "'/>";
//             html += "<p>這裡是新圖片尺寸 " + imgNewWidth + "x" + imgNewHeight + "</p>";
//             html += "<p>檔案大小約 " + Math.round(0.75 * newImg.length / 1000) + "k</p>"; // 出處 https://stackoverflow.com/questions/18557497/how-to-get-html5-canvas-todataurl-file-size-in-javascript
//             $("#newDiv").html(html);
//
// // canvas 轉換為 blob 格式、上傳
//             canvas.toBlob(function (blob) {
// // 輸入上傳程式碼
//                 upload('file_id_01', 'identity_01.jpg', blob)
//             }, "image/jpeg", compressRatio);
//         };

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

        // 創建，簽名板
        function createSign(servName)
        {
            var canvas = document.getElementById('upSignImg'+servName);
            var signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255, 255, 255)' });

            function resizeCanvas() {
                var ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext('2d').scale(ratio, ratio);
                signaturePad.clear();
            }
            window.onresize = resizeCanvas;
            resizeCanvas();
            $('#signClear'+servName).click(function(){
                signaturePad.clear();
            });
            //console.log(' createSign >>')
            resetSignButton('open',servName)
        }

        // 重新簽名，Button
        function resetSignButton(p_type,servName)
        {
            //console.log('func resetSignButton p_type=='+p_type+'  servName==='+servName)
            if(p_type === 'open') {
                //console.log('chk open ok')
                // 重新簽名
                $('#signRestBtn'+servName).addClass('d-none')
                $('#signCloseBtn'+servName).removeClass('d-none')
                $('#signUpBtn'+servName).removeClass('d-none')
                $('#signShow'+servName).addClass('d-none')
                $('#signaturePad'+servName).removeClass('d-none')
            } else if(p_type === 'close') {
                //console.log('chk close ok')
                // 上傳
                $('#signRestBtn'+servName).removeClass('d-none')
                $('#signCloseBtn'+servName).addClass('d-none')
                $('#signUpBtn'+servName).addClass('d-none')
                $('#signShow'+servName).removeClass('d-none')
                $('#signaturePad'+servName).addClass('d-none')
            }
        }


        // 維修原因[第二層，API回傳]
        function setServiceResonLast(json)
        {
            $('#select_srviceReasonLast').find('option').remove();
            var p_html = '<option>請選擇</option>';
            $('#select_srviceReasonLast').append(p_html);
            json.forEach(function(t){
                var p_html = '<option data-mscode="'+t.mscode+'" data-servicecode="'+t.servicecode+'">'+t.dataname+'</option>';
                $('#select_srviceReasonLast').append(p_html);
            })
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

        // create PDF
        function createPDF() {
            $('#pdfBody').find('div object').remove();
            var url = '/api/createpdf/app/'+$('#p_pdf_v').val()+'/'+$('#p_id').val();
            var formData = new FormData();
            //console.log('chk url=='+url)
            $.ajax({
                url: url,
                type: 'get',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (json) {
                    pdf_reload();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr);
                }
            });
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

        function stbApi(apitype,params='')
        {
            var url = '{{config('order.STB_API')}}/api/';
            var jsonData = {
                    'companyNo': $('#p_companyNo').val(),
                    'workSheet': $('#p_workSheet').val(),
                    'custId': $('#p_custId').val(),
                    'p_userCode': $('#p_userCode').val(),
                    'p_userName': $('#p_userName').val(),
                    'worker':$('#p_userCode').val()
                };
            console.log('stbAPI jsonData==');
            console.log(jsonData);
            switch(apitype)
            {
                case "authorstb": //開通
                    $('#openalert').text('等待結果');
                    var strVal = $('#open'+params+'_scanstr').val();
                    var chkStr = strVal.search('Facisno')
                    if(chkStr < 0) {
                        $('#openalert').text('資料有錯，請重新掃描');
                        alert('掃描資料有錯[not find Facisno]!')
                        return false;
                    }

                    var strAry = strVal.split(',')
                    var deviceAry = strAry[1].split(':');
                    var deviceName = deviceAry[0];
                    var deviceNo = deviceAry[1];

                    if(deviceName !== ' Facisno')
                    {
                        $('#openalert').text('資料有錯，請重新掃描');
                        alert('開通資料有錯[not find Facisno]!')
                        return false;
                    }
                    jsonData['deviceNo'] = deviceNo;
                    jsonData['mobile'] = $('#p_userMobile').val();
                    jsonData['ivrNo'] = $('#open'+params+'_btn').data('subscp');
                    jsonData['workSheet'] = params;
                    url += 'authorstb';
                    //console.log('jsonDatajsonData==');
                    //console.log(jsonData);
                    break;

                case "1_裝機":
                case "2_復機":
                case "6_移機":
                case "8_工程收費":
                case "9_停後復機":
                case "A_加裝":
                case "C_換機":
                    if($('input[name="receivemoney"]:checked').length === 0){
                        alert('請選擇收款方式!')
                        return false;
                    }
                    var radioText = $('input[name="receivemoney"]:checked').next('label:first').text();
                    var chk = true;
                    chk = confirm('確認：'+radioText)
                    if(!chk){
                        alert('請確任收款方式!')
                        return;
                    }

                    url += 'installedfinished';
                    var dataMatch = $('input[name="receivemoney"]:checked').val();
                    jsonData['dataMatch'] = dataMatch;
                    jsonData['p_receiveType'] = $('input[name="receivemoney"]:checked').val();
                    jsonData['p_receiveMoney'] = $('#p_totalAmt').val();
                    $('#installFinshalert').text('等待結果');
                    break;

                case "3_拆機":
                case "4_停機":
                case "7_移拆":
                case "H_退拆設備":
                case "I_退拆分機":
                case "K_退次週期項":
                    jsonData['devices'] = 'ms0200.SingleSN';
                    url += 'removefinished';
                    break;

                case "5_維修":
                    jsonData['mfCode1'] = '601 頭端設備-機房機電設施';
                    jsonData['mfCode2'] = '101 機電設備更換作業影響';
                    url += 'maintainfinished';
                    break;

                case "4_退單":
                case "8_退單":
                case "11_退單":
                    var p_desc = $('#chargeBackDesc :selected').val();
                    var p_type = (apitype.split('_'))[0];
                    jsonData['type'] = p_type;
                    jsonData['returnCode'] = p_desc;
                    url += 'chargeback';
                    break;
                case "chgEquipment": //修改，維修設備
                    url += 'changedevice';
                    var val_siginsn = $("input[name='chg_siginsn']:checked").data('sn');
                    var val_smartcard = $("input[name='chg_siginsn']:checked").data('sc');
                    jsonData['custid'] = "{{$p_data['info']->CustID}}";
                    jsonData['singleSN'] = val_siginsn;
                    jsonData['smartCard'] = val_smartcard;
                    break;
                case "serviceResonLast": // 維修原因[api回傳第二層]
                    $('#select_srviceReasonLast').find('option').remove();
                    if($('#select_srviceReasonFirst :selected').text() === '請選擇維修原因')
                    {
                        return false;
                    }
                    url += 'servicesecondreason';
                    var p_servicecode = $('#select_srviceReasonFirst :selected').data('servicecode');
                    var p_mscode = $('#select_srviceReasonFirst :selected').data('mscode');
                    jsonData['services'] = p_servicecode;
                    jsonData['firstCode'] = p_mscode;
                    break;
                case 'creditcard': //完工>>刷信用卡
                    url += 'creditcard';
                    jsonData['creditNumber'] = $('#creditcardCode').val();
                    jsonData['validDate'] = $('#creditcardMMYY').val();
                    jsonData['amount'] = $('#p_totalAmt').val();
                    jsonData['assignsheet'] = '{{$p_data['info']->AssignSheet}}';
                    $('#creditcardAlert').text('刷卡處理中...')
                    break;
                case 'cmqualityforkg': // 網路品質查詢
                    url += 'cmqualityforkg';
                    jsonData['subsId'] = params;
                    break;
                default:
                    alert('API 錯誤[error005]');
                    return;
                    break;
            }

            $.ajax({
                url: url,
                type: 'post',
                data: jsonData,
                cache: false,
                dataType:'json',
                success: function (json) {
                    console.log('-------------STB API----------');
                    console.log(json);
                    if(apitype === "authorstb") // 開通
                    {
                        openApiResponse(json,params);
                    }
                    else if(['4_退單','8_退單','11_退單'].indexOf(apitype) >= 0) // 退單
                    {
                        var p_str = '退單時間:'+json['date'];
                        $('#label_chargeback').text(p_str);
                    }
                    else if(apitype === "serviceResonLast") // 維修原因[第二層]
                    {
                        if(json.code === '0000')
                            setServiceResonLast(json.data);
                        else
                            alert('維修原因[api錯誤]');
                    }
                    else if(apitype === "chgEquipment") // 換設備SN
                    {
                        $('#label_chgSiginsn').removeClass('d-none');
                        var p_str = "切換singleSN:"+json['data']['singleSN']+';時間:'+json['date'];
                        $('#label_chgSiginsn').text(p_str);
                    }
                    else if(['1_裝機','C_換機','2_復機','6_移機','8_工程收費','9_停後復機','A_加裝'].indexOf(apitype) >= 0) // 裝機完工
                    {
                        installFinshApiResponse(json);
                    }
                    else if(['3_拆機','4_停機','7_移拆','H_退拆設備','I_退拆分機','K_退次週期項'].indexOf(apitype) >= 0) // 拆機完工
                    {
                        installFinshApiResponse(json);
                    }
                    else if(apitype === 'creditcard') //完工>>刷信用卡
                    {
                        $('#creditcardAlert').text('status:'+json.status+';code:'+json.code+';'+json.meg+';時間：'+json.date)
                        $('#finishBtnDiv').show();
                        $('#installFinshalert').show();
                    }
                    else if(apitype === 'cmqualityforkg') // 網路品質查詢
                    {
                        if(json.code !== '0000') {
                            alert('網路查詢品質[error_code:'+json.code+']')
                            console.log(json)
                            var labelStr = '網路查詢：'+json.meg+';Time:'+json.date;
                            $('#cmqualityforkg_'+params+'_label').text(labelStr)
                            return;
                        }
                        var labelStr = '網路查詢：'+json.status+';Time:'+json.date;
                        $('#cmqualityforkg_'+params+'_label').text(labelStr)
                        var htmlUl = '<ul class="list-group list-group-flush">';
                        var d1 = json.data
                        var kAry = [];
                        kAry['DocsIfSigQSignalNoise'] = 'CM下行(RX)SNR(>=32)dB即時';
                        kAry['DocsIfDownChannelPower'] = 'CM下行(RX)接收功率(-10~15)dBmV即時';
                        kAry['DocsIfCmtsCmStatusSignalNoise'] = '上行(TX)SNR(>=30)dB即時';
                        kAry['DocsIfCmStatusTxPower'] = 'CM上行(TX)發射功率(40~52)dB即時';
                        kAry['true'] = '符合';
                        kAry['false'] = '不符合';

                        for(var k in (d1)){
                            htmlUl += '<li class="list-group-item list-group-item-success">'+kAry[k]+'</li>';
                            var d2 = d1[k]
                            for(var k2 in d2) {
                                var d3 = d2[k2];
                                htmlUl += '<li class="list-group-item">值：'+d3['Value'];
                                var chk = d3['Qualified'];
                                htmlUl += '##'+ kAry[d3['Qualified']]+'</li>';
                            }
                        }
                        htmlUl += '</ul>';
                        $('#cmqualityforkg_'+params+'_Body').find('div').html('');
                        $('#cmqualityforkg_'+params+'_Body').find('div').append(htmlUl);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert('API 失敗');
                    $('#cmqualityforkg_'+params+'_label').text('網路品質查找失敗');
                    console.log(xhr);
                }
            });
        }

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

        // 檔案上傳
        function upload(imgId,fileName,obj='') {
            var formData = new FormData();
            // if(imgId === "upSignImg")
            // {
            //     formData.append('image',obj);
            // }
            // else if(imgId === "file_id_01")
            // {
            //     formData.append('image',obj);
            // }
            // else
            // {
            //     formData.append('image',$('#'+imgId)[0].files[0]);
            // }
            //
            // if(imgId === 'file_constructionPhoto')
            //     formData.append('image',$('#'+imgId)[0].files[0]);
            // else
            //     formData.append('image',obj);

            formData.append('image',obj);

            var img_colum = [];
            img_colum['checkIn.jpg'] = 'checkin';
            img_colum['identity_01.jpg'] = 'id01';
            img_colum['identity_02.jpg'] = 'id02';
            img_colum['identity_03.jpg'] = 'id03';
            img_colum['certificate_01.jpg'] = 'cert01';
            img_colum['certificate_02.jpg'] = 'cert02';
            img_colum['sign.jpg'] = 'sign';
            img_colum['sign_dstb.jpg'] = 'sign_dstb';
            img_colum['sign_cm.jpg'] = 'sign_cm';
            img_colum['sign_twmbb.jpg'] = 'sign_twmbb';

            formData.append('_token',$('#p_token').val());
            formData.append('id',$('#p_id').val());
            formData.append('BookDate',$('#p_BookDate').val());
            formData.append('CustID',$('#p_custId').val());
            formData.append('companyNo',$('#p_companyNo').val());
            formData.append('workSheet',$('#p_workSheet').val());
            formData.append('p_userCode',$('#p_userCode').val());
            formData.append('p_userName',$('#p_userName').val());
            if(['sign_dstb.jpg','sign_cm.jpg','sign_twmbb.jpg'].indexOf(fileName) >= 0) {
                var fileNameAry = fileName.split('.');
                fileName = fileNameAry[0] + '_' + $('#p_workSheet').val() + '.' + fileNameAry[1];
            }
            formData.append('fileName',fileName);

            if(imgId === 'file_constructionPhoto')
            { // 施工照片
                formData.append('p_columnName', imgId.split('_')[1]);
                formData.append('names', $('#'+imgId).data('names'));
            }
            else if(imgId === "file_checkIn")
            { // 打卡
                formData.append('p_columnName',img_colum[fileName]);
                formData.append('lat',$('#localLat').val());
                formData.append('lng',$('#localLng').val());
            }
            else if(imgId === "file_id03Photo")
            { // 第二證件
                formData.append('p_columnName', imgId.split('_')[1]);
                formData.append('names', $('#'+imgId).data('names'));
            }
            else if(['upSignImg_cm','upSignImg_dstb','upSignImg_twmbb'].indexOf(imgId) >= 0)
            { // 簽名，加入 PDF版本
                //alert(imgId)
                formData.append('p_columnName', 'sign_'+imgId.split('_')[1]);
                formData.append('p_pdf_v', $('#p_pdf_v').val());
            }
            else
                formData.append('p_columnName',img_colum[fileName]);


            $("#" + imgId + "_img").remove();
            var url = '/ewo/order_info/uploadimg';
            // console.log('upload paramdata=='+formData)
            // console.log('upload fromdata=='+formData)

            $.ajax({
                url: url,
                type: 'post',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                // dataType:'json',
                success: function (json) {
                    console.log(json);

                    if(json['code'] !== "0000") {
                        alert('上傳圖片錯誤');
                        return false;
                    }
                    if(imgId === "upSignImg") {
                        // 簽名檔
                        $('#signShow').attr('src',json['data']);
                    } else if(imgId === "upSignImg_dstb" || imgId === "upSignImg_cm") {
                        // 簽名檔
                        var id_lastName = '_'+imgId.split('_')[1];
                        $('#signShow'+id_lastName).attr('src',json['data']);
                    } else if(imgId === 'file_constructionPhoto') {
                        // 施工照片
                        $('#file_constructionPhoto').data('names',json['data']['names']);
                        var html_img = '<img class=" constructionPhoto-img pb-1 ml-1" width="500" name="'+fileName.split('.')[0]+'" src="'+json['data']['img']+'" ondblclick="constructionPhotoDBClick($(this))" />';
                        var img_length = $('#constructionPhoto_img').find('img').length;
                        if(img_length >= 5) {
                            $('#constructionPhoto_img').find('img[name='+fileName.split('.')[0]+']').before(html_img);
                            $('#constructionPhoto_img').find('img[name='+fileName.split('.')[0]+']').eq(1).remove();
                        } else if(img_length < 1) {
                            $('#constructionPhoto_img').html(html_img);
                        } else {
                            $('#constructionPhoto_img').find('img').eq(img_length-1).after(html_img);
                        }
                    } else if(imgId === 'file_id03Photo') { // 第二證件
                        $('#file_id03Photo').data('names',json['data']['names']);
                        var p_id = fileName.split('.')[0];
                        var html_img = '<div class="divWatemark mb-1">' +
                            '<img class=" constructionPhoto-img" width="500" id="'+p_id+'" name="img_id_01" src="'+json['data']['img']+'" ondblclick="id03PhotDBClick($(this))" />' +
                            // '<div class=\"imgWatemark\">限用申請中嘉服務限用申請中嘉服務限用申請中嘉服務</div>' +
                            '</div>';
                        var img_length = $('#id03Photo_img').find('img').length;
                        if(img_length >= 3) {
                            // $('#id03Photo_img').find('img[name='+fileName.split('.')[0]+']').before(html_img);
                            // $('#id03Photo_img').find('img[name='+fileName.split('.')[0]+']').eq(1).closest('div').remove();

                            $('#id03Photo_img').find("div[class='divWatemark mb-1']").eq(0).after(html_img);
                            $('#id03Photo_img').find("div[class='divWatemark mb-1']").eq(0).remove();
                        } else if(img_length < 1) {
                            $('#id03Photo_img').html(html_img);
                        } else {
                            $('#id03Photo_img').find("div[class='divWatemark mb-1']").eq(img_length-1).after(html_img);
                        }
                        // load OK 再加[浮水印]
                        $('#'+p_id).on('load',function(){
                            createImgWatemark(p_id)
                        })
                    } else {
                        var imgid = 'img'+imgId.substr(imgId.search('_'),99);
                        var labelid = 'label'+imgId.substr(imgId.search('_'),99);
                        $('#'+imgid).attr('src',json.data);
                        $('#'+imgid).removeClass('d-none');
                        $('#'+labelid).text('上傳時間：'+json.date);
                        $('#'+labelid).removeClass('d-none');

                        //createImgWatemark(imgid); // 生成浮水印
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert('上傳檔案失敗[API Error!]');
                    console.log(thrownError);
                }
            });
        }


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
                            default:
                                console.log('default');
                                // console.log(json);
                                break;
                        }
                    }
                }
            });
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
            var pdf_url = '/upload/{{$p_data['uploaddir']}}/'+$('#p_workSheet').val()+'.pdf{{'?i='.date('His')}}';

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
