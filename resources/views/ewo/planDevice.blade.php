{{--@extends('ewo.layouts.default')--}}

<div class="container bg-grey collapse" id="plandevice" name="divpage">

    <div class="card w-100 mb-3">
        <div class="card-header" >
            <div class="input-group">
                設備準備清單
            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">工單</th>
                    <th scope="col">施工項目</th>
                    <th scope="col">建議設備</th>
                </tr>
                </thead>
                <tbody>
{{--                <tr>--}}
{{--                    <th scope="row">1</th>--}}
{{--                    <td>Mark</td>--}}
{{--                    <td>Otto</td>--}}
{{--                    <td>@mdo</td>--}}
{{--                </tr>--}}
                @foreach($p_data['orderList'] as $k => $t)
                    @if(!in_array(($t['SheetStatus']),['4.結款','4 結案','A 取消']))

                        @foreach($t['planDevice'] as $k2 => $t2)
                            @if($k2 !== 'null')
                                <tr>
                                    <th scope="row">{{ $k }}</th>
                                    <td>{{ $k2 }}</td>
                                    <td>{{ $t2 }}</td>
                                </tr>
                            @endif
                        @endforeach

                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    //
</script>
