<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDFv2</title>
    <link rel="stylesheet" href="{{ asset('/cns/css/bootstrap.min.css') }}">
    @include('pdf.css')

    <style>
        p {
            margin: 0;
        }
    </style>
</head>

<div style="" class="w-100 wordWrap line-h08 font-s10">
    <p class="m-0 mr-1 mb-3 text-top font-s20 line-h08">
        條款[提示]
    </p>
</div>

@if(in_array('CATV',$page_list) === true || in_array('DSTB',$page_list) === true)
    <div style="" class="w-100 wordWrap line-h08 font-s10">
        <p class="m-0 mr-1 text-top font-s15 line-h08">
            特約條款[DSTB]：
        </p>
        <p class="m-0 mr-1 mb-3 text-top font-s15 line-h08">
            {{ $data['CATV']['MSContract'] }}
        </p>

        <p class="m-0 mr-1 text-top font-s15 line-h08">
            1.申裝設備內含(1)數位機上盒乙台(2)智慧卡乙張(3)遙控器乙個(4)變壓器乙組(5)AV端子線乙組(6)色差端子線乙組。2.申裝設備/月繳現付用戶須附上身分證正反面影本。3.請選擇「有線電視收視費」次期繳費週期：口月繳現付、口雙月繳、口季繳、口半年繳、口全年繳。4.用戶若選擇借用本設備，其所有權為本公司所有，用戶限於上述裝機地址使用。當雙方有線電視收視合約終止時，用戶應立即將本設備全數及完整歸還予本公司。借用人應善盡保管義務使用本設備，歸還時如發生部分設備或整組設備遺失、毀損之情事，借用人應按設備市價賠償予本公司。5.使用DTV數位加值服務之用戶必須同時為本公司有效之有線電視基本頻道收視戶。若與本公司之有線電視基本頻道收視契約終止時，本公司得不經預告立即終止本服務。6.已申裝銀行自動扣款者同意因訂購本公司之服務所生之一切費用，均由原自動扣繳方式支付。本人茲確認及同意1.本申裝方案及特約條款內容2.申請書背面之有線電視定型化契約3.貴公司保護及使用用戶資料權益聲明:為提供服務我們將保存及使用您所提供之「有線電視及加值服務」用戶資料，包括您與我們聯絡所提供之個人資料(例：姓名、電話、地址、聯絡人姓名、信用卡資料、付款帳務、資訊流服務調查等)。除了個人資料外，我們也將收集您使用機上盒、機上盒回傳本公司系統之資料(例：機上盒開關待機資訊、家戶收視頻道及時間、家戶收視習慣等)。用戶資料之保存與使用主要是用於提供我們的服務(包括基本及加值服務)、提昇產品服務品質(如：了解家戶收視習慣及偏好，以提供用戶更好的內容及服務)、加強個人化服務(例：提供個人化內容之推薦、個人化廣告之提供)、及停止服務後之服務產品訊息告知(包括以電郵、簡訊、語音及視訊等方式提供適合您的服務及行銷訊息，例：有線電視匯流相關服務產品、視訊服務之產品銷售訊息通知、節目及服務數位資訊流匯整等)，未經您同意，不會另外將您的用戶資料揭露於與本公司無關之第三人非上述目的以外之用途、或非在必要之範圍內。若您不想再收到我們的訊息或用戶資訊需要更新等，請與客服聯絡，我們將由專人為您服務。4.申裝數位電視服務，本人了解需負責保管貴公司數位機上盒及終止服務時至營業櫃檯返還之義務及法律責任。
        </p>
        <p class="m-0 mr-1 mb-3 text-top font-s15 line-h08">
            此致 中嘉寬頻股份有限公司
        </p>
    </div>
@endif

@if(in_array('TWMBB',$page_list) === true || in_array('TWMBB_789',$page_list) === true)
    <div style="" class="w-100 wordWrap line-h08 font-s10">
        <p class="m-0 mr-1 text-top font-s15 line-h08">
            特約條款[TWMBB]：
        </p>
        <p class="m-0 mr-1 mb-3 text-top font-s15 line-h08">
            {{ $data['TWMBB']['MSContract'] }}
        </p>
    </div>
@endif

@if(in_array('CM',$page_list) === true)
    <div style="" class="w-100 wordWrap line-h08 font-s10">
        <p class="m-0 text-top font-s15 line-h08">
            特約條款[CM]：
        </p>
        <p class="m-0 mr-1 mb-3 text-top font-s15 line-h08">
            {{ $data['CM']['MSContract'] }}
        </p>

        <p class="m-0 mr-1 text-top font-s15 line-h08">
            本人茲確認及同意 1.本申裝方案及抨約條款內容 2.申請書背面之寬頻連線服務契約 3.申裝設備用戶須附上雙證（身份證影本十駕照或健保卡）正反面影本 4.貴公司保護及使用用戶資料權益聲明:為提供服務我們將保存及使用您所提供之「寬頻上網及加值服務」用戶資料，包括您與我們聯絡所提供之用戶資料（例 :帳務、資訊流服務調查等）。用戶資料之保存與使用主要是用於提昇產品服務品質、加強個人化服務及停止服務後之服務產品訊息告知（包括以電郵、簡訊、語音及視訊等方式提供適合您的服務及行銷訊息，例:有線電視匯流相關服務產品、視訊服務後之產品銷售訊息通知、節目及服務數位資訊流匯整等），未經您的同意，不會另外將您的用戶資料揭露於與本服務無關之第三人或非上述目的以外之用途。若您不想再收到我們的訊息或用戶資訊需要更新等，請與客服聯絡，我們將由專人為您服務。5申裝光纖寬頻網路服務，本人了解終止服務時至營業櫃擡返還之義務及法律責任。
        </p>
        <p class="m-0 mr-1 mb-3 text-top font-s15 line-h08">
            此致 @if($data['CM']['CompanyNo'] == '209') 寶島聯網股份有限公司@else 中嘉寬頻股份有限公司@endif
        </p>
    </div>
@endif

@if(in_array('FTTH',$page_list) === true)
    <div style="" class="w-100 wordWrap line-h08 font-s10">
        <p class="m-0 text-top font-s15 line-h08">
            特約條款[FTTH]：
        </p>

        <p class="m-0 mr-1 mb-3 text-top font-s15 line-h08">
            工單備註：{{ $data['FTTH']['MSContract'] }}
        </p>

        <p class="m-0 mr-1 text-top font-s15 line-h08">
            本人茲確認及同意 1.本申裝方案及抨約條款內容 2.申請書背面之寬頻連線服務契約 3.申裝設備用戶須附上雙證（身份證影本十駕照或健保卡）正反面影本 4.貴公司保護及使用用戶資料權益聲明:為提供服務我們將保存及使用您所提供之「寬頻上網及加值服務」用戶資料，包括您與我們聯絡所提供之用戶資料（例 :帳務、資訊流服務調查等）。用戶資料之保存與使用主要是用於提昇產品服務品質、加強個人化服務及停止服務後之服務產品訊息告知（包括以電郵、簡訊、語音及視訊等方式提供適合您的服務及行銷訊息，例:有線電視匯流相關服務產品、視訊服務後之產品銷售訊息通知、節目及服務數位資訊流匯整等），未經您的同意，不會另外將您的用戶資料揭露於與本服務無關之第三人或非上述目的以外之用途。若您不想再收到我們的訊息或用戶資訊需要更新等，請與客服聯絡，我們將由專人為您服務。5申裝光纖寬頻網路服務，本人了解終止服務時至營業櫃擡返還之義務及法律責任。
        </p>
        <p class="m-0 mr-1 mb-3 text-top font-s15 line-h08">
            此致 @if($data['FTTH']['CompanyNo'] == '209') 寶島聯網股份有限公司@else 中嘉寬頻股份有限公司@endif
        </p>
    </div>
@endif




{{----}}
{{----}}
<i class="page_break"></i>
{{----}}
{{----}}
