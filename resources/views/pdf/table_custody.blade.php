<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDFv2</title>
    <link rel="stylesheet" href="{{ asset('/cns/css/bootstrap.min.css') }}">
    @include('pdf.css')
</head>

<table class="w-100">
    <tr class="text-center">
        <td class="col-12 text-center">
            <p class="m-0 font-s30">{{ $data['CUSTODY']['WorkKindType'] }} 設備借用/取回保管單</p>
        </td>
    </tr>
</table>


<table class="pb-3" id="table101">
    <tr class="text-center">
        <td>借用設備</td>
        <td colspan="2">纜線數據機</td>
        <td colspan="2">數位機上盒</td>
        <td>光纖數據機</td>
        <td colspan="3">智能家庭</td>
    </tr>
    <tr class="text-center">
        <td>主機功能</td>
        <td>單埠</td>
        <td>WiFi</td>
        <td>基本型</td>
        <td>雙向型</td>
        <td>家計用ONT</td>
        <td>攝影機</td>
        <td>門窗感應器</td>
        <td>煙霧感測器</td>
    </tr>
    <tr class="text-center">
        <td>主機價值</td>
        <td class="font-s10">1,500 元/台</td>
        <td class="font-s10">2,500 元/台</td>
        <td class="font-s10">2,000 元/台</td>
        <td class="font-s10">3,000 元/台</td>
        <td class="font-s10">3,000 元/台</td>
        <td class="font-s10">1,500 元/台</td>
        <td class="font-s10">500 元/台</td>
        <td class="font-s10">800 元/台</td>
    </tr>
    <tr class="text-center">
        <td>借用數量</td>
        <td>{{ $data['CUSTODY']['Cable_modem_port'] }}</td>
        <td>{{ $data['CUSTODY']['Cable_modem_two_way'] }}</td>
        <td>{{ $data['CUSTODY']['Basic_digital_set_top_box'] }}</td>
        <td>{{ $data['CUSTODY']['Digital_set_top_box_two_way_type'] }}</td>
        <td>{{ $data['CUSTODY']['Fiber_Modem_HomeOnt'] }}</td>
        <td>{{ $data['CUSTODY']['camera'] }}</td>
        <td>{{ $data['CUSTODY']['Door_and_window_sensor'] }}</td>
        <td>{{ $data['CUSTODY']['Smoke_detector'] }}</td>
    </tr>
    <tr class="text-center">
        <td>取回數量</td>
        <td>{{ $data['CUSTODY']['get_Cable_modem_port'] }}</td>
        <td>{{ $data['CUSTODY']['get_Cable_modem_two_way'] }}</td>
        <td>{{ $data['CUSTODY']['get_Basic_digital_set_top_box'] }}</td>
        <td>{{ $data['CUSTODY']['get_Digital_set_top_box_two_way_type'] }}</td>
        <td>{{ $data['CUSTODY']['get_Fiber_Modem_HomeOnt'] }}</td>
        <td>{{ $data['CUSTODY']['get_camera'] }}</td>
        <td>{{ $data['CUSTODY']['get_Door_and_window_sensor'] }}</td>
        <td>{{ $data['CUSTODY']['get_Smoke_detector'] }}</td>
    </tr>
</table>
<table class="pb-3" id="table101">
    <tr class="text-center">
        <td>纜線數據機配件</td>
        <td>無線寬頻分享器</td>
        <td>變壓器電源線</td>
        <td>乙太網路線</td>
        <td>USB無線寬頻網卡</td>
    </tr>
    <tr class="text-center">
        <td>配件價值</td>
        <td class="font-s10">650 元/台</td>
        <td class="font-s10">300 元/個</td>
        <td class="font-s10">150 元/條</td>
        <td class="font-s10">600 元/個</td>
    </tr>
    <tr class="text-center">
        <td>借用數量</td>
        <td>{{ $data['CUSTODY']['Cable_accessories_wireless_anti_frequency_sharing_device'] }}</td>
        <td>{{ $data['CUSTODY']['Cable_accessories_transformer_power_cord'] }}</td>
        <td>{{ $data['CUSTODY']['Cable_accessories_Ethernet_cable'] }}</td>
        <td>{{ $data['CUSTODY']['Cable_accessories_USB_wireless_anti_frequency_network_card'] }}</td>
    </tr>
    <tr class="text-center">
        <td>取回數量</td>
        <td>{{ $data['CUSTODY']['get_Cable_accessories_wireless_anti_frequency_sharing_device'] }}</td>
        <td>{{ $data['CUSTODY']['get_Cable_accessories_transformer_power_cord'] }}</td>
        <td>{{ $data['CUSTODY']['get_Cable_accessories_Ethernet_cable'] }}</td>
        <td>{{ $data['CUSTODY']['get_Cable_accessories_USB_wireless_anti_frequency_network_card'] }}</td>
    </tr>
</table>
<table class="pb-3" id="table101">
    <tr class="text-center">
        <td>數位機上盒配件</td>
        <td>遙控器</td>
        <td>HDMI</td>
        <td>AV 線(1.SM)</td>
        <td>色差線(1.SM)</td>
        <td>變壓器電源線</td>
    </tr>
    <tr class="text-center">
        <td>配件價值</td>
        <td class="font-s10">300 元/支</td>
        <td class="font-s10">200 元/條</td>
        <td class="font-s10">25 元/條</td>
        <td class="font-s10">170 元/條</td>
        <td class="font-s10">300 元/個</td>
    </tr>
    <tr class="text-center">
        <td>借用數量</td>
        <td>{{ $data['CUSTODY']['Set_top_box_accessories_remote_control'] }}</td>
        <td>{{ $data['CUSTODY']['Set_top_box_accessories_HDI'] }}</td>
        <td>{{ $data['CUSTODY']['Set_top_box_accessories_AV_cable'] }}</td>
        <td>{{ $data['CUSTODY']['Set_top_box_accessories_Chromatic_aberration_line'] }}</td>
        <td>{{ $data['CUSTODY']['Set_top_box_accessories_transformer_power_cord'] }}</td>
    </tr>
    <tr class="text-center">
        <td>取回數量</td>
        <td>{{ $data['CUSTODY']['get_Set_top_box_accessories_remote_control'] }}</td>
        <td>{{ $data['CUSTODY']['get_Set_top_box_accessories_HDI'] }}</td>
        <td>{{ $data['CUSTODY']['get_Set_top_box_accessories_AV_cable'] }}</td>
        <td>{{ $data['CUSTODY']['get_Set_top_box_accessories_Chromatic_aberration_line'] }}</td>
        <td>{{ $data['CUSTODY']['get_Set_top_box_accessories_transformer_power_cord'] }}</td>
    </tr>
</table>
<table class="pb-3" id="table101">
    <tr class="text-center">
        <td>數位機上盒配件</td>
        <td>智慧卡</td>
        <td>外接式硬碟</td>
        <td>USB無線寬頻綱卡</td>
        <td>ATV 機上盒</td>
        <td>藍芽遙控器</td>
    </tr>
    <tr class="text-center">
        <td>配件價值</td>
        <td class="font-s10">250 元/張</td>
        <td class="font-s10">2,000 元/台</td>
        <td class="font-s10">600 元/個</td>
        <td class="font-s10">3,000元/台</td>
        <td class="font-s10">500元/支</td>
    </tr>
    <tr class="text-center">
        <td>借用數量</td>
        <td>{{ $data['CUSTODY']['Set_top_box_accessories_smart_card'] }}</td>
        <td>{{ $data['CUSTODY']['Set_top_box_accessories_external_hard_disk'] }}</td>
        <td>{{ $data['CUSTODY']['Set_top_box_accessories_USB_wireless_anti_frequency_network_card'] }}</td>
        <td>{{ $data['CUSTODY']['Set_top_box_accessories_ATV_set_top_box'] }}</td>
        <td>{{ $data['CUSTODY']['Set_top_box_accessories_Bluetooth_remote_control'] }}</td>
    </tr>
    <tr class="text-center">
        <td>取回數量</td>
        <td>{{ $data['CUSTODY']['get_Set_top_box_accessories_smart_card'] }}</td>
        <td>{{ $data['CUSTODY']['get_Set_top_box_accessories_external_hard_disk'] }}</td>
        <td>{{ $data['CUSTODY']['get_Set_top_box_accessories_USB_wireless_anti_frequency_network_card'] }}</td>
        <td>{{ $data['CUSTODY']['get_Set_top_box_accessories_ATV_set_top_box'] }}</td>
        <td>{{ $data['CUSTODY']['get_Set_top_box_accessories_Bluetooth_remote_control'] }}</td>
    </tr>
</table>
<table class="pb-3" id="table101">
    <tr class="text-center">
        <td>智能家庭配件</td>
        <td class="col-2">變壓器電源線</td>
        <td class="col-2">M4-Mesh</td>
        <td class="col-2"></td>
        <td class="col-2"></td>
        <td class="col-2"></td>
    </tr>
    <tr class="text-center">
        <td>配件價值</td>
        <td>300 元/個</td>
        <td>台</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr class="text-center">
        <td>借用數量</td>
        <td>{{ $data['CUSTODY']['Smart_home_accessories_transformer_power_cord'] }}</td>
        <td>{{ $data['CUSTODY']['HP_M4_Mesh'] }}</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr class="text-center">
        <td>取回數量</td>
        <td>{{ $data['CUSTODY']['get_Smart_home_accessories_transformer_power_cord'] }}</td>
        <td>{{ $data['CUSTODY']['get_HP_M4_Mesh'] }}</td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>

<table id="" class="wordWrap w-100">
    <tr>
        <td class="font-s10 line-h08">
            <p class="pl-2 mb-0">1、乙方借用之設備僅限申裝同址同人使用，不得移往他處，乙方使用期間應善盡保管之義務，如有不當處置、使用，致本借用設備毀損、滅失、減少正常功能、遭扣押丶或為第三人佔有時，乙方慮負完全損害賠償之責，乙方並同意賠償設備主機及配件價值如上。</p>
            <p class="pl-2 mb-0">2、乙方使用期間，設備因可歸責乙方事由發生故障時致需送修時，甲方得酌收維修材料及工資。</p>
            <p class="pl-2 mb-0">3、乙方停用/終止甲方有線電視（含數位加值）或 光纖寬頻網路之服務後 7 日內，應至甲方櫃檯完整歸回借用之設備，並經甲方確認型號／序號無誤者，甲方將開立結清退租之證明，提醒您，如未歸還恐將構成刑法侵占之虞，請務必注意。</p>
            <p class="pl-2 mb-0">4、退訂戶須憑【設備借用／取回保管單】丶【身分證】丶【印章】至本公司櫃檯辦理退租，請退訂戶於設備取回七日內至本公司櫃檯辦理退費。（如有委託人要提供其身分證及印章）。</p>
            <p class="pl-2 mb-0">5、退訂設備之主機及配件需完好無缺損，如發生損害或遺失時，本公司得自保證金及退還費用中酌扣材料費用。</p>
        </td>
    </tr>
</table>

{{--<i class="page_break"></i>--}}
