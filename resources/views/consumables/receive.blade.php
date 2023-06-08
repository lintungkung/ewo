<html>

<head>
    <meta charset="UTF-8">
    <title>設備調撥</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="format-detection" content="telephone=no">

    <link rel="stylesheet" href="{{ asset('cns/css/bootstrap.min.css') }}">

    <!-- BootStrap -->
    <script src="{{ asset('js/jquery-3.5.1.min.js') }}"></script>
{{--    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>--}}
    <script src="{{ asset('cns/scripts/bootstrap.min.js') }}"></script>

    <style>
    body {
        font-size: 15px;
    }

    label {
        margin-top: 10px;
        font-size: 20px;
        font-weight: 500;
    }

    .content {
        cursor: pointer;
        margin-right: 10px;
        color: blue;
    }

    .warn {
        color: red;
    }
    .font-s20 {
        font-size: 20px;
    }
    </style>
</head>

<body>

    <div class="Container" style="margin-bottom: 20px;margin-top: 20px;">
        <div class="row">
            <div class="col-md-12" style="padding:5px;border-bottom: 1px solid;">
                <button class="btn btn-outline-dark" onclick="location.href='/consumables/menu'">
                    <svg width="32" height="32" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                    </svg>
                </button>
                <b class="h3 mb-0 pr-1 w-auto">調撥</b>

                <h4 class="d-inline">工程:{{ $p_data['usercode'].' '.$p_data['username'] }}</h4>
                <b class="content" style="magrin:5px;float:right;"
                    onclick="javascript:location.href='{{route('consumables.logout')}}'">登出</b>
            </div>

            <input type="hidden" id="deptname" value="{{ $p_data['deptname'] }}">
            <input type="hidden" id="username" value="{{ $p_data['username'] }}">
            <input type="hidden" id="usercode" value="{{ $p_data['usercode'] }}">

            <div class="col-md-4">
                <label>撥出儲位</label>
                <input type="text" class="form-control" id="placenoOut" readonly>
            </div>

            <div class="col-md-4">
                <label>公司別</label>
                <input type="text" class="form-control bg-white" readonly id="companyno" placeholder="公司別">
{{--                <select class="form-control" id="companyno">--}}
{{--                    @foreach($p_data['companynoary'] as $k => $t)--}}
{{--                        <option value="{{ $k }}">{{ $k.'|'.$t }}</option>--}}
{{--                    @endforeach--}}
{{--                </select>--}}
            </div>

            <div class="col-md-4">
                <label>設備型號</label>
                <input type="text" class="form-control bg-white" id="csmodel" placeholder="型號" readonly >
            </div>

            <div class="col-md-4">
                <label>料號</label>
                <input type="text" class="form-control bg-white" id="mtno" placeholder="料號" readonly >
            </div>

            <div class="col-md-4">
                <label>訂編</label>
                <input type="text" class="form-control bg-white" id="subsid" placeholder="訂編" readonly >
            </div>

            <div class="col-md-4">
                <label><span class='warn'>*</span>序號</label>
                <input type="text" class="form-control bg-white" id="singlesn" maxlength="30" placeholder="序號">
            </div>

{{--            <div class="col-md-4">--}}
{{--                <label>訂編</label>--}}
{{--                <input type="text" class="form-control" id="subsid">--}}
{{--            </div>--}}

            <div class="col-md-4">
                <label>撥入儲位</label>
                <input type="text" class="form-control" id="placenoIn" readonly value="{{ $p_data['placeno'] }}">
            </div>

            <div class="col-md-4">
{{--                <label>回復</label>--}}
                <label class="alert alert-info pb-0 pt-0" id="resultStr">請輸入設備序號掃描</label>
            </div>

            <div class="col-md-12 mt-3">
                <div class="col-md-4 offset-md-4">
                    <button class="btn btn-success btn-block" onclick="scan()">掃描</button>
                    <button class="btn btn-info btn-block" onclick="if(confirm('確認領取\n'+$('#singlesn').val()))allotApi()">確認領取</button>
{{--                    <button class="btn btn-warning btn-block" onclick="if(confirm('清除全部'))$('#signlist').find('li').remove()">清除全部</button>--}}
                </div>
            </div>
        </div>

        {{--    提示 Dialog    --}}
        <div class="modal fade" id="alertDialog" role="dialog" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
{{--                        <button type="button" class="close" data-dismiss="modal">&times;</button>--}}
                        <h4 class="modal-title" id="titleH4">設備調撥</h4>
                    </div>

                    <div class="modal-body">
                        alert text
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btnAllot" onclick="history.back()">回首頁</button>
                    </div>
                </div>
            </div>
        </div>


    </div>
</body>
{{--<script src="{{asset('/js/jquery-3.5.1.min.js')}}"></script>--}}
<script type="text/javascript">

$(document).ready(function () {
    $('#singlesn').change(function(){
        var str = $(this).val();
        $(this).val(str.toUpperCase())
    })
    $('#singlesn').focus(function(){
        $(this).val('');
    });

    @if(empty($p_data['placeno']))
    $('#alertDialog .modal-body').text('倉位資料錯誤，\n請到[App登入帳號管理]確認!!');
    $('#alertDialog').modal('show');
    @endif

});
var scanStr = '';
var liID = 0;


// 呼叫APK
function scan() {
    // $('#scanval').html('')
    var singlesn = $('#singlesn').val();
    var companyno = $('#companyno').val();
    if(singlesn.length > 5) {
        scan_confirm(companyno+','+singlesn);
        return;
    }

    try {
        app.scan();
    } catch (e) {
        // scanStr = '620,F0F2495203B0,321031314-1,340 CGNF-TWN-1G';
        // var companyno = $('#companyno').val();
        // var singlesn = $('#singlesn').val();
        if(singlesn.length > 8) {
            scan_confirm(companyno+','+singlesn);
        } else {
            alert('請輸入[公司別]、[設備序號]');
        }
        // alert('手機不支援掃描功能');
    }

}


// APK回複
function getScanValue(value) {
    scanStr = value;
    scan_confirm(scanStr);
}


function scan_confirm(p_scanstr) {
    var scanAry = p_scanstr.split(',');
    var companyno = scanAry[0];
    var singlesn = scanAry[1];
    console.log(scanAry);
    if(confirm('掃描設備序號:'+ scanAry[1]+'\n確定查詢!?')) {
        searchAPI(companyno,singlesn)
    } else {
        //
    }
}


function allotApi() {
    $('#resultStr').text('設備轉移中');

    var data = JSON.stringify({
        "companyno": $('#companyno').val(),
        "username": $('#username').val(),
        "usercode": $('#usercode').val(),
        "deptnoname": $('#deptname').val(),
        "keyinno": $('#subsid').val(),
        "placenoout": $('#placenoOut').val(),
        "placenoin": $('#placenoIn').val(),
        "singlesn": $('#singlesn').val(),
    });

    $.ajax({
        url: '/api/consumables/apiAllot',
        type: 'post',
        headers: {"Content-Type": "application/json"},
        data: data,
        success: function(json) {
            console.log('api apiAllot success');
            console.log(json);

            var htmlStr = '設備收取[' + $('#singlesn').val() + ']' + json.msg + '請到清單查詢';
            alert(htmlStr);
            if(json.code == '0000') {
                location.reload();
            } else {
                alert('收取失敗，請確認資料後\n重新[收取]');
            }
            $('#resultStr').text('設備序號:'+singlesn+'; '+json.msg);

        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert('API呼叫失敗');
        }
    });
}


// 查詢，單品序號，查詢[倉位]
function searchAPI(companyno, singlesn) {
    $('#resultStr').text('查詢中...');

    if(singlesn.length < 1) {
        alert('掃描功能異常')
        return;
    }

    var data = JSON.stringify({
        "companyno": companyno,
        "singlesn": singlesn,
        "devInfo": 'Y'
    });

    $.ajax({
        url: '/api/consumables/getDeviceDetail',
        type: 'post',
        headers: {"Content-Type": "application/json"},
        data: data,
        success: function(json) {
            console.log('searchAPI success');
            console.log(json);
            let msg02 = '';
            if(json.code === '0000') {
                var data = json.data;
                $('#placenoOut').val(data.placeno);
                // $('#companyno option[value="'+data.companyno+'"]').attr('selected','true');
                $('#companyno').val(data.companyno);
                $('#csmodel').val(data.csmodel);
                $('#mtno').val(data.mtno);
                $('#singlesn').val(data.singlesn);
                msg02 = data.devInfo;
            }
            let resultStr = '設備序號:'+singlesn+'; 查找:'+json.msg;
            resultStr += '#'+msg02;
            $('#resultStr').text(resultStr);

        },error: function(xhr, ajaxOptions, thrownError) {
            alert('API呼叫失敗');
        }

    });
}


</script>

</html>
