<html>

<head>
    <meta charset="UTF-8">
    <title>倉管連動</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="format-detection" content="telephone=no">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

    <!-- jquery -->
    <script src="{{asset('/js/jquery-3.5.1.min.js')}}"></script>

    <!-- BootStrap -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

    <style>
    body {
        font-size: 15px;
    }

    .content {
        cursor: pointer;
        margin-right: 10px;
        color: blue;
    }
    </style>
</head>

<body>

    <div class="Container" style="margin-bottom: 20px;">
        <div class="row align-items-center justify-content-center">
            <div class="col-md-12" style="padding:5px;border-bottom: 1px solid;margin: 20px 0;">
                <b class="h3 mb-0 pr-1 w-auto">功能列</b>

                <h4 class="d-inline">工程:{{ $p_data['userCode'].' '.$p_data['userName'] }}</h4>

                <b class="content" style="magrin:5px;float:right;"
                    onclick="javascript:location.href='{{route('consumables.logout')}}'">登出</b>
            </div>

            <div class="col-md-4">
                <div class="form-group text-center">
                    <button class="btn btn-info btn-block"
                        onclick="javascript:location.href='{{route('consumables.list')}}'">庫存清單</button>
                </div>
                <div class="form-group text-center">
                    <button class="btn btn-success btn-block"
                        onclick="javascript:location.href='{{route('consumables.receive')}}'">接收設備</button>
                </div>
                <div class="form-group text-center">
                    <button class="btn btn-success btn-block"
                        onclick="javascript:location.href='{{route('consumables.recyldevice')}}'">回收設備</button>
                </div>
            </div>

        </div>
    </div>
</body>

</html>
