 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDFv2</title>
    <link rel="stylesheet" href="{{ asset('/cns/css/bootstrap.min.css') }}">
    @include('pdf.css')
</head>

<div class="container">
    <table class="w-100">
        <tr>
            <td class="col-3" >
                <img src="{{ $p_data['head_logo_img'] }}" width="150">
            </td>
            <td class="col-6 text-center">
                <p class="m-0 font-s30">中嘉寬頻</p>
                <p class="m-0">自動扣款/轉帳同意書</p>
            </td>
            <td class="col-3"></td>
        </tr>
    </table>

    <div class="w-100 wordWrap line-h08 ">

        <p class="">
            為繳費便利、節省申辦時間及流程，立同意書人(如下方所示，及帳戶/信用卡持有人以下簡稱授權人)。
        </p>
        <p class="">
            ※帳戶授權約定事項：
        </p>
        <p class="">
            一、立同意書人同意於本公司已辦理之指定銀行帳號自動扣款或信用卡自動扣繳
        </p>
        <p class="ml-20p">
            茲同意就帳號授權扣款之原指定用戶(包括名下多戶地址)，
            <br>於本公司任服務進行該帳戶扣款/轉帳事項。
        </p>

        <p class="ml-35p">
            <svg width="16" height="16" fill="currentColor" class="bi bi-slash-square" viewBox="0 0 16 16">
                <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"></path>
            </svg> 【已於本公司辦理 {{ $p_data['payType'] }} 定扣授權資料】
        </p>
        <p class="ml-55p">
            <svg width="16" height="16" fill="currentColor" class="bi bi-slash-circle-fill" viewBox="0 0 16 16">
                <circle cx="8" cy="8" r="8"/>
            </svg>  銀行名稱：{{ $p_data['bankName'] }}
        </p>
        <p class="ml-55p">
            <svg width="16" height="16" fill="currentColor" class="bi bi-slash-circle-fill" viewBox="0 0 16 16">
                <circle cx="8" cy="8" r="8"/>
            </svg>  銀行帳號：xxxx-xxxx-xxxx-{{ $p_data['chkCode'] }}
        </p>
        <p class="">
            二、立同意書人同意凡辦理與自動控款相關之一切往來事項，悉以留存於本公司之簽樣為憑，使用
        </p>
        <p class="ml-35p">該簽樣，即視為已取得立同意書人之允許或同意。</p>
        <p class="">
            三、立同意書人已詳閱及明瞭本同意書內容及本公司「蒐集、處理及 利用個人資料告知事項」。
        </p>
        <p class="ml-35p">
            立同意書人：
        </p>
        <p class="ml-35p">
            姓名：<img src="{{ $p_data['signImage'] }}" height="50px">
            ({{ $p_data['custName'] }})(限持卡人及銀行帳戶本人簽名，非本人請勿代簽)
        </p>
        <p class="ml-35p">
            連絡電話： {{ $p_data['custPhone'] }}
        </p>
        <p class="ml-35p mt-3 mb-3 text-center">
            中華明國 {{ $p_data['dateStr'] }}
        </p>
    </div>



    <table class="w-100" id="table101">
        <tr>
            <td colspan="2">
                【本公司專用欄位】
            </td>
        </tr>
        <tr>
            <td class="col-6 text-center">
                主管簽名
            </td>
            <td class="col-6 text-center">
               建檔人簽名
            </td>
        </tr>
        <tr>
            <td class="col-6">.</td>
            <td class="col-6"></td>
        </tr>
    </table>

</div>
