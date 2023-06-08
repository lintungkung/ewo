@extends('func.layouts.default')

@section('title', '勤務派工APP')

@section('content')

<main>
    <div class="container bg-grey">

        <input type="hidden" name="p_token" id="p_token" value="{{ csrf_token() }}">
        <input type="hidden" id="p_userCode" value="{{$p_data['userCode']}}">
        <input type="hidden" id="p_userName" value="{{$p_data['userName']}}">

        <div class="card w-100 mt-3 mb-3">
            <div class="card-header" >
                <div class="input-group">
                    APP帳號
                </div>
            </div>
            <div class=" ">
                <div class="card-body">
                    <div class="input-group ">
                        <div class="input-group-prepend p-0 col-3">
                            <span class="input-group-text w-100 pl-1">登入帳號</span>
                        </div>
                        <div class="input-group-append p-0 col-9">
                            <span class="input-group-text bg-white w-100">
                                {{$p_data['userCode']}}
                            </span>
                        </div>
                    </div>
                    <div class="input-group ">
                        <div class="input-group-prepend p-0 col-3">
                            <span class="input-group-text w-100 pl-1">名稱</span>
                        </div>
                        <div class="input-group-append p-0 col-9">
                            <span class="input-group-text bg-white w-100">
                                {{$p_data['userName']}}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="card w-100 mb-3">
            <div class="card-header" >
                <div class="input-group">
                    更改密碼
                </div>
            </div>
            <div class="">
                <div class="card-body">
                    <div class="input-group ">
                        <div class="input-group-prepend p-0 col-3">
                            <span class="input-group-text w-100 pl-1">現在密碼</span>
                        </div>
                        <div class="input-group-append p-0 col-9">
                            <input  class="form-control" type="password" id="passwordOld" maxlength="12">
                        </div>
                    </div>
                    <div class="input-group ">
                        <div class="input-group-prepend p-0 col-3">
                            <span class="input-group-text w-100 pl-1">更新密碼</span>
                        </div>
                        <div class="input-group-append p-0 col-9">
                            <input  class="form-control" type="password" id="passwordNew" name="passwordChk" maxlength="12">
                        </div>
                    </div>
                    <div class="input-group ">
                        <div class="input-group-prepend p-0 col-3">
                            <span class="input-group-text w-100 pl-1">確認密碼</span>
                        </div>
                        <div class="input-group-append p-0 col-9">
                            <input  class="form-control" type="password" id="passwordChk" name="passwordChk" maxlength="12">
                        </div>
                    </div>
                    <label class="btn btn-warning mb-0">
                        <input class="d-none" type="button" onclick="if(confirm('確認修改密碼!!!'))changePassword()">
                        送出
                    </label>
                    <label id="password_label"></label>
                </div>
            </div>
        </div>


        <div class="card w-100 mb-3">
            <div class="card-header" >
                <div class="input-group">
                    APK版本
                </div>
            </div>
            <div class="card-body">
                <div class="input-group mb-0">
                    <label class="" id="apkVersion">???</label>
                </div>
                <div class="input-group mb-0" id="downloaddiv">
                    <button class="btn btn-info" onclick="downloadApk()">[{{ env('APK_VERSION') }}]App下載</button>
                </div>
            </div>

        </div>

        <div class="card w-100 mb-3">
            <div class="card-header" >
                <div class="input-group">
                    工程簽名
                </div>
            </div>
            <div class="card-body">
                <div class="input-group mb-0">
                    <div class="w-100" id="signDiv_mengineer">
                        <div class="input-group-prepend w-100 p-0" id="signButton_mengineer">
                            <button class="btn btn-success mr-3" id="signRestBtn_mengineer" onclick="resetSignButton('open','mengineer')">工程重新簽名</button>
                            <button class="btn btn-info mr-3" id="signUpBtn_mengineer" onclick="signUpload('mengineer');resetSignButton('close','mengineer')">工程簽名上傳</button>
                            <button class="btn btn-secondary" id="signCloseBtn_mengineer" onclick="resetSignButton('close','mengineer')">取消</button>
                            <label class="alert alert-info mb-0" id="signAlert" style="display: none;"></label>
                        </div>
                        <img src="/upload/SignMengineer/SignMengineer_{{$p_data['userCode']}}.jpg?i={{date('His')}}" width="500" id="signShow_mengineer">
                        <div id="signaturePad_mengineer" class="signature-pad d-none">
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

        <div class="card w-100 mb-3">
            <div class="card-header" >
                <div class="input-group">
                    安卓ID
                </div>
            </div>
            <div class="card-body">
                <div class="input-group mb-0">
                    <label id="androidId">???</label>
                </div>
            </div>
        </div>

        <div>
            <label class="btn btn-primary w-100">
                <input class="d-none" type="button" onclick="document.location = '/ewo/login'">
                登出
            </label>
        </div>

        {{-- LoadIng --}}
        <div class="modal fade" id="loadIng" role="dialog" >
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
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

    </div>
</main>

@endsection

@section('script')
<script>

    $(document).ready(function () {

        // AppInfo，版本資訊
        if(true) {
            try{
                var p_apkversion = app.getVersion();
                var p_runversion = '{{ env('APK_VERSION') }}';
                var str = '';
                if(p_apkversion !== p_runversion) {
                    str = '請安裝['+p_runversion+']版本';
                } else {
                    str = 'v:'+p_apkversion;
                }
                $('#apkVersion').text(str);

            } catch (e) {
                $('#apkVersion').text('請安裝[{{env('APK_VERSION')}}]版本.');
            }
        }


        // AppInfo，安卓ID
        if(true) {
            try{
                var p_androidId = app.getAndroidId();
                $('#androidId').text(p_androidId);
            } catch (e) {
                $('#apkVersion').text('請安裝[{{env('APK_VERSION')}}]版本.');
            }
        }



        // 修改密碼>[確認密碼]檢查
        $("input[name='passwordChk']").change(function(){
            var p_pwdNew = $('#passwordNew').val();
            var p_pwdChk = $('#passwordChk').val();

            if(p_pwdNew.length < 8) {
                $('#password_label').text('修改密碼，請輸入8~12碼');
                return;
            }

            if(p_pwdChk != p_pwdNew) {
                $('#passwordChk').removeClass('text-info');
                $("input[name='passwordChk']").addClass('bg-warning');
                $('#password_label').text('請檢查[確認密碼]是否一致.');
            } else {
                $("input[name='passwordChk']").addClass('bg-info');
                $("input[name='passwordChk']").removeClass('bg-warning');
                $('#password_label').text('密碼檢查，OK');
            }
        })


    });
    /*********** Redy end *************/

    //修改密碼
    function changePassword() {
        var params = {
            'account' : '{{$p_data['userCode']}}',
            'user_name' : '{{$p_data['userName']}}',
            'password_old' : $('#passwordOld').val(),
            'password_new' : $('#passwordNew').val(),

        };
        $('#password_label').removeClass('bg-info');
        $('#password_label').removeClass('bg-warning');
        $('#password_label').text('密碼修改中...');
        $('#loadIng').modal('show');
        $.ajax({
            url: '/api/changepwd',
            type: 'POST',
            data: params,
            cache: false,
            dataType:'json',
            success: function (json) {
                console.log(json);
                switch(json.code) {
                    case'0000':
                        $('#password_label').addClass('bg-info');
                        $('#password_label').text(json.meg);
                        alert('密碼修改，成功，請重新登入!!');
                        window.location.href = '/ewo/login';
                        break;

                    case '0531':
                        $('#password_label').text(json.meg);
                        alert('帳號已被鎖定!!!')
                        window.location.href = '/ewo/login';
                        break;

                    case '0511':
                    case '0512':
                        $('#password_label').addClass('bg-warning');
                        $('#password_label').text(json.meg);
                        alert('密碼修改，失敗!!');
                        break;
                }
                modalClose();

            }, error: function (data) {
                console.log(data);
                modalClose();
                $('#password_label').removeClass('bg-info');
                $('#password_label').text(json.meg)
                alert('APP，功能錯誤[修改密碼]');
            }
        });
    }

    function modalClose() {
        for(let i=1; i<10; i++) {
            setTimeout(function(){
                $('#loadIng').modal('hide');
            },1000);
        }
    }

    // 下載APK
    function downloadApk() {
        let url = '{{ env('STB_API') }}/apk/ewo-app_{{ substr(env('APK_VERSION'),-6) }}.apk';
        try {
            app.openUrl(url);
        } catch (e) {
            $('#downloaddiv').html('#版本不支援請更新');
        }
    }

    // 簽名檔，上傳
    function signUpload(servName) {
        var alertObj = $('#signAlert');
        alertObj.html('上傳中...');
        alertObj.show();
        const canvas = document.getElementById("upSignImg_"+servName);
        const dataURL = canvas.toDataURL('image/jpg')
        const blobBin = atob(dataURL.split(',')[1])
        const array = []
        for (let i = 0; i < blobBin.length; i++) {
            array.push(blobBin.charCodeAt(i))
        }
        const obj = new Blob([new Uint8Array(array)], { type: 'image/jpg' })
        // var fileName = 'sign_'+servName+'_'+$('#p_userCode').val()+'.jpg';
        // var p_columnName = 'sign_'+servName;
        var imgId = 'upSignImg_'+servName;

        var formData = new FormData();
        formData.append('file',obj);
        formData.append('_token',$('#p_token').val());
        formData.append('userCode',$('#p_userCode').val());
        formData.append('userName',$('#p_userName').val());

        var url = '/api/EWO/updSignMengineer';
        $.ajax({
            url: url,
            type: 'post',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function (result) {
                console.log(result);
                let code = result.code;
                let data = result.data;
                let sec = 1000;

                if(code != '0000') {
                    let htmlStr = '<span style="padding: 0em 0.1em; margin: 0em 0.1em; background-image: linear-gradient(transparent 0%, #ffb1b1 100%);">'+data+'</span>';
                    alertObj.html(htmlStr);
                    sec = sec * 99;
                } else {
                    let imgObj = $('#signShow_mengineer');
                    imgObj.attr('src',data);
                    alertObj.html('上傳ok'+result.date);
                    sec = sec * 10;
                }

                alertObj.show();
                window.setTimeout(function () {
                    alertObj.hide();
                },sec);

            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('上傳檔案失敗[API Error!]');
                console.log(thrownError);
            }
        });
    }

    // 創建，簽名板
    function createSign(servName)
    {
        var idStr = 'upSignImg_'+servName;
        console.log('func_createSign_id==' +idStr);

        var canvas = document.getElementById(idStr);
        var signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            dotSize: 1, //點的大小
            minWidth: 5, //最細的線條寬度
            // maxWidth: 5, //最粗的線條寬度
        });

        function resizeCanvas() {
            var ratio = Math.max(window.devicePixelRatio || 1, 1);
            console.log('ratio='+ratio)
            console.log('offsetWidth='+canvas.offsetWidth);
            console.log('offsetHeight='+canvas.offsetHeight);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext('2d').scale(ratio, ratio);
            canvas.lineWidth = 111;
            signaturePad.clear();
        }
        window.onresize = resizeCanvas;
        resizeCanvas();
        $('#signClear_'+servName).click(function(){
            signaturePad.clear();
        });
    }
    // 預設，先建立簽名板
    createSign('mengineer');

    // 預設，重設簽名按鈕
    resetSignButton('close','mengineer')


    // 重新簽名，Button
    function resetSignButton(p_type,servName) {
        if(p_type == 'open') {
            // 重新簽名
            $('#signRestBtn_'+servName).addClass('d-none');
            $('#signCloseBtn_'+servName).removeClass('d-none');
            $('#signUpBtn_'+servName).removeClass('d-none');
            $('#signShow_'+servName).addClass('d-none');
            $('#signaturePad_'+servName).removeClass('d-none');
            createSign(servName);
        } else if(p_type == 'close') {
            // 上傳/取消
            $('#signRestBtn_'+servName).removeClass('d-none');
            $('#signCloseBtn_'+servName).addClass('d-none');
            $('#signUpBtn_'+servName).addClass('d-none');
            $('#signShow_'+servName).removeClass('d-none');
            $('#signaturePad_'+servName).addClass('d-none');
        }
    }



</script>
@endsection
