<style>
    .input-group-append.mr-1 {
        display: inline-block;
    }
    header .container {
        height: 55px;
        position: fixed;
        top: 0;
        z-index: 99;
        background-color: #fff;
        left: 50%;
        transform: translateX(-50%);
    }
    .wordRed { color:red; font-weight:bold; }
    .wordBlue { color:blue; font-weight:bold; }

    /* 篩選btn */
    .btn-check:active+.btn-outline-dark, .btn-check:checked+.btn-outline-dark, .btn-outline-dark.active, .btn-outline-dark.dropdown-toggle.show, .btn-outline-dark:active {
        color: #fff;
        background-color: #343a40;
        border-color: #343a40;
    }
    .btn-check:active+.btn-outline-primary, .btn-check:checked+.btn-outline-primary, .btn-outline-primary.active, .btn-outline-primary.dropdown-toggle.show, .btn-outline-primary:active {
        color: #fff;
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .btn-check:active+.btn-outline-success, .btn-check:checked+.btn-outline-success, .btn-outline-success.active, .btn-outline-success.dropdown-toggle.show, .btn-outline-success:active {
        color: #fff;
        background-color: #28a745;
        border-color: #28a745;
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
</style>
<header>
    <div class="container" id="">
        <div class="row ml-0">

        @if($p_data['header'] === "list" && false)
            {{--            --}}
        @elseif($p_data['header'] === "list")
            <div class="input-group pt-3 pb-3" id="header_list_div">
                <b class="h3 mb-0 pr-1 w-auto">{{config('order.title')}}</b>

                <input type="hidden" name="p_token" id="p_token" value="{{ csrf_token() }}">
                <input type="hidden" id="p_userCode" value="{{$p_data['userId']}}">
                <input type="hidden" id="p_userName" value="{{$p_data['userName']}}">
                <div class="text-center">
                    {{$p_data['userId']}}<br>
{{--                    @if($p_data['userId'] === '001265')--}}
{{--                        <a href="https://ewo-app.hmps.cc/apk/ewo-app_0715.apk">{{$p_data['userName']}}</a>--}}
{{--                    @else--}}
                        {{$p_data['userName']}}
{{--                    @endif--}}
                </div>

                <div class="input-group-append pl-2">
                    <button class="btn btn-warning" id="btnBack" onclick="location.href='/ewo/func';">
                        <svg width="16" height="16" fill="currentColor" class="bi bi-chevron-double-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8.354 1.646a.5.5 0 0 1 0 .708L2.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                            <path fill-rule="evenodd" d="M12.354 1.646a.5.5 0 0 1 0 .708L6.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                        </svg>
                    </button>
                </div>

                <div class="input-group-append">
                    <a class="btn btn-info h-100" href="javascript:void(0)" title="Reload" id="reload">
                        <svg width="24" height="24" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                            <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                        </svg>
                    </a>
                </div>

                <select class="d-none" id="soSelect" onchange="searchBtn('DEL')">
                    <option value="all">全部</option>
                    <option value="1 裝機">裝機</option>
                    <option value="3 拆除">拆除</option>
                    <option value="5 維修">維修</option>
{{--                    <option value="laborsafety">檢點表</option>--}}
                    <option value="addsign">補簽名</option>
                    <option value="appmsg">通知訊息</option>
                    <option value="appstatistics">統計數據</option>
                    <option value="appinfo">APP資訊</option>
                    <option value="plandevice">設備建議</option>
                    <option value="laborsafety">勞安設備</option>
                </select>

                <div class="input-group-append">
                    <button type="button" class="btn btn-primary pl-0" name="headerBtn" onclick="countBtn('UNFIN')">
                        未完工 <span class="badge badge-light" id="unfinish_count">{{$p_data['unfinish_count']}}</span>
                    </button>
                    <button type="button" class="btn btn-secondary pl-0"  name="headerBtn" onclick="countBtn('FIN')">
                        已完工 <span class="badge badge-light" id="finish_count">{{$p_data['finish_count']}}</span>
                    </button>
                    <button type="button" class="btn btn-info pl-0" name="headerBtn" onclick="countBtn('UNPLAN')">
                        未約件 <span class="badge badge-light" id="unplan_count">{{$p_data['unplan_count']}}</span>
                    </button>
                </div>

                <div class="input-group-append">
                    <input type="checkbox" class="btn-check" id="btnIns" checked autocomplete="off">
                    <label class="btn btn-outline-success p-0 pt-2 mb-0" for="btnIns" onclick="orderFilter('btnIns')">裝機</label>

                    <input type="checkbox" class="btn-check" id="btnDel" checked autocomplete="off">
                    <label class="btn btn-outline-dark p-0 pt-2 mb-0" for="btnDel" onclick="orderFilter('btnDel')">拆機</label>

                    <input type="checkbox" class="btn-check" id="btnMai" checked autocomplete="off">
                    <label class="btn btn-outline-primary p-0 pt-2 mb-0" for="btnMai" onclick="orderFilter('btnMai')">維修</label>
                </div>
            </div>

        @elseif($p_data['header'] === "info")
            <div class="input-group pt-3 pb-3">
                <button class="btn btn-warning mr-3" id="btnBack" onclick="history.back()">
                    <svg width="16" height="16" fill="currentColor" class="bi bi-chevron-double-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8.354 1.646a.5.5 0 0 1 0 .708L2.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                        <path fill-rule="evenodd" d="M12.354 1.646a.5.5 0 0 1 0 .708L6.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                    </svg>
                </button>

                <div class="input-group-append mr-1">
                    <a class="btn btn-info" href="javascript:void(0)" title="Reload" onclick="location.reload();">
                        <svg width="24" height="24" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                            <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                        </svg>
                    </a>
                </div>

                <div class="input-group-append mr-1">
                    <a class="btn btn-info" href="#userInfoHead" title="用戶資料">
                        <svg viewBox="0 0 24 24" width="24" height="24"><path fill-rule="evenodd" d="M12 2.5a5.5 5.5 0 00-3.096 10.047 9.005 9.005 0 00-5.9 8.18.75.75 0 001.5.045 7.5 7.5 0 0114.993 0 .75.75 0 101.499-.044 9.005 9.005 0 00-5.9-8.181A5.5 5.5 0 0012 2.5zM8 8a4 4 0 118 0 4 4 0 01-8 0z"></path></svg>
                    </a>
                </div>

                <div class="input-group-append mr-1">
                    <a class="btn btn-info" href="#orderListHead" title="工作清單">
                        <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">
                            <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
                            <path d="M5 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 5 8zm0-2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-1-5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zM4 8a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0zm0 2.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
                        </svg>
                    </a>
                </div>

{{--                <div class="input-group-append mr-1">--}}
{{--                    <button class="btn btn-info" onclick="">--}}
{{--                        我會準時--}}
{{--                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill-rule="evenodd" d="M12 1C8.318 1 5 3.565 5 7v4.539a3.25 3.25 0 01-.546 1.803l-2.2 3.299A1.518 1.518 0 003.519 19H8.5a3.5 3.5 0 107 0h4.982a1.518 1.518 0 001.263-2.36l-2.2-3.298A3.25 3.25 0 0119 11.539V7c0-3.435-3.319-6-7-6zM6.5 7c0-2.364 2.383-4.5 5.5-4.5s5.5 2.136 5.5 4.5v4.539c0 .938.278 1.854.798 2.635l2.199 3.299a.017.017 0 01.003.01l-.001.006-.004.006-.006.004-.007.001H3.518l-.007-.001-.006-.004-.004-.006-.001-.007.003-.01 2.2-3.298a4.75 4.75 0 00.797-2.635V7zM14 19h-4a2 2 0 104 0z"></path></svg>--}}
{{--                    </button>--}}
{{--                </div>--}}

{{--                <div class="input-group-append mr-1">--}}
{{--                    <a class="btn btn-warning" href="#delateHead" onclick="">--}}
{{--                        我會遲到--}}
{{--                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">--}}
{{--                            <path fill-rule="evenodd" d="M1.22 1.22a.75.75 0 011.06 0l20.5 20.5a.75.75 0 11-1.06 1.06L17.94 19H15.5a3.5 3.5 0 11-7 0H3.518a1.518 1.518 0 01-1.263-2.36l2.2-3.298A3.25 3.25 0 005 11.539V7c0-.294.025-.583.073-.866L1.22 2.28a.75.75 0 010-1.06zM10 19a2 2 0 104 0h-4zM6.5 7.56l9.94 9.94H3.517l-.007-.001-.006-.004-.004-.006-.001-.007.003-.01 2.2-3.298a4.75 4.75 0 00.797-2.635V7.56z"></path>--}}
{{--                            <path d="M12 2.5c-1.463 0-2.8.485-3.788 1.257l-.04.032a.75.75 0 11-.935-1.173l.05-.04C8.548 1.59 10.212 1 12 1c3.681 0 7 2.565 7 6v4.539c0 .642.19 1.269.546 1.803l1.328 1.992a.75.75 0 11-1.248.832l-1.328-1.992a4.75 4.75 0 01-.798-2.635V7c0-2.364-2.383-4.5-5.5-4.5z"></path>--}}
{{--                        </svg>--}}
{{--                    </a>--}}
{{--                </div>--}}

{{--                <div class="input-group-append mr-1">--}}
{{--                    <a class="btn btn-danger" href="#uploCheckInHead" onclick="getLocalGPS()">--}}
{{--                        到站打卡--}}
{{--                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">--}}
{{--                            <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>--}}
{{--                            <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>--}}
{{--                        </svg>--}}
{{--                    </a>--}}
{{--                </div>--}}

{{--                <div class="input-group-append ">--}}
{{--                    <button class="btn btn-wow0" style="width: 130px;" onclick="ChargeBack('{{$p_data['info']->Id}}')">--}}
{{--                        退單--}}
{{--                        <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">--}}
{{--                            <path d="M6.5 7a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1h-4z"></path>--}}
{{--                            <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"></path>--}}
{{--                        </svg>--}}
{{--                    </button>--}}
{{--                </div>--}}

                <div class="input-group-append mr-1">
                    <a class="btn btn-danger" href="#pdfHead" onclick="createPDF()">
                        PDF
                        <svg width="24" height="24" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16">
                            <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
                            <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                        </svg>
                    </a>
                </div>

{{--                <div class="input-group-append mr-1">--}}
{{--                    <button class="btn btn-primary" onclick="">--}}
{{--                        開始工作--}}
{{--                        <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">--}}
{{--                            <path d="M6.75 1a.75.75 0 0 1 .75.75V8a.5.5 0 0 0 1 0V5.467l.086-.004c.317-.012.637-.008.816.027.134.027.294.096.448.182.077.042.15.147.15.314V8a.5.5 0 0 0 1 0V6.435l.106-.01c.316-.024.584-.01.708.04.118.046.3.207.486.43.081.096.15.19.2.259V8.5a.5.5 0 1 0 1 0v-1h.342a1 1 0 0 1 .995 1.1l-.271 2.715a2.5 2.5 0 0 1-.317.991l-1.395 2.442a.5.5 0 0 1-.434.252H6.118a.5.5 0 0 1-.447-.276l-1.232-2.465-2.512-4.185a.517.517 0 0 1 .809-.631l2.41 2.41A.5.5 0 0 0 6 9.5V1.75A.75.75 0 0 1 6.75 1zM8.5 4.466V1.75a1.75 1.75 0 1 0-3.5 0v6.543L3.443 6.736A1.517 1.517 0 0 0 1.07 8.588l2.491 4.153 1.215 2.43A1.5 1.5 0 0 0 6.118 16h6.302a1.5 1.5 0 0 0 1.302-.756l1.395-2.441a3.5 3.5 0 0 0 .444-1.389l.271-2.715a2 2 0 0 0-1.99-2.199h-.581a5.114 5.114 0 0 0-.195-.248c-.191-.229-.51-.568-.88-.716-.364-.146-.846-.132-1.158-.108l-.132.012a1.26 1.26 0 0 0-.56-.642 2.632 2.632 0 0 0-.738-.288c-.31-.062-.739-.058-1.05-.046l-.048.002zm2.094 2.025z"/>--}}
{{--                        </svg>--}}
{{--                    </button>--}}
{{--                </div>--}}

{{--                <div class="input-group-append mr-1">--}}
{{--                    <button class="btn btn-success" onclick="">--}}
{{--                        完成訂單--}}
{{--                        <svg width="24" height="24" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">--}}
{{--                            <path d="M8.864.046C7.908-.193 7.02.53 6.956 1.466c-.072 1.051-.23 2.016-.428 2.59-.125.36-.479 1.013-1.04 1.639-.557.623-1.282 1.178-2.131 1.41C2.685 7.288 2 7.87 2 8.72v4.001c0 .845.682 1.464 1.448 1.545 1.07.114 1.564.415 2.068.723l.048.03c.272.165.578.348.97.484.397.136.861.217 1.466.217h3.5c.937 0 1.599-.477 1.934-1.064a1.86 1.86 0 0 0 .254-.912c0-.152-.023-.312-.077-.464.201-.263.38-.578.488-.901.11-.33.172-.762.004-1.149.069-.13.12-.269.159-.403.077-.27.113-.568.113-.857 0-.288-.036-.585-.113-.856a2.144 2.144 0 0 0-.138-.362 1.9 1.9 0 0 0 .234-1.734c-.206-.592-.682-1.1-1.2-1.272-.847-.282-1.803-.276-2.516-.211a9.84 9.84 0 0 0-.443.05 9.365 9.365 0 0 0-.062-4.509A1.38 1.38 0 0 0 9.125.111L8.864.046zM11.5 14.721H8c-.51 0-.863-.069-1.14-.164-.281-.097-.506-.228-.776-.393l-.04-.024c-.555-.339-1.198-.731-2.49-.868-.333-.036-.554-.29-.554-.55V8.72c0-.254.226-.543.62-.65 1.095-.3 1.977-.996 2.614-1.708.635-.71 1.064-1.475 1.238-1.978.243-.7.407-1.768.482-2.85.025-.362.36-.594.667-.518l.262.066c.16.04.258.143.288.255a8.34 8.34 0 0 1-.145 4.725.5.5 0 0 0 .595.644l.003-.001.014-.003.058-.014a8.908 8.908 0 0 1 1.036-.157c.663-.06 1.457-.054 2.11.164.175.058.45.3.57.65.107.308.087.67-.266 1.022l-.353.353.353.354c.043.043.105.141.154.315.048.167.075.37.075.581 0 .212-.027.414-.075.582-.05.174-.111.272-.154.315l-.353.353.353.354c.047.047.109.177.005.488a2.224 2.224 0 0 1-.505.805l-.353.353.353.354c.006.005.041.05.041.17a.866.866 0 0 1-.121.416c-.165.288-.503.56-1.066.56z"/>--}}
{{--                        </svg>--}}
{{--                    </button>--}}
{{--                </div>--}}

            </div>
        @endif
        </div>
    </div>
</header>
