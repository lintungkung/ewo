<html>

<head>
    <meta charset="UTF-8">
    <title>倉管連動</title>
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

<main class="align-self-start">
    <div class="Container">
        <div class="row align-items-center justify-content-center h-100">
            <div class="col-md-4">
                <h2 class="text-center">使用者登入</h2>
                <div class="form-group">
                    <label class="control-label" for="userId">帳號：</label>
                    <input id="account" class="form-control text-center" maxlength="10" placeholder="請輸入帳號" value=""
                        onchange="this.value=this.value.toUpperCase()">
                </div>
                <div class="form-group">
                    <label class="control-label" for="password">密碼：</label>
                    <input id="password" type="password" class="form-control text-center" maxlength="20" value=""
                        placeholder="請輸入密碼">
                </div>
                <div class="form-group text-center">
                    <input type="button" class="btn btn-success btn-block" value="登入" onclick="login()">
                </div>
            </div>
        </div>
    </div>
</main>

</html>

<script src="{{asset('/js/jquery-3.5.1.min.js')}}"></script>
<script type="text/javascript">
function login() {
    $.ajax({
        url: "{{route('consumables.login')}}",
        type: 'post',
        data: {
            'account': $('#account').val(),
            'password': $('#password').val(),
            '_token': "{{ csrf_token() }}"
        },
        success: function(json) {
            if (json.status == "OK") {
                document.location = "{{route('consumables.menu')}}";
            } else {
                alert(json.meg)
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert('登入失敗');
        }
    });
}
</script>