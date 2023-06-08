<html>
<head>
    <meta charset="UTF-8">
    <title>EWO_電子工單</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="format-detection" content="telephone=no">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

    <!-- BootStrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <!-- jQuery -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

</head>

<style>
    {{--    --}}
</style>
<main class="align-self-start">

    <div class="Container">
        <div class="row align-items-center justify-content-center h-100">
            <div class="col-md-4">
                <h2 class="text-center">使用者登入</h2>
                <div class="form-group">
                    <label class="control-label" for="userId">帳號：</label>
                    <input id="account" class="form-control text-center" maxlength="10" placeholder="請輸入帳號"
                           value="{{$p_userId}}" onchange="this.value=this.value.toUpperCase()">
                </div>
                <div class="form-group">
                    <label class="control-label" for="password">密碼：</label>
                    <input id="password" type="password" class="form-control text-center" maxlength="20" value="{{$p_password}}"
                           placeholder="請輸入密碼">
                    <input type="checkbox" name="rememberPWD" id="rememberPWD" value="Y" @if($p_rememberPWD === '1') checked @endif>記住密碼
                </div>
                <div class="form-group text-center">
                    <input type="button" class="btn btn-success btn-block" value="登入" onclick="login()">
                </div>

                @if(!empty($error_msg))
                <div class="alert alert-danger w-100" role="alert">
                    {{$error_msg}}
                </div>
                @endif

                <div class="text-center w-100">
                    <span id="apk_versioin_span">安裝版本：???</span>
                </div>
                <div class="text-center w-100">
                    <span>現行版本：{{env('APK_VERSION')}}</span>
                </div>
                <div class="text-center w-100">
                    <span id="androidId_span" data-id="">安卓ID：???</span>
                </div>
            </div>
        </div>
    </div>
</main>
</html>


{{--<script src="/js/jquery-3.5.1.min.js"></script>--}}

<script type="text/javascript">

    $(document).ready(function () {
        if(true)
        {
            app.getFcmToken();
            app.getVersion();
            app.getAndroidId();

            var p_value = app.getVersion();

            var p_androidId = app.getAndroidId();

            if(p_value.length > 0) {
                $('#apk_versioin_span').text('安裝版本：' + p_value);
            }

            $('#androidId_span').text('安卓ID：' + p_androidId);
            $('#androidId_span').data('id',p_androidId);

        }
    });

    function getAndroidId() {
        var androidId = app.getAndroidId();
        alert(androidId);
    }

    function login() {
        // document.location = '/ewo/order_list/d';
        // return false;

        var url = '/ewo/login';

        var account = $('#account').val();
        var password = $('#password').val();
        var rememberPWD = $('#rememberPWD').val();
        var fcmtoken = '';
        var uuid = $('#androidId_span').data('id')

        //檢查版本
        chkVersion();
        //存取fcmToken
        fcmtoken = getToken();

        //設備
        var device = '';

        var data = {
            'account' : account,
            'password' : password,
            'rememberPWD' : rememberPWD,
            'fcmtoken' : fcmtoken,
            'device' : device,
            'uuid' : uuid,
            '_token' : "{{ csrf_token() }}",
        };

        $.ajax({
            url: url,
            type: 'post',
            data: data,
            success: function (json) {

                if (json.status == "OK") {
                    var token = json.data.token;
                    document.location = "/ewo/func";
                } else {
                    alert(json.meg);
                }

            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert('登入失敗');
            }
        });
    }

    var alert_item = 0;

    function getToken() {
        var operation_version = '{{env('APK_VERSION')}}';
        try {
            var p_token = app.getFcmToken();
            var p_strLength = p_token.length;
            return p_token;

        } catch(e) {
            if(alert_item < 1) {
                alert('請更新版本['+operation_version+']');
                alert_item = 1;
            }
        }
    }

    function chkVersion() {
        try {
            var get_version = app.getVersion();
            var operation_version = '{{env('APK_VERSION')}}';

            if(`${get_version}` != `${operation_version}`)
                if(alert_item < 1) {
                    alert('請更新版本['+operation_version+'].');
                    alert_item = 1;
                }

        } catch(e) {
            if(alert_item < 1) {
                alert('請更新版本['+operation_version+']..');
                alert_item = 1;
            }
        }
    }

</script>
