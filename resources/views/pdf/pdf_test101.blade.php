<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel 7 PDF</title>
    <link href="{{ asset('cns/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
</head>

<style type="text/css">
@font-face {
    font-family: SimHei;
    src: url('{{ asset("font/SimHei.ttf") }}') format('truetype');
}

* {
    font-family: SimHei;
}
</style>
<table border="1" style="width:100%;">
    <tr>
        <td>TEST 101</td>
        <td>
            @foreach($data as $t)
                <p>{{$t}}</p>
            @endforeach
        </td>
        <td>
            <button type="button" class="btn btn-primary">Primary</button>
            <button type="button" class="bg-info">Secondary</button>
        </td>
        <td style="width:15%; background-color: red;">派工類別</td>
        <td style="width:15%;">IVR簡碼</td>
        <td style="width:26%;">派工單序號</td>
        <td style="width:26%;">

{{--            <img src="{{ asset('/img/logo_01.png') }}"  >--}}
            <img src="{{ asset('img/logo_01.png') }}"  >
            <p>{{ asset('img/logo_01.png') }}</p>

        </td>

    </tr>

</table>
