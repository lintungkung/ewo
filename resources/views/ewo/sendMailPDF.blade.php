<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<style>
    .h1_backcolor {
        width: 100%;
        background-color: #ffa000;
        height: 30px;
    }

    p {
        margin-top: 0;
        margin-bottom: 1rem;
    }

    b, strong {
        font-weight: bolder;
    }

</style>
<head>
    <meta charset="utf-8">
    <title>Mail</title>
</head>
<body>
<div class="h1_backcolor">&nbsp</div>
<div>
    <div>
        <h2>親愛的用戶您好:</h2>

        <p>本公司為落實節能減碳政策及推動無紙化，已運用E-Mail寄送個資告知、申裝資訊、收據，請參閱附件檔!!</p>
        <p>附件檔案為加密PDF檔，密碼為[0000]。</p>
        <p>
            提醒您，附件為PDF檔案格式，如無法開啟，請至Adobe官網<a href="https://get.adobe.com/tw/reader/" target=_blank>下載安裝Adobe Reader</a>
        </p>
    </div>
</div>
</body>
</html>
