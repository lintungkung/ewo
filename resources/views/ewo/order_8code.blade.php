<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
</head>
<style>
    .mb30p {
        margin-bottom: 30px;
    }
    .ml30p {
        margin-left: 30px;
    }
</style>
<body>
    {{ date('Y-m-d H:i:s') }}
    <hr>
    <button class="mb30p ml30p" onclick="alert('reload');location.reload()">reload</button>
    <button class="mb30p ml30p" onclick="wifi()">test wifi</button>
    <button class="mb30p ml30p" onclick="wifiSSID()">test wifiSSID</button>
    <button class="mb30p ml30p" onclick="openUrl('https://www.google.com.tw')">test openUrl</button>
    <button class="mb30p ml30p" onclick="androidId()">test androidId</button>
    <button class="mb30p ml30p" onclick="Fcm()">test Fcm</button>
    <button class="mb30p ml30p" onclick="Alarm('Dio', '無馱無馱無馱無馱無馱無馱!', 1,  '1')">test Alarm 1</button>
    <button class="mb30p ml30p" onclick="Alarm('JoJo', '歐拉歐拉歐拉歐拉歐拉歐拉!', 1,  '2')">test Alarm 2</button>
    <button class="mb30p ml30p" onclick="AlarmS()">test AlarmS</button>
       <a href="app://homeplus">OpenApp</a>
    <span id="show"></span>
</body>
<script>
function wifi() {
    let wifi = app.getWifi();
    document.getElementById('show').innerHTML = wifi;
    alert(wifi);
}

function wifiSSID() {
    let wifi = '';
    wifi = app.getWifiSSID();
    document.getElementById('show').innerHTML = wifi;
    alert(wifi);
}

function openUrl(url) {
    app.openUrl(url);
}


function androidId() {
    let androidId = app.getAndroidId();
    document.getElementById('show').innerHTML = androidId;
    alert(androidId);
}

function Fcm() {
    let Fcm = app.getFcmToken();
    document.getElementById('show').innerHTML = Fcm;
    alert(Fcm);
}

function AlarmS() {
    app.Alarm('title1', 'msg1', 5, '1001');
    app.Alarm('title2', 'msg2', 20, '1002');
    app.Alarm('title3', 'msg3', 35, '1003');
    app.Alarm('title4', 'msg4', 50, '1004');
    //alert('ok');
}
function Alarm(title, msg, sec, channel) {
    app.Alarm(title, msg, sec, channel);
}
</script>

</html>
