<div class="container bg-grey collapse" id="appInfo" name="divpage">

    <div class="card w-100 mb-3">
        <div class="card-header" >
            <div class="input-group">
                APP帳號
            </div>
        </div>
        <div class="collapse show">
            <div class="card-body">
                <div class="input-group ">
                    <div class="input-group-prepend p-0 col-3">
                        <span class="input-group-text w-100 pl-1">登入帳號</span>
                    </div>
                    <div class="input-group-append p-0 col-9">
                        <span class="input-group-text bg-white w-100">
                            {{$p_data['userId']}}
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
        <div class="collapse show">
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

</div>

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
            'account' : '{{$p_data['userId']}}',
            'user_name' : '{{$p_data['userName']}}',
            'password_old' : $('#passwordOld').val(),
            'password_new' : $('#passwordNew').val(),

        };
        $('#password_label').removeClass('bg-info');
        $('#password_label').removeClass('bg-warning');
        $('#password_label').text('密碼修改中...');
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

            }, error: function (data) {
                console.log(data);
                $('#password_label').removeClass('bg-info');
                $('#password_label').text(json.meg)
                alert('APP，功能錯誤[修改密碼]');
            }
        });
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


</script>
